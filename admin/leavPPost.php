<?php
session_start();

$file = $_SERVER['PHP_SELF'];

include_once 'connect.php';
include_once 'Function.php';

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    echo '<script>alert("CSRF验证失败，请重试");history.back();</script>';
    exit;
}

if (isset($_SESSION['loginadmin']) && $_SESSION['loginadmin'] <> '') {
    $jiequ = trim($_POST['jiequ']);
    if (!is_numeric($jiequ)) {
        echo "0";
        exit;
    }
    $lanjie = htmlspecialchars(trim($_POST['lanjie'] ?? ''), ENT_QUOTES, 'UTF-8');
    $lanjiezf = htmlspecialchars(trim($_POST['lanjiezf']), ENT_QUOTES, 'UTF-8');
    
    $stmt = mysqli_prepare($connect, "update leavSet set jiequ = ?, lanjie = ?, lanjiezf = ?");
    mysqli_stmt_bind_param($stmt, "sss", $jiequ, $lanjie, $lanjiezf);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_affected_rows($stmt) >= 0;
    mysqli_stmt_close($stmt);
    if ($result) {
        echo "1";
    } else {
        echo "0";
    }
} else {
    echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route=$file';</script>";
}
