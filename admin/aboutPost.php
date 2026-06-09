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

$title = htmlspecialchars(trim($_POST['title'] ?? ''), ENT_QUOTES, 'UTF-8');
$aboutimg = htmlspecialchars(trim($_POST['aboutimg'] ?? ''), ENT_QUOTES, 'UTF-8');
$info1 = htmlspecialchars(trim($_POST['info1'] ?? ''), ENT_QUOTES, 'UTF-8');
$info2 = htmlspecialchars(trim($_POST['info2'] ?? ''), ENT_QUOTES, 'UTF-8');
$info3 = htmlspecialchars(trim($_POST['info3'] ?? ''), ENT_QUOTES, 'UTF-8');
$btn1 = htmlspecialchars(trim($_POST['btn1'] ?? ''), ENT_QUOTES, 'UTF-8');
$btn2 = htmlspecialchars(trim($_POST['btn2'] ?? ''), ENT_QUOTES, 'UTF-8');
$infox1 = htmlspecialchars(trim($_POST['infox1'] ?? ''), ENT_QUOTES, 'UTF-8');
$infox2 = htmlspecialchars(trim($_POST['infox2'] ?? ''), ENT_QUOTES, 'UTF-8');
$infox3 = htmlspecialchars(trim($_POST['infox3'] ?? ''), ENT_QUOTES, 'UTF-8');
$infox4 = htmlspecialchars(trim($_POST['infox4'] ?? ''), ENT_QUOTES, 'UTF-8');
$infox5 = htmlspecialchars(trim($_POST['infox5'] ?? ''), ENT_QUOTES, 'UTF-8');
$infox6 = htmlspecialchars(trim($_POST['infox6'] ?? ''), ENT_QUOTES, 'UTF-8');
$btnx2 = htmlspecialchars(trim($_POST['btnx2'] ?? ''), ENT_QUOTES, 'UTF-8');
$infof1 = htmlspecialchars(trim($_POST['infof1'] ?? ''), ENT_QUOTES, 'UTF-8');
$infof2 = htmlspecialchars(trim($_POST['infof2'] ?? ''), ENT_QUOTES, 'UTF-8');
$infof3 = htmlspecialchars(trim($_POST['infof3'] ?? ''), ENT_QUOTES, 'UTF-8');
$infof4 = htmlspecialchars(trim($_POST['infof4'] ?? ''), ENT_QUOTES, 'UTF-8');
$btnf3 = htmlspecialchars(trim($_POST['btnf3'] ?? ''), ENT_QUOTES, 'UTF-8');
$infod1 = htmlspecialchars(trim($_POST['infod1'] ?? ''), ENT_QUOTES, 'UTF-8');
$infod2 = htmlspecialchars(trim($_POST['infod2'] ?? ''), ENT_QUOTES, 'UTF-8');
$infod3 = htmlspecialchars(trim($_POST['infod3'] ?? ''), ENT_QUOTES, 'UTF-8');
$infod4 = htmlspecialchars(trim($_POST['infod4'] ?? ''), ENT_QUOTES, 'UTF-8');
$infod5 = htmlspecialchars(trim($_POST['infod5'] ?? ''), ENT_QUOTES, 'UTF-8');

$stmt = mysqli_prepare($connect, "UPDATE about SET title=?, aboutimg=?, info1=?, info2=?, info3=?, btn1=?, btn2=?, infox1=?, infox2=?, infox3=?, infox4=?, infox5=?, infox6=?, btnx2=?, infof1=?, infof2=?, infof3=?, infof4=?, btnf3=?, infod1=?, infod2=?, infod3=?, infod4=?, infod5=? WHERE id=1");
mysqli_stmt_bind_param($stmt, 'ssssssssssssssssssssssss',
    $title, $aboutimg, $info1, $info2, $info3, $btn1, $btn2,
    $infox1, $infox2, $infox3, $infox4, $infox5, $infox6, $btnx2,
    $infof1, $infof2, $infof3, $infof4, $btnf3,
    $infod1, $infod2, $infod3, $infod4, $infod5
);
$result = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo $result ? "1" : "0";
