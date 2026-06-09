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

$boyCity = htmlspecialchars(trim($_POST['boyCity'] ?? ''), ENT_QUOTES, 'UTF-8');
$girlCity = htmlspecialchars(trim($_POST['girlCity'] ?? ''), ENT_QUOTES, 'UTF-8');
$boyLat = floatval($_POST['boyLat'] ?? 0);
$boyLng = floatval($_POST['boyLng'] ?? 0);
$girlLat = floatval($_POST['girlLat'] ?? 0);
$girlLng = floatval($_POST['girlLng'] ?? 0);

$stmt = mysqli_prepare($connect, "UPDATE text SET boyCity=?, girlCity=?, boyLat=?, boyLng=?, girlLat=?, girlLng=? WHERE id=1");
mysqli_stmt_bind_param($stmt, 'ssdddd', $boyCity, $girlCity, $boyLat, $boyLng, $girlLat, $girlLng);
$result = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo $result ? "1" : "0";
