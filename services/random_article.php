<?php
/**
 * 随机文章 API
 * 返回一篇文章的 id（用于跳转到详情页）
 */
header('Content-Type: application/json; charset=utf-8');
$allowedOrigins = ['lovedemo.54oimx.top', 'love.54oimx.top', 'localhost', '127.0.0.1'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$originHost = $origin ? parse_url($origin, PHP_URL_HOST) : '';
if (in_array($originHost, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

error_reporting(0);
include_once __DIR__ . '/../admin/connect.php';

$response = ['success' => false, 'code' => 200, 'msg' => '暂无可用文章', 'id' => 0];

if (!$connect) {
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = mysqli_prepare($connect, "SELECT id, title, text FROM little WHERE encrypted = 0 ORDER BY RAND() LIMIT 1");
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    if ($r && $row = mysqli_fetch_assoc($r)) {
        $response = [
            'success' => true,
            'code' => 200,
            'id' => intval($row['id']),
            'title' => $row['title'] ?? '',
        ];
    }
    mysqli_stmt_close($stmt);
} catch (Throwable $e) {
    // 静默
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
