<?php
/**
 * Regression tests for services/info-service.php
 *
 * info-service.php is a public JSON endpoint. The test surface we lock
 * down here is the request-validation and graceful-degradation path:
 *
 *   - Unknown `action` values must be rejected (whitelist enforcement).
 *   - The heartbeat action must always succeed (it is used for
 *     keep-alive checks from the front-end chat component).
 *   - The default (no action) response must announce the service is up.
 *   - When the database is unreachable, get_stats / get_location must
 *     fall back to zero-filled / default values rather than 500ing.
 *
 * The script calls exit() on invalid action, so we run it in a child
 * PHP process and parse the captured stdout. This keeps the PHPUnit
 * runner alive across all assertions.
 *
 * The database-backed distance calculation (Haversine) is not exercised
 * here because it requires a live MySQL connection. The
 * services/visitor-stats.php and services/weather.php suites cover the
 * remaining "no-DB" degradation paths.
 */

declare(strict_types=1);

namespace LikeGirl\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class InfoServiceTest extends TestCase
{
    private const SCRIPT = __DIR__ . '/../../services/info-service.php';

    /**
     * Run info-service.php in a child PHP process and return the
     * decoded JSON body. Using a subprocess is required because the
     * production script calls exit() on the invalid-action path.
     *
     * @return array{exit:int, body:array<string,mixed>, stderr:string}
     */
    private function runScript(array $get = [], array $post = []): array
    {
        $scriptPath = realpath(self::SCRIPT);
        $this->assertNotFalse($scriptPath, 'info-service.php must exist');

        $boot = realpath(__DIR__ . '/../bootstrap.php');
        $this->assertNotFalse($boot, 'bootstrap.php must exist');

        // Build a query string. We urlencode to keep values safe.
        $query = http_build_query(array_merge($get, $post));
        // We pass ?… on the CLI so the script picks it up via $_GET.
        $cmd = sprintf(
            '%s -d error_reporting=0 -d display_errors=0 -r %s 2>&1',
            escapeshellarg(PHP_BINARY),
            escapeshellarg(
                'parse_str(' . var_export($query, true) . ', $p);' .
                '$_GET = $p; chdir(' . var_export(dirname($scriptPath), true) . ');' .
                'include ' . var_export($scriptPath, true) . ';'
            )
        );

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $proc = proc_open($cmd, $descriptors, $pipes);
        $this->assertIsResource($proc, 'proc_open must succeed');

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exitCode = proc_close($proc);

        $this->assertNotFalse(
            $stdout,
            'info-service.php produced no output; stderr=' . $stderr
        );

        // The body is a single JSON object somewhere in the output.
        // Take everything from the FIRST '{' to the LAST '}' — this
        // survives any preceding warnings or notices that may have
        // been printed (we ask PHP to suppress them, but defensive
        // parsing keeps the test robust if the script starts printing
        // extra debug output in a future patch).
        $firstBrace = strpos($stdout, '{');
        $lastBrace  = strrpos($stdout, '}');
        $this->assertNotFalse(
            $firstBrace,
            "info-service.php must emit JSON; got: {$stdout}\nstderr: {$stderr}"
        );
        $this->assertNotFalse(
            $lastBrace,
            "info-service.php must emit JSON; got: {$stdout}\nstderr: {$stderr}"
        );
        $this->assertGreaterThan(
            $firstBrace,
            $lastBrace,
            "info-service.php output has no JSON object; got: {$stdout}"
        );
        $body = substr($stdout, $firstBrace, $lastBrace - $firstBrace + 1);

        $decoded = json_decode($body, true);
        $this->assertIsArray(
            $decoded,
            "info-service.php must return JSON; got: {$body}\nstderr: {$stderr}"
        );

        return [
            'exit'  => (int) $exitCode,
            'body'  => $decoded,
            'stderr'=> $stderr,
        ];
    }

    public function testDefaultActionReportsServiceRunning(): void
    {
        $r = $this->runScript();
        $this->assertSame(0, $r['exit']);
        $this->assertSame(200, $r['body']['code']);
        $this->assertSame(
            'LG-NewUi Service is running',
            $r['body']['message']
        );
        $this->assertArrayHasKey('timestamp', $r['body']);
    }

    public function testHeartbeatActionReturnsPong(): void
    {
        $r = $this->runScript(['action' => 'heartbeat']);
        $this->assertTrue($r['body']['success']);
        $this->assertSame(200,  $r['body']['code']);
        $this->assertSame('pong', $r['body']['message']);
    }

    public function testUnknownActionIsRejectedWith400Status(): void
    {
        // The whitelist is the first line of defence against
        // reflection-style abuse on public endpoints; a regression that
        // strips it would let attackers route to private code paths.
        $r = $this->runScript(['action' => 'definitely_not_real']);
        $this->assertFalse($r['body']['success']);
        $this->assertSame('Invalid action parameter', $r['body']['error']);
    }

    public function testGetStatsFallsBackToZeroFilledShapeWhenDbUnreachable(): void
    {
        // No DB is configured under test. The handler must still return a
        // 200 with the documented zero-filled shape, not a 500.
        $r = $this->runScript(['action' => 'get_stats']);
        $this->assertTrue($r['body']['success']);
        $this->assertSame(200, $r['body']['code']);
        $this->assertSame(
            ['articles' => 0, 'photos' => 0, 'messages' => 0, 'days' => 0],
            $r['body']['data']
        );
    }

    public function testGetLocationFallsBackToDefaultValuesWhenDbUnreachable(): void
    {
        // The "degrade gracefully" contract is critical: the front-end
        // distance widget depends on always getting back the documented
        // keys even when the DB is down.
        $r = $this->runScript(['action' => 'get_location']);
        $this->assertTrue($r['body']['success']);
        $this->assertSame(200, $r['body']['code']);

        $data = $r['body']['data'];
        $this->assertSame('北京', $data['boyCity']);
        $this->assertSame('上海', $data['girlCity']);
        $this->assertSame(39.9042, $data['boyLat']);
        $this->assertSame(116.4074, $data['boyLng']);
        $this->assertSame(31.2304, $data['girlLat']);
        $this->assertSame(121.4737, $data['girlLng']);
        $this->assertSame(0, $data['distance']);
    }

    public function testGeoAliasIsAcceptedAsValidAction(): void
    {
        // 'geo' is a documented alias in the whitelist. A regression
        // that removes it would break the front-end geo widget silently.
        $r = $this->runScript(['action' => 'geo']);
        $this->assertTrue($r['body']['success']);
    }
}
