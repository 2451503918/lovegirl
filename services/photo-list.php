<?php
/**
 * 相册照片列表API
 * @param code 相册code
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

include_once '../admin/connect.php';
include_once '../admin/Function.php';

header('Content-Type: application/json; charset=utf-8');

$code = isset($_GET['code']) ? trim($_GET['code']) : '';

if (empty($code)) {
    echo json_encode(['code' => 400, 'msg' => '缺少相册code参数'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 查询相册信息
$album = null;
if ($connect) {
    $stmt = mysqli_prepare($connect, "SELECT id, code, title, img, `desc`, author, location, lng, lat, views, likes, password, private, date FROM photo WHERE code = ?");
    mysqli_stmt_bind_param($stmt, 's', $code);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $album = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}

if (!$album) {
    echo json_encode(['code' => 404, 'msg' => '相册不存在'], JSON_UNESCAPED_UNICODE);
    exit;
}

// 获取用户信息用于头像
$text = [];
if ($connect) {
    $sql = "SELECT boy, girl, boyimg, girlimg FROM text LIMIT 1";
    $result = mysqli_query($connect, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $text = mysqli_fetch_assoc($result);
    }
}

$boyimg = $text['boyimg'] ?? '';
$girlimg = $text['girlimg'] ?? '';
if ($boyimg && !preg_match('/^https?:\/\//', $boyimg)) {
    $boyimg = 'https://q1.qlogo.cn/g?b=qq&nk=' . $boyimg . '&s=640';
}
if ($girlimg && !preg_match('/^https?:\/\//', $girlimg)) {
    $girlimg = 'https://q1.qlogo.cn/g?b=qq&nk=' . $girlimg . '&s=640';
}

// 解析照片列表
$photos = [];
if (!empty($album['img'])) {
    $imgLines = explode("\n", $album['img']);
    $index = 0;
    foreach ($imgLines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $isVideo = preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', $line);
            $photoDate = !empty($album['date']) ? $album['date'] : date('Y-m-d');
            
            // 计算相对时间
            $dateAgo = '';
            try {
                $dateTime = new DateTime($photoDate);
                $now = new DateTime();
                $diff = $dateTime->diff($now);
                if ($diff->y > 0) {
                    $dateAgo = $diff->y . '年前';
                } elseif ($diff->m > 0) {
                    $dateAgo = $diff->m . '个月前';
                } elseif ($diff->d > 0) {
                    $dateAgo = $diff->d . '天前';
                } else {
                    $dateAgo = '今天';
                }
            } catch (Exception $e) {
                $dateAgo = '';
            }
            
            // 缩略图处理
            $thumb = $line;
            if (!$isVideo && strpos($line, '_thumb.webp') === false) {
                $thumb = preg_replace('/(\.[^.]+)$/', '_thumb.webp', $line);
            }
            
            $isMaleAuthor = ($album['author'] === 'boy' || $album['author'] === 'male');
            $upAvatar = $isMaleAuthor ? $boyimg : $girlimg;
            $upGender = $isMaleAuthor ? 'male' : 'female';
            
            $photos[] = [
                'id' => $index + 1,
                'photo_text' => '',
                'photo_url' => $line,
                'photo_thumb' => $thumb,
                'photo_date' => $photoDate,
                'photo_date_ago' => $dateAgo,
                'photo_byname' => $isMaleAuthor ? ($text['boy'] ?? '男方') : ($text['girl'] ?? '女方'),
                'photo_location' => '',
                'photo_lng' => '',
                'photo_lat' => '',
                'photo_type' => $isVideo ? 1 : 0,
                'VideoCover' => $isVideo ? $thumb : '',
                'up_avatar' => $upAvatar,
                'up_gender' => $upGender
            ];
            $index++;
        }
    }
}

// 组装响应数据
$response = [
    'code' => 200,
    'data' => [
        'album' => [
            'title' => $album['title'] ?? '',
            'date' => $album['date'] ?? '',
            'location' => $album['location'] ?? '',
            'desc' => $album['desc'] ?? '',
            'cover' => !empty($photos) ? $photos[0]['photo_url'] : '',
            'author' => $album['author'] ?? 'boy'
        ],
        'photos' => $photos,
        'total' => count($photos)
    ]
];

echo json_encode($response, JSON_UNESCAPED_UNICODE);
