<?php
/**
 * 天气服务接口 - LG-NewUi
 * 支持多种天气模式：情侣天气、IP定位天气等
 */

header('Content-Type: application/json; charset=utf-8');
$allowedOrigins = [$_SERVER['HTTP_HOST']]; // Add your actual domain(s) here
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if ($origin && in_array(parse_url($origin, PHP_URL_HOST), $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');

// 配置参数 - 从环境变量或配置文件加载API密钥
$weatherKey = getenv('QWEATHER_API_KEY');
if (empty($weatherKey)) {
    // Try loading from config file
    $configFile = dirname(__DIR__) . '/admin/Config_DB.php';
    if (file_exists($configFile)) {
        include $configFile;
        $weatherKey = defined('QWEATHER_API_KEY') ? QWEATHER_API_KEY : '';
    }
}
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'ip';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$slot = isset($_GET['slot']) ? intval($_GET['slot']) : 1;

// File-based cache
$cacheFile = sys_get_temp_dir() . '/lg_weather_' . md5($mode . $slot . $location) . '.json';
$cacheTime = 600; // 10 minutes

if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
    header('X-Cache: HIT');
    readfile($cacheFile);
    exit;
}

// 模拟天气数据（因为演示站使用了模拟数据）
$mockWeatherData = [
    '1' => [
        'city' => '深圳',
        'temp' => '26',
        'text' => '多云',
        'icon' => '104',
        'humidity' => '72',
        'vis' => '25',
        'feelsLike' => '28',
        'windDir' => '东南风',
        'windScale' => '3'
    ],
    '2' => [
        'city' => '广州',
        'temp' => '28',
        'text' => '晴',
        'icon' => '100',
        'humidity' => '65',
        'vis' => '30',
        'feelsLike' => '31',
        'windDir' => '南风',
        'windScale' => '2'
    ],
    'ip' => [
        'city' => '本地',
        'temp' => '25',
        'text' => '晴间多云',
        'icon' => '101',
        'humidity' => '68',
        'vis' => '28',
        'feelsLike' => '27',
        'windDir' => '东北风',
        'windScale' => '2'
    ]
];

// 确定返回哪个位置的数据
$key = $mode === 'couple' ? (string)$slot : 'ip';
$data = isset($mockWeatherData[$key]) ? $mockWeatherData[$key] : $mockWeatherData['ip'];

// 返回JSON响应
$response = json_encode([
    'code' => 200,
    'data' => $data,
    'mode' => $mode,
    'slot' => $slot,
    'timestamp' => time()
], JSON_UNESCAPED_UNICODE);

// Save to cache
file_put_contents($cacheFile, $response);
header('X-Cache: MISS');

echo $response;
