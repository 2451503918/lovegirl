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
        $sql = "delete from lovelist where id = $id";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            echo "<script>alert('删除事件成功');location.href = '/admin/lovelist.php';</script>";
        } else {
            echo "<script>alert('删除内容失败');history.back();</script>";
        }
    } else {
        echo "<script>alert('参数错误');history.back();</script>";
    }
} else {
    echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route=$file';</script>";
}

