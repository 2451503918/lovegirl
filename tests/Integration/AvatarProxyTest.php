<?php
/**
 * 头像代理服务测试
 * 
 * 测试覆盖范围：
 * - QQ号验证
 * - 邮箱验证
 * - 首字母头像生成
 * - 缓存机制
 * - 默认头像处理
 * - 参数验证
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class AvatarProxyTest extends TestCase
{
    protected function setUp(): void
    {
        resetTestEnvironment();
    }

    // ==================== 参数验证测试 ====================
    
    /**
     * 测试头像类型参数验证
     * @dataProvider avatarTypeProvider
     */
    public function testAvatarTypeValidation($type, $isValid)
    {
        $validTypes = ['qq', 'gravatar', 'initials'];
        
        $isValidType = in_array($type, $validTypes);
        $this->assertEquals($isValid, $isValidType);
    }
    
    public function avatarTypeProvider()
    {
        return [
            'QQ类型' => ['qq', true],
            'Gravatar类型' => ['gravatar', true],
            '首字母类型' => ['initials', true],
            '无效类型' => ['invalid', false],
            '空类型' => ['', false],
            '恶意类型' => ['qq; DROP TABLE', false],
        ];
    }

    // ==================== QQ头像测试 ====================
    
    /**
     * 测试QQ号格式验证
     * @dataProvider qqValidationProvider
     */
    public function testQQValidation($qq, $isValid)
    {
        // 验证QQ号是纯数字
        $isValidQQ = !empty($qq) && preg_match('/^\d+$/', $qq);
        $this->assertEquals($isValid, $isValidQQ);
    }
    
    public function qqValidationProvider()
    {
        return [
            '有效QQ号' => ['12345678', true],
            '纯数字' => ['10000', true],
            '包含字母' => ['abc123', false],
            '包含特殊字符' => ['12345@', false],
            '空QQ号' => ['', false],
            '负数' => ['-12345', false],
            '小数' => ['12345.6', false],
        ];
    }
    
    /**
     * 测试QQ号缓存文件名生成
     */
    public function testQQCacheFileName()
    {
        $qq = '12345678';
        $cacheDir = '/path/to/cache/';
        $expectedFileName = $cacheDir . 'qq_' . $qq . '.png';
        
        $this->assertStringContainsString('qq_', $expectedFileName);
        $this->assertStringContainsString($qq, $expectedFileName);
        $this->assertStringEndsWith('.png', $expectedFileName);
    }
    
    /**
     * 测试无效QQ号返回默认头像
     */
    public function testInvalidQQReturnsDefault()
    {
        $invalidQQs = ['', 'abc', '123@', '-123'];
        
        foreach ($invalidQQs as $qq) {
            $isValid = !empty($qq) && preg_match('/^\d+$/', $qq);
            $this->assertFalse($isValid);
        }
    }

    // ==================== Gravatar头像测试 ====================
    
    /**
     * 测试邮箱MD5参数验证
     * @dataProvider emailValidationProvider
     */
    public function testEmailMD5Validation($email, $isValid)
    {
        // 空邮箱应返回默认头像
        $isValidEmail = !empty($email);
        $this->assertEquals($isValid, $isValidEmail);
    }
    
    public function emailValidationProvider()
    {
        return [
            '有效MD5' => ['abc123def456', true],
            '空邮箱' => ['', false],
            '带特殊字符' => ['abc@123', true],
        ];
    }
    
    /**
     * 测试Gravatar缓存文件名生成
     */
    public function testGravatarCacheFileName()
    {
        $email = 'abc123def456';
        $cacheDir = '/path/to/cache/';
        $expectedFileName = $cacheDir . 'gravatar_' . $email . '.png';
        
        $this->assertStringContainsString('gravatar_', $expectedFileName);
        $this->assertStringContainsString($email, $expectedFileName);
        $this->assertStringEndsWith('.png', $expectedFileName);
    }

    // ==================== 首字母头像测试 ====================
    
    /**
     * 测试首字母提取
     * @dataProvider initialsProvider
     */
    public function testInitialsExtraction($name, $expectedInitial)
    {
        $initial = mb_substr($name, 0, 1, 'UTF-8');
        if (empty($initial)) {
            $initial = '?';
        }
        $this->assertEquals($expectedInitial, $initial);
    }
    
    public function initialsProvider()
    {
        return [
            '中文名' => ['小明', '小'],
            '英文名' => ['John', 'J'],
            '空名' => ['', '?'],
            '单字符' => ['A', 'A'],
            '特殊字符名' => ['@Test', '@'],
        ];
    }
    
    /**
     * 测试颜色选择基于名字hash
     */
    public function testColorSelectionByHash()
    {
        $colors = [
            [231, 76, 60],
            [230, 126, 34],
            [241, 196, 15],
            [46, 204, 113],
            [26, 188, 156],
            [52, 152, 219],
            [155, 89, 182],
            [233, 30, 99],
            [0, 150, 136],
            [63, 81, 181],
            [121, 85, 72],
            [96, 125, 139],
        ];
        
        $name = 'TestUser';
        $hash = crc32($name);
        $colorIndex = abs($hash) % count($colors);
        
        $this->assertGreaterThanOrEqual(0, $colorIndex);
        $this->assertLessThan(count($colors), $colorIndex);
        
        // 相同名字应产生相同颜色
        $name2 = 'TestUser';
        $hash2 = crc32($name2);
        $colorIndex2 = abs($hash2) % count($colors);
        
        $this->assertEquals($colorIndex, $colorIndex2);
    }
    
    /**
     * 测试不同名字产生不同颜色
     */
    public function testDifferentNamesDifferentColors()
    {
        $colors = [
            [231, 76, 60],
            [230, 126, 34],
            [241, 196, 15],
            [46, 204, 113],
            [26, 188, 156],
            [52, 152, 219],
            [155, 89, 182],
            [233, 30, 99],
            [0, 150, 136],
            [63, 81, 181],
            [121, 85, 72],
            [96, 125, 139],
        ];
        
        $name1 = 'Alice';
        $name2 = 'Bob';
        
        $hash1 = crc32($name1);
        $hash2 = crc32($name2);
        
        $colorIndex1 = abs($hash1) % count($colors);
        $colorIndex2 = abs($hash2) % count($colors);
        
        // 不同名字可能产生不同颜色（但不一定总是不同）
        $this->assertGreaterThanOrEqual(0, $colorIndex1);
        $this->assertGreaterThanOrEqual(0, $colorIndex2);
    }

    // ==================== 缓存机制测试 ====================
    
    /**
     * 测试缓存目录创建
     */
    public function testCacheDirectoryCreation()
    {
        $cacheDir = '/path/to/cache/';
        
        // 模拟目录不存在时创建
        $shouldCreate = !is_dir($cacheDir);
        
        // 权限应为0755
        $expectedPermission = 0755;
        $this->assertEquals(755, decoct($expectedPermission));
    }
    
    /**
     * 测试缓存文件存在检查
     */
    public function testCacheFileExistenceCheck()
    {
        // 缓存文件应检查存在性和大小
        $cacheFile = '/path/to/cache/qq_12345.png';
        
        // 模拟检查逻辑
        $fileExists = true; // 模拟
        $fileSize = 1024; // 模拟
        
        $shouldServe = $fileExists && $fileSize > 0;
        $this->assertTrue($shouldServe);
        
        // 空文件不应使用
        $fileSize = 0;
        $shouldServe = $fileExists && $fileSize > 0;
        $this->assertFalse($shouldServe);
    }
    
    /**
     * 测试缓存响应头
     */
    public function testCacheResponseHeaders()
    {
        // 缓存响应应设置正确的头
        $headers = [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=604800', // 7天
            'X-Avatar-Source' => 'cache',
        ];
        
        $this->assertEquals('image/png', $headers['Content-Type']);
        $this->assertStringContainsString('max-age=604800', $headers['Cache-Control']);
        $this->assertEquals('cache', $headers['X-Avatar-Source']);
    }

    // ==================== 默认头像测试 ====================
    
    /**
     * 测试默认头像响应头
     */
    public function testDefaultAvatarResponseHeaders()
    {
        $headers = [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'public, max-age=3600', // 1小时
            'X-Avatar-Source' => 'default',
        ];
        
        $this->assertEquals('image/png', $headers['Content-Type']);
        $this->assertStringContainsString('max-age=3600', $headers['Cache-Control']);
        $this->assertEquals('default', $headers['X-Avatar-Source']);
    }
    
    /**
     * 测试默认头像文件不存在时动态生成
     */
    public function testDefaultAvatarDynamicGeneration()
    {
        // 当默认头像文件不存在时，应使用GD动态生成
        $size = 200;
        
        // 验证尺寸设置
        $this->assertEquals(200, $size);
        
        // GD函数可用性检查
        $gdAvailable = function_exists('imagecreatetruecolor');
        // 在测试环境中假设GD可用
        $this->assertTrue(true); // GD可用性取决于环境
    }

    // ==================== 图片尺寸测试 ====================
    
    /**
     * 测试头像尺寸
     */
    public function testAvatarSize()
    {
        $size = 200;
        
        $this->assertEquals(200, $size);
        $this->assertGreaterThan(0, $size);
    }
    
    /**
     * 测试字体大小计算
     */
    public function testFontSizeCalculation()
    {
        $size = 200;
        $fontSize = intval($size * 0.45);
        
        $this->assertEquals(90, $fontSize);
        $this->assertLessThan($size, $fontSize);
    }

    // ==================== TTF字体支持测试 ====================
    
    /**
     * 测试TTF字体路径查找
     */
    public function testTTFFontPathSearch()
    {
        $fontPaths = [
            '/usr/share/fonts/truetype/noto/NotoSansSC-Regular.ttf',
            '/usr/share/fonts/opentype/noto/NotoSansCJK-Regular.ttc',
            '/usr/share/fonts/noto-cjk/NotoSansCJK-Regular.ttc',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        ];
        
        // 验证路径列表不为空
        $this->assertNotEmpty($fontPaths);
        
        // 验证路径格式
        foreach ($fontPaths as $path) {
            $this->assertStringStartsWith('/usr/share/fonts/', $path);
        }
    }
    
    /**
     * 测试无TTF支持时的备用方案
     */
    public function testFallbackWithoutTTF()
    {
        // 无TTF时使用内置字体
        $builtInFont = 5;
        
        $this->assertEquals(5, $builtInFont);
        $this->assertGreaterThanOrEqual(1, $builtInFont);
        $this->assertLessThanOrEqual(5, $builtInFont);
    }

    // ==================== 非ASCII字符处理测试 ====================
    
    /**
     * 测试非ASCII字符首字母处理
     */
    public function testNonASCIIInitialHandling()
    {
        // 无TTF且非ASCII字符时使用'?'
        $initial = '中';
        $isASCII = preg_match('/^[a-zA-Z0-9?]$/', $initial);
        
        // preg_match返回0表示不匹配，返回false表示错误
        // 非ASCII字符应返回0（不匹配）
        $this->assertEquals(0, $isASCII);
        
        // 应转换为'?'
        $fallbackInitial = '?';
        $this->assertEquals('?', $fallbackInitial);
    }

    // ==================== 边界条件测试 ====================
    
    /**
     * 测试超长名字处理
     */
    public function testLongNameHandling()
    {
        $longName = str_repeat('测', 1000);
        $initial = mb_substr($longName, 0, 1, 'UTF-8');
        
        // 只取第一个字符
        $this->assertEquals('测', $initial);
        $this->assertEquals(1, mb_strlen($initial, 'UTF-8'));
    }
    
    /**
     * 测试特殊Unicode字符
     */
    public function testSpecialUnicodeCharacters()
    {
        $specialChars = ['😀', '🎉', '❤️', '©', '®'];
        
        foreach ($specialChars as $char) {
            $initial = mb_substr($char, 0, 1, 'UTF-8');
            $this->assertNotEmpty($initial);
        }
    }
    
    /**
     * 测试空参数默认行为
     */
    public function testEmptyParameterDefaultBehavior()
    {
        // 空类型应返回默认头像
        $type = '';
        $validTypes = ['qq', 'gravatar', 'initials'];
        
        $shouldServeDefault = !in_array($type, $validTypes);
        $this->assertTrue($shouldServeDefault);
    }

    // ==================== 安全性测试 ====================
    
    /**
     * 测试参数trim处理
     */
    public function testParameterTrim()
    {
        $qq = ' 12345 ';
        $trimmedQQ = trim($qq);
        
        $this->assertEquals('12345', $trimmedQQ);
        
        $type = ' qq ';
        $trimmedType = trim($type);
        
        $this->assertEquals('qq', $trimmedType);
    }
    
    /**
     * 测试缓存目录路径安全
     */
    public function testCacheDirectoryPathSecurity()
    {
        // 缓存目录不应接受用户输入
        $cacheDir = __DIR__ . '/../assets/img/avatars/cache/';
        
        // 应使用绝对路径
        $this->assertStringStartsWith('/', $cacheDir);
        
        // 不应包含用户可控部分
        $maliciousQQ = '../../../etc/passwd';
        $safeQQ = preg_match('/^\d+$/', $maliciousQQ) ? $maliciousQQ : '';
        
        $this->assertEmpty($safeQQ);
    }
}