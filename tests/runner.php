<?php
/**
 * Lightweight test runner.
 *
 * Lifecycle:
 *   1. Pick a free local TCP port and start `php -S` with tests/router.php.
 *   2. Wait until the server is accepting connections.
 *   3. Run every discovered test (each test method is isolated by the
 *      subprocess HTTP round-trip; no global state leaks between tests).
 *   4. Stop the server and report pass/fail counts.
 *
 * Usage:
 *   php tests/runner.php                                  # run all
 *   php tests/runner.php tests/AvatarGenerateTest.php     # one file
 *
 * Exit code is 0 on success and 1 on failure.
 */
declare(strict_types=1);

require_once __DIR__ . '/TestCase.php';

$argvLocal = $argv ?? [];
$only = [];
foreach (array_slice($argvLocal, 1) as $arg) {
    if (strpos($arg, '--') === 0) {
        continue;
    }
    $only[] = $arg;
}

$dir = __DIR__;
$files = glob($dir . '/*Test.php') ?: [];
$files = array_values(array_filter($files, static function (string $f): bool {
    return basename($f) !== 'TestCase.php';
}));

if (!empty($only)) {
    $filtered = [];
    foreach ($only as $needle) {
        foreach ($files as $f) {
            $b = basename($f);
            if ($b === basename($needle) || $b === basename($needle, '.php') . '.php') {
                $filtered[] = $f;
            }
        }
    }
    $files = $filtered;
}

if (empty($files)) {
    fwrite(STDERR, "no test files found\n");
    exit(2);
}

/* ------------------------------------------------------------------ */
/* Start the built-in PHP server                                      */
/* ------------------------------------------------------------------ */

$port = pickFreePort();
// We deliberately do NOT pass `-t <docroot>` so the built-in server
// uses the directory of router.php (tests/) as the document root.
// This matches what `php -S` does by default, and it makes the
// scripts' relative includes (e.g. require_once '../admin/connect.php')
// resolve to the right files. The router chdirs to the target script's
// directory before requiring it, so the scripts always see a sane cwd.
$cmd = sprintf(
    'exec %s -d error_reporting=0 -d display_errors=0 -S 127.0.0.1:%d %s',
    escapeshellarg(PHP_BINARY),
    $port,
    escapeshellarg(__DIR__ . '/router.php')
);
$descriptors = [
    0 => ['pipe', 'r'],
    1 => ['pipe', 'w'],
    2 => ['pipe', 'w'],
];
$server = proc_open($cmd, $descriptors, $serverPipes, __DIR__);
if (!is_resource($server)) {
    fwrite(STDERR, "failed to start test server\n");
    exit(2);
}

LgTestCase::$baseUrl = "http://127.0.0.1:$port";

// Wait for the server to be ready.
$ready = false;
for ($i = 0; $i < 50; $i++) {
    $errno = 0; $errstr = '';
    $sock = @stream_socket_client("tcp://127.0.0.1:$port", $errno, $errstr, 0.5);
    if ($sock) {
        fclose($sock);
        $ready = true;
        break;
    }
    usleep(100_000);
}
if (!$ready) {
    proc_terminate($server);
    fwrite(STDERR, "test server did not become ready on port $port\n");
    exit(2);
}

/* ------------------------------------------------------------------ */
/* Run tests                                                          */
/* ------------------------------------------------------------------ */

$totalTests = 0;
$failed = 0;
$errored = 0;
$start = microtime(true);

foreach ($files as $file) {
    $base = basename($file, '.php');
    require_once $file;
    if (!class_exists($base)) {
        fwrite(STDERR, "warning: $base does not define class $base\n");
        continue;
    }
    $ref = new ReflectionClass($base);
    if ($ref->isAbstract() || !$ref->isSubclassOf('LgTestCase')) {
        continue;
    }

    $instance = $ref->newInstance();
    $hasSetUp    = $ref->hasMethod('setUp')    && $ref->getMethod('setUp')->getDeclaringClass()->getName() !== 'LgTestCase';
    $hasTearDown = $ref->hasMethod('tearDown') && $ref->getMethod('tearDown')->getDeclaringClass()->getName() !== 'LgTestCase';

    foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $m) {
        if ($m->isStatic() || $m->isAbstract()) {
            continue;
        }
        if (strpos($m->getName(), 'test') !== 0) {
            continue;
        }
        $totalTests++;
        $name = $base . '::' . $m->getName();
        $t0 = microtime(true);
        try {
            if ($hasSetUp) {
                $ref->getMethod('setUp')->invoke($instance);
            }
            $m->invoke($instance);
            if ($hasTearDown) {
                $ref->getMethod('tearDown')->invoke($instance);
            }
        } catch (LgTestFailureException $e) {
            $failed++;
            printf("  FAIL  %s  (%0.1f ms)\n    %s\n", $name, (microtime(true) - $t0) * 1000, $e->getMessage());
            continue;
        } catch (Throwable $e) {
            $errored++;
            printf("  ERROR %s  (%0.1f ms)\n    %s: %s\n    at %s:%d\n",
                $name, (microtime(true) - $t0) * 1000, get_class($e), $e->getMessage(),
                $e->getFile(), $e->getLine());
            continue;
        }
        printf("  ok    %s  (%0.1f ms)\n", $name, (microtime(true) - $t0) * 1000);
    }
}

$elapsed = (microtime(true) - $start) * 1000;
printf("\n%d tests, %d failed, %d errored (%0.1f ms)\n", $totalTests, $failed, $errored, $elapsed);

/* ------------------------------------------------------------------ */
/* Shutdown                                                           */
/* ------------------------------------------------------------------ */

proc_terminate($server, 9);
foreach ($serverPipes as $p) { @fclose($p); }
proc_close($server);

exit(($failed + $errored) === 0 ? 0 : 1);

/* ------------------------------------------------------------------ */
/* Helpers                                                            */
/* ------------------------------------------------------------------ */

function pickFreePort(): int
{
    $sock = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
    if ($sock === false) {
        fwrite(STDERR, "could not allocate free port: $errstr\n");
        exit(2);
    }
    $name = stream_socket_get_name($sock, false);
    fclose($sock);
    $port = (int) substr((string) $name, strrpos((string) $name, ':') + 1);
    if ($port <= 0) {
        fwrite(STDERR, "could not derive allocated port from $name\n");
        exit(2);
    }
    return $port;
}
