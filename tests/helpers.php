<?php
/**
 * Test helpers shared across the Like Girl - LGNewUi regression suite.
 */

declare(strict_types=1);

namespace LikeGirl\Tests;

if (!function_exists('LikeGirl\\Tests\\loadFunctionPhp')) {
    /**
     * Include admin/Function.php into a sandboxed scope.
     *
     * admin/Function.php calls session_start() at the top of the file and
     * references $_SERVER['REMOTE_ADDR']. Loading it inside a function lets
     * each test get a fresh, isolated state without polluting the global
     * session or the global $_SERVER superglobal.
     *
     * After this returns, the test may freely call the file's functions:
     *   - checkQQ()
     *   - replaceSpecialChar()
     *   - escapeXSS()
     *   - generateCSRFToken() / verifyCSRFToken()
     *   - time_tran()
     *   - get_ip_city_New()  (network-dependent; only the input-validation
     *                         branches are exercised by the tests).
     */
    function loadFunctionPhp(): void
    {
        // Already loaded? Skip to keep $_SESSION stable across the test.
        if (function_exists('LikeGirl\\Tests\\_functionPhpLoaded')) {
            return;
        }

        $path = dirname(__DIR__) . '/admin/Function.php';
        if (!is_file($path)) {
            throw new \RuntimeException("Cannot find admin/Function.php at {$path}");
        }
        require_once $path;

        // Mark as loaded so repeated calls are a no-op.
        eval('namespace LikeGirl\\Tests; function _functionPhpLoaded(): bool { return true; }');
    }
}

if (!function_exists('LikeGirl\\Tests\\resetSession')) {
    /**
     * Reset the superglobal $_SESSION between tests.
     */
    function resetSession(): void
    {
        $_SESSION = [];
    }
}
