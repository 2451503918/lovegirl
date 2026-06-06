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
    $title = htmlspecialchars(trim($_POST['articletitle']), ENT_QUOTES);
    $text = trim($_POST['articletext']);
    $name = trim($_POST['articlename']);
    $time = gmdate("Y-m-d", time() + 8 * 3600);
    
    // 插入 little 表（v5.2.1 新表结构）
    $charu = "INSERT INTO little (title, text, author, date) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($charu);
    $datetime = $time . ' ' . date('H:i:s');
    $stmt->bind_param("ssss", $title, $text, $name, $datetime);
    $result = $stmt->execute();
    $stmt->close();
    
    if ($result) {
        echo "1";
    } else {
        echo "0";
    }
} else {
    echo "<script>alert('非法操作，行为已记录');location.href = 'warning.php?route=$file';</script>";
}
