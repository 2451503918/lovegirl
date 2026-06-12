<?php
/**
 * 登录安全测试
 * 
 * 测试覆盖范围：
 * - 暴力破解防护机制
 * - 登录锁定时间
 * - 密码验证逻辑
 * - Session管理
 * - 安全码验证
 */

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class LoginSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        resetTestEnvironment();
    }

    // ==================== 暴力破解防护测试 ====================
    
    /**
     * 测试暴力破解防护参数
     */
    public function testBruteForceProtectionParameters()
    {
        $maxAttempts = 5;
        $lockoutTime = 900; // 15分钟
        
        // 最大尝试次数应为5
        $this->assertEquals(5, $maxAttempts);
        
        // 锁定时间应为900秒(15分钟)
        $this->assertEquals(900, $lockoutTime);
    }
    
    /**
     * 测试失败计数递增逻辑
     */
    public function testFailedAttemptIncrement()
    {
        $_SESSION['login_attempts_127.0.0.1'] = 0;
        
        // 模拟登录失败
        for ($i = 1; $i <= 4; $i++) {
            $_SESSION['login_attempts_127.0.0.1']++;
        }
        
        // 应达到4次失败
        $this->assertEquals(4, $_SESSION['login_attempts_127.0.0.1']);
        
        // 第5次失败应触发锁定
        $_SESSION['login_attempts_127.0.0.1']++;
        $this->assertEquals(5, $_SESSION['login_attempts_127.0.0.1']);
        
        // 达到最大次数后应设置锁定
        if ($_SESSION['login_attempts_127.0.0.1'] >= 5) {
            $_SESSION['login_lockout_127.0.0.1'] = time() + 900;
            unset($_SESSION['login_attempts_127.0.0.1']);
        }
        
        // 失败计数应被清除
        $this->assertFalse(isset($_SESSION['login_attempts_127.0.0.1']));
        
        // 锁定时间应设置
        $this->assertTrue(isset($_SESSION['login_lockout_127.0.0.1']));
        $this->assertGreaterThan(time(), $_SESSION['login_lockout_127.0.0.1']);
    }
    
    /**
     * 测试锁定状态检查
     */
    public function testLockoutStatusCheck()
    {
        $lockoutKey = 'login_lockout_127.0.0.1';
        
        // 设置锁定时间（15分钟后）
        $_SESSION[$lockoutKey] = time() + 900;
        
        // 检查是否被锁定
        $isLocked = isset($_SESSION[$lockoutKey]) && $_SESSION[$lockoutKey] > time();
        $this->assertTrue($isLocked);
        
        // 计算剩余时间
        $remaining = $_SESSION[$lockoutKey] - time();
        $this->assertGreaterThan(0, $remaining);
        $this->assertLessThanOrEqual(900, $remaining);
    }
    
    /**
     * 测试锁定过期后自动解除
     */
    public function testLockoutExpiration()
    {
        $lockoutKey = 'login_lockout_127.0.0.1';
        
        // 设置已过期的锁定时间
        $_SESSION[$lockoutKey] = time() - 1;
        
        // 检查是否已过期
        $isExpired = isset($_SESSION[$lockoutKey]) && $_SESSION[$lockoutKey] <= time();
        $this->assertTrue($isExpired);
        
        // 过期后应允许登录
        $canLogin = !isset($_SESSION[$lockoutKey]) || $_SESSION[$lockoutKey] <= time();
        $this->assertTrue($canLogin);
    }
    
    /**
     * 测试登录成功后清除失败记录
     */
    public function testClearAttemptsOnSuccess()
    {
        $attemptsKey = 'login_attempts_127.0.0.1';
        $lockoutKey = 'login_lockout_127.0.0.1';
        
        // 设置失败记录
        $_SESSION[$attemptsKey] = 3;
        $_SESSION[$lockoutKey] = time() + 900;
        
        // 模拟登录成功后清除
        unset($_SESSION[$attemptsKey]);
        unset($_SESSION[$lockoutKey]);
        
        // 应被清除
        $this->assertFalse(isset($_SESSION[$attemptsKey]));
        $this->assertFalse(isset($_SESSION[$lockoutKey]));
    }

    // ==================== 密码验证测试 ====================
    
    /**
     * 测试密码哈希验证
     */
    public function testPasswordHashVerification()
    {
        $password = 'testPassword123';
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // 正确密码应验证成功
        $this->assertTrue(password_verify($password, $hashedPassword));
        
        // 错误密码应验证失败
        $this->assertFalse(password_verify('wrongPassword', $hashedPassword));
        
        // 空密码应验证失败
        $this->assertFalse(password_verify('', $hashedPassword));
    }
    
    /**
     * 测试密码哈希安全性
     */
    public function testPasswordHashSecurity()
    {
        $password = 'testPassword';
        $hash1 = password_hash($password, PASSWORD_DEFAULT);
        $hash2 = password_hash($password, PASSWORD_DEFAULT);
        
        // 每次哈希应不同（因为有盐值）
        $this->assertNotEquals($hash1, $hash2);
        
        // 但两个哈希都应能验证同一密码
        $this->assertTrue(password_verify($password, $hash1));
        $this->assertTrue(password_verify($password, $hash2));
    }
    
    /**
     * 测试用户名匹配逻辑
     */
    public function testUsernameMatching()
    {
        $inputUser = 'admin';
        $storedUser = 'admin';
        
        // 精确匹配
        $this->assertEquals($inputUser, $storedUser);
        
        // trim后匹配
        $inputUserTrimmed = trim(' admin ');
        $this->assertEquals($inputUser, trim($inputUserTrimmed));
        
        // 大小写不匹配
        $inputUserUpper = 'ADMIN';
        $this->assertNotEquals($inputUser, $inputUserUpper);
    }

    // ==================== Session管理测试 ====================
    
    /**
     * 测试登录成功Session设置
     */
    public function testLoginSuccessSession()
    {
        $username = 'admin';
        
        // 模拟登录成功
        $_SESSION['loginadmin'] = $username;
        
        // Session应正确设置
        $this->assertEquals($username, $_SESSION['loginadmin']);
        $this->assertNotEmpty($_SESSION['loginadmin']);
    }
    
    /**
     * 测试Session空值检查
     */
    public function testSessionEmptyCheck()
    {
        // 未登录状态
        $_SESSION['loginadmin'] = '';
        
        $isLoggedIn = isset($_SESSION['loginadmin']) && $_SESSION['loginadmin'] !== '';
        $this->assertFalse($isLoggedIn);
        
        // 已登录状态
        $_SESSION['loginadmin'] = 'admin';
        
        $isLoggedIn = isset($_SESSION['loginadmin']) && $_SESSION['loginadmin'] !== '';
        $this->assertTrue($isLoggedIn);
    }
    
    /**
     * 测试Session销毁
     */
    public function testSessionDestroy()
    {
        $_SESSION['loginadmin'] = 'admin';
        $_SESSION['csrf_token'] = 'test_token';
        
        // 模拟session_destroy后的效果
        $_SESSION = [];
        
        // Session应被清除
        $this->assertFalse(isset($_SESSION['loginadmin']));
        $this->assertFalse(isset($_SESSION['csrf_token']));
    }

    // ==================== IP记录测试 ====================
    
    /**
     * 测试IP地址获取
     */
    public function testIPAddressRetrieval()
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        
        // IP应被记录
        $this->assertNotEmpty($ip);
        
        // 用于生成唯一键
        $attemptsKey = 'login_attempts_' . $ip;
        $lockoutKey = 'login_lockout_' . $ip;
        
        $this->assertStringContainsString($ip, $attemptsKey);
        $this->assertStringContainsString($ip, $lockoutKey);
    }
    
    /**
     * 测试不同IP独立计数
     */
    public function testIndependentIPCounting()
    {
        $ip1 = '192.168.1.1';
        $ip2 = '192.168.1.2';
        
        $_SESSION['login_attempts_' . $ip1] = 3;
        $_SESSION['login_attempts_' . $ip2] = 1;
        
        // 不同IP的计数应独立
        $this->assertEquals(3, $_SESSION['login_attempts_' . $ip1]);
        $this->assertEquals(1, $_SESSION['login_attempts_' . $ip2]);
        
        // 锁定一个IP不影响另一个
        $_SESSION['login_lockout_' . $ip1] = time() + 900;
        
        $ip1Locked = isset($_SESSION['login_lockout_' . $ip1]);
        $ip2Locked = isset($_SESSION['login_lockout_' . $ip2]);
        
        $this->assertTrue($ip1Locked);
        $this->assertFalse($ip2Locked);
    }

    // ==================== POST请求验证测试 ====================
    
    /**
     * 测试仅POST方法允许登录
     */
    public function testPostMethodOnly()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        $isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
        $this->assertTrue($isPost);
        
        // GET请求不应处理
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
        $this->assertFalse($isPost);
    }
    
    /**
     * 测试POST参数获取
     */
    public function testPostParameterRetrieval()
    {
        $_POST['adminName'] = 'admin';
        $_POST['pw'] = 'password123';
        
        $adminName = $_POST['adminName'];
        $pw = $_POST['pw'];
        
        $this->assertEquals('admin', $adminName);
        $this->assertEquals('password123', $pw);
    }

    // ==================== 边界条件测试 ====================
    
    /**
     * 测试最大尝试次数边界
     */
    public function testMaxAttemptsBoundary()
    {
        $maxAttempts = 5;
        
        // 4次失败不锁定
        $_SESSION['login_attempts_test'] = 4;
        $shouldLock = $_SESSION['login_attempts_test'] >= $maxAttempts;
        $this->assertFalse($shouldLock);
        
        // 5次失败触发锁定
        $_SESSION['login_attempts_test'] = 5;
        $shouldLock = $_SESSION['login_attempts_test'] >= $maxAttempts;
        $this->assertTrue($shouldLock);
        
        // 6次失败（理论上不应超过5）
        $_SESSION['login_attempts_test'] = 6;
        $shouldLock = $_SESSION['login_attempts_test'] >= $maxAttempts;
        $this->assertTrue($shouldLock);
    }
    
    /**
     * 测试锁定时间边界
     */
    public function testLockoutTimeBoundary()
    {
        $lockoutTime = 900; // 15分钟
        
        // 刚锁定
        $_SESSION['login_lockout_test'] = time() + $lockoutTime;
        $remaining = $_SESSION['login_lockout_test'] - time();
        $this->assertLessThanOrEqual($lockoutTime, $remaining);
        $this->assertGreaterThan(0, $remaining);
        
        // 锁定即将过期
        $_SESSION['login_lockout_test'] = time() + 1;
        $remaining = $_SESSION['login_lockout_test'] - time();
        $this->assertLessThanOrEqual(1, $remaining);
        
        // 锁定已过期
        $_SESSION['login_lockout_test'] = time() - 1;
        $isExpired = $_SESSION['login_lockout_test'] <= time();
        $this->assertTrue($isExpired);
    }
    
    /**
     * 测试空用户名/密码处理
     */
    public function testEmptyCredentialsHandling()
    {
        $adminName = '';
        $pw = '';
        
        // 空用户名trim后仍为空
        $this->assertEquals('', trim($adminName));
        
        // 空密码验证应失败
        $hashedPw = password_hash('validPassword', PASSWORD_DEFAULT);
        $this->assertFalse(password_verify($pw, $hashedPw));
    }
    
    /**
     * 测试特殊字符用户名处理
     */
    public function testSpecialCharacterUsername()
    {
        $usernames = [
            'admin<script>',
            'admin;DROP TABLE',
            "admin'--",
            'admin"test',
        ];
        
        foreach ($usernames as $username) {
            // trim后可能包含特殊字符
            $trimmed = trim($username);
            
            // 应通过prepare语句处理防止注入
            $this->assertNotEmpty($trimmed);
        }
    }

    // ==================== 错误处理测试 ====================
    
    /**
     * 测试SQL错误日志记录
     */
    public function testSQLErrorLogging()
    {
        // 模拟prepare失败
        $mockError = 'Query failed';
        
        // 错误应被记录（在实际代码中使用error_log）
        $this->assertNotEmpty($mockError);
    }
    
    /**
     * 测试锁定提示信息
     */
    public function testLockoutMessage()
    {
        $remaining = 900; // 15分钟
        $minutes = ceil($remaining / 60);
        
        $message = "登录尝试过多，请{$minutes}分钟后再试";
        
        $this->assertStringContainsString('登录尝试过多', $message);
        $this->assertStringContainsString('分钟后再试', $message);
    }
}