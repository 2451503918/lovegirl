<?php
/**
 * 本地头像代理服务
 * 替代外部头像API（q1.qlogo.cn, weavatar.com, ui-avatars.com）
 * 不依赖任何外部API，完全本地化
 *
 * 接受参数:
 *   type   - 头像类型: qq/gravatar/initials
 *   qq     - QQ号（type=qq时使用）
 *   email  - 邮箱MD5（type=gravatar时使用）
 *   name   - 用户名（type=initials时使用）
 */

// 配置
$cacheDir = __DIR__ . '/../assets/img/avatars/cache/';
$defaultAvatar = __DIR__ . '/../assets/img/avatars/default.png';

// 确保缓存目录存在
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

// 获取参数
$type = isset($_GET['type']) ? trim($_GET['type']) : '';
$qq = isset($_GET['qq']) ? trim($_GET['qq']) : '';
$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$name = isset($_GET['name']) ? trim($_GET['name']) : '';

// 根据类型处理
switch ($type) {
    case 'qq':
        serveQQAvatar($qq, $cacheDir, $defaultAvatar);
        break;

    case 'gravatar':
        serveGravatarAvatar($email, $cacheDir, $defaultAvatar);
        break;

    case 'initials':
        serveInitialsAvatar($name);
        break;

    default:
        serveDefaultAvatar($defaultAvatar);
        break;
}

/**
 * 提供QQ头像（仅本地缓存，不请求外部API）
 */
function serveQQAvatar($qq, $cacheDir, $defaultAvatar) {
    if (empty($qq) || !preg_match('/^\d+$/', $qq)) {
        serveDefaultAvatar($defaultAvatar);
        return;
    }

    $cacheFile = $cacheDir . 'qq_' . $qq . '.png';

    // 尝试从本地缓存读取
    if (file_exists($cacheFile) && filesize($cacheFile) > 0) {
        serveCachedImage($cacheFile);
        return;
    }

    // 缓存不存在，返回默认头像
    serveDefaultAvatar($defaultAvatar);
}

/**
 * 提供Gravatar头像（仅本地缓存，不请求外部API）
 */
function serveGravatarAvatar($email, $cacheDir, $defaultAvatar) {
    if (empty($email)) {
        serveDefaultAvatar($defaultAvatar);
        return;
    }

    $cacheFile = $cacheDir . 'gravatar_' . $email . '.png';

    // 尝试从本地缓存读取
    if (file_exists($cacheFile) && filesize($cacheFile) > 0) {
        serveCachedImage($cacheFile);
        return;
    }

    // 缓存不存在，返回默认头像
    serveDefaultAvatar($defaultAvatar);
}

/**
 * 提供首字母头像（本地GD生成）
 */
function serveInitialsAvatar($name) {
    $name = empty($name) ? '?' : $name;
    $size = 200;

    // 根据名字hash选择颜色
    $colors = [
        [231, 76, 60],
        [230, 126, 34],
        [241, 196, 15],
        [46, 204, 113],
        [26, 188, 156],
        [52, 152, 219],
        [155, 89, 182],
        [233, 30, 99],
        [0, 150, 136],
        [63, 81, 181],
        [121, 85, 72],
        [96, 125, 139],
    ];

    $hash = crc32($name);
    $colorIndex = abs($hash) % count($colors);
    $bgRgb = $colors[$colorIndex];

    $initial = mb_substr($name, 0, 1, 'UTF-8');
    if (empty($initial)) {
        $initial = '?';
    }

    $img = imagecreatetruecolor($size, $size);
    imageantialias($img, true);

    $bgColor = imagecolorallocate($img, $bgRgb[0], $bgRgb[1], $bgRgb[2]);
    imagefill($img, 0, 0, $bgColor);

    $textColor = imagecolorallocate($img, 255, 255, 255);

    // 尝试使用TTF字体
    $ttfAvailable = function_exists('imagettftext');
    $fontPath = null;

    if ($ttfAvailable) {
        $fontPaths = [
            '/usr/share/fonts/truetype/noto/NotoSansSC-Regular.ttf',
            '/usr/share/fonts/opentype/noto/NotoSansCJK-Regular.ttc',
            '/usr/share/fonts/noto-cjk/NotoSansCJK-Regular.ttc',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        ];
        foreach ($fontPaths as $fp) {
            if (file_exists($fp)) {
                $fontPath = $fp;
                break;
            }
        }
    }

    if ($fontPath) {
        $fontSize = intval($size * 0.45);
        $textBox = imagettfbbox($fontSize, 0, $fontPath, $initial);
        $textWidth = $textBox[2] - $textBox[0];
        $textHeight = $textBox[1] - $textBox[7];
        $x = ($size - $textWidth) / 2;
        $y = ($size + $textHeight) / 2;
        imagettftext($img, $fontSize, 0, $x, $y, $textColor, $fontPath, $initial);
    } else {
        // 无TTF支持：使用内置字体+缩放
        $builtInFont = 5;
        $charWidth = imagefontwidth($builtInFont);
        $charHeight = imagefontheight($builtInFont);

        $smallImg = imagecreatetruecolor($charWidth, $charHeight);
        $smallBg = imagecolorallocate($smallImg, $bgRgb[0], $bgRgb[1], $bgRgb[2]);
        imagefill($smallImg, 0, 0, $smallBg);
        $smallText = imagecolorallocate($smallImg, 255, 255, 255);

        $asciiInitial = $initial;
        if (!preg_match('/^[a-zA-Z0-9?]$/', $asciiInitial)) {
            $asciiInitial = '?';
        }
        imagestring($smallImg, $builtInFont, 0, 0, $asciiInitial, $smallText);

        $targetTextSize = intval($size * 0.55);
        $scaleX = $targetTextSize / $charWidth;
        $scaleY = $targetTextSize / $charHeight;
        $scale = min($scaleX, $scaleY);

        $destW = intval($charWidth * $scale);
        $destH = intval($charHeight * $scale);
        $destX = intval(($size - $destW) / 2);
        $destY = intval(($size - $destH) / 2);

        imagecopyresampled($img, $smallImg, $destX, $destY, 0, 0, $destW, $destH, $charWidth, $charHeight);
        imagedestroy($smallImg);
    }

    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=86400');
    imagepng($img, null, 9);
    imagedestroy($img);
}

/**
 * 输出缓存图片
 */
function serveCachedImage($cacheFile) {
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=604800');
    header('X-Avatar-Source: cache');
    readfile($cacheFile);
}

/**
 * 输出默认头像
 */
function serveDefaultAvatar($defaultAvatar) {
    header('Content-Type: image/png');
    header('Cache-Control: public, max-age=3600');
    header('X-Avatar-Source: default');

    if (file_exists($defaultAvatar) && filesize($defaultAvatar) > 0) {
        readfile($defaultAvatar);
    } else {
        // 如果默认头像文件不存在，用GD动态生成一个
        $size = 200;
        $img = imagecreatetruecolor($size, $size);
        $bgColor = imagecolorallocate($img, 189, 189, 189);
        imagefill($img, 0, 0, $bgColor);
        $white = imagecolorallocate($img, 255, 255, 255);

        // 头部（圆形）
        imagefilledellipse($img, 100, 65, 60, 60, $white);

        // 身体（梯形）
        $points = [
            60, 95,
            140, 95,
            170, 180,
            30, 180,
        ];
        imagefilledpolygon($img, $points, $white);

        imagepng($img, null, 9);
        imagedestroy($img);
    }
}
