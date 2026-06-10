<?php
/**
 * PHPUnit bootstrap for Like Girl - LGNewUi regression tests.
 *
 * - Initializes a clean, isolated PHP superglobal state.
 * - Stages a fake admin/Config_DB.php so that production files which
 *   `include_once` it do not fail with "file not found". The real
 *   config is .gitignore'd, so we copy in a stub and clean it up
 *   on shutdown.
 */

declare(strict_types=1);

$_SERVER['HTTP_HOST']      = $_SERVER['HTTP_HOST']      ?? 'localhost';
$_SERVER['REMOTE_ADDR']    = $_SERVER['REMOTE_ADDR']    ?? '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$_SERVER['REQUEST_URI']    = $_SERVER['REQUEST_URI']    ?? '/';
$_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? 'PHPUnit';

// Stage a stub config file in admin/ so production includes succeed.
$stubFile  = __DIR__ . '/Fixtures/Config_DB_stub.php';
$realConfig = dirname(__DIR__) . '/admin/Config_DB.php';
$hadReal   = file_exists($realConfig);
if (!$hadReal) {
    copy($stubFile, $realConfig);
    register_shutdown_function(static function () use ($realConfig): void {
        if (file_exists($realConfig)) {
            @unlink($realConfig);
        }
    });
}

// Each test starts with a clean $_SESSION.
$_SESSION = [];

require_once __DIR__ . '/helpers.php';
