<?php
/**
 * 访问信标服务
 * 接收前端访问时长追踪信标
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// 接收但不强制要求参数
$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;

if ($duration > 0) {
    // 记录访问时长（简单实现）
    @session_start();
    $_SESSION['last_beacon_duration'] = $duration;
}

http_response_code(204); // No Content - 信标不需要响应体
