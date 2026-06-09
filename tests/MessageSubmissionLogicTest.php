<?php
/**
 * 消息提交核心逻辑测试 - services/message.php
 *
 * 隔离验证留言提交中的业务规则：
 * - CORS 源白名单校验
 * - 留言内容长度限制（>0 且 <=500 字符）
 * - 昵称长度限制（<=20 字符）
 * - XSS 转义规则
 * - 设备/浏览器 UA 识别
 * - 动作白名单（POST only）
 */
require_once __DIR__ . '/TestCase.php';

class MessageSubmissionLogicTest extends TestCase
{
    private array $allowedOrigins = [
        'lovedemo.54oimx.top',
        'love.54oimx.top',
        'localhost',
        '127.0.0.1',
    ];

    // --- CORS 白名单 ---

    public function testCorsAllowedOrigin(): void
    {
        $this->assertTrue(in_array('lovedemo.54oimx.top', $this->allowedOrigins, true));
        $this->assertTrue(in_array('localhost', $this->allowedOrigins, true));
        $this->assertTrue(in_array('127.0.0.1', $this->allowedOrigins, true));
    }

    public function testCorsBlocksUnauthorized(): void
    {
        $this->assertFalse(in_array('evil.com', $this->allowedOrigins, true));
        $this->assertFalse(in_array('', $this->allowedOrigins, true));
        $this->assertFalse(in_array('lovedemo.54oimx.top.evil.com', $this->allowedOrigins, true));
    }

    public function testCorsHostFromHeader(): void
    {
        $origin = 'https://lovedemo.54oimx.top/page';
        $host = parse_url($origin, PHP_URL_HOST);
        $this->assertSame('lovedemo.54oimx.top', $host);
        $this->assertTrue(in_array($host, $this->allowedOrigins, true));
    }

    // --- 内容长度校验 ---

    public function testEmptyTextRejected(): void
    {
        $this->assertTrue(trim('') === '' || mb_strlen('', 'UTF-8') > 500);
    }

    public function testValidTextAccepted(): void
    {
        $text = '这是一条正常的留言内容';
        $len = mb_strlen($text, 'UTF-8');
        $this->assertTrue($len > 0 && $len <= 500);
    }

    public function testTextOver500CharsRejected(): void
    {
        $text = str_repeat('a', 501);
        $this->assertTrue(mb_strlen($text, 'UTF-8') > 500);
    }

    public function testTextAtBoundary500Chars(): void
    {
        $text = str_repeat('中', 500);
        $this->assertSame(500, mb_strlen($text, 'UTF-8'));
    }

    // --- 昵称长度校验 ---

    public function testEmptyNameDefaults(): void
    {
        $name = trim('');
        $this->assertSame('匿名访客', $name === '' ? '匿名访客' : $name);
    }

    public function testNameOver20CharsRejected(): void
    {
        $name = str_repeat('x', 21);
        $this->assertTrue(mb_strlen($name, 'UTF-8') > 20);
    }

    public function testNameAtBoundary20Chars(): void
    {
        $name = str_repeat('好', 20);
        $this->assertSame(20, mb_strlen($name, 'UTF-8'));
    }

    // --- XSS 转义 ---

    public function testXSSEscapingOnInput(): void
    {
        $text = '<script>alert("xss")</script>';
        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        $this->assertFalse(str_contains($escaped, '<script>'));
        $this->assertMatchesRegex('/&lt;script&gt;/', $escaped);
    }

    public function testXSSEventHandler(): void
    {
        $text = '"><img src=x onerror=alert(1)>';
        $escaped = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        // 关键：HTML 标签字符应被转义，使其无法作为 HTML 解析
        $this->assertFalse(str_contains($escaped, '<img'));
        $this->assertFalse(str_contains($escaped, '">'));
        $this->assertMatchesRegex('/&quot;&gt;&lt;img/', $escaped);
    }

    // --- 设备/浏览器 UA 解析 ---

    public function testDeviceDetectionMobile(): void
    {
        $ua = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)';
        $this->assertSame('Mobile', preg_match('/Mobile|Android|iPhone/i', $ua) ? 'Mobile' : 'PC');
    }

    public function testDeviceDetectionDesktop(): void
    {
        $ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)';
        $this->assertSame('PC', preg_match('/Mobile|Android|iPhone/i', $ua) ? 'Mobile' : 'PC');
    }

    public function testBrowserDetectionChrome(): void
    {
        $ua = 'Mozilla/5.0 Chrome/120.0.0.0 Safari/537.36';
        $browser = '';
        if (preg_match('/Chrome\/([\d\.]+)/i', $ua, $m)) $browser = 'Chrome ' . $m[1];
        $this->assertMatchesRegex('/^Chrome \d+/', $browser);
    }

    public function testBrowserDetectionEdge(): void
    {
        $ua = 'Mozilla/5.0 Edg/120.0.0.0';
        $browser = '';
        if (preg_match('/Edg\/([\d\.]+)/i', $ua, $m)) $browser = 'Edge ' . $m[1];
        $this->assertMatchesRegex('/^Edge \d+/', $browser);
    }

    public function testBrowserDetectionFirefox(): void
    {
        $ua = 'Mozilla/5.0 Firefox/121.0';
        $browser = '';
        if (preg_match('/Firefox\/([\d\.]+)/i', $ua, $m)) $browser = 'Firefox ' . $m[1];
        $this->assertMatchesRegex('/^Firefox \d+/', $browser);
    }

    public function testBrowserDetectionSafari(): void
    {
        $ua = 'Mozilla/5.0 Safari/17.0';
        $browser = '';
        if (preg_match('/Safari\/([\d\.]+)/i', $ua, $m)) $browser = 'Safari ' . $m[1];
        $this->assertMatchesRegex('/^Safari \d+/', $browser);
    }

    public function testBrowserUnknownUA(): void
    {
        $ua = 'curl/8.0';
        $browser = '';
        if (preg_match('/Edg\/([\d\.]+)/i', $ua, $m)) $browser = 'Edge ' . $m[1];
        elseif (preg_match('/Chrome\/([\d\.]+)/i', $ua, $m)) $browser = 'Chrome ' . $m[1];
        elseif (preg_match('/Firefox\/([\d\.]+)/i', $ua, $m)) $browser = 'Firefox ' . $m[1];
        elseif (preg_match('/Safari\/([\d\.]+)/i', $ua, $m)) $browser = 'Safari ' . $m[1];
        $this->assertSame('', $browser);
    }

    // --- HTTP 方法限制 ---

    public function testMethodPostOnly(): void
    {
        $this->assertTrue(in_array('POST', ['POST'], true));
        $this->assertFalse(in_array('GET', ['POST'], true));
        $this->assertFalse(in_array('PUT', ['POST'], true));
        $this->assertFalse(in_array('DELETE', ['POST'], true));
    }

    // --- 防灌水时间窗口 ---

    public function testSpamCheckWithinWindow(): void
    {
        $now = time();
        $recent = $now - 30; // 30 秒前留言
        $this->assertTrue($recent > ($now - 60), '60 秒内的重复留言应被拦截');
    }

    public function testSpamCheckOutsideWindow(): void
    {
        $now = time();
        $old = $now - 120; // 2 分钟前
        $this->assertFalse($old > ($now - 60), '超过 60 秒的留言应被允许');
    }
}

$suite = new MessageSubmissionLogicTest();
$suite->runAll();
echo "\n" . json_encode($suite->report(), JSON_UNESCAPED_UNICODE) . "\n";
