<?php
/**
 * 本地首字母头像生成服务
 * 接受参数: name (用户名), size (尺寸，默认100)
 * 用PHP GD库生成彩色首字母头像，颜色根据名字hash自动选择
 */

header('Content-Type: image/png');
header('Cache-Control: public, max-age=86400');

$name = isset($_GET['name']) ? trim($_GET['name']) : '';
$size = isset($_GET['size']) ? intval($_GET['size']) : 100;

if ($size < 16) $size = 16;
if ($size > 512) $size = 512;

if (empty($name)) {
    $name = '?';
}

// 根据名字hash选择颜色
$colors = [
    [231, 76, 60],    // 红
    [230, 126, 34],   // 橙
    [241, 196, 15],   // 黄
    [46, 204, 113],   // 绿
    [26, 188, 156],   // 青
    [52, 152, 219],   // 蓝
    [155, 89, 182],   // 紫
    [233, 30, 99],    // 粉
    [0, 150, 136],    // 深青
    [63, 81, 181],    // 靛蓝
    [121, 85, 72],    // 棕
    [96, 125, 139],   // 蓝灰
];

$hash = crc32($name);
$colorIndex = abs($hash) % count($colors);
$bgRgb = $colors[$colorIndex];

// 提取首字母
$initial = mb_substr($name, 0, 1, 'UTF-8');
if (empty($initial)) {
    $initial = '?';
}

// 创建图像
$img = imagecreatetruecolor($size, $size);
imageantialias($img, true);

// 背景色
$bgColor = imagecolorallocate($img, $bgRgb[0], $bgRgb[1], $bgRgb[2]);
imagefill($img, 0, 0, $bgColor);

// 白色文字
$textColor = imagecolorallocate($img, 255, 255, 255);

// 尝试使用TTF字体（需要FreeType支持）
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
    // 使用TTF字体
    $fontSize = intval($size * 0.45);
    $textBox = imagettfbbox($fontSize, 0, $fontPath, $initial);
    $textWidth = $textBox[2] - $textBox[0];
    $textHeight = $textBox[1] - $textBox[7];
    $x = ($size - $textWidth) / 2;
    $y = ($size + $textHeight) / 2;
    imagettftext($img, $fontSize, 0, $x, $y, $textColor, $fontPath, $initial);
} else {
    // 无TTF字体支持：使用内置字体+缩放方式绘制大号文字
    // 先用内置字体在小画布上绘制，再缩放到目标尺寸
    $builtInFont = 5; // 最大内置字体
    $charWidth = imagefontwidth($builtInFont);
    $charHeight = imagefontheight($builtInFont);

    // 创建小画布绘制文字
    $smallImg = imagecreatetruecolor($charWidth, $charHeight);
    $smallBg = imagecolorallocate($smallImg, $bgRgb[0], $bgRgb[1], $bgRgb[2]);
    imagefill($smallImg, 0, 0, $smallBg);
    $smallText = imagecolorallocate($smallImg, 255, 255, 255);

    // 只取ASCII首字母用于内置字体
    $asciiInitial = $initial;
    if (!preg_match('/^[a-zA-Z0-9?]$/', $asciiInitial)) {
        // 非ASCII字符，转大写拼音首字母或用?
        $asciiInitial = '?';
    }
    imagestring($smallImg, $builtInFont, 0, 0, $asciiInitial, $smallText);

    // 计算缩放后的文字区域大小（占画布60%）
    $targetTextSize = intval($size * 0.55);
    $scaleX = $targetTextSize / $charWidth;
    $scaleY = $targetTextSize / $charHeight;
    $scale = min($scaleX, $scaleY);

    $destW = intval($charWidth * $scale);
    $destH = intval($charHeight * $scale);
    $destX = intval(($size - $destW) / 2);
    $destY = intval(($size - $destH) / 2);

    // 缩放绘制到目标画布（使用平滑缩放）
    imagecopyresampled($img, $smallImg, $destX, $destY, 0, 0, $destW, $destH, $charWidth, $charHeight);
    imagedestroy($smallImg);
}

// 输出
imagepng($img, null, 9);
imagedestroy($img);
