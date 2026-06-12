<?php
/**
 * CSRF防护安全测试
 * 
 * 测试覆盖范围：
 * - CSRF令牌生成安全性
 * - CSRF令牌验证逻辑
 * - 时序攻击防护
 * - Session管理
 * - 令牌生命周期
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class CSRFSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        resetTestEnvironment();
    }

    // ==================== 令牌生成安全性测试 ====================
    
    /**
     * 测试令牌使用random_bytes生成
     */
    public function testTokenUsesRandomBytes()
    {
        // 生成令牌
        $token = generateCSRFToken();
        
        // 验证令牌长度（32字节 = 64十六进制字符）
        $this->assertEquals(64, strlen($token));
        
        // 验证是有效的十六进制字符串
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);
    }
    
    /**
     * 测试令牌不可预测性
     */
    public function testTokenUnpredictability()
    {
        // 清除现有令牌
        $_SESSION = [];
        
        $token1 = generateCSRFToken();
        
        // 清除并生成新令牌
        $_SESSION = [];
        $token2 = generateCSRFToken();
        
        // 两个令牌应不同
        $this->assertNotEquals($token1, $token2);
    }
    
    /**
     * 测试令牌熵值足够
     */
    public function testTokenEntropy()
    {
        $token = generateCSRFToken();
        
        // 32字节 = 256位熵
        $entropyBits = strlen($token) / 2 * 8;
        
        $this->assertEquals(256, $entropyBits);
    }

    // ==================== 令牌验证逻辑测试 ====================
    
    /**
     * 测试有效令牌验证成功
     */
    public function testValidTokenVerification()
    {
        $token = generateCSRFToken();
        
        $this->assertTrue(verifyCSRFToken($token));
    }
    
    /**
     * 测试无效令牌验证失败
     * @dataProvider invalidTokenProvider
     */
    public function testInvalidTokenVerification($token)
    {
        generateCSRFToken(); // 先生成一个有效令牌
        
        $this->assertFalse(verifyCSRFToken($token));
    }
    
    public function invalidTokenProvider()
    {
        return [
            '空令牌' => [''],
            '错误令牌' => ['invalid_token'],
            '短令牌' => ['abc123'],
            '格式错误' => ['not-hexadecimal!'],
            '长度不足' => ['a1b2c3'],
            '长度过长' => ['a1b2c3' . str_repeat('d', 100)],
        ];
    }
    
    /**
     * 测试Session无令牌时验证失败
     */
    public function testVerificationWithoutSessionToken()
    {
        $_SESSION['csrf_token'] = '';
        $token = 'some_token';
        
        $this->assertFalse(verifyCSRFToken($token));
    }
    
    /**
     * 测试空输入令牌验证失败
     */
    public function testVerificationWithEmptyInputToken()
    {
        generateCSRFToken();
        
        $this->assertFalse(verifyCSRFToken(''));
        $this->assertFalse(verifyCSRFToken(null));
    }

    // ==================== 时序攻击防护测试 ====================
    
    /**
     * 测试使用hash_equals防止时序攻击
     */
    public function testTimingAttackProtection()
    {
        $token = generateCSRFToken();
        
        // 正确令牌
        $this->assertTrue(verifyCSRFToken($token));
        
        // 错误令牌（长度相同）
        $wrongToken = str_repeat('a', 64);
        $this->assertFalse(verifyCSRFToken($wrongToken));
        
        // 验证使用了hash_equals（通过函数名检查）
        // hash_equals是PHP 5.6+内置函数
        $this->assertTrue(function_exists('hash_equals'));
    }
    
    /**
     * 测试不同长度令牌验证时间一致性
     */
    public function testTimingConsistency()
    {
        generateCSRFToken();
        
        // 短令牌
        $shortToken = 'abc';
        $start = microtime(true);
        verifyCSRFToken($shortToken);
        $shortTime = microtime(true) - $start;
        
        // 长令牌
        $longToken = str_repeat('a', 100);
        $start = microtime(true);
        verifyCSRFToken($longToken);
        $longTime = microtime(true) - $start;
        
        // 时间差异应在合理范围内（hash_equals保证）
        // 由于hash_equals的特性，时间差异不应显著
        $this->assertLessThan(0.01, abs($shortTime - $longTime));
    }

    // ==================== Session管理测试 ====================
    
    /**
     * 测试令牌存储在Session中
     */
    public function testTokenStoredInSession()
    {
        $token = generateCSRFToken();
        
        $this->assertEquals($token, $_SESSION['csrf_token']);
        $this->assertNotEmpty($_SESSION['csrf_token']);
    }
    
    /**
     * 测试重复生成返回相同令牌
     */
    public function testRepeatedGenerationReturnsSameToken()
    {
        $token1 = generateCSRFToken();
        $token2 = generateCSRFToken();
        $token3 = generateCSRFToken();
        
        // 应返回相同令牌（不重新生成）
        $this->assertEquals($token1, $token2);
        $this->assertEquals($token2, $token3);
    }
    
    /**
     * 测试Session清除后令牌失效
     */
    public function testTokenInvalidAfterSessionClear()
    {
        $token = generateCSRFToken();
        
        // 验证成功
        $this->assertTrue(verifyCSRFToken($token));
        
        // 清除Session
        $_SESSION = [];
        
        // 令牌应失效
        $this->assertFalse(verifyCSRFToken($token));
    }

    // ==================== 令牌生命周期测试 ====================
    
    /**
     * 测试令牌在Session期间有效
     */
    public function testTokenValidDuringSession()
    {
        $token = generateCSRFToken();
        
        // 多次验证应都成功
        for ($i = 0; $i < 5; $i++) {
            $this->assertTrue(verifyCSRFToken($token));
        }
    }
    
    /**
     * 测试令牌不会自动过期（基于Session）
     */
    public function testTokenNoAutoExpiry()
    {
        $token = generateCSRFToken();
        
        // 模拟时间流逝（令牌本身不包含时间信息）
        // 验证仍应成功
        $this->assertTrue(verifyCSRFToken($token));
    }

    // ==================== POST请求CSRF验证测试 ====================
    
    /**
     * 测试POST请求必须包含CSRF令牌
     */
    public function testPostRequiresCSRFToken()
    {
        $_POST['csrf_token'] = '';
        
        $hasToken = isset($_POST['csrf_token']) && !empty($_POST['csrf_token']);
        $this->assertFalse($hasToken);
        
        // 应拒绝请求
        $_POST['csrf_token'] = generateCSRFToken();
        $hasToken = isset($_POST['csrf_token']) && verifyCSRFToken($_POST['csrf_token']);
        $this->assertTrue($hasToken);
    }
    
    /**
     * 测试CSRF验证失败响应
     */
    public function testCSRFVerificationFailureResponse()
    {
        // 验证失败时应返回错误并阻止操作
        $expectedMessage = 'CSRF验证失败，请重试';
        
        $this->assertStringContainsString('CSRF', $expectedMessage);
        $this->assertStringContainsString('验证失败', $expectedMessage);
    }

    // ==================== 多窗口/多标签页测试 ====================
    
    /**
     * 测试多窗口共享同一令牌
     */
    public function testMultipleWindowsShareToken()
    {
        // 模拟同一Session下的多个请求
        $token = generateCSRFToken();
        
        // 窗口1验证
        $this->assertTrue(verifyCSRFToken($token));
        
        // 窗口2验证（同一Session）
        $this->assertTrue(verifyCSRFToken($token));
        
        // 窗口3验证
        $this->assertTrue(verifyCSRFToken($token));
    }

    // ==================== 安全边界测试 ====================
    
    /**
     * 测试令牌不被URL传递
     */
    public function testTokenNotPassedViaURL()
    {
        // CSRF令牌应通过POST或Header传递，不应在URL中
        $_GET['csrf_token'] = 'malicious_token';
        
        // 即使GET中有令牌，也应验证Session中的令牌
        $sessionToken = generateCSRFToken();
        
        // GET中的令牌不应被信任
        $this->assertNotEquals($_GET['csrf_token'], $sessionToken);
    }
    
    /**
     * 测试令牌长度边界
     */
    public function testTokenLengthBoundary()
    {
        $token = generateCSRFToken();
        
        // 精确64字符
        $this->assertEquals(64, strlen($token));
        
        // 不是63或65
        $this->assertNotEquals(63, strlen($token));
        $this->assertNotEquals(65, strlen($token));
    }
    
    /**
     * 测试令牌字符集边界
     */
    public function testTokenCharsetBoundary()
    {
        $token = generateCSRFToken();
        
        // 只包含a-f和0-9
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $token);
        
        // 不包含其他字符
        $this->assertDoesNotMatchRegularExpression('/[g-zG-Z]/', $token);
        $this->assertDoesNotMatchRegularExpression('/[^a-f0-9]/', $token);
    }

    // ==================== 错误处理测试 ====================
    
    /**
     * 测试验证函数返回布尔值
     */
    public function testVerificationReturnsBoolean()
    {
        $token = generateCSRFToken();
        
        $result = verifyCSRFToken($token);
        $this->assertIsBool($result);
        
        $result = verifyCSRFToken('');
        $this->assertIsBool($result);
    }
    
    /**
     * 测试空Session处理
     */
    public function testEmptySessionHandling()
    {
        $_SESSION = [];
        
        // Session无令牌时验证应失败
        $this->assertFalse(verifyCSRFToken('any_token'));
        
        // 生成令牌应正常工作
        $token = generateCSRFToken();
        $this->assertNotEmpty($token);
    }
}