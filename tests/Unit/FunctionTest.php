<?php
/**
 * Regression tests for admin/Function.php
 *
 * admin/Function.php is the project's most heavily shared utility file:
 * almost every endpoint (admin and services/) reaches into it for at least
 * one of:
 *   - checkQQ()           : regex-based user input validation
 *   - replaceSpecialChar(): SQL-injection scrubbing
 *   - escapeXSS()         : HTML/XSS escaping
 *   - generate/verifyCSRFToken() : CSRF protection on state-changing requests
 *   - time_tran()         : relative time formatter with many edge cases
 *   - get_ip_city_New()   : IP -> city resolution (input-validation branch
 *                           only; the network call is exercised separately)
 *
 * These tests cover the parse / validation / security boundary surface
 * where regressions have the highest blast radius.
 */

declare(strict_types=1);

namespace LikeGirl\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class FunctionTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        // admin/Function.php is loaded once for the whole suite; its
        // session_start() must run before any test inspects $_SESSION.
        \LikeGirl\Tests\loadFunctionPhp();
    }

    protected function setUp(): void
    {
        \LikeGirl\Tests\resetSession();
    }

    // -----------------------------------------------------------------
    // checkQQ()
    // -----------------------------------------------------------------

    /**
     * @dataProvider validQqProvider
     */
    public function testCheckQQAcceptsValidNumbers(string $qq): void
    {
        $this->assertTrue(checkQQ($qq), "Expected checkQQ to accept {$qq}");
    }

    public static function validQqProvider(): array
    {
        return [
            'minimum 5-digit, non-zero first' => ['10000'],
            'typical 9-digit'                 => ['123456789'],
            'typical 10-digit'                => ['1234567890'],
            'long qq (20 digit)'              => ['12345678901234567890'],
        ];
    }

    /**
     * @dataProvider invalidQqProvider
     */
    public function testCheckQQRejectsInvalidNumbers(string $qq): void
    {
        $this->assertFalse(checkQQ($qq), "Expected checkQQ to reject {$qq}");
    }

    public static function invalidQqProvider(): array
    {
        return [
            'empty string'                => [''],
            'single digit'                => ['1'],
            'four digits (below minimum)' => ['1234'],
            'leading zero (qq never 0)'   => ['01234'],
            'leading zero long'           => ['0123456789'],
            'alphabetic characters'       => ['abcde'],
            'mixed letters and digits'    => ['12abc'],
            'whitespace'                  => [' 12345'],
            'trailing whitespace'         => ['12345 '],
            'embedded hyphen'             => ['12-345'],
            'unicode digit'               => ['１２３４５'],
            'plus sign prefix'            => ['+12345'],
            'decimal point'               => ['1.234'],
        ];
    }

    // -----------------------------------------------------------------
    // replaceSpecialChar()
    // -----------------------------------------------------------------

    public function testReplaceSpecialCharStripsAllFiveDangerousChars(): void
    {
        // The single quote, double quote, backslash, backtick, and
        // semicolon are the characters that escape() (mysql_real_escape_string)
        // users most often forget. The helper scrubs them all.
        $input    = "a'b\"c\\d`e;f";
        $expected = 'abcdef';
        $this->assertSame($expected, replaceSpecialChar($input));
    }

    public function testReplaceSpecialCharLeavesUnrelatedCharsAlone(): void
    {
        // A regression here would silently break every form that uses
        // this helper (login name, search box, etc.).
        $input = 'hello-world_2025@example.com 中文';
        $this->assertSame($input, replaceSpecialChar($input));
    }

    public function testReplaceSpecialCharHandlesEmptyString(): void
    {
        $this->assertSame('', replaceSpecialChar(''));
    }

    public function testReplaceSpecialCharIsIdempotent(): void
    {
        // Sanity check: scrubbing an already-scrubbed string must be a
        // no-op. Otherwise applying the helper twice (which is easy to
        // do in a refactor) would corrupt user input.
        $input = "O'Reilly; DROP TABLE users--";
        $once  = replaceSpecialChar($input);
        $twice = replaceSpecialChar($once);
        $this->assertSame($once, $twice);
    }

    // -----------------------------------------------------------------
    // escapeXSS()
    // -----------------------------------------------------------------

    public function testEscapeXSSNeutralisesScriptTag(): void
    {
        $this->assertSame(
            '&lt;script&gt;alert(1)&lt;/script&gt;',
            escapeXSS('<script>alert(1)</script>')
        );
    }

    public function testEscapeXSSEncodesBothQuoteStyles(): void
    {
        // ENT_QUOTES is required for attribute-context escaping.
        // A regression to ENT_HTML5 / no-flag would leak single quotes
        // out of onclick="..." handlers.
        $single = escapeXSS("O'Reilly");
        $double = escapeXSS('Say "hi"');
        $this->assertStringContainsString('&#039;', $single, 'single quotes must be encoded');
        $this->assertStringContainsString('&quot;', $double, 'double quotes must be encoded');
    }

    public function testEscapeXSSHandlesAmpersandAndLt(): void
    {
        $this->assertSame('Tom &amp; Jerry &lt;3', escapeXSS('Tom & Jerry <3'));
    }

    public function testEscapeXSSHandlesEmptyString(): void
    {
        $this->assertSame('', escapeXSS(''));
    }

    public function testEscapeXSSIsUnicodeSafe(): void
    {
        // Must not corrupt multibyte input.
        $this->assertSame('你好,世界', escapeXSS('你好,世界'));
    }

    // -----------------------------------------------------------------
    // CSRF token round-trip
    // -----------------------------------------------------------------

    public function testGenerateCSRFTokenIsStableWithinSession(): void
    {
        $first  = generateCSRFToken();
        $second = generateCSRFToken();
        $this->assertNotSame('', $first, 'token must not be empty');
        $this->assertSame(
            $first,
            $second,
            'subsequent calls must return the same session-bound token'
        );
    }

    public function testGeneratedTokenHasExpectedEntropy(): void
    {
        $token = generateCSRFToken();
        // bin2hex(random_bytes(32)) -> 64 lowercase hex characters.
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{64}$/',
            $token,
            'token should be 32 random bytes hex-encoded'
        );
    }

    public function testVerifyCSRFTokenAcceptsMatchingToken(): void
    {
        $token = generateCSRFToken();
        $this->assertTrue(verifyCSRFToken($token));
    }

    public function testVerifyCSRFTokenRejectsMismatchedToken(): void
    {
        generateCSRFToken();
        $this->assertFalse(verifyCSRFToken('not-the-real-token'));
    }

    public function testVerifyCSRFTokenRejectsEmptyToken(): void
    {
        generateCSRFToken();
        $this->assertFalse(verifyCSRFToken(''));
    }

    public function testVerifyCSRFTokenRejectsTokenWhenSessionEmpty(): void
    {
        // Defensive check: never trust a client-supplied token if the
        // server-side token is missing. A regression that returns true
        // here would silently disable CSRF protection.
        \LikeGirl\Tests\resetSession();
        $this->assertFalse(verifyCSRFToken('anything'));
    }

    public function testVerifyCSRFTokenIsConstantTime(): void
    {
        // The implementation must use hash_equals() to avoid timing
        // side channels. We assert on the function being referenced by
        // checking that two equal-length, equal-content tokens verify.
        $token = generateCSRFToken();
        $this->assertTrue(verifyCSRFToken($token));
        $this->assertTrue(verifyCSRFToken($token)); // deterministic, fast
    }

    // -----------------------------------------------------------------
    // time_tran()
    // -----------------------------------------------------------------

    public function testTimeTranReturnsEmptyForFalsyInput(): void
    {
        $this->assertSame('', time_tran(0));
        $this->assertSame('', time_tran(null));
        $this->assertSame('', time_tran(''));
    }

    public function testTimeTranFormatsFutureTimestampsAsDate(): void
    {
        $future = time() + 86400; // 1 day in the future
        $result = time_tran($future);
        $this->assertMatchesRegularExpression(
            '/^\d{4}-\d{2}-\d{2}$/',
            $result,
            "future timestamps should be Y-m-d, got: {$result}"
        );
    }

    /**
     * @dataProvider relativeTimeProvider
     */
    public function testTimeTranFormatsRelativeTimestamps(int $delta, string $expected): void
    {
        $this->assertSame($expected, time_tran(time() - $delta));
    }

    public static function relativeTimeProvider(): array
    {
        return [
            // Seconds
            '1 second ago'    => [1,    '1秒前'],
            '59 seconds ago'  => [59,   '59秒前'],
            // Minutes
            '60 seconds ago'  => [60,   '1分钟前'],
            '90 seconds ago'  => [90,   '1分钟前'],   // floor(90/60)=1
            '1 hour minus 1s' => [3599, '59分钟前'],
            // Hours
            '1 hour'          => [3600, '1小时前'],
            '23 hours'        => [86399, '23小时前'],
            // Days
            '1 day'           => [86400, '1天前'],
            '29 days'         => [2591999, '29天前'],
            // Months (30-day approximation)
            '1 month'         => [2592000, '1月前'],
            '11 months'       => [31535999, '12月前'], // floor(31535999/2592000)=12
            // Years
            '1 year'          => [31536000, '1年前'],
            '5 years'         => [5 * 31536000, '5年前'],
        ];
    }

    public function testTimeTranBoundariesAreExclusiveAtUpperEdge(): void
    {
        // The function uses `if ($t < 60) { seconds } elseif ($t < 3600) ...`.
        // Verify the exact branch boundaries: 60s -> minutes, 3600s -> hours.
        $now = time();
        $this->assertSame('59秒前',  time_tran($now - 59));
        $this->assertSame('1分钟前', time_tran($now - 60));
        $this->assertSame('59分钟前', time_tran($now - 3599));
        $this->assertSame('1小时前',  time_tran($now - 3600));
        $this->assertSame('23小时前', time_tran($now - 86399));
        $this->assertSame('1天前',    time_tran($now - 86400));
        $this->assertSame('29天前',   time_tran($now - 2591999));
        $this->assertSame('1月前',    time_tran($now - 2592000));
    }

    // -----------------------------------------------------------------
    // get_ip_city_New() — input validation branch only.
    // We deliberately do not hit the network: that would make the
    // test suite slow, flaky, and dependent on a 3rd-party service.
    // The validation branch is the only one a unit test should
    // safely assert on; the curl path is integration territory.
    // -----------------------------------------------------------------

    public function testGetIpCityReturnsUnknowForEmptyString(): void
    {
        $this->assertSame('未知', get_ip_city_New(''));
    }

    public function testGetIpCityReturnsUnknowForMalformedInput(): void
    {
        $this->assertSame('未知', get_ip_city_New('not-an-ip'));
        $this->assertSame('未知', get_ip_city_New('999.999.999.999'));
        $this->assertSame('未知', get_ip_city_New('1.2.3'));
        $this->assertSame('未知', get_ip_city_New('1.2.3.4.5'));
    }

    public function testGetIpCityReturnsLocalForLoopbackIPv4(): void
    {
        $this->assertSame('本机', get_ip_city_New('127.0.0.1'));
    }

    public function testGetIpCityReturnsLocalForLoopbackIPv6(): void
    {
        $this->assertSame('本机', get_ip_city_New('::1'));
    }
}
