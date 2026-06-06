<?php
/**
 * 随机一言服务 - LG-NewUi
 * 提供浪漫的情侣语录
 */

header('Content-Type: application/json; charset=utf-8');
$allowedOrigins = [$_SERVER['HTTP_HOST']]; // Add your actual domain(s) here
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if ($origin && in_array(parse_url($origin, PHP_URL_HOST), $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');

$quotes = [
    [
        'text' => '朝暮与年岁并往，与你行至天光',
        'author' => '佚名',
        'type' => 'romantic'
    ],
    [
        'text' => '收好我们的日常与心动',
        'author' => 'LG-NewUi',
        'type' => 'warm'
    ],
    [
        'text' => '愿得一人心，白首不相离',
        'author' => '卓文君',
        'type' => 'classic'
    ],
    [
        'text' => '爱晨雾漫过青瓦，爱暮色染透篱笆，更爱与君并肩立',
        'author' => '佚名',
        'type' => 'poetic'
    ],
    [
        'text' => '你是我的今天，以及所有的明天',
        'author' => '佚名',
        'type' => 'sweet'
    ],
    [
        'text' => '斯人若彩虹，遇上方知有',
        'author' => '韩寒',
        'type' => 'classic'
    ],
    [
        'text' => '世界这么大，人生这么长，总会有这么一个人，让你想要温柔地对待',
        'author' => '宫崎骏',
        'type' => 'warm'
    ],
    [
        'text' => '你的过去我来不及参与，你的未来我奉陪到底',
        'author' => '余秋雨',
        'type' => 'romantic'
    ],
    [
        'text' => '答案很长，我准备用一生的时间来回答，你准备要听了吗？',
        'author' => '林徽因',
        'type' => 'sweet'
    ],
    [
        'text' => '风很温柔，花很浪漫，你很特别，我很喜欢',
        'author' => '佚名',
        'type' => 'poetic'
    ]
];

$randomIndex = array_rand($quotes);
$quote = $quotes[$randomIndex];

echo json_encode([
    'code' => 200,
    'data' => $quote,
    'timestamp' => time()
], JSON_UNESCAPED_UNICODE);
