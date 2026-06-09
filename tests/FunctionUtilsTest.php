<?php
/**
 * 核心工具函数测试 - admin/Function.php
 *
 * 覆盖高风险的共享工具函数：
 * - checkQQ: QQ号格式验证（正则边界）
 * - replaceSpecialChar: SQL 字符过滤
 * - escapeXSS: XSS 转义
 * - generateCSRFToken / verifyCSRFToken: CSRF 令牌生成与校验
 * - time_tran: 相对时间转换（边界条件
 */
require_once __DIR__ . '/TestCase.php';

// 从 admin/Function.php 加载函数定义。使用输出缓冲来避免
// 顶层 session_start() 产生的 headers already sent 警告
if (!function_exists('checkQQ')) {
    ob_start();
    require_once __DIR__ . '/../admin/Function.php';
    ob_end_clean();
}

class FunctionUtilsTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    // --- checkQQ 正则边界测试 ---

    public function testCheckQQValidQQ(): void
    {
        $this->assertTrue(checkQQ('10001'));
        $this->assertTrue(checkQQ('123456789'));
        $this->assertTrue(checkQQ('10000000000000'));
    }

    public function testCheckQQRejectsLeadingZero(): void
    {
        $this->assertFalse(checkQQ('012345'));
        $this->assertFalse(checkQQ('0'));
        $this->assertFalse(checkQQ('00000'));
    }

    public function testCheckQQRejectsNonNumeric(): void
    {
        $this->assertFalse(checkQQ('abc'));
        $this->assertFalse(checkQQ('12 34'));
        $this->assertFalse(checkQQ('12-34'));
        $this->assertFalse(checkQQ(''));
    }

    public function testCheckQQTooShort(): void
    {
        $this->assertFalse(checkQQ('1'));
        $this->assertFalse(checkQQ('12'));
        $this->assertFalse(checkQQ('123'));
        $this->assertFalse(checkQQ('1234'));
    }

    // --- replaceSpecialChar 字符过滤测试 ---

    public function testReplaceSpecialCharRemovesQuotes(): void
    {
        $this->assertSame('abc', replaceSpecialChar("a'b\"c"));
        $this->assertSame('abc', replaceSpecialChar('a`b;c'));
        $this->assertSame('abc', replaceSpecialChar('a\\b\\c'));
    }

    public function testReplaceSpecialCharKeepsNormalText(): void
    {
        $this->assertSame('hello world', replaceSpecialChar('hello world'));
        $this->assertSame('123_abc-xyz', replaceSpecialChar('123_abc-xyz'));
    }

    public function testReplaceSpecialCharEmptyString(): void
    {
        $this->assertSame('', replaceSpecialChar(''));
    }

    // --- escapeXSS 转义测试 ---

    public function testEscapeXSSEscapesAngleBrackets(): void
    {
        $this->assertSame('&lt;script&gt;', escapeXSS('<script>'));
        $this->assertSame('&lt;/div&gt;', escapeXSS('</div>'));
    }

    public function testEscapeXSSEscapesQuotes(): void
    {
        $this->assertSame('&quot;', escapeXSS('"'));
        $this->assertSame('&#039;', escapeXSS("'"));
    }

    public function testEscapeXSSAmpersand(): void
    {
        $this->assertSame('a &amp; b', escapeXSS('a & b'));
    }

    public function testEscapeXSSAttackVector(): void
    {
        $payload = '<img src=x onerror="alert(1)">';
        $escaped = escapeXSS($payload);
        // XSS 转义后不应出现原始的 < 字符
        $this->assertFalse(str_contains($escaped, '<img'));
        $this->assertMatchesRegex('/&lt;img/', $escaped);
    }

    public function testEscapeXSSPlainText(): void
    {
        $this->assertSame('hello world', escapeXSS('hello world'));
    }

    // --- CSRF 令牌测试 ---

    public function testGenerateCSRFTokenProducesNonEmptyString(): void
    {
        $token = generateCSRFToken();
        $this->assertIsString($token);
        $this->assertGreaterThan(10, strlen($token));
    }

    public function testGenerateCSRFTokenStoresInSession(): void
    {
        $token = generateCSRFToken();
        $this->assertSame($token, $_SESSION['csrf_token']);
    }

    public function testVerifyCSRFTokenValidatesOwnToken(): void
    {
        $token = generateCSRFToken();
        $this->assertTrue(verifyCSRFToken($token));
    }

    public function testVerifyCSRFTokenRejectsFake(): void
    {
        generateCSRFToken();
        $this->assertFalse(verifyCSRFToken('fake-token-12345'));
    }

    public function testVerifyCSRFTokenEmpty(): void
    {
        generateCSRFToken();
        $this->assertFalse(verifyCSRFToken(''));
    }

    public function testVerifyCSRFTokenNoSession(): void
    {
        $_SESSION = [];
        $this->assertFalse(verifyCSRFToken('anything'));
    }

    public function testCSRFTokenDifferPerSession(): void
    {
        // 不同生成调用应返回相同的会话级令牌（函数会复用已生成的 token）
        $_SESSION = [];
        $t1 = generateCSRFToken();
        $t2 = generateCSRFToken();
        $this->assertSame($t1, $t2, '同一会话内两次生成应得到相同令牌');

        // 清空会话后重新生成，令牌应不同
        $_SESSION = [];
        $t3 = generateCSRFToken();
        $this->assertFalse($t1 === $t3 && !empty($t1), '新会话应生成不同令牌');
    }

    // --- time_tran 相对时间转换 ---

    public function testTimeTranJustNow(): void
    {
        $now = time();
        $this->assertMatchesRegex('/秒前/', time_tran($now - 10));
        $this->assertMatchesRegex('/秒前/', time_tran($now - 30));
    }

    public function testTimeTranMinutes(): void
    {
        $now = time();
        $this->assertMatchesRegex('/分钟前/', time_tran($now - 120));
        $this->assertMatchesRegex('/分钟前/', time_tran($now - 3000));
    }

    public function testTimeTranHours(): void
    {
        $now = time();
        $this->assertMatchesRegex('/小时前/', time_tran($now - 7200));
        $this->assertMatchesRegex('/小时前/', time_tran($now - 80000));
    }

    public function testTimeTranDays(): void
    {
        $now = time();
        $this->assertMatchesRegex('/天前/', time_tran($now - 200000));
    }

    public function testTimeTranMonths(): void
    {
        $now = time();
        $this->assertMatchesRegex('/月前/', time_tran($now - 4000000));
    }

    public function testTimeTranYears(): void
    {
        $now = time();
        $this->assertMatchesRegex('/年前/', time_tran($now - 40000000));
    }

    public function testTimeTranFuture(): void
    {
        $now = time();
        $result = time_tran($now + 86400);
        // 未来时间应返回格式化日期
        $this->assertMatchesRegex('/^\d{4}-\d{2}-\d{2}/', $result);
    }

    public function testTimeTranZero(): void
    {
        $this->assertSame('', time_tran(0));
    }

    // --- get_ip_city_New 边界 ---

    public function testGetIpCityNewInvalidIp(): void
    {
        $result = get_ip_city_New('not-an-ip');
        $this->assertSame('未知', $result);
    }

    public function testGetIpCityNewEmpty(): void
    {
        $result = get_ip_city_New('');
        $this->assertSame('未知', $result);
    }
}

$suite = new FunctionUtilsTest();
$suite->runAll();
echo "\n" . json_encode($suite->report(), JSON_UNESCAPED_UNICODE) . "\n";
