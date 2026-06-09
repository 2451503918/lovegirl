<?php

include_once 'admin/connect.php';

$ip = $_SERVER["REMOTE_ADDR"] ?? '127.0.0.1';
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    return;
}
if (!$connect) {
    // 数据库不可用时跳过IP检查
    return;
}
$ipcheck = "SELECT 1 FROM IPerror WHERE State = ? LIMIT 1";
$ipstmt = mysqli_prepare($connect, $ipcheck);
if ($ipstmt) {
    mysqli_stmt_bind_param($ipstmt, "s", $ip);
    mysqli_stmt_execute($ipstmt);
    mysqli_stmt_store_result($ipstmt);
    if (mysqli_stmt_num_rows($ipstmt) > 0) {
        $safe_ip = htmlspecialchars($ip, ENT_QUOTES, 'UTF-8');
        die("<script>alert('你的IP(" . $safe_ip . ")已被封禁，禁止访问本页面');location.href = 'error.php';</script>");
    }
    mysqli_stmt_close($ipstmt);
}
