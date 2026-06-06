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
    $ip = trim($_POST['ipdz']);
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        echo "0";
        exit;
    }
    $bz = trim($_POST['bz']);
    $time = gmdate("Y-m-d H:i:s", time() + 8 * 3600);
    $ipgsd = get_ip_city_New($ip);

    $stmt = mysqli_prepare($connect, "insert into IPerror (ipAdd, Time, State, text) values (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $ipgsd, $time, $ip, $bz);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_affected_rows($stmt) > 0;
    mysqli_stmt_close($stmt);
    if ($result) {
        echo "1";
    } else {
        echo "0";
    }
} else {
    echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route=$file';</script>";
}
