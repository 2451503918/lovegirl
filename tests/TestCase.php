<?php
/**
 * Minimal assertion + harness helpers for regression tests.
 *
 * The project has no composer/phpunit dependency, so this file ships a
 * tiny, zero-dependency test runner and assertion library in plain PHP.
 *
 * Conventions:
 *  - Test classes extend LgTestCase.
 *  - Public methods whose name starts with "test" are auto-discovered.
 *  - The runner prints a TAP-ish summary and exits non-zero on failure.
 *
 * The harness is intentionally small. It is NOT a replacement for PHPUnit; it
 * exists to add a deterministic regression net around the recently-merged
 * service files (avatar-proxy.php, avatar-generate.php, info-service.php,
 * visitor-stats.php) that previously had zero coverage.
 *
 * How HTTP endpoints are tested:
 *  The runner starts a single `php -S` built-in server (served by
 *  tests/router.php) on a free local port, runs every test, then stops
 *  the server. Tests call request() to obtain a real HTTP response with
 *  the full set of response headers, status code and body. Using a real
 *  HTTP round-trip is what lets us assert on Content-Type, CORS, exit
 *  status and other SAPI-only behavior that CLI subprocesses cannot
 *  exercise.
 */
declare(strict_types=1);

class LgTestCase
{
    /** Base URL of the test server (set by the runner). */
    public static string $baseUrl = '';

    /* ------------------------------------------------------------------ */
    /* Assertion helpers                                                   */
    /* ------------------------------------------------------------------ */

    protected function assertTrue(bool $cond, string $msg = 'expected true'): void
    {
        if (!$cond) {
            $this->fail($msg);
        }
    }

    protected function assertFalse(bool $cond, string $msg = 'expected false'): void
    {
        if ($cond) {
            $this->fail($msg);
        }
    }

    protected function assertEquals($expected, $actual, string $msg = ''): void
    {
        if ($expected !== $actual) {
            $this->fail(
                $msg !== '' ? $msg : sprintf(
                    "expected %s, got %s",
                    var_export($expected, true),
                    var_export($actual, true)
                )
            );
        }
    }

    protected function assertNotEquals($expected, $actual, string $msg = ''): void
    {
        if ($expected === $actual) {
            $this->fail($msg !== '' ? $msg : 'values should not be equal');
        }
    }

    protected function assertContains(string $needle, string $haystack, string $msg = ''): void
    {
        if (strpos($haystack, $needle) === false) {
            $this->fail(
                $msg !== '' ? $msg : sprintf(
                    "expected substring %s in body, got: %s",
                    var_export($needle, true),
                    substr($haystack, 0, 200)
                )
            );
        }
    }

    protected function assertNotContains(string $needle, string $haystack, string $msg = ''): void
    {
        if (strpos($haystack, $needle) !== false) {
            $this->fail(
                $msg !== '' ? $msg : sprintf(
                    "did not expect substring %s in body, got: %s",
                    var_export($needle, true),
                    substr($haystack, 0, 200)
                )
            );
        }
    }

    protected function assertHeaderContains(array $headers, string $needle, string $msg = ''): void
    {
        foreach ($headers as $h) {
            if (stripos($h, $needle) !== false) {
                return;
            }
        }
        $this->fail(
            $msg !== '' ? $msg : sprintf(
                "expected header containing %s, got headers: %s",
                var_export($needle, true),
                json_encode($headers)
            )
        );
    }

    protected function assertHeaderNotContains(array $headers, string $needle, string $msg = ''): void
    {
        foreach ($headers as $h) {
            if (stripos($h, $needle) !== false) {
                $this->fail(
                    $msg !== '' ? $msg : sprintf(
                        "did not expect header containing %s, but found it in: %s",
                        var_export($needle, true),
                        json_encode($headers)
                    )
                );
            }
        }
    }

    protected function assertSameSize(int $expected, string $body, string $msg = ''): void
    {
        $actual = strlen($body);
        if ($actual !== $expected) {
            $this->fail(
                $msg !== '' ? $msg : sprintf(
                    "expected body length %d, got %d",
                    $expected,
                    $actual
                )
            );
        }
    }

    protected function assertStartsWith(string $prefix, string $body, string $msg = ''): void
    {
        if (strpos($body, $prefix) !== 0) {
            $this->fail(
                $msg !== '' ? $msg : sprintf(
                    "expected body to start with %s, got: %s",
                    var_export($prefix, true),
                    substr($body, 0, 32)
                )
            );
        }
    }

    /* ------------------------------------------------------------------ */
    /* HTTP request helper                                                 */
    /* ------------------------------------------------------------------ */

    /**
     * Make an HTTP request to the test server.
     *
     * @param array<string,string> $server Extra values to merge into
     *   the request's $_SERVER (e.g. HTTP_ORIGIN, HTTP_X_FOO).
     * @return array{status:int,headers:array<string>,body:string,raw:string}
     */
    protected function request(string $path, string $method = 'GET', array $post = [], array $server = []): array
    {
        if (self::$baseUrl === '') {
            $this->fail('test server is not running; baseUrl not set');
        }

        $url = self::$baseUrl . $path;
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER         => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CUSTOMREQUEST  => $method,
        ]);
        if (strtoupper($method) === 'POST' && !empty($post)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        $extraHeaders = [];
        if (isset($server['HTTP_ORIGIN'])) {
            $extraHeaders[] = 'Origin: ' . $server['HTTP_ORIGIN'];
        }
        if (!empty($extraHeaders)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $extraHeaders);
        }

        $raw = curl_exec($ch);
        if ($raw === false) {
            $err = curl_error($ch);
            curl_close($ch);
            $this->fail('curl error: ' . $err);
        }
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        $hdrSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $rawHeaders = substr((string) $raw, 0, $hdrSize);
        $body = substr((string) $raw, $hdrSize);

        $headers = [];
        foreach (preg_split("/\r?\n/", $rawHeaders) as $line) {
            $line = trim($line);
            if ($line === '' || stripos($line, 'HTTP/') === 0) {
                continue;
            }
            $headers[] = $line;
        }

        return [
            'status'  => $status,
            'headers' => $headers,
            'body'    => $body,
            'raw'     => (string) $raw,
        ];
    }

    /* ------------------------------------------------------------------ */

    protected function fail(string $msg): void
    {
        throw new LgTestFailureException($msg);
    }
}

/**
 * Thrown by LgTestCase::fail() to signal an assertion failure without
 * aborting the entire test run. The runner catches it per test method.
 */
class LgTestFailureException extends RuntimeException
{
}
