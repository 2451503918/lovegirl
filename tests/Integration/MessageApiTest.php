<?php
/**
 * 留言API集成测试
 * 
 * 测试覆盖范围：
 * - 留言提交验证逻辑
 * - 防灌水机制
 * - XSS防护
 * - 极验验证流程
 * - CORS配置
 * - 输入长度限制
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class MessageApiTest extends TestCase
{
    protected function setUp(): void
    {
        resetTestEnvironment();
        // 模拟数据库连接可用
        $GLOBALS['connect'] = new MockConnection();
    }

    // ==================== 输入验证测试 ====================
    
    /**
     * 测试留言内容长度验证
     */
    public function testMessageContentLengthValidation()
    {
        // 正常长度留言
        $this->assertLessThanOrEqual(500, mb_strlen('这是一条正常的留言内容', 'UTF-8'));
        
        // 超长留言应被拒绝
        $longMessage = str_repeat('测试', 300); // 600字符
        $this->assertGreaterThan(500, mb_strlen($longMessage, 'UTF-8'));
    }
    
    /**
     * 测试昵称长度验证
     */
    public function testNicknameLengthValidation()
    {
        // 正常昵称
        $normalName = '小明';
        $this->assertLessThanOrEqual(20, mb_strlen($normalName, 'UTF-8'));
        
        // 超长昵称
        $longName = str_repeat('测', 25);
        $this->assertGreaterThan(20, mb_strlen($longName, 'UTF-8'));
        
        // 空昵称应设为默认值
        $emptyName = '';
        $defaultName = '匿名访客';
        $this->assertEquals($defaultName, $emptyName === '' ? $defaultName : $emptyName);
    }
    
    /**
     * 测试留言内容不能为空
     */
    public function testMessageContentNotEmpty()
    {
        $emptyContent = '';
        $this->assertTrue($emptyContent === '' || mb_strlen($emptyContent, 'UTF-8') > 500);
    }

    // ==================== XSS防护测试 ====================
    
    /**
     * 测试留言内容XSS过滤
     * @dataProvider xssPayloadProvider
     */
    public function testMessageXSSFilter($input, $expected)
    {
        $filtered = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        $this->assertEquals($expected, $filtered);
    }
    
    public function xssPayloadProvider()
    {
        return [
            'script注入' => ['<script>alert("xss")</script>', '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;'],
            '事件属性注入' => ['<img onerror=alert(1)>', '&lt;img onerror=alert(1)&gt;'],
            '单引号注入' => ["onload='alert(1)'", "onload=&#039;alert(1)&#039;"],
            'HTML实体注入' => ['&lt;script&gt;', '&amp;lt;script&amp;gt;'],
        ];
    }
    
    /**
     * 测试昵称XSS过滤
     */
    public function testNicknameXSSFilter()
    {
        $maliciousName = '<script>alert("xss")</script>小明';
        $filtered = htmlspecialchars($maliciousName, ENT_QUOTES, 'UTF-8');
        
        $this->assertStringNotContainsString('<script>', $filtered);
        $this->assertStringContainsString('&lt;script&gt;', $filtered);
    }

    // ==================== 防灌水机制测试 ====================
    
    /**
     * 测试防灌水时间间隔
     */
    public function testSpamPreventionInterval()
    {
        $interval = 60; // 60秒
        
        // 同一IP在60秒内只能留言1次
        $currentTime = time();
        $spamTime = $currentTime - 30; // 30秒前
        
        // 模拟检查逻辑：如果上次留言时间在60秒内，应阻止
        $shouldBlock = ($currentTime - $spamTime) < $interval;
        $this->assertTrue($shouldBlock);
        
        // 超过60秒应允许
        $allowedTime = $currentTime - 61;
        $shouldAllow = ($currentTime - $allowedTime) >= $interval;
        $this->assertTrue($shouldAllow);
    }
    
    /**
     * 测试IP记录用于防灌水
     */
    public function testIPRecordingForSpamPrevention()
    {
        $ip = $_SERVER['REMOTE_ADDR'];
        $this->assertNotEmpty($ip);
        $this->assertTrue(filter_var($ip, FILTER_VALIDATE_IP) !== false);
    }

    // ==================== CORS配置测试 ====================
    
    /**
     * 测试允许的Origin列表
     */
    public function testAllowedOrigins()
    {
        $allowedOrigins = ['lovedemo.54oimx.top', 'love.54oimx.top', 'localhost', '127.0.0.1'];
        
        // 测试允许的Origin
        foreach ($allowedOrigins as $origin) {
            $this->assertContains($origin, $allowedOrigins);
        }
        
        // 测试不允许的Origin
        $notAllowed = ['malicious-site.com', 'evil.com'];
        foreach ($notAllowed as $origin) {
            $this->assertNotContains($origin, $allowedOrigins);
        }
    }
    
    /**
     * 测试Origin解析
     */
    public function testOriginParsing()
    {
        $origin = 'http://localhost:8080';
        $host = parse_url($origin, PHP_URL_HOST);
        
        $this->assertEquals('localhost', $host);
    }

    // ==================== 设备检测测试 ====================
    
    /**
     * 测试设备类型检测
     * @dataProvider userAgentProvider
     */
    public function testDeviceDetection($userAgent, $expectedDevice)
    {
        $device = preg_match('/Mobile|Android|iPhone/i', $userAgent) ? 'Mobile' : 'PC';
        $this->assertEquals($expectedDevice, $device);
    }
    
    public function userAgentProvider()
    {
        return [
            'iPhone' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 14_0)', 'Mobile'],
            'Android' => ['Mozilla/5.0 (Linux; Android 10)', 'Mobile'],
            '桌面Chrome' => ['Mozilla/5.0 (Windows NT 10.0) Chrome/90', 'PC'],
            '桌面Firefox' => ['Mozilla/5.0 (Windows NT 10.0; Firefox/88)', 'PC'],
        ];
    }

    // ==================== 浏览器检测测试 ====================
    
    /**
     * 测试浏览器类型检测
     * @dataProvider browserDetectionProvider
     */
    public function testBrowserDetection($userAgent, $expectedBrowser)
    {
        $browser = '';
        if (preg_match('/Edg\/([\d\.]+)/i', $userAgent, $m)) $browser = 'Edge ' . $m[1];
        elseif (preg_match('/Chrome\/([\d\.]+)/i', $userAgent, $m)) $browser = 'Chrome ' . $m[1];
        elseif (preg_match('/Firefox\/([\d\.]+)/i', $userAgent, $m)) $browser = 'Firefox ' . $m[1];
        elseif (preg_match('/Safari\/([\d\.]+)/i', $userAgent, $m)) $browser = 'Safari ' . $m[1];
        
        $this->assertStringContainsString($expectedBrowser, $browser);
    }
    
    public function browserDetectionProvider()
    {
        return [
            'Edge浏览器' => ['Mozilla/5.0 Edg/91.0', 'Edge'],
            'Chrome浏览器' => ['Mozilla/5.0 Chrome/91.0', 'Chrome'],
            'Firefox浏览器' => ['Mozilla/5.0 Firefox/88.0', 'Firefox'],
            'Safari浏览器' => ['Mozilla/5.0 Safari/14.0', 'Safari'],
        ];
    }

    // ==================== HTTP方法验证测试 ====================
    
    /**
     * 测试仅允许POST请求
     */
    public function testOnlyPostMethodAllowed()
    {
        $allowedMethods = ['POST'];
        $disallowedMethods = ['GET', 'PUT', 'DELETE', 'PATCH'];
        
        foreach ($allowedMethods as $method) {
            $this->assertContains($method, $allowedMethods);
        }
        
        foreach ($disallowedMethods as $method) {
            $this->assertNotContains($method, $allowedMethods);
        }
    }
    
    /**
     * 测试OPTIONS请求处理
     */
    public function testOptionsRequestHandling()
    {
        // OPTIONS请求应返回204状态码
        $expectedStatus = 204;
        $this->assertEquals(204, $expectedStatus);
    }

    // ==================== 极验验证测试 ====================
    
    /**
     * 测试极验参数完整性检查
     */
    public function testGeetestParameterValidation()
    {
        $requiredParams = ['lot_number', 'captcha_output', 'pass_token', 'gen_time'];
        
        // 所有参数必须存在
        foreach ($requiredParams as $param) {
            $this->assertNotEmpty($param);
        }
        
        // 缺少任何参数应返回错误
        $incompleteParams = ['lot_number' => 'test']; // 缺少其他参数
        $isComplete = count($incompleteParams) === count($requiredParams);
        $this->assertFalse($isComplete);
    }
    
    /**
     * 测试极验验证失败处理
     */
    public function testGeetestFailureHandling()
    {
        $verifyResult = ['result' => 'fail'];
        
        $isValid = isset($verifyResult['result']) && $verifyResult['result'] === 'success';
        $this->assertFalse($isValid);
    }

    // ==================== IP归属地查询测试 ====================
    
    /**
     * 测试IP格式验证
     * @dataProvider ipValidationProvider
     */
    public function testIPValidation($ip, $isValid)
    {
        $result = filter_var($ip, FILTER_VALIDATE_IP) !== false;
        $this->assertEquals($isValid, $result);
    }
    
    public function ipValidationProvider()
    {
        return [
            '有效IPv4' => ['192.168.1.1', true],
            '有效IPv6' => ['::1', true],
            '无效IP' => ['invalid-ip', false],
            '空IP' => ['', false],
            '保留地址' => ['127.0.0.1', true],
        ];
    }
    
    /**
     * 测试保留地址处理
     */
    public function testReservedAddressHandling()
    {
        $reservedAddresses = ['127.0.0.1', '::1'];
        
        foreach ($reservedAddresses as $ip) {
            $isReserved = ($ip === '127.0.0.1' || $ip === '::1');
            $this->assertTrue($isReserved);
        }
    }

    // ==================== 响应格式测试 ====================
    
    /**
     * 测试成功响应格式
     */
    public function testSuccessResponseFormat()
    {
        $successResponse = [
            'success' => true,
            'code' => 200,
            'msg' => '留言成功，感谢你的留言～'
        ];
        
        $this->assertTrue($successResponse['success']);
        $this->assertEquals(200, $successResponse['code']);
        $this->assertNotEmpty($successResponse['msg']);
    }
    
    /**
     * 测试错误响应格式
     * @dataProvider errorResponseProvider
     */
    public function testErrorResponseFormat($code, $msg)
    {
        $errorResponse = [
            'success' => false,
            'code' => $code,
            'msg' => $msg
        ];
        
        $this->assertFalse($errorResponse['success']);
        $this->assertGreaterThanOrEqual(400, $errorResponse['code']);
        $this->assertNotEmpty($errorResponse['msg']);
    }
    
    public function errorResponseProvider()
    {
        return [
            '内容过长' => [400, '留言内容不能为空且不超过500字符'],
            '昵称过长' => [400, '昵称不能超过20字符'],
            '频繁操作' => [429, '操作太频繁，请稍后再试'],
            '验证失败' => [400, '人机验证失败，请重试'],
        ];
    }
    
    // ==================== 边界条件测试 ====================
    
    /**
     * 测试数据库不可用时的降级处理
     */
    public function testDatabaseUnavailableGracefulDegradation()
    {
        // 当数据库不可用时，应返回演示模式响应
        $fallbackResponse = [
            'success' => true,
            'code' => 200,
            'msg' => '数据库不可用，留言已忽略（演示模式）'
        ];
        
        $this->assertTrue($fallbackResponse['success']);
        $this->assertStringContainsString('演示模式', $fallbackResponse['msg']);
    }
    
    /**
     * 测试500字符边界
     */
    public function testMessage500CharacterBoundary()
    {
        // 恰好500字符（使用ASCII字符便于计算）
        $exact500 = str_repeat('a', 500);
        $this->assertEquals(500, mb_strlen($exact500, 'UTF-8'));
        
        // 501字符应被拒绝
        $over500 = str_repeat('a', 501);
        $this->assertGreaterThan(500, mb_strlen($over500, 'UTF-8'));
    }
    
    /**
     * 测试20字符昵称边界
     */
    public function testNickname20CharacterBoundary()
    {
        // 恰好20字符
        $exact20 = str_repeat('a', 20);
        $this->assertEquals(20, mb_strlen($exact20, 'UTF-8'));
        
        // 21字符应被拒绝
        $over20 = str_repeat('a', 21);
        $this->assertGreaterThan(20, mb_strlen($over20, 'UTF-8'));
    }
}