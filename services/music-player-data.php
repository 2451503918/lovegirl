<?php
/**
 * 音乐播放器数据接口 - LG-NewUi
 * MetingJS 期望直接的数组格式: [{name, artist, url, cover, lrc}, ...]
 * 不能用 {code, data} 包装，否则 MetingJS 报 "API response is not an array"
 */

header('Content-Type: application/json; charset=utf-8');

include_once __DIR__ . '/../admin/connect.php';

$allowedOrigins = [$_SERVER['HTTP_HOST']];
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
if ($origin && in_array(parse_url($origin, PHP_URL_HOST), $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}
header('Access-Control-Allow-Credentials: true');

// 尝试从数据库获取播放列表
$playlist = [];

if ($connect) {
    $stmt = mysqli_prepare($connect, "SELECT * FROM music ORDER BY id DESC");
    if ($stmt) {
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $playlist[] = [
                    'name' => $row['music_name'] ?? $row['name'] ?? '',
                    'artist' => $row['music_artist'] ?? $row['artist'] ?? '',
                    'url' => $row['music_url'] ?? $row['url'] ?? '',
                    'cover' => $row['music_cover'] ?? $row['cover'] ?? '',
                    'lrc' => $row['music_lrc'] ?? $row['lrc'] ?? '',
                    'type' => 'auto'
                ];
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// 如果数据库没有数据，使用默认播放列表
if (empty($playlist)) {
    $playlist = [
        [
            'name' => '小幸运',
            'artist' => '田馥甄',
            'url' => 'https://music.163.com/song/media/outer/url?id=32507615.mp3',
            'cover' => 'https://p2.music.126.net/6y-Ule0EDXC81d5q6k62bQ==/18227031569521043.jpg',
            'lrc' => '',
            'type' => 'auto'
        ],
        [
            'name' => '简单爱',
            'artist' => '周杰伦',
            'url' => 'https://music.163.com/song/media/outer/url?id=186016.mp3',
            'cover' => 'https://p2.music.126.net/fqf3q36Bj4X0r3q6mU2cQQ==/109951165726151642.jpg',
            'lrc' => '',
            'type' => 'auto'
        ],
        [
            'name' => '七里香',
            'artist' => '周杰伦',
            'url' => 'https://music.163.com/song/media/outer/url?id=186001.mp3',
            'cover' => 'https://p2.music.126.net/LRzJt9QwJv0f6f6q6q3q3Q==/109951165611648138.jpg',
            'lrc' => '',
            'type' => 'auto'
        ],
        [
            'name' => '告白气球',
            'artist' => '周杰伦',
            'url' => 'https://music.163.com/song/media/outer/url?id=424470008.mp3',
            'cover' => 'https://p2.music.126.net/sKQm1LqQq3Qq3q3Qq3q3Q==/109951163207418342.jpg',
            'lrc' => '',
            'type' => 'auto'
        ]
    ];
}

// MetingJS 期望直接的数组格式，不要包装
echo json_encode($playlist, JSON_UNESCAPED_UNICODE);
