<?php
session_start();

include_once 'Function.php';

if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    echo '<script>alert("CSRF验证失败，请重试");history.back();</script>';
    exit;
}

$name = htmlspecialchars(trim($_POST['eventname']),ENT_QUOTES);
$file = $_SERVER['PHP_SELF'];
if ($_POST['img'] === '0') {
    $img = 0;
} else {
    $img = htmlspecialchars($_POST['img'],ENT_QUOTES);
}
if ($_POST['icon'] == 1) {
    $icon = 1;
} else {
    $icon = 0;
}

include_once 'Database.php';

if (isset($_SESSION['loginadmin']) && $_SESSION['loginadmin'] <> '') {
    // 插入 lovelist 表（v5.2.1 新表结构，含 is_done, note, location 等字段）
    $charu = "INSERT INTO lovelist (eventname, icon, imgurl, is_done, note, location, date) VALUES (?, ?, ?, 0, '', '', NOW())";
    $stmt = $conn->prepare($charu);
    $stmt->bind_param("sss", $name, $icon, $img);
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
