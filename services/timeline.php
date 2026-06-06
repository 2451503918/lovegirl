<?php
/**
 * 时间轴数据 API
 * 返回 JSON 格式的时间轴事件数据
 */
header('Content-Type: application/json; charset=utf-8');
$allowedOrigins = ['lovedemo.54oimx.top', 'love.54oimx.top'];
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if ($origin && in_array(parse_url($origin, PHP_URL_HOST), $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

include_once __DIR__ . '/../admin/connect.php';

$response = ['success' => true, 'code' => 200, 'data' => []];

if (!$connect) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database connection failed',
        'timestamp' => time()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 获取 timeline 表数据
$stmt = mysqli_prepare($connect, "SELECT id, type, title, content, date, location FROM timeline ORDER BY date DESC");
if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database query preparation failed',
        'timestamp' => time()
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $typeMap = [
        0 => 'text',
        1 => 'image',
        2 => 'video',
        3 => 'audio',
    ];

    while ($row = mysqli_fetch_assoc($result)) {
        // 解析日期
        $dateStr = $row['date'] ?? '';
        $year = '';
        $month = '';
        $day = '';
        $time = '';

        if ($dateStr) {
            $ts = strtotime($dateStr);
            if ($ts) {
                $year = date('Y', $ts);
                $month = date('m', $ts);
                $day = date('d', $ts);
                $time = date('H:i', $ts);
            }
        }

        $typeInt = intval($row['type'] ?? 0);
        $typeStr = $typeMap[$typeInt] ?? 'text';

        $event = [
            'id'            => strval($row['id']),
            'author_id'     => 1,
            'type'          => $typeStr,
            'year'          => $year,
            'month'         => $month,
            'day'           => $day,
            'time'          => $time,
            'title'         => $row['title'] ?? '',
            'desc'          => $row['content'] ?? '',
            'location'      => $row['location'] ?? '',
            'map_lat'       => null,
            'map_lng'       => null,
            'weather'       => '',
            'weatherIcon'   => '',
            'mood'          => '',
            'moodLabel'     => '',
            'moodIconHtml'  => '',
            'signature'     => '',
            'mediaUrl'      => '',
            'thumbUrl'      => null,
            'mediaMeta'     => null,
            'duration'      => '',
            'giftName'      => '',
            'giftFrom'      => '',
            'giftTo'        => '',
            'giftImage'     => '',
            'giftThumbUrl'  => null,
            'giftPrice'     => '',
            'milestoneValue'    => '',
            'milestoneUnit'     => '',
            'milestoneCategory' => 'default',
            'from'          => '',
            'to'            => '',
            'fromCode'      => '',
            'toCode'        => '',
            'flightNo'      => '',
            'seat'          => '',
            'items'         => null,
            'linkType'      => 'none',
            'linkTarget'    => '',
            'linkTitle'     => '',
            'linkPath'      => '',
            'published'     => '1',
        ];

        $response['data'][] = $event;
    }
    mysqli_stmt_close($stmt);
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
