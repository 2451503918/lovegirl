<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
header("Content-Type:text/html; charset=utf8");
include_once __DIR__.'/Config_DB.php';
try {
    $connect = @mysqli_connect($db_address,$db_username,$db_password,$db_name, 3306, $db_socket ?? null);
} catch (Exception $e) {
    $connect = false;
}
$LikeGirl_Code = $Like_Code;
if (!$connect) {
    // 开发环境降级：返回空连接，不阻断页面渲染
    $connect = null;
    if (isset($_SERVER['HTTP_HOST'])) {
        // 生产环境仍跳转错误页
        if (strpos($_SERVER['HTTP_HOST'], 'localhost') === false && strpos($_SERVER['HTTP_HOST'], '127.0.0.1') === false) {
            die("<script>location.href = '../admin/connectDie.php';</script>");
        }
    }
}
if ($connect) {
    $connect->set_charset("utf8mb4");
}
// 兼容：同时设置 $conn 供使用 OOP mysqli 的文件引用
$conn = $connect;