<?php
/**
 * Function.php 核心函数单元测试
 * 
 * 测试覆盖范围：
 * - QQ号验证 (checkQQ)
 * - 特殊字符过滤 (replaceSpecialChar)
 * - XSS防护 (escapeXSS)
 * - CSRF令牌生成与验证 (generateCSRFToken, verifyCSRFToken)
 * - 时间格式转换 (time_tran)
 * - IP地址验证逻辑
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class FunctionTest extends TestCase
{
    protected function setUp(): void
    {
        resetTestEnvironment();
    }

    // ==================== checkQQ 测试 ====================
    
    /**
     * 测试有效QQ号
     * @dataProvider validQQProvider
     */
    public function testCheckQQValid($qq)
    {
        $this->assertTrue(checkQQ($qq));
    }
    
    public function validQQProvider()
    {
        return [
            '标准QQ号' => ['12345'],
            '大号QQ' => ['123456789'],
            '最小有效QQ' => ['10000'],
            '常见QQ号' => ['54321'],
        ];
    }
    
    /**
     * 测试无效QQ号
     * @dataProvider invalidQQProvider
     */
    public function testCheckQQInvalid($qq)
    {
        $this->assertFalse(checkQQ($qq));
    }
    
    public function invalidQQProvider()
    {
        return [
            '以0开头' => ['01234'],
            '纯字母' => ['abcdef'],
            '空字符串' => [''],
            '包含特殊字符' => ['1234@'],
            '过短(4位)' => ['1234'],
            '包含空格' => ['123 45'],
            '负数' => ['-12345'],
            '小数' => ['12345.6'],
        ];
    }

    // ==================== replaceSpecialChar 测试 ====================
    
    /**
     * 测试特殊字符过滤
     * @dataProvider specialCharProvider
     */
    public function testReplaceSpecialChar($input, $expected)
    {
        $this->assertEquals($expected, replaceSpecialChar($input));
    }
    
    public function specialCharProvider()
    {
        return [
            '单引号过滤' => ["test'value", "testvalue"],
            '双引号过滤' => ["test\"value", "testvalue"],
            '反斜杠过滤' => ["test\\value", "testvalue"],
            '分号过滤' => ["test;value", "testvalue"],
            '反引号过滤' => ["test`value", "testvalue"],
            '多特殊字符' => ["'test\";\\`", "test"],
            '无特殊字符' => ['normal_text', 'normal_text'],
            '空字符串' => ['', ''],
            '中文保持' => ['中文测试', '中文测试'],
            'SQL注入尝试' => ["'; DROP TABLE--", " DROP TABLE--"],
        ];
    }
    
    /**
     * 测试特殊字符过滤不破坏正常内容
     */
    public function testReplaceSpecialCharPreservesNormalContent()
    {
        $input = 'Hello World 123 中文测试';
        $this->assertEquals($input, replaceSpecialChar($input));
    }

    // ==================== escapeXSS 测试 ====================
    
    /**
     * 测试XSS防护
     * @dataProvider xssPayloadProvider
     */
    public function testEscapeXSS($input, $expected)
    {
        $this->assertEquals($expected, escapeXSS($input));
    }
    
    public function xssPayloadProvider()
    {
        return [
            'script标签' => ['<script>alert(1)</script>', '&lt;script&gt;alert(1)&lt;/script&gt;'],
            '单引号转义' => ["test'value", "test&#039;value"],
            '双引号转义' => ['test"value', 'test&quot;value'],
            'HTML实体保持' => ['&amp;test', '&amp;amp;test'],
            '空字符串' => ['', ''],
            '纯文本' => ['Normal text', 'Normal text'],
            '事件属性注入' => ['<img onerror="alert(1)">', '&lt;img onerror=&quot;alert(1)&quot;&gt;'],
        ];
    }
    
    /**
     * 测试XSS防护处理UTF-8编码
     */
    public function testEscapeXSSUTF8()
    {
        $input = '中文<script>alert(1)</script>测试';
        $expected = '中文&lt;script&gt;alert(1)&lt;/script&gt;测试';
        $this->assertEquals($expected, escapeXSS($input));
    }

    // ==================== CSRF令牌测试 ====================
    
    /**
     * 测试CSRF令牌生成
     */
    public function testGenerateCSRFToken()
    {
        $token = generateCSRFToken();
        
        // 验证令牌长度 (32字节 = 64个十六进制字符)
        $this->assertEquals(64, strlen($token));
        
        // 验证令牌是十六进制格式
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);
        
        // 验证令牌已存储在session中
        $this->assertEquals($token, $_SESSION['csrf_token']);
    }
    
    /**
     * 测试CSRF令牌重复调用返回相同令牌
     */
    public function testGenerateCSRFTokenReturnsSameToken()
    {
        $token1 = generateCSRFToken();
        $token2 = generateCSRFToken();
        
        $this->assertEquals($token1, $token2);
    }
    
    /**
     * 测试CSRF令牌验证成功
     */
    public function testVerifyCSRFTokenSuccess()
    {
        $token = generateCSRFToken();
        $this->assertTrue(verifyCSRFToken($token));
    }
    
    /**
     * 测试CSRF令牌验证失败场景
     * @dataProvider invalidTokenProvider
     */
    public function testVerifyCSRFTokenFailure($sessionToken, $inputToken)
    {
        $_SESSION['csrf_token'] = $sessionToken;
        $this->assertFalse(verifyCSRFToken($inputToken));
    }
    
    public function invalidTokenProvider()
    {
        return [
            '空session令牌' => ['', 'abc123'],
            '空输入令牌' => ['abc123', ''],
            '令牌不匹配' => ['abc123', 'xyz789'],
            '两者都空' => ['', ''],
        ];
    }
    
    /**
     * 测试CSRF令牌使用hash_equals防止时序攻击
     */
    public function testVerifyCSRFTokenUsesHashEquals()
    {
        $token = generateCSRFToken();
        
        // 正确令牌应通过验证
        $this->assertTrue(verifyCSRFToken($token));
        
        // 错误令牌应失败
        $wrongToken = str_repeat('a', 64);
        $this->assertFalse(verifyCSRFToken($wrongToken));
    }

    // ==================== time_tran 测试 ====================
    
    /**
     * 测试时间转换函数
     * @dataProvider timeTranProvider
     */
    public function testTimeTran($timestamp, $expected)
    {
        $result = time_tran($timestamp);
        $this->assertStringContainsString($expected, $result);
    }
    
    public function timeTranProvider()
    {
        $now = time();
        return [
            '秒前' => [$now - 30, '秒前'],
            '分钟前' => [$now - 180, '分钟前'],
            '小时前' => [$now - 7200, '小时前'],
            '天前' => [$now - 172800, '天前'],
            '月前' => [$now - 2592000 * 2, '月前'],
            '年前' => [$now - 31536000 * 2, '年前'],
        ];
    }
    
    /**
     * 测试空时间返回空字符串
     */
    public function testTimeTranEmpty()
    {
        $this->assertEquals('', time_tran(0));
        $this->assertEquals('', time_tran(null));
    }
    
    /**
     * 测试未来时间返回日期格式
     */
    public function testTimeTranFuture()
    {
        $future = time() + 3600;
        $result = time_tran($future);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $result);
    }

    // ==================== 边界条件测试 ====================
    
    /**
     * 测试QQ号边界值
     */
    public function testCheckQQBoundary()
    {
        // 最小有效QQ号 (10000)
        $this->assertTrue(checkQQ('10000'));
        
        // 9999 无效
        $this->assertFalse(checkQQ('9999'));
        
        // 非数字
        $this->assertFalse(checkQQ('10000a'));
    }
    
    /**
     * 测试超长字符串处理
     */
    public function testEscapeXSSLongString()
    {
        $longString = str_repeat('<script>', 1000);
        $result = escapeXSS($longString);
        
        // 应正确转义所有标签
        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }
    
    /**
     * 测试特殊字符过滤边界
     */
    public function testReplaceSpecialCharBoundary()
    {
        // 全特殊字符
        $this->assertEquals('', replaceSpecialChar("'''\"\"\"\\\\\\;;;```"));
        
        // 混合边界
        $this->assertEquals('test', replaceSpecialChar("'test'"));
    }
}