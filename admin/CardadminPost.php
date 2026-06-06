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
    $card1 = htmlspecialchars(trim($_POST['card1']), ENT_QUOTES);
    $card2 = htmlspecialchars(trim($_POST['card2']), ENT_QUOTES);
    $card3 = htmlspecialchars(trim($_POST['card3']), ENT_QUOTES);
    $deci1 = htmlspecialchars(trim($_POST['deci1']), ENT_QUOTES);
    $deci2 = htmlspecialchars(trim($_POST['deci2']), ENT_QUOTES);
    $deci3 = htmlspecialchars(trim($_POST['deci3']), ENT_QUOTES);
    $icp = htmlspecialchars(trim($_POST['icp']), ENT_QUOTES);
    $Copyright = htmlspecialchars(trim($_POST['Copyright']), ENT_QUOTES);
    $bgimg = htmlspecialchars(trim($_POST['bgimg']), ENT_QUOTES);

    $stmt = mysqli_prepare($connect, "update text set icp = ?, Copyright = ?, card1 = ?, card2 = ?, card3 = ?, deci1 = ?, deci2 = ?, deci3 = ?, bgimg = ?");
    mysqli_stmt_bind_param($stmt, "sssssssss", $icp, $Copyright, $card1, $card2, $card3, $deci1, $deci2, $deci3, $bgimg);
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

