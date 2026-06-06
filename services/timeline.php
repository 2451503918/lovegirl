<?php
/**
 * 时间轴数据 API
 * 返回 JSON 格式的时间轴事件数据
 */
header('Content-Type: application/json; charset=utf-8');
$allowedOrigins = [$_SERVER['HTTP_HOST']]; // Add your actual domain(s) here
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if ($origin && in_array(parse_url($origin, PHP_URL_HOST), $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');

error_reporting(0);

include_once __DIR__ . '/../admin/connect.php';

$response = ['code' => 200, 'data' => []];

if (!$connect) {
    echo json_encode($response);
    exit;
}

// 获取 timeline 表数据
$sql = "SELECT * FROM timeline ORDER BY date DESC";
$result = mysqli_query($connect, $sql);

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
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
