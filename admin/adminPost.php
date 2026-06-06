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
    $title = htmlspecialchars(trim($_POST['title']), ENT_QUOTES);
    $logo = htmlspecialchars(trim($_POST['logo']), ENT_QUOTES);
    $writing = htmlspecialchars(trim($_POST['writing']), ENT_QUOTES);
    $WebPjax = trim($_POST['WebPjax']);
    $WebBlur = trim($_POST['WebBlur']);

    $stmt = mysqli_prepare($connect, "update text set title = ?, logo = ?, writing = ? where id = '1'");
    mysqli_stmt_bind_param($stmt, "sss", $title, $logo, $writing);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_affected_rows($stmt) >= 0;
    mysqli_stmt_close($stmt);
    if ($result) {
        echo "1";
    } else {
        echo "0";
    }

    $stmt = mysqli_prepare($connect, "update diySet set Pjaxkg = ?, Blurkg = ? where id = '1'");
    mysqli_stmt_bind_param($stmt, "ss", $WebPjax, $WebBlur);
    mysqli_stmt_execute($stmt);
    $diyresult = mysqli_stmt_affected_rows($stmt) >= 0;
    mysqli_stmt_close($stmt);
    if ($diyresult) {
        echo "3";
    } else {
        echo "4";
    }
} else {
    echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route=$file';</script>";
}
