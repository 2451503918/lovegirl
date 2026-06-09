<?php
session_start();
include_once 'Function.php';

if (!isset($_SESSION['loginadmin']) || $_SESSION['loginadmin'] === '') {
    die("<script>alert('非法操作');history.back();</script>");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<script>alert('非法请求');history.back();</script>");
}

$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($csrf_token)) {
    die("<script>alert('CSRF验证失败');history.back();</script>");
}

include_once 'connect.php';

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    die("<script>alert('参数错误');history.back();</script>");
}

$stmt = mysqli_prepare($connect, "DELETE FROM music WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

if ($ok) {
    echo "<script>alert('删除成功');location.href='musicSet.php';</script>";
} else {
    echo "<script>alert('删除失败');history.back();</script>";
}
