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
    $id = intval($_POST['id']);
    $imgText = htmlspecialchars(trim($_POST['imgText']), ENT_QUOTES);
    $imgDatd = trim($_POST['imgDatd']);
    $imgUrl = htmlspecialchars(trim($_POST['imgUrl']), ENT_QUOTES);
    
    // v5.2.1: 更新 photo 表
    $stmt = mysqli_prepare($connect, "UPDATE photo SET title = ?, `desc` = ?, img = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "sssi", $imgText, $imgDatd, $imgUrl, $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_affected_rows($stmt) >= 0;
    mysqli_stmt_close($stmt);
    if ($result) {
        echo "1";
    } else {
        echo "0";
    }
} else {
    echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route=$file';</script>";
}

