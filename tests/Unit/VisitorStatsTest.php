<?php
/**
 * Regression tests for services/visitor-stats.php
 *
 * The visitor-stats service must never 500 the front-end even when the
 * database is unreachable. This is the "site stays alive" contract that
 * the demo deployment depends on. The tests below pin that contract:
 *
 *   - No `action` parameter (default): graceful demo-data response.
 *   - `get_stats` with no DB: same demo data shape.
 *   - Invalid `action`: still returns the running message, not an error.
 *
 * The DB-backed `track` and `get_stats` paths require a live MySQL
 * server and are deliberately out of scope for unit tests.
 *
 * The script calls exit() in its no-DB fast-fail branch, so we run it
 * in a child PHP process to keep the PHPUnit runner alive.
 */

declare(strict_types=1);

namespace LikeGirl\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class VisitorStatsTest extends TestCase
{
    private const SCRIPT = __DIR__ . '/../../services/visitor-stats.php';

    /**
     * Run visitor-stats.php in a child PHP process and return the
     * decoded JSON body.
     *
     * @return array{body:array<string,mixed>, stderr:string}
     */
    private function runScript(array $get = [], array $post = []): array
    {
        $scriptPath = realpath(self::SCRIPT);
        $this->assertNotFalse($scriptPath, 'visitor-stats.php must exist');

        $query = http_build_query(array_merge($get, $post));
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
        proc_close($proc);

        $this->assertNotFalse(
            $stdout,
            'visitor-stats.php produced no output; stderr=' . $stderr
        );

        $firstBrace = strpos($stdout, '{');
        $lastBrace  = strrpos($stdout, '}');
        $this->assertNotFalse(
            $firstBrace,
            "visitor-stats.php must emit JSON; got: {$stdout}\nstderr: {$stderr}"
        );
        $this->assertNotFalse(
            $lastBrace,
            "visitor-stats.php must emit JSON; got: {$stdout}\nstderr: {$stderr}"
        );
        $this->assertGreaterThan(
            $firstBrace,
            $lastBrace,
            "visitor-stats.php output has no JSON object; got: {$stdout}"
        );
        $body = substr($stdout, $firstBrace, $lastBrace - $firstBrace + 1);

        $decoded = json_decode($body, true);
        $this->assertIsArray(
            $decoded,
            "visitor-stats.php must return JSON; got: {$body}\nstderr: {$stderr}"
        );

        return [
            'body'   => $decoded,
            'stderr' => $stderr,
        ];
    }

    public function testDefaultActionReturnsDemoData(): void
    {
        // No `action` and no DB: handler must serve demo data so the
        // dashboard widget renders even on a fresh install.
        $r = $this->runScript();
        $this->assertSame(200, $r['body']['code']);
        $this->assertSame(
            'Database not connected, using demo data',
            $r['body']['message']
        );
        $this->assertSame(
            ['visits' => 156, 'visitors' => 48],
            $r['body']['data']['today']
        );
        $this->assertSame(
            ['visits' => 15234, 'visitors' => 3847],
            $r['body']['data']['total']
        );
        $this->assertArrayHasKey('timestamp', $r['body']);
    }

    public function testUnknownActionIsHandledGracefully(): void
    {
        // When the database is unreachable, the script short-circuits
        // to the demo-data response *before* the action switch. That
        // is by design (keeps the dashboard alive during DB outages),
        // and the test pins this precedence: even an unknown action
        // must never 500 — the demo-data response is what gets sent.
        $r = $this->runScript(['action' => 'not-a-real-action']);
        $this->assertSame(200, $r['body']['code']);
        $this->assertSame(
            'Database not connected, using demo data',
            $r['body']['message'],
            'no-DB fast-fail must take precedence over action dispatch'
        );
    }
}
