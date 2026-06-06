<?php
/**
 * 音乐播放器数据接口 - LG-NewUi
 * 提供播放列表和配置
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$playlist = [
    [
        'title' => '小幸运',
        'artist' => '田馥甄',
        'cover' => 'https://p2.music.126.net/6y-Ule0EDXC81d5q6k62bQ==/18227031569521043.jpg',
        'url' => 'https://music.163.com/song/media/outer/url?id=32507615.mp3',
        'lrc' => ''
    ],
    [
        'title' => '简单爱',
        'artist' => '周杰伦',
        'cover' => 'https://p2.music.126.net/fqf3q36Bj4X0r3q6mU2cQQ==/109951165726151642.jpg',
        'url' => 'https://music.163.com/song/media/outer/url?id=186016.mp3',
        'lrc' => ''
    ],
    [
        'title' => '七里香',
        'artist' => '周杰伦',
        'cover' => 'https://p2.music.126.net/LRzJt9QwJv0f6f6q6q3q3Q==/109951165611648138.jpg',
        'url' => 'https://music.163.com/song/media/outer/url?id=186001.mp3',
        'lrc' => ''
    ],
    [
        'title' => '告白气球',
        'artist' => '周杰伦',
        'cover' => 'https://p2.music.126.net/sKQm1LqQq3Qq3q3Qq3q3Q==/109951163207418342.jpg',
        'url' => 'https://music.163.com/song/media/outer/url?id=424470008.mp3',
        'lrc' => ''
    ]
];

echo json_encode([
    'code' => 200,
    'data' => [
        'playlist' => $playlist,
        'autoplay' => false,
        'loop' => 'all',
        'theme' => 'pink'
    ],
    'timestamp' => time()
], JSON_UNESCAPED_UNICODE);
