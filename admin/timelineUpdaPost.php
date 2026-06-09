<?php
session_start();
include_once 'Function.php';

if (!isset($_SESSION['loginadmin']) || $_SESSION['loginadmin'] === '') {
    die("<script>alert('非法操作');history.back();</script>");
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("<script>alert('非法请求');history.back();</script>");
}
$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCSRFToken($csrf_token)) {
    die("<script>alert('CSRF验证失败');history.back();</script>");
}

include_once 'connect.php';

$id = intval($_POST['id'] ?? 0);
$type = trim($_POST['type'] ?? 'other');
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');
$date = trim($_POST['date'] ?? date('Y-m-d'));
$location = trim($_POST['location'] ?? '');

if ($id <= 0 || empty($title) || empty($content)) {
    echo "0"; exit;
}

$allowedTypes = ['love','travel','life','work','study','other'];
if (!in_array($type, $allowedTypes)) $type = 'other';

$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
$content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
$location = htmlspecialchars($location, ENT_QUOTES, 'UTF-8');
$date = htmlspecialchars($date, ENT_QUOTES, 'UTF-8');

$stmt = mysqli_prepare($connect, "UPDATE timeline SET type=?, title=?, content=?, date=?, location=? WHERE id=?");
mysqli_stmt_bind_param($stmt, 'sssssi', $type, $title, $content, $date, $location, $id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo $ok ? "1" : "0";
