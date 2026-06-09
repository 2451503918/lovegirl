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
    $adminName = htmlspecialchars(trim($_POST['adminName']), ENT_QUOTES, 'UTF-8');
    $icp = htmlspecialchars(trim($_POST['icp']), ENT_QUOTES, 'UTF-8');
    $Copyright = htmlspecialchars(trim($_POST['Copyright']), ENT_QUOTES, 'UTF-8');

    $stmt = mysqli_prepare($connect, "update text set user = ?, icp = ?, Copyright = ?");
    mysqli_stmt_bind_param($stmt, "sss", $adminName, $icp, $Copyright);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_affected_rows($stmt) >= 0;
    mysqli_stmt_close($stmt);
    if ($result) {
        echo "<script>alert('更新成功');location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('更新失败');history.back();</script>";
    }
} else {
    echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route=$file';</script>";
}
