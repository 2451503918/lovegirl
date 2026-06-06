<?php
/**
 * 访客统计服务
 * 处理访问统计和追踪
 */

header('Content-Type: application/json; charset=utf-8');
$allowedOrigins = [$_SERVER['HTTP_HOST']]; // Add your actual domain(s) here
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if ($origin && in_array(parse_url($origin, PHP_URL_HOST), $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');

// 获取客户端IP
function getClientIP() {
    // Only trust REMOTE_ADDR for security
    $ip = $_SERVER['REMOTE_ADDR'];
    // Validate it's a real IP
    if (filter_var($ip, FILTER_VALIDATE_IP)) {
        return $ip;
    }
    return '0.0.0.0';
}

// 获取请求方式
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

$response = [
    'code' => 400,
    'message' => 'Invalid request',
    'data' => null
];

// 数据库连接 - 不使用connect.php的die逻辑
$db_connected = false;
$connect = null;

if (file_exists(__DIR__ . '/../admin/Config_DB.php')) {
    include_once __DIR__ . '/../admin/Config_DB.php';
    $connect = mysqli_connect($db_address ?? '', $db_username ?? '', $db_password ?? '', $db_name ?? '');
    if ($connect) {
        $connect->set_charset("utf8mb4");
        $db_connected = true;
    }
}

// 如果没有数据库连接，返回模拟数据
if (!$db_connected) {
    $response = [
        'code' => 200,
        'message' => 'Database not connected, using demo data',
        'data' => [
            'today' => ['visits' => 156, 'visitors' => 48],
            'total' => ['visits' => 15234, 'visitors' => 3847]
        ],
        'timestamp' => time()
    ];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

switch ($action) {
    case 'track':
        // 记录访问
        $today = date('Y-m-d');
        $visitorIP = getClientIP();
        
        // 使用 upsert 避免竞态条件 (Query 1)
        $stmt = mysqli_prepare($connect, "INSERT INTO visitor_stats (visit_date, visit_count, visitor_count) VALUES (?, 1, 0) ON DUPLICATE KEY UPDATE visit_count = visit_count + 1");
        mysqli_stmt_bind_param($stmt, "s", $today);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // 更新累计访问次数 (Query 2)
        $stmt = mysqli_prepare($connect, "UPDATE visitor_total SET 
            total_visits = total_visits + 1,
            last_visit_time = NOW()
            WHERE id = 1");
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        // 使用 INSERT IGNORE 基于 IP 去重判断是否是新访客，避免 SELECT+INSERT 两次查询 (Query 3)
        $isNewVisitor = false;
        $stmt = mysqli_prepare($connect, "INSERT IGNORE INTO visitor_ips (visit_date, ip) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $today, $visitorIP);
        mysqli_stmt_execute($stmt);
        $isNewVisitor = mysqli_stmt_affected_rows($stmt) > 0;
        mysqli_stmt_close($stmt);
        
        // 仅在新访客时更新访客计数 (Query 4-5, 仅新访客时执行)
        if ($isNewVisitor) {
            $stmt = mysqli_prepare($connect, "UPDATE visitor_stats SET visitor_count = visitor_count + 1 WHERE visit_date = ?");
            mysqli_stmt_bind_param($stmt, "s", $today);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            
            $stmt = mysqli_prepare($connect, "UPDATE visitor_total SET total_visitors = total_visitors + 1 WHERE id = 1");
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        $response = [
            'code' => 200,
            'message' => 'Tracked',
            'data' => [
                'newVisitor' => $isNewVisitor,
                'timestamp' => time()
            ]
        ];
        break;
        
    case 'get_stats':
        // 获取统计数据
        $today = date('Y-m-d');
        $stats = [
            'today' => ['visits' => 0, 'visitors' => 0],
            'total' => ['visits' => 0, 'visitors' => 0]
        ];
        
        // 获取今日统计
        $stmt = mysqli_prepare($connect, "SELECT visit_count, visitor_count FROM visitor_stats WHERE visit_date = ?");
        mysqli_stmt_bind_param($stmt, "s", $today);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $stats['today']['visits'] = intval($row['visit_count']);
            $stats['today']['visitors'] = intval($row['visitor_count']);
        }
        mysqli_stmt_close($stmt);
        
        // 获取累计统计
        $stmt = mysqli_prepare($connect, "SELECT total_visits, total_visitors FROM visitor_total WHERE id = 1");
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $stats['total']['visits'] = intval($row['total_visits']);
            $stats['total']['visitors'] = intval($row['total_visitors']);
        }
        mysqli_stmt_close($stmt);
        
        $response = [
            'code' => 200,
            'data' => $stats,
            'timestamp' => time()
        ];
        break;
        
    default:
        $response = [
            'code' => 200,
            'message' => 'Visitor Stats Service is running',
            'timestamp' => time()
        ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);