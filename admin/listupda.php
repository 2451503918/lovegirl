<?php
session_start();

include_once 'Function.php';

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    echo '<script>alert("CSRF验证失败，请重试");history.back();</script>';
    exit;
}

$name = htmlspecialchars(trim($_POST['eventname']), ENT_QUOTES, 'UTF-8');
$icon = intval($_POST['icon'] ?? 0);
$id = intval($_POST['id'] ?? 0);
$img = htmlspecialchars(trim($_POST['imgurl'] ?? ''), ENT_QUOTES, 'UTF-8');
$file = $_SERVER['PHP_SELF'];
include_once 'connect.php';
if (empty($img)) {
    $img = '0';
}

if (isset($_SESSION['loginadmin']) && $_SESSION['loginadmin'] <> '') {
    $stmt = mysqli_prepare($connect, "update lovelist set eventname = ?, icon = ?, imgurl = ? where id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $name, $icon, $img, $id);
    mysqli_stmt_execute($stmt);
    $reslove = mysqli_stmt_affected_rows($stmt) >= 0;
    mysqli_stmt_close($stmt);
    if ($reslove) {
        echo "1";
    } else {
        echo "0";
    }
} else {
    echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route=$file';</script>";
}
