<?php
session_start();
@($user = $_POST['adminName']);
@($pw = $_POST['pw']);
include_once "Database.php";

$login_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "select * from login where user =?";
    $stmt = $conn->prepare($sql);
    $USER = trim($user);
    $stmt->bind_param("s", $USER);
    $PW = md5($pw);
    $stmt->bind_result($id, $Login_user, $Login_pw);
    $result = $stmt->execute();
    if (!$result) {
        error_log("loginPost.php query error: " . $stmt->error);
    }
    $stmt->fetch();
    
    if ($USER === $Login_user && $PW === $Login_pw) {
        $login_success = true;
    }
}

if ($login_success) {
    $_SESSION['loginadmin'] = $USER;
    echo "<script>alert('登录成功 欢迎进入小站后台管理页面！');location.href = '../admin/index.php';</script>";
} else {
    die("<script>alert('登录失败，用户名或密码错误！！！');history.back();</script>");
}

