<?php
/**
 * 交互数据 API
 * 处理点赞、浏览等交互操作
 */
header('Content-Type: application/json; charset=utf-8');

$allowedOrigins = ['lovedemo.54oimx.top', 'love.54oimx.top'];
$origin = isset($_SERVER['HTTP_ORIGIN']) ? parse_url($_SERVER['HTTP_ORIGIN'], PHP_URL_HOST) : '';
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: https://$origin");
}
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

include_once __DIR__ . '/../admin/connect.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$allowedActions = ['like', 'view', 'get_likes', 'get_views'];

if (!in_array($action, $allowedActions)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid action']);
    exit;
}

if (!$connect) {
    // Return graceful fallback for like/view/get actions
    if ($action === 'get_likes' || $action === 'get_views') {
        echo json_encode(['success' => true, 'count' => 0]);
    } else {
        echo json_encode(['success' => true, 'message' => 'Database unavailable, action ignored']);
    }
    exit;
}

$type = $_GET['type'] ?? $_POST['type'] ?? '';
$id = intval($_GET['id'] ?? $_POST['id'] ?? 0);

if (empty($type) || $id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Missing type or id']);
    exit;
}

// 表名映射
$tableMap = [
    'article' => 'little',
    'album' => 'photo',
    'message' => 'leaving',
    'event' => 'lovelist',
];

$table = $tableMap[$type] ?? '';
if (empty($table)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid type']);
    exit;
}

switch ($action) {
    case 'like':
        $stmt = mysqli_prepare($connect, "UPDATE `$table` SET likes = COALESCE(likes, 0) + 1 WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if ($success) {
            // 获取更新后的值
            $stmt = mysqli_prepare($connect, "SELECT COALESCE(likes, 0) as likes FROM `$table` WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            echo json_encode(['success' => true, 'likes' => intval($row['likes'] ?? 0)]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Update failed']);
        }
        break;

    case 'view':
        $stmt = mysqli_prepare($connect, "UPDATE `$table` SET views = COALESCE(views, 0) + 1 WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => $success]);
        break;

    case 'get_likes':
        $stmt = mysqli_prepare($connect, "SELECT COALESCE(likes, 0) as likes FROM `$table` WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => true, 'likes' => intval($row['likes'] ?? 0)]);
        break;

    case 'get_views':
        $stmt = mysqli_prepare($connect, "SELECT COALESCE(views, 0) as views FROM `$table` WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        echo json_encode(['success' => true, 'views' => intval($row['views'] ?? 0)]);
        break;
}
