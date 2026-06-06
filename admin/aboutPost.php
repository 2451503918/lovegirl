<?php
session_start();
$file = $_SERVER['PHP_SELF'];
include_once 'Database.php';
include_once 'Function.php';

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    echo '<script>alert("CSRF验证失败，请重试");history.back();</script>';
    exit;
}

if (isset($_SESSION['loginadmin']) && $_SESSION['loginadmin'] <> '') {

    $id = 1;
    $title = trim($_POST['title']);
    $aboutimg = trim($_POST['aboutimg']);
    $info1 = trim($_POST['info1']);
    $info2 = trim($_POST['info2']);
    $info3 = trim($_POST['info3']);
    $btn1 = trim($_POST['btn1']);
    $btn2 = trim($_POST['btn2']);
    $infox1 = trim($_POST['infox1']);
    $infox2 = trim($_POST['infox2']);
    $infox3 = trim($_POST['infox3']);
    $infox4 = trim($_POST['infox4']);
    $infox5 = trim($_POST['infox5']);
    $infox6 = trim($_POST['infox6']);
    $btnx2 = trim($_POST['btnx2']);
    $infof1 = trim($_POST['infof1']);
    $infof2 = trim($_POST['infof2']);
    $infof3 = trim($_POST['infof3']);
    $infof4 = trim($_POST['infof4']);
    $btnf3 = trim($_POST['btnf3']);
    $infod1 = trim($_POST['infod1']);
    $infod2 = trim($_POST['infod2']);
    $infod3 = trim($_POST['infod3']);
    $infod4 = trim($_POST['infod4']);
    $infod5 = trim($_POST['infod5']);
    
    $sql = "update about set title = ?,aboutimg = ?,info1 = ?,info2 = ?,info3 = ?,btn1 = ?,btn2 = ?,infox1 = ?,infox2 = ?,infox3 = ?,infox4 = ?,infox5 = ?,infox6 = ?,btnx2 = ?,infof1 = ?,infof2 = ?,infof3 = ?,infof4 = ?,btnf3 = ?,infod1 = ?,infod2 = ?,infod3 = ?,infod4 = ?,infod5 = ? where id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssssssssssssssi", $title, $aboutimg, $info1, $info2, $info3, $btn1, $btn2, $infox1, $infox2, $infox3, $infox4, $infox5, $infox6, $btnx2, $infof1, $infof2, $infof3, $infof4, $btnf3, $infod1, $infod2, $infod3, $infod4, $infod5, $id);
    $result = $stmt->execute();
    
    if ($result) {
        echo "1";
    } else {
        echo "0";
    }
} else {
    echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route=$file';</script>";
}

