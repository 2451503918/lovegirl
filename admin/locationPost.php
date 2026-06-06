<?php
/**
 * 位置配置处理脚本
 */

session_start();
include_once 'Nav.php';
include_once 'Function.php';

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    echo '<script>alert("CSRF验证失败，请重试");history.back();</script>';
    exit;
}

// 检查是否是POST请求
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '<script>alert("非法访问");location.href="Set.php";</script>';
    exit;
}

// 获取表单数据
$boyCity = isset($_POST['boyCity']) ? trim($_POST['boyCity']) : '北京';
$girlCity = isset($_POST['girlCity']) ? trim($_POST['girlCity']) : '上海';
$boyLat = isset($_POST['boyLat']) ? floatval($_POST['boyLat']) : 39.9042;
$boyLng = isset($_POST['boyLng']) ? floatval($_POST['boyLng']) : 116.4074;
$girlLat = isset($_POST['girlLat']) ? floatval($_POST['girlLat']) : 31.2304;
$girlLng = isset($_POST['girlLng']) ? floatval($_POST['girlLng']) : 121.4737;

// 验证数据
if (empty($boyCity) || empty($girlCity)) {
    echo '<script>alert("城市名称不能为空");history.back();</script>';
    exit;
}

if ($boyLat < -90 || $boyLat > 90 || $girlLat < -90 || $girlLat > 90) {
    echo '<script>alert("纬度必须在-90到90之间");history.back();</script>';
    exit;
}

if ($boyLng < -180 || $boyLng > 180 || $girlLng < -180 || $girlLng > 180) {
    echo '<script>alert("经度必须在-180到180之间");history.back();</script>';
    exit;
}

// 更新数据库
$sql = "UPDATE text SET 
    boyCity = '" . mysqli_real_escape_string($connect, $boyCity) . "',
    girlCity = '" . mysqli_real_escape_string($connect, $girlCity) . "',
    boyLat = $boyLat,
    boyLng = $boyLng,
    girlLat = $girlLat,
    girlLng = $girlLng
    WHERE id = 1";

$result = mysqli_query($connect, $sql);

if ($result) {
    echo '<script>alert("位置配置更新成功");location.href="Set.php";</script>';
} else {
    echo '<script>alert("位置配置更新失败，请重试");history.back();</script>';
}
?>