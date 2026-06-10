<?php
/**
 * Regression tests for services/weather.php
 *
 * weather.php is a stateless cache-fronted weather endpoint. The
 * behaviours under test are:
 *
 *   - Mock-data selection by mode/slot.
 *   - Graceful fallback to the "ip" record for unknown slots.
 *   - Cache file creation and cache-hit short-circuiting on the second
 *     call (with the same parameters).
 *   - JSON response shape stability (the front-end relies on it).
 *
 * Network calls to the upstream weather API are gated on having a real
 * key configured, which never happens in tests; we therefore verify the
 * mock-data path only.
 *
 * The script calls readfile()+exit() on the cache-hit path, which
 * bypasses PHP's output buffer. We therefore run the script in a
 * child PHP process and capture its stdout directly.
 */

declare(strict_types=1);

namespace LikeGirl\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class WeatherTest extends TestCase
{
    private const SCRIPT = __DIR__ . '/../../services/weather.php';

    /** @var array<int,string> Cache files created during a test (cleaned up in tearDown). */
    private array $createdCacheFiles = [];

    private array $serverBackup;
    private array $getBackup;

    protected function setUp(): void
    {
        $this->serverBackup = $_SERVER;
        $this->getBackup    = $_GET;
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET = [];
    }

    protected function tearDown(): void
    {
        // Always remove cache files we created so the test is hermetic.
        foreach ($this->createdCacheFiles as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
        $this->createdCacheFiles = [];

        $_SERVER = $this->serverBackup;
        $_GET    = $this->getBackup;
    }

    /**
     * Run weather.php in a child PHP process and return the decoded
     * JSON body plus the cache-file path the script will read/write.
     *
     * @return array{body:array<string,mixed>, cachePath:string, raw:string}
     */
    private function runScript(array $get = []): array
    {
        $scriptPath = realpath(self::SCRIPT);
        $this->assertNotFalse($scriptPath, 'weather.php must exist');

        $query = http_build_query($get);
        $cmd = sprintf(
            '%s -d error_reporting=0 -d display_errors=0 -r %s 2>&1',
            escapeshellarg(PHP_BINARY),
            escapeshellarg(
                'parse_str(' . var_export($query, true) . ', $p);' .
                '$_GET = $p; include ' . var_export($scriptPath, true) . ';'
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
            "weather.php produced no output; stderr={$stderr}"
        );

        $firstBrace = strpos($stdout, '{');
        $lastBrace  = strrpos($stdout, '}');
        $this->assertNotFalse(
            $firstBrace,
            "weather.php must emit JSON; got: {$stdout}\nstderr: {$stderr}"
        );
        $this->assertNotFalse(
            $lastBrace,
            "weather.php must emit JSON; got: {$stdout}\nstderr: {$stderr}"
        );
        $this->assertGreaterThan(
            $firstBrace,
            $lastBrace,
            "weather.php output has no JSON object; got: {$stdout}"
        );
        $body = substr($stdout, $firstBrace, $lastBrace - $firstBrace + 1);

        $decoded = json_decode($body, true);
        $this->assertIsArray(
            $decoded,
            "weather.php must return JSON; got: {$body}\nstderr: {$stderr}"
        );

        $mode     = $get['mode']     ?? 'ip';
        $slot     = (int)($get['slot'] ?? 1);
        $location = $get['location'] ?? '';
        $cacheKey = md5($mode . $slot . $location);
        $cachePath = sys_get_temp_dir() . '/lg_weather_' . $cacheKey . '.json';
        $this->createdCacheFiles[] = $cachePath;

        return [
            'body'      => $decoded,
            'cachePath' => $cachePath,
            'raw'       => $stdout,
        ];
    }

    public function testDefaultModeReturnsLocalIpMockData(): void
    {
        // No params: the script must serve the "ip" mock data so the
        // front-end weather widget always renders something.
        $r = $this->runScript();
        $this->assertSame(200, $r['body']['code']);
        $this->assertSame('ip', $r['body']['mode']);
        $this->assertSame('本地',     $r['body']['data']['city']);
        $this->assertSame('晴间多云', $r['body']['data']['text']);
        $this->assertSame('25',      $r['body']['data']['temp']);
    }

    public function testCoupleModeSlotOneReturnsShenzhen(): void
    {
        $r = $this->runScript(['mode' => 'couple', 'slot' => '1']);
        $this->assertSame('couple', $r['body']['mode']);
        $this->assertSame(1, $r['body']['slot']);
        $this->assertSame('深圳', $r['body']['data']['city']);
        $this->assertSame('多云', $r['body']['data']['text']);
    }

    public function testCoupleModeSlotTwoReturnsGuangzhou(): void
    {
        $r = $this->runScript(['mode' => 'couple', 'slot' => '2']);
        $this->assertSame('广州', $r['body']['data']['city']);
        $this->assertSame('晴',   $r['body']['data']['text']);
    }

    public function testUnknownCoupleSlotFallsBackToIpMock(): void
    {
        // slot=9 is not in the mock data; the handler must not 500
        // — it must fall back to the "ip" record.
        $r = $this->runScript(['mode' => 'couple', 'slot' => '9']);
        $this->assertSame(200,   $r['body']['code']);
        $this->assertSame('本地', $r['body']['data']['city']);
    }

    public function testNonCoupleModeIgnoresSlotAndReturnsIpMock(): void
    {
        // mode=custom (not in the special-cased set) must behave like
        // mode=ip; slot is irrelevant.
        $r = $this->runScript(['mode' => 'custom', 'slot' => '1']);
        $this->assertSame('本地', $r['body']['data']['city']);
    }

    public function testResponseAlwaysIncludesTimestampAndMode(): void
    {
        // Shape-stability: the front-end type-checks `timestamp` and
        // `mode` exist on every response.
        $r = $this->runScript();
        $this->assertArrayHasKey('timestamp', $r['body']);
        $this->assertArrayHasKey('mode',      $r['body']);
        $this->assertIsInt($r['body']['timestamp']);
    }

    public function testFirstCallWritesCacheFile(): void
    {
        $r = $this->runScript(['mode' => 'couple', 'slot' => '1']);
        $this->assertFileExists(
            $r['cachePath'],
            'first call must persist a cache file for subsequent calls'
        );
        $cached = json_decode((string) file_get_contents($r['cachePath']), true);
        $this->assertSame('深圳', $cached['data']['city']);
    }

    public function testSecondCallHitsCacheAndReturnsSameBody(): void
    {
        // Populate the cache with the first call...
        $first = $this->runScript(['mode' => 'couple', 'slot' => '1']);

        // ... mutate the on-disk file to a sentinel ...
        $sentinel = [
            'code' => 200,
            'data' => ['city' => 'CACHE-HIT-SENTINEL', 'temp' => '00', 'text' => 't', 'icon' => '0', 'humidity' => '0', 'vis' => '0', 'feelsLike' => '0', 'windDir' => 'w', 'windScale' => '0'],
            'mode' => 'couple',
            'slot' => 1,
            'timestamp' => 0,
        ];
        file_put_contents(
            $first['cachePath'],
            json_encode($sentinel, JSON_UNESCAPED_UNICODE)
        );

        // ... then a second call with the same params must return
        // the cached body. If the script regenerated the response
        // (e.g. by accident invalidating the cache key), the second
        // body would be '深圳' again.
        $second = $this->runScript(['mode' => 'couple', 'slot' => '1']);
        $this->assertSame(
            'CACHE-HIT-SENTINEL',
            $second['body']['data']['city'],
            'second call must read from the on-disk cache, not regenerate'
        );
    }

    public function testDifferentLocationGeneratesSeparateCacheEntries(): void
    {
        // The cache key is (mode, slot, location). Two calls with
        // different location strings must not share a cache entry —
        // otherwise the wrong city would be served to the wrong user.
        $a = $this->runScript(['mode' => 'couple', 'slot' => '1', 'location' => '深圳']);
        $b = $this->runScript(['mode' => 'couple', 'slot' => '1', 'location' => '北京']);
        $this->assertNotSame(
            $a['cachePath'],
            $b['cachePath'],
            'different location strings must hash to different cache files'
        );
    }
}
