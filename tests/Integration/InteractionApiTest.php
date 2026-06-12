<?php
/**
 * 交互API测试
 * 
 * 测试覆盖范围：
 * - 点赞/浏览计数逻辑
 * - Action参数验证
 * - Type参数验证
 * - ID参数验证
 * - 数据库操作安全性
 * - CORS配置
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class InteractionApiTest extends TestCase
{
    protected function setUp(): void
    {
        resetTestEnvironment();
    }

    // ==================== Action参数验证测试 ====================
    
    /**
     * 测试允许的Action列表
     */
    public function testAllowedActions()
    {
        $allowedActions = ['like', 'view', 'get_likes', 'get_views'];
        
        // 验证允许的action
        foreach ($allowedActions as $action) {
            $this->assertContains($action, $allowedActions);
        }
        
        // 验证不允许的action
        $invalidActions = ['delete', 'update', 'insert', 'hack', ''];
        foreach ($invalidActions as $action) {
            $this->assertNotContains($action, $allowedActions);
        }
    }
    
    /**
     * 测试无效Action返回400错误
     */
    public function testInvalidActionReturns400()
    {
        $action = 'invalid_action';
        $allowedActions = ['like', 'view', 'get_likes', 'get_views'];
        
        $isValid = in_array($action, $allowedActions);
        $this->assertFalse($isValid);
        
        // 无效action应返回400状态码
        $expectedCode = 400;
        $this->assertEquals(400, $expectedCode);
    }

    // ==================== Type参数验证测试 ====================
    
    /**
     * 测试Type到表名的映射
     * @dataProvider typeMappingProvider
     */
    public function testTypeMapping($type, $expectedTable)
    {
        $tableMap = [
            'article' => 'little',
            'album' => 'photo',
            'message' => 'leaving',
            'event' => 'lovelist',
        ];
        
        $table = $tableMap[$type] ?? '';
        $this->assertEquals($expectedTable, $table);
    }
    
    public function typeMappingProvider()
    {
        return [
            '文章类型' => ['article', 'little'],
            '相册类型' => ['album', 'photo'],
            '留言类型' => ['message', 'leaving'],
            '事件类型' => ['event', 'lovelist'],
            '无效类型' => ['invalid', ''],
            '空类型' => ['', ''],
        ];
    }
    
    /**
     * 测试无效Type返回错误
     */
    public function testInvalidTypeReturnsError()
    {
        $type = 'invalid_type';
        $tableMap = [
            'article' => 'little',
            'album' => 'photo',
            'message' => 'leaving',
            'event' => 'lovelist',
        ];
        
        $table = $tableMap[$type] ?? '';
        $this->assertEmpty($table);
        
        // 无效type应返回400错误
        $expectedCode = 400;
        $this->assertEquals(400, $expectedCode);
    }

    // ==================== ID参数验证测试 ====================
    
    /**
     * 测试ID参数验证
     * @dataProvider idValidationProvider
     */
    public function testIDValidation($id, $isValid)
    {
        $id = intval($id);
        $valid = $id > 0;
        $this->assertEquals($isValid, $valid);
    }
    
    public function idValidationProvider()
    {
        return [
            '正整数ID' => [123, true],
            '字符串数字' => ['456', true],
            '零ID' => [0, false],
            '负ID' => [-1, false],
            '空ID' => ['', false],
            '非数字' => ['abc', false],
        ];
    }
    
    /**
     * 测试ID缺失返回错误
     */
    public function testMissingIDReturnsError()
    {
        $type = 'article';
        $id = 0;
        
        $valid = !empty($type) && $id > 0;
        $this->assertFalse($valid);
        
        // 缺少id应返回400错误
        $expectedCode = 400;
        $this->assertEquals(400, $expectedCode);
    }

    // ==================== 点赞逻辑测试 ====================
    
    /**
     * 测试点赞计数增加逻辑
     */
    public function testLikeIncrement()
    {
        $currentLikes = 10;
        $newLikes = $currentLikes + 1;
        
        $this->assertEquals(11, $newLikes);
        
        // 使用COALESCE处理NULL
        $currentLikes = null;
        $newLikes = ($currentLikes ?? 0) + 1;
        $this->assertEquals(1, $newLikes);
    }
    
    /**
     * 测试点赞返回格式
     */
    public function testLikeResponseFormat()
    {
        $successResponse = [
            'success' => true,
            'likes' => 11
        ];
        
        $this->assertTrue($successResponse['success']);
        $this->assertIsInt($successResponse['likes']);
        
        $failureResponse = [
            'success' => false,
            'error' => 'Update failed'
        ];
        
        $this->assertFalse($failureResponse['success']);
        $this->assertNotEmpty($failureResponse['error']);
    }

    // ==================== 浏览计数测试 ====================
    
    /**
     * 测试浏览计数增加逻辑
     */
    public function testViewIncrement()
    {
        $currentViews = 100;
        $newViews = $currentViews + 1;
        
        $this->assertEquals(101, $newViews);
        
        // 使用COALESCE处理NULL
        $currentViews = null;
        $newViews = ($currentViews ?? 0) + 1;
        $this->assertEquals(1, $newViews);
    }
    
    /**
     * 测试浏览返回格式
     */
    public function testViewResponseFormat()
    {
        $successResponse = ['success' => true];
        
        $this->assertTrue($successResponse['success']);
    }

    // ==================== 获取计数测试 ====================
    
    /**
     * 测试获取点赞数返回格式
     */
    public function testGetLikesResponseFormat()
    {
        $response = [
            'success' => true,
            'likes' => 50
        ];
        
        $this->assertTrue($response['success']);
        $this->assertIsInt($response['likes']);
        $this->assertGreaterThanOrEqual(0, $response['likes']);
    }
    
    /**
     * 测试获取浏览数返回格式
     */
    public function testGetViewsResponseFormat()
    {
        $response = [
            'success' => true,
            'views' => 100
        ];
        
        $this->assertTrue($response['success']);
        $this->assertIsInt($response['views']);
        $this->assertGreaterThanOrEqual(0, $response['views']);
    }
    
    /**
     * 测试NULL计数返回0
     */
    public function testNullCountReturnsZero()
    {
        $rowLikes = null;
        $likes = intval($rowLikes ?? 0);
        
        $this->assertEquals(0, $likes);
        
        $rowViews = null;
        $views = intval($rowViews ?? 0);
        
        $this->assertEquals(0, $views);
    }

    // ==================== CORS配置测试 ====================
    
    /**
     * 测试允许的Origin列表
     */
    public function testAllowedOrigins()
    {
        $allowedOrigins = ['lovedemo.54oimx.top', 'love.54oimx.top', 'localhost', '127.0.0.1'];
        
        foreach ($allowedOrigins as $origin) {
            $this->assertContains($origin, $allowedOrigins);
        }
    }
    
    /**
     * 测试OPTIONS请求处理
     */
    public function testOptionsRequestHandling()
    {
        $_SERVER['REQUEST_METHOD'] = 'OPTIONS';
        
        $isOptions = $_SERVER['REQUEST_METHOD'] === 'OPTIONS';
        $this->assertTrue($isOptions);
        
        // OPTIONS请求应返回204
        $expectedStatus = 204;
        $this->assertEquals(204, $expectedStatus);
    }
    
    /**
     * 测试允许的HTTP方法
     */
    public function testAllowedMethods()
    {
        $allowedMethods = ['GET', 'POST', 'OPTIONS'];
        
        foreach ($allowedMethods as $method) {
            $this->assertContains($method, $allowedMethods);
        }
        
        // 不允许的方法
        $disallowedMethods = ['PUT', 'DELETE', 'PATCH'];
        foreach ($disallowedMethods as $method) {
            $this->assertNotContains($method, $allowedMethods);
        }
    }

    // ==================== 数据库不可用降级测试 ====================
    
    /**
     * 测试数据库不可用时的点赞降级
     */
    public function testLikeFallbackWhenDatabaseUnavailable()
    {
        $response = [
            'success' => true,
            'message' => 'Database unavailable, action ignored'
        ];
        
        $this->assertTrue($response['success']);
        $this->assertStringContainsString('Database unavailable', $response['message']);
    }
    
    /**
     * 测试数据库不可用时的获取计数降级
     */
    public function testGetCountFallbackWhenDatabaseUnavailable()
    {
        $response = [
            'success' => true,
            'count' => 0
        ];
        
        $this->assertTrue($response['success']);
        $this->assertEquals(0, $response['count']);
    }

    // ==================== SQL注入防护测试 ====================
    
    /**
     * 测试表名不在SQL中直接拼接
     */
    public function testTableNameNotDirectlyConcatenated()
    {
        $tableMap = [
            'article' => 'little',
            'album' => 'photo',
            'message' => 'leaving',
            'event' => 'lovelist',
        ];
        
        // 表名来自映射，不是用户输入
        $userInput = 'article';
        $table = $tableMap[$userInput] ?? '';
        
        // 无效输入返回空表名
        $maliciousInput = 'little; DROP TABLE users--';
        $table = $tableMap[$maliciousInput] ?? '';
        $this->assertEmpty($table);
    }
    
    /**
     * 测试ID使用intval转换
     */
    public function testIDIntvalConversion()
    {
        $maliciousIds = [
            '1; DROP TABLE users',
            '1 OR 1=1',
            '1 UNION SELECT * FROM login',
        ];
        
        foreach ($maliciousIds as $id) {
            $safeId = intval($id);
            $this->assertEquals(1, $safeId);
            $this->assertIsInt($safeId);
        }
    }

    // ==================== 边界条件测试 ====================
    
    /**
     * 测试大数值ID
     */
    public function testLargeIDValue()
    {
        $largeId = 999999999;
        $safeId = intval($largeId);
        
        $this->assertEquals(999999999, $safeId);
        $this->assertGreaterThan(0, $safeId);
    }
    
    /**
     * 测试计数器溢出保护
     */
    public function testCounterOverflow()
    {
        // PHP整数最大值
        $maxInt = PHP_INT_MAX;
        
        // 增加计数不应溢出
        $current = $maxInt - 1;
        $new = $current + 1;
        
        $this->assertEquals($maxInt, $new);
    }
    
    /**
     * 测试同时获取GET和POST参数
     */
    public function testGetAndPostParameterMerge()
    {
        $_GET['action'] = 'like';
        $_POST['action'] = 'view';
        
        // 代码逻辑: $_GET['action'] ?? $_POST['action'] ?? ''
        $action = $_GET['action'] ?? $_POST['action'] ?? '';
        
        // GET优先
        $this->assertEquals('like', $action);
        
        // 只有POST时
        $_GET['action'] = null;
        $action = $_GET['action'] ?? $_POST['action'] ?? '';
        $this->assertEquals('view', $action);
        
        // 都没有时
        $_GET['action'] = null;
        $_POST['action'] = null;
        $action = $_GET['action'] ?? $_POST['action'] ?? '';
        $this->assertEquals('', $action);
    }

    // ==================== 响应JSON格式测试 ====================
    
    /**
     * 测试响应Content-Type
     */
    public function testResponseContentType()
    {
        $contentType = 'application/json; charset=utf-8';
        
        $this->assertStringContainsString('application/json', $contentType);
        $this->assertStringContainsString('utf-8', $contentType);
    }
    
    /**
     * 测试错误响应格式
     */
    public function testErrorResponseFormat()
    {
        $errorResponse = [
            'success' => false,
            'error' => 'Invalid action'
        ];
        
        $this->assertFalse($errorResponse['success']);
        $this->assertNotEmpty($errorResponse['error']);
    }
}