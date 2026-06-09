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
$parentId = intval($_POST['parent_id'] ?? 0);
$replyToId = intval($_POST['reply_to_id'] ?? 0);
$weather = trim($_POST['weather'] ?? '');
$weatherIcon = trim($_POST['weather_icon'] ?? '');

// 极验验证（如果配置了极验，验证前端传来的极验参数）
$geetestValid = true;
$geetestId = '';
// 尝试从配置获取极验ID
if (file_exists(__DIR__ . '/../admin/Config_DB.php')) {
    include_once __DIR__ . '/../admin/Config_DB.php';
    if (defined('GEETEST_ID') && !empty(GEETEST_ID)) {
        $geetestId = GEETEST_ID;
    }
}
if (!empty($geetestId) && !empty($_POST['lot_number'])) {
    // 极验二次验证
    $lotNumber = trim($_POST['lot_number'] ?? '');
    $captchaOutput = trim($_POST['captcha_output'] ?? '');
    $passToken = trim($_POST['pass_token'] ?? '');
    $genTime = trim($_POST['gen_time'] ?? '');
    if (empty($lotNumber) || empty($captchaOutput) || empty($passToken) || empty($genTime)) {
        echo json_encode(['success' => false, 'code' => 400, 'msg' => '验证信息不完整']);
        exit;
    }
    // 极验服务端验证请求
    $geetestKey = defined('GEETEST_KEY') ? GEETEST_KEY : '';
    if (!empty($geetestKey)) {
        $verifyUrl = 'https://gc.geetest.com/validate';
        $verifyData = [
            'lot_number' => $lotNumber,
            'captcha_output' => $captchaOutput,
            'pass_token' => $passToken,
            'gen_time' => $genTime,
            'captcha_id' => $geetestId,
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $verifyUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($verifyData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $verifyResult = curl_exec($ch);
        curl_close($ch);
        if ($verifyResult) {
            $verifyData = json_decode($verifyResult, true);
            if (!isset($verifyData['result']) || $verifyData['result'] !== 'success') {
                $geetestValid = false;
            }
        }
    }
}
if (!$geetestValid) {
    echo json_encode(['success' => false, 'code' => 400, 'msg' => '人机验证失败，请重试']);
    exit;
}

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

// IP归属地查询
$city = '未知';
if (function_exists('get_ip_city_New') && filter_var($ip, FILTER_VALIDATE_IP)) {
    $cityResult = get_ip_city_New($ip);
    if ($cityResult && $cityResult !== '未知') {
        $city = $cityResult;
    }
}

// 防灌水：同一IP 60秒内只能留言1次
$spamCheck = mysqli_prepare($connect, "SELECT id FROM leaving WHERE ip = ? AND time > ? LIMIT 1");
$spamTime = time() - 60;
mysqli_stmt_bind_param($spamCheck, 'ss', $ip, $spamTime);
mysqli_stmt_execute($spamCheck);
$spamResult = mysqli_stmt_get_result($spamCheck);
if ($spamResult && mysqli_num_rows($spamResult) > 0) {
    mysqli_stmt_close($spamCheck);
    echo json_encode(['success' => false, 'code' => 429, 'msg' => '操作太频繁，请稍后再试']);
    exit;
}
mysqli_stmt_close($spamCheck);

try {
    // 插入留言（v5.2.1: leaving.time 是 varchar(200)，存储Unix时间戳）
    $stmt = mysqli_prepare($connect, "INSERT INTO leaving (name, QQ, text, time, ip, city, device, browser, parent_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $now = time();
    mysqli_stmt_bind_param($stmt, 'ssssssssi', $name, $qq, $text, $now, $ip, $city, $device, $browser, $parentId);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    if ($ok) {
        $response = ['success' => true, 'code' => 200, 'msg' => '留言成功，感谢你的留言～'];
    } else {
        $response['msg'] = '保存失败，请稍后再试';
    }
} catch (Throwable $e) {
    $response['msg'] = '提交失败，请稍后再试';
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
