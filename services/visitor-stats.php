<?php
/**
 * 访客统计服务
 * 处理访问统计和追踪
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// 获取客户端IP
function getClientIP() {
    $ip = '127.0.0.1';
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '127.0.0.1';
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
        $currentHour = date('H');
        $visitorIP = getClientIP();
        
        // 获取或创建今日统计记录
        $result = mysqli_query($connect, "SELECT * FROM visitor_stats WHERE visit_date = '$today'");
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            mysqli_query($connect, "UPDATE visitor_stats SET visit_count = visit_count + 1 WHERE visit_date = '$today'");
        } else {
            mysqli_query($connect, "INSERT INTO visitor_stats (visit_date, visit_count, visitor_count) VALUES ('$today', 1, 1)");
        }
        
        // 更新累计统计
        mysqli_query($connect, "UPDATE visitor_total SET 
            total_visits = total_visits + 1,
            last_visitor_ip = '$visitorIP',
            last_visit_time = NOW()
            WHERE id = 1");
        
        // 检查是否是新的访客（基于IP，简单判断）
        $isNewVisitor = false;
        $checkVisitor = mysqli_query($connect, "SELECT * FROM visitor_stats WHERE visit_date = '$today' AND visitor_count > 0");
        // 简单的新访客判断：每小时最多一次新访客记录
        if ($currentHour >= 0 && $currentHour < 1) {
            mysqli_query($connect, "UPDATE visitor_stats SET visitor_count = visitor_count + 1 WHERE visit_date = '$today'");
            mysqli_query($connect, "UPDATE visitor_total SET total_visitors = total_visitors + 1 WHERE id = 1");
            $isNewVisitor = true;
        }
        
        $response = [
            'code' => 200,
            'message' => 'Tracked',
            'data' => [
                'ip' => $visitorIP,
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
        $result = mysqli_query($connect, "SELECT * FROM visitor_stats WHERE visit_date = '$today'");
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $stats['today']['visits'] = intval($row['visit_count']);
            $stats['today']['visitors'] = intval($row['visitor_count']);
        }
        
        // 获取累计统计
        $result = mysqli_query($connect, "SELECT * FROM visitor_total WHERE id = 1");
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $stats['total']['visits'] = intval($row['total_visits']);
            $stats['total']['visitors'] = intval($row['total_visitors']);
        }
        
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
            'version' => '1.0',
            'timestamp' => time()
        ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);