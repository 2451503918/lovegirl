<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../admin/Function.php';

$GLOBALS['conn'] = null;
$GLOBALS['connect'] = null;

class FunctionTest extends TestCase {

    protected function setUp(): void {
        $_SESSION = array();
    }

    public function testCheckQQValid() {
        $this->assertTrue(checkQQ('12345'));
        $this->assertTrue(checkQQ('10000'));
        $this->assertTrue(checkQQ('999999999'));
        $this->assertTrue(checkQQ('1234567890123'));
    }

    public function testCheckQQInvalid() {
        $this->assertFalse(checkQQ('0'));
        $this->assertFalse(checkQQ('1234'));
        $this->assertFalse(checkQQ('01234'));
        $this->assertFalse(checkQQ('abc'));
        $this->assertFalse(checkQQ(''));
        $this->assertFalse(checkQQ(null));
        $this->assertFalse(checkQQ('1234567890123456789'));
    }

    public function testReplaceSpecialChar() {
        $this->assertEquals('test', replaceSpecialChar("test"));
        $this->assertEquals('test', replaceSpecialChar("te'st"));
        $this->assertEquals('test', replaceSpecialChar('te"st'));
        $this->assertEquals('test', replaceSpecialChar('te\st'));
        $this->assertEquals('test', replaceSpecialChar('te`st'));
        $this->assertEquals('test', replaceSpecialChar('te;st'));
        $this->assertEquals('', replaceSpecialChar("\'\"\\`;"));
    }

    public function testEscapeXSS() {
        $this->assertEquals('&lt;script&gt;alert(1)&lt;/script&gt;', escapeXSS('<script>alert(1)</script>'));
        $this->assertEquals('&quot;test&quot;', escapeXSS('"test"'));
        $this->assertEquals("&#039;test&#039;", escapeXSS("'test'"));
        $this->assertEquals('&amp;test', escapeXSS('&test'));
        $this->assertEquals('test', escapeXSS('test'));
    }

    public function testGenerateCSRFToken() {
        $token1 = generateCSRFToken();
        $token2 = generateCSRFToken();
        
        $this->assertNotNull($token1);
        $this->assertEquals(64, strlen($token1));
        $this->assertEquals($token1, $token2);
        $this->assertNotEmpty($_SESSION['csrf_token']);
    }

    public function testVerifyCSRFToken() {
        $token = generateCSRFToken();
        
        $this->assertTrue(verifyCSRFToken($token));
        $this->assertFalse(verifyCSRFToken('invalid_token'));
        $this->assertFalse(verifyCSRFToken(''));
        $this->assertFalse(verifyCSRFToken(null));
    }

    public function testVerifyCSRFTokenEmptySession() {
        $_SESSION['csrf_token'] = null;
        $this->assertFalse(verifyCSRFToken('any_token'));
    }

    public function testTimeTranFuture() {
        $futureTime = time() + 3600;
        $result = time_tran($futureTime);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $result);
    }

    public function testTimeTranJustNow() {
        $justNow = time() - 5;
        $this->assertEquals('5秒前', time_tran($justNow));
    }

    public function testTimeTranSeconds() {
        $secondsAgo = time() - 59;
        $result = time_tran($secondsAgo);
        $this->assertMatchesRegularExpression('/^\d+秒前$/', $result);
    }

    public function testTimeTranMinutes() {
        $minutesAgo = time() - 120;
        $result = time_tran($minutesAgo);
        $this->assertEquals('2分钟前', $result);
    }

    public function testTimeTranHours() {
        $hoursAgo = time() - 7200;
        $result = time_tran($hoursAgo);
        $this->assertEquals('2小时前', $result);
    }

    public function testTimeTranDays() {
        $daysAgo = time() - 172800;
        $result = time_tran($daysAgo);
        $this->assertEquals('2天前', $result);
    }

    public function testTimeTranMonths() {
        $monthsAgo = time() - 5184000;
        $result = time_tran($monthsAgo);
        $this->assertEquals('2月前', $result);
    }

    public function testTimeTranYears() {
        $yearsAgo = time() - 63072000;
        $result = time_tran($yearsAgo);
        $this->assertEquals('2年前', $result);
    }

    public function testTimeTranZero() {
        $this->assertEquals('', time_tran(0));
        $this->assertEquals('', time_tran(false));
    }

    public function testGetIpCityNewInvalid() {
        $this->assertEquals('未知', get_ip_city_New('invalid_ip'));
        $this->assertEquals('未知', get_ip_city_New(''));
        $this->assertEquals('未知', get_ip_city_New(null));
    }

    public function testGetIpCityNewLocalhost() {
        $this->assertEquals('本机', get_ip_city_New('127.0.0.1'));
        $this->assertEquals('本机', get_ip_city_New('::1'));
    }
}
?>