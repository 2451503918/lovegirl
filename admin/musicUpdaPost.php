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
$music_name = trim($_POST['music_name'] ?? '');
$music_artist = trim($_POST['music_artist'] ?? '');
$music_url = trim($_POST['music_url'] ?? '');
$music_cover = trim($_POST['music_cover'] ?? '');
$music_lrc = trim($_POST['music_lrc'] ?? '');

if ($id <= 0 || empty($music_name) || empty($music_artist) || empty($music_url)) {
    echo "0";
    exit;
}

$music_name = htmlspecialchars($music_name, ENT_QUOTES, 'UTF-8');
$music_artist = htmlspecialchars($music_artist, ENT_QUOTES, 'UTF-8');
$music_url = htmlspecialchars($music_url, ENT_QUOTES, 'UTF-8');
$music_cover = htmlspecialchars($music_cover, ENT_QUOTES, 'UTF-8');
$music_lrc = htmlspecialchars($music_lrc, ENT_QUOTES, 'UTF-8');

$stmt = mysqli_prepare($connect, "UPDATE music SET music_name=?, music_artist=?, music_url=?, music_cover=?, music_lrc=? WHERE id=?");
mysqli_stmt_bind_param($stmt, 'sssssi', $music_name, $music_artist, $music_url, $music_cover, $music_lrc, $id);
$ok = mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

echo $ok ? "1" : "0";
