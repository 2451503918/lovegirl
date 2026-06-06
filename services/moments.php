<?php
/**
 * 动态内容接口 - LG-NewUi
 * 提供首页时光碎片等动态内容
 */

header('Content-Type: application/json; charset=utf-8');
$allowedOrigins = [$_SERVER['HTTP_HOST']]; // Add your actual domain(s) here
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if ($origin && in_array(parse_url($origin, PHP_URL_HOST), $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');

// 连接数据库 - 不使用connect.php的die逻辑
$db_connected = false;
$connect = null;

if (file_exists('../admin/Config_DB.php')) {
    include_once '../admin/Config_DB.php';
    $connect = mysqli_connect($db_address ?? '', $db_username ?? '', $db_password ?? '', $db_name ?? '');
    if ($connect) {
        $connect->set_charset("utf8mb4");
        $db_connected = true;
    }
}

$moments = [];

// 获取最新的点滴文章
if ($db_connected) {
    $stmt = mysqli_prepare($connect, "SELECT id, title, text, date FROM little ORDER BY id DESC LIMIT 6");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $moments[] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'content' => mb_substr(strip_tags($row['text']), 0, 100, 'UTF-8'),
                    'date' => $row['date'],
                    'type' => 'article'
                ];
            }
        }
        mysqli_stmt_close($stmt);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Database query preparation failed',
            'timestamp' => time()
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}

// 如果数据库里没有内容，返回一些示例数据
if (empty($moments)) {
    $moments = [
        [
            'id' => 1,
            'title' => '今天一起去看海',
            'content' => '今天天气真好，我们一起去海边，吹着海风，看着日落...',
            'date' => '2026-04-15',
            'type' => 'article'
        ],
        [
            'id' => 2,
            'title' => '美食探店',
            'content' => '发现了一家超棒的餐厅，菜色很好吃，环境也很浪漫...',
            'date' => '2026-04-10',
            'type' => 'article'
        ]
    ];
}

echo json_encode([
    'success' => true,
    'code' => 200,
    'data' => $moments,
    'total' => count($moments),
    'timestamp' => time()
], JSON_UNESCAPED_UNICODE);
