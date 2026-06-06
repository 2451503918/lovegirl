<?php
/**
 * 综合信息服务接口 - LG-NewUi
 * 处理各类信息请求
 */

header('Content-Type: application/json; charset=utf-8');
$allowedOrigins = [$_SERVER['HTTP_HOST']]; // Add your actual domain(s) here
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if ($origin && in_array(parse_url($origin, PHP_URL_HOST), $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');

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
        require_once '../admin/connect.php';
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
        
    case 'get_location':
        // 获取位置信息
        require_once '../admin/connect.php';
        $location = [
            'boyCity' => '北京',
            'girlCity' => '上海',
            'boyLat' => 39.9042,
            'boyLng' => 116.4074,
            'girlLat' => 31.2304,
            'girlLng' => 121.4737,
            'distance' => 0
        ];
        
        if ($connect) {
            $result = mysqli_query($connect, "SELECT * FROM text LIMIT 1");
            if ($result && mysqli_num_rows($result) > 0) {
                $text = mysqli_fetch_assoc($result);
                $location['boyCity'] = $text['boyCity'] ?? '北京';
                $location['girlCity'] = $text['girlCity'] ?? '上海';
                $location['boyLat'] = floatval($text['boyLat'] ?? 39.9042);
                $location['boyLng'] = floatval($text['boyLng'] ?? 116.4074);
                $location['girlLat'] = floatval($text['girlLat'] ?? 31.2304);
                $location['girlLng'] = floatval($text['girlLng'] ?? 121.4737);
                
                // 计算距离
                $earthRadius = 6371;
                $latDelta = deg2rad($location['girlLat'] - $location['boyLat']);
                $lonDelta = deg2rad($location['girlLng'] - $location['boyLng']);
                $a = sin($latDelta / 2) * sin($latDelta / 2) +
                     cos(deg2rad($location['boyLat'])) * cos(deg2rad($location['girlLat'])) *
                     sin($lonDelta / 2) * sin($lonDelta / 2);
                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
                $location['distance'] = round($earthRadius * $c, 1);
            }
        }
        
        $response = [
            'code' => 200,
            'data' => $location,
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
            'timestamp' => time()
        ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
