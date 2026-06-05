<?php
/**
 * 天气服务接口 - LG-NewUi
 * 支持多种天气模式：情侣天气、IP定位天气等
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// 配置参数
$weatherKey = 'b65cfa0c849145c283dfdf9cc6b87dd1'; // 和风天气API密钥（演示用）
$mode = isset($_GET['mode']) ? $_GET['mode'] : 'ip';
$location = isset($_GET['location']) ? $_GET['location'] : '';
$slot = isset($_GET['slot']) ? intval($_GET['slot']) : 1;

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
echo json_encode([
    'code' => 200,
    'data' => $data,
    'mode' => $mode,
    'slot' => $slot,
    'timestamp' => time()
], JSON_UNESCAPED_UNICODE);
