<?php
session_start();
$file = $_SERVER['PHP_SELF'];
include_once 'connect.php';
include_once 'Function.php';

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    echo '<script>alert("CSRF验证失败，请重试");history.back();</script>';
    exit;
}

if (!isset($_SESSION['loginadmin']) || $_SESSION['loginadmin'] === '') {
    echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route=$file';</script>";
    exit;
}

$articlename = htmlspecialchars(trim($_POST['articlename'] ?? ''), ENT_QUOTES, 'UTF-8');
$articletitle = htmlspecialchars(trim($_POST['articletitle'] ?? ''), ENT_QUOTES, 'UTF-8');
$articletext = trim($_POST['articletext'] ?? '');

if (empty($articletitle) || empty($articletext)) {
    echo "0";
    exit;
}

$stmt = mysqli_prepare($connect, "INSERT INTO little (title, text, author, date) VALUES (?, ?, ?, NOW())");
mysqli_stmt_bind_param($stmt, 'sss', $articletitle, $articletext, $articlename);
$result = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo $result ? "1" : "0";
