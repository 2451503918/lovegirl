<?php
session_start();
include_once 'Function.php';
include_once 'connect.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<script>alert("非法请求");history.back();</script>';
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    echo '<script>alert("CSRF验证失败");history.back();</script>';
    exit;
}

$file = $_SERVER['PHP_SELF'];

if (isset($_SESSION['loginadmin']) && $_SESSION['loginadmin'] <> '') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    if ($id > 0) {
        // v5.2.1: 从 little 表删除（预处理语句防SQL注入）
        $stmt = mysqli_prepare($connect, "DELETE FROM little WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_affected_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        if ($result) {
            echo "<script>alert('删除文章成功');location.href = 'littleSet.php';</script>";
        } else {
            echo "<script>alert('删除文章失败');history.back();</script>";
        }
    } else {
        echo "<script>alert('参数错误');history.back();</script>";
    }

} else {
    echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route=$file';</script>";
}

