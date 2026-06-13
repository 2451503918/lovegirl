<?php
/**
 * Test HTTP router.
 *
 * The PHP built-in server is started with this file as its router. Each
 * request asks for a service file by name via ?__target=NAME, and we
 * include the corresponding file from the project's services/ directory
 * after stripping the routing parameter. This lets tests exercise the
 * production service scripts end-to-end (including header() / exit()).
 *
 * Only the targets listed in $allowedTargets are reachable. Anything
 * else returns 400, so a misbehaving test cannot reach into the rest
 * of the project tree.
 */
declare(strict_types=1);

$allowedTargets = [
    'avatar-proxy'        => __DIR__ . '/../services/avatar-proxy.php',
    'avatar-generate'     => __DIR__ . '/../services/avatar-generate.php',
    'info-service'        => __DIR__ . '/../services/info-service.php',
    'visitor-stats'       => __DIR__ . '/../services/visitor-stats.php',
    'access-beacon'       => __DIR__ . '/../services/access-beacon.php',
    'random-quote'        => __DIR__ . '/../services/random_quote.php',
];

$target = isset($_GET['__target']) ? (string) $_GET['__target'] : '';
if ($target === '') {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo "missing __target";
    return true;
}

if (!isset($allowedTargets[$target])) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo "unknown target: $target";
    return true;
}

unset($_GET['__target']);
$script = $allowedTargets[$target];
if (!is_file($script)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "target file missing: $script";
    return true;
}

// chdir to the script's directory so its relative includes
// (e.g. require_once '../admin/connect.php') resolve correctly.
chdir(dirname($script));

require $script;
return true;
