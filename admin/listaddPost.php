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

$eventname = htmlspecialchars(trim($_POST['eventname'] ?? ''), ENT_QUOTES, 'UTF-8');
$icon = intval($_POST['icon'] ?? 0);
$img = htmlspecialchars(trim($_POST['img'] ?? '0'), ENT_QUOTES, 'UTF-8');

if (empty($eventname)) {
    echo "0";
    exit;
}

$isDone = $icon >= 1 ? 1 : 0;

$stmt = mysqli_prepare($connect, "INSERT INTO lovelist (icon, is_done, eventname, imgurl, note, location, lng, lat, date) VALUES (?, ?, ?, ?, '', '', 0, 0, NOW())");
mysqli_stmt_bind_param($stmt, 'iiss', $icon, $isDone, $eventname, $img);
$result = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo $result ? "1" : "0";
