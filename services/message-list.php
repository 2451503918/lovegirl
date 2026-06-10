<?php
/**
 * 留言列表 API
 * 分页获取留言数据，支持匿名头像
 * 兼容前端 page-messages.js 的 AJAX 调用格式
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

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

error_reporting(0);

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// 前端使用 offset/limit 分页
$limit = min(50, max(5, intval($_GET['limit'] ?? 20)));
$offset = max(0, intval($_GET['offset'] ?? 0));

$response = ['code' => 200, 'msg' => '查询成功', 'data' => ['items' => [], 'pagination' => ['total' => 0, 'has_more' => false]]];

include_once __DIR__ . '/../admin/connect.php';

if (!$connect) {
    // 数据库未连接时返回示例数据
    $response['data']['items'] = [];
    $response['data']['pagination'] = ['total' => 0, 'has_more' => false];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $stmt = mysqli_prepare($connect, "SELECT COUNT(*) AS total FROM leaving");
    mysqli_stmt_execute($stmt);
    $total = 0;
    $r = mysqli_stmt_get_result($stmt);
    if ($r) { $row = mysqli_fetch_assoc($r); $total = intval($row['total'] ?? 0); }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($connect, "SELECT id, name, QQ, text, time, ip, city, device, browser, likes, parent_id FROM leaving ORDER BY id DESC LIMIT ? OFFSET ?");
    mysqli_stmt_bind_param($stmt, 'ii', $limit, $offset);
    mysqli_stmt_execute($stmt);
    $r = mysqli_stmt_get_result($stmt);
    if ($r) {
        while ($row = mysqli_fetch_assoc($r)) {
            $qqRaw = $row['QQ'] ?? '';
            $qqHash = '';
            if (!empty($qqRaw)) {
                $qqHash = md5(strtolower(trim($qqRaw)));
            }
            $avatarUrl = '';
            if (!empty($qqRaw)) {
                $avatarUrl = 'https://weavatar.com/avatar/' . $qqHash . '?s=100&d=https://q1.qlogo.cn/g?b=qq&nk=' . urlencode($qqRaw) . '&s=100';
            }
            $response['data']['items'][] = [
                'id' => intval($row['id']),
                'name' => $row['name'] ?? '匿名',
                'qq' => $row['QQ'] ?? '',
                'qq_hash' => $qqHash,
                'text' => $row['text'] ?? '',
                'time' => $row['time'] ?? '',
                'city' => $row['city'] ?? '未知',
                'device' => $row['device'] ?? '',
                'browser' => $row['browser'] ?? '',
                'likes' => intval($row['likes'] ?? 0),
                'parent_id' => intval($row['parent_id'] ?? 0),
                'avatar' => $avatarUrl,
            ];
        }
    }
    mysqli_stmt_close($stmt);
    $response['data']['pagination'] = [
        'total' => $total,
        'has_more' => ($offset + $limit) < $total
    ];
} catch (Throwable $e) {
    // 静默失败，保持空数据
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
