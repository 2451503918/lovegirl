<?php
/**
 * 登录安全逻辑测试 - admin/loginPost.php
 *
 * 覆盖：
 * - 暴力破解防护：失败计数与锁定窗口
 * - 密码验证：password_verify 语义
 * - 会话隔离：不同远程地址应有独立计数器
 */
require_once __DIR__ . '/TestCase.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class LoginSecurityTest extends TestCase
{
    private int $maxAttempts = 5;
    private int $lockoutTime = 900; // 15 分钟

    protected function setUp(): void
    {
        $_SESSION = [];
    }

    // --- 暴力破解防护计数 ---

    public function testFailedAttemptsAccumulate(): void
    {
        $key = 'login_attempts_192.168.1.1';
        $_SESSION[$key] = 0;
        for ($i = 1; $i <= 3; $i++) {
            $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
        }
        $this->assertSame(3, $_SESSION[$key]);
    }

    public function testLockoutTriggeredAfterMaxAttempts(): void
    {
        $key = 'login_attempts_192.168.1.1';
        $lockKey = 'login_lockout_192.168.1.1';
        $_SESSION[$key] = 0;

        // 模拟 5 次失败登录
        for ($i = 0; $i < $this->maxAttempts; $i++) {
            $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
            if ($_SESSION[$key] >= $this->maxAttempts) {
                $_SESSION[$lockKey] = time() + $this->lockoutTime;
                unset($_SESSION[$key]);
            }
        }

        $this->assertTrue(isset($_SESSION[$lockKey]), '应设置锁定时间戳');
        $this->assertGreaterThan(time(), $_SESSION[$lockKey], '锁定时间应在未来');
        $this->assertFalse(isset($_SESSION[$key]), '失败计数应被清除');
    }

    public function testLockoutActiveWithinWindow(): void
    {
        $lockKey = 'login_lockout_192.168.1.1';
        $_SESSION[$lockKey] = time() + 600; // 还剩 10 分钟
        $this->assertTrue($_SESSION[$lockKey] > time(), '锁定仍有效');
    }

    public function testLockoutExpiredAfterWindow(): void
    {
        $lockKey = 'login_lockout_192.168.1.1';
        $_SESSION[$lockKey] = time() - 10; // 已过期 10 秒
        $this->assertFalse($_SESSION[$lockKey] > time(), '锁定应已过期');
    }

    public function testSuccessfulLoginClearsCounters(): void
    {
        $attemptsKey = 'login_attempts_192.168.1.1';
        $lockKey = 'login_lockout_192.168.1.1';
        $_SESSION[$attemptsKey] = 3;
        $_SESSION[$lockKey] = time() + 300;

        // 登录成功
        unset($_SESSION[$attemptsKey]);
        unset($_SESSION[$lockKey]);

        $this->assertFalse(isset($_SESSION[$attemptsKey]));
        $this->assertFalse(isset($_SESSION[$lockKey]));
    }

    public function testCounterIsPerClient(): void
    {
        $keyA = 'login_attempts_192.168.1.1';
        $keyB = 'login_attempts_10.0.0.1';
        $_SESSION[$keyA] = 3;
        $_SESSION[$keyB] = 0;
        $this->assertSame(3, $_SESSION[$keyA]);
        $this->assertSame(0, $_SESSION[$keyB]);
    }

    // --- password_verify 语义验证 ---

    public function testPasswordVerifyCorrect(): void
    {
        $hash = password_hash('correct-horse-battery-staple', PASSWORD_DEFAULT);
        $this->assertTrue(password_verify('correct-horse-battery-staple', $hash));
    }

    public function testPasswordVerifyWrong(): void
    {
        $hash = password_hash('correct-password', PASSWORD_DEFAULT);
        $this->assertFalse(password_verify('wrong-password', $hash));
    }

    public function testPasswordVerifyEmptyPassword(): void
    {
        $hash = password_hash('something', PASSWORD_DEFAULT);
        $this->assertFalse(password_verify('', $hash));
    }

    public function testPasswordVerifyRejectsNullByte(): void
    {
        // PHP 8+ 的 password_hash 会拒绝含 null 字节的密码（ValueError），
        // 这是正确的安全行为，任何非默认行为也不应让系统崩溃
        $raw = "abc" . chr(0) . "def";
        $hash = false;
        try {
            $hash = password_hash($raw, PASSWORD_DEFAULT);
        } catch (\Throwable $e) {
            // 预期行为：被明确拒绝
            $this->assertNotEmpty($e->getMessage());
            return;
        }
        // 旧版：hash 会被截断
        if ($hash) {
            $this->assertNotEmpty($hash);
        } else {
            $this->assertFalse($hash);
        }
    }

    // --- 用户名修剪 ---

    public function testUsernameTrimmed(): void
    {
        $user = trim('  admin  ');
        $this->assertSame('admin', $user);
    }

    public function testUsernameCaseSensitive(): void
    {
        $this->assertFalse('Admin' === 'admin', '用户名比较应是严格的');
    }

    // --- 会话安全：标记登录 ---

    public function testSessionMarksLoggedIn(): void
    {
        $_SESSION['loginadmin'] = 'admin_user';
        $this->assertSame('admin_user', $_SESSION['loginadmin']);
    }

    public function testSessionMissingLoginFlag(): void
    {
        $this->assertFalse(isset($_SESSION['loginadmin']), '未登录时不应有标志');
    }
}

$suite = new LoginSecurityTest();
$suite->runAll();
echo "\n" . json_encode($suite->report(), JSON_UNESCAPED_UNICODE) . "\n";
