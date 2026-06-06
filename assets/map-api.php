<?php
/**
 * 地图数据API - LG-NewUi
 * 提供足迹地图相关数据
 */

header('Content-Type: application/json; charset=utf-8');
$allowedOrigins = [$_SERVER['HTTP_HOST']]; // Add your actual domain(s) here
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if ($origin && in_array(parse_url($origin, PHP_URL_HOST), $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');

$module = isset($_GET['module']) ? $_GET['module'] : 'all';

// 示例足迹数据
$footprints = [
    [
        'id' => 1,
        'name' => '深圳',
        'lat' => 22.5431,
        'lng' => 114.0579,
        'date' => '2026-04-15',
        'description' => '我们相遇的地方',
        'photo' => ''
    ],
    [
        'id' => 2,
        'name' => '广州',
        'lat' => 23.1291,
        'lng' => 113.2644,
        'date' => '2026-04-10',
        'description' => '第一次旅行',
        'photo' => ''
    ],
    [
        'id' => 3,
        'name' => '大理',
        'lat' => 25.6065,
        'lng' => 100.2679,
        'date' => '2026-03-20',
        'description' => '浪漫之旅',
        'photo' => ''
    ]
];

$response = [
    'code' => 200,
    'data' => [
        'footprints' => $footprints,
        'total' => count($footprints),
        'center' => [
            'lat' => 30.5928,
            'lng' => 114.3055
        ],
        'zoom' => 4
    ],
    'module' => $module,
    'timestamp' => time()
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
