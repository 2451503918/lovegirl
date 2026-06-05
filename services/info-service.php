<?php
/**
 * 综合信息服务接口 - LG-NewUi
 * 处理各类信息请求
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// 获取请求方式
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

$response = [
    'code' => 400,
    'message' => 'Invalid request',
    'data' => null
];

switch ($action) {
    case 'get_stats':
        // 获取统计数据
        require_once 'admin/connect.php';
        $stats = [
            'articles' => 0,
            'photos' => 0,
            'messages' => 0,
            'days' => 0
        ];
        
        if ($connect) {
            // 文章数
            $result = mysqli_query($connect, "SELECT COUNT(*) as c FROM little");
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $stats['articles'] = intval($row['c']);
            }
            
            // 照片数
            $result = mysqli_query($connect, "SELECT COUNT(*) as c FROM photo");
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $stats['photos'] = intval($row['c']);
            }
            
            // 留言数
            $result = mysqli_query($connect, "SELECT COUNT(*) as c FROM leaving");
            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $stats['messages'] = intval($row['c']);
            }
            
            // 恋爱天数
            $result = mysqli_query($connect, "SELECT * FROM text LIMIT 1");
            if ($result && mysqli_num_rows($result) > 0) {
                $text = mysqli_fetch_assoc($result);
                $startTime = strtotime(str_replace('T', ' ', $text['startTime']));
                $stats['days'] = floor((time() - $startTime) / 86400);
            }
        }
        
        $response = [
            'code' => 200,
            'data' => $stats,
            'timestamp' => time()
        ];
        break;
        
    case 'heartbeat':
        // 心跳检测
        $response = [
            'code' => 200,
            'message' => 'pong',
            'timestamp' => time()
        ];
        break;
        
    default:
        $response = [
            'code' => 200,
            'message' => 'LG-NewUi Service is running',
            'version' => '5.2.1-Stable',
            'timestamp' => time()
        ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
