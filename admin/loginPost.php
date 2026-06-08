<?php
session_start();
@($user = $_POST['adminName']);
@($pw = $_POST['pw']);
include_once "Database.php";

// 暴力破解防护：5次失败后锁定15分钟
$maxAttempts = 5;
$lockoutTime = 900; // 15 minutes
$attemptsKey = 'login_attempts_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
$lockoutKey = 'login_lockout_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');

if (isset($_SESSION[$lockoutKey]) && $_SESSION[$lockoutKey] > time()) {
    $remaining = $_SESSION[$lockoutKey] - time();
    die("<script>alert('登录尝试过多，请" . ceil($remaining / 60) . "分钟后再试');history.back();</script>");
}

$login_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "SELECT id, user, pw FROM login WHERE user = ?";
    $stmt = $conn->prepare($sql);
    $USER = trim($user);
    $stmt->bind_param("s", $USER);
    $stmt->bind_result($id, $Login_user, $Login_pw);
    $result = $stmt->execute();
    if (!$result) {
        error_log("loginPost.php query error: " . $stmt->error);
    }
    $stmt->fetch();
    
    if ($USER === $Login_user && password_verify($pw, $Login_pw)) {
        $login_success = true;
        // 登录成功，清除失败计数
        unset($_SESSION[$attemptsKey]);
        unset($_SESSION[$lockoutKey]);
    } else {
        // 登录失败，增加计数
        $_SESSION[$attemptsKey] = ($_SESSION[$attemptsKey] ?? 0) + 1;
        if ($_SESSION[$attemptsKey] >= $maxAttempts) {
            $_SESSION[$lockoutKey] = time() + $lockoutTime;
            unset($_SESSION[$attemptsKey]);
        }
    }
}

if ($login_success) {
    $_SESSION['loginadmin'] = $USER;
    echo "<script>alert('登录成功 欢迎进入小站后台管理页面！');location.href = '../admin/index.php';</script>";
} else {
    die("<script>alert('登录失败，请检查用户名和密码');history.back();</script>");
}

