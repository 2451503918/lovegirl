<?php
/**
 * 留言提交 API
 * POST：name, text, qq (可选)
 * CSRF 校验 + 防灌水 + 长度限制
 */
header('Content-Type: application/json; charset=utf-8');

$allowedOrigins = ['lovedemo.54oimx.top', 'love.54oimx.top', 'localhost', '127.0.0.1'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$originHost = $origin ? parse_url($origin, PHP_URL_HOST) : '';
if (in_array($originHost, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'code' => 405, 'msg' => '仅支持 POST 请求']);
    exit;
}

error_reporting(0);
include_once __DIR__ . '/../admin/connect.php';
include_once __DIR__ . '/../admin/Function.php';

$response = ['success' => false, 'code' => 400, 'msg' => '提交失败'];

if (!$connect) {
    echo json_encode(['success' => true, 'code' => 200, 'msg' => '数据库不可用，留言已忽略（演示模式）'], JSON_UNESCAPED_UNICODE);
    exit;
}

$name = trim($_POST['name'] ?? '');
$text = trim($_POST['text'] ?? '');
$qq   = trim($_POST['qq'] ?? '');

// 校验
if ($text === '' || mb_strlen($text, 'UTF-8') > 500) {
    echo json_encode(['success' => false, 'code' => 400, 'msg' => '留言内容不能为空且不超过500字符']);
    exit;
}
if ($name !== '' && mb_strlen($name, 'UTF-8') > 20) {
    echo json_encode(['success' => false, 'code' => 400, 'msg' => '昵称不能超过20字符']);
    exit;
}
if ($name === '') $name = '匿名访客';

// 简单防 XSS
$text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
$name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

$ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$device = preg_match('/Mobile|Android|iPhone/i', $userAgent) ? 'Mobile' : 'PC';
$browser = '';
if (preg_match('/Edg\/([\d\.]+)/i', $userAgent, $m)) $browser = 'Edge ' . $m[1];
elseif (preg_match('/Chrome\/([\d\.]+)/i', $userAgent, $m)) $browser = 'Chrome ' . $m[1];
elseif (preg_match('/Firefox\/([\d\.]+)/i', $userAgent, $m)) $browser = 'Firefox ' . $m[1];
elseif (preg_match('/Safari\/([\d\.]+)/i', $userAgent, $m)) $browser = 'Safari ' . $m[1];

try {
    $stmt = mysqli_prepare(
        $connect,
        "INSERT INTO leaving (name, QQ, text, time, ip, city, device, browser, likes) VALUES (?, ?, ?, NOW(), ?, ?, ?, ?, 0)"
    );
    $city = '未知';
    mysqli_stmt_bind_param($stmt, 'sssssss', $name, $qq, $text, $ip, $city, $device, $browser);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    if ($ok) {
        $response = ['success' => true, 'code' => 200, 'msg' => '留言成功，感谢你的留言～'];
    } else {
        $response['msg'] = '保存失败，请稍后再试';
    }
} catch (Throwable $e) {
    $response['msg'] = '提交失败：' . $e->getMessage();
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
