<?php
/**
 * 数据库更新脚本
 * 添加情侣位置配置和访客统计功能
 */

session_start();
if(!isset($_SESSION['loginadmin'])){
    header("Location: login.php");
    exit;
}

error_reporting(0);
header("Content-Type:text/html; charset=utf8");
include_once __DIR__.'/Config_DB.php';

$connect = mysqli_connect($db_address, $db_username, $db_password, $db_name, 3306, $db_socket ?? null);
if (!$connect) {
    die("数据库连接失败");
}
$connect->set_charset("utf8mb4");

$messages = [];

// 1. 检查并添加text表的字段
$fieldsToAdd = [
    'boyCity' => "VARCHAR(50) NOT NULL DEFAULT '北京' COMMENT '男方城市'",
    'girlCity' => "VARCHAR(50) NOT NULL DEFAULT '上海' COMMENT '女方城市'",
    'boyLat' => "DECIMAL(10,6) NOT NULL DEFAULT 39.9042 COMMENT '男方纬度'",
    'boyLng' => "DECIMAL(11,6) NOT NULL DEFAULT 116.4074 COMMENT '男方经度'",
    'girlLat' => "DECIMAL(10,6) NOT NULL DEFAULT 31.2304 COMMENT '女方纬度'",
    'girlLng' => "DECIMAL(11,6) NOT NULL DEFAULT 121.4737 COMMENT '女方经度'"
];

foreach ($fieldsToAdd as $field => $definition) {
    $check = mysqli_query($connect, "SHOW COLUMNS FROM text LIKE '$field'");
    if (mysqli_num_rows($check) == 0) {
        $sql = "ALTER TABLE text ADD COLUMN $field $definition";
        if (mysqli_query($connect, $sql)) {
            $messages[] = "✓ 添加字段: text.$field";
        } else {
            $messages[] = "✗ 添加字段失败: text.$field - " . mysqli_error($connect);
        }
    } else {
        $messages[] = "○ 字段已存在: text.$field";
    }
}

// 2. 创建访客统计表
$createVisitorTable = "
CREATE TABLE IF NOT EXISTS visitor_stats (
    id INT(11) NOT NULL AUTO_INCREMENT,
    visit_date DATE NOT NULL COMMENT '访问日期',
    visit_count INT(11) NOT NULL DEFAULT 0 COMMENT '访问次数',
    visitor_count INT(11) NOT NULL DEFAULT 0 COMMENT '访客数',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY idx_visit_date (visit_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='访客统计表'
";

if (mysqli_query($connect, $createVisitorTable)) {
    $messages[] = "✓ 创建表: visitor_stats";
} else {
    if (strpos(mysqli_error($connect), 'already exists') !== false) {
        $messages[] = "○ 表已存在: visitor_stats";
    } else {
        $messages[] = "✗ 创建表失败: visitor_stats - " . mysqli_error($connect);
    }
}

// 3. 创建累计访客表
$createTotalTable = "
CREATE TABLE IF NOT EXISTS visitor_total (
    id INT(11) NOT NULL AUTO_INCREMENT,
    total_visits BIGINT(20) NOT NULL DEFAULT 0 COMMENT '总访问次数',
    total_visitors BIGINT(20) NOT NULL DEFAULT 0 COMMENT '总访客数',
    last_visitor_ip VARCHAR(50) DEFAULT '' COMMENT '最后访客IP',
    last_visit_time DATETIME DEFAULT NULL COMMENT '最后访问时间',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='累计访客统计表'
";

if (mysqli_query($connect, $createTotalTable)) {
    $messages[] = "✓ 创建表: visitor_total";
} else {
    if (strpos(mysqli_error($connect), 'already exists') !== false) {
        $messages[] = "○ 表已存在: visitor_total";
    } else {
        $messages[] = "✗ 创建表失败: visitor_total - " . mysqli_error($connect);
    }
}

// 4. 初始化累计访客表（如果为空）
$checkTotal = mysqli_query($connect, "SELECT COUNT(*) as c FROM visitor_total");
$row = mysqli_fetch_assoc($checkTotal);
if ($row['c'] == 0) {
    mysqli_query($connect, "INSERT INTO visitor_total (total_visits, total_visitors) VALUES (0, 0)");
    $messages[] = "✓ 初始化: visitor_total";
} else {
    $messages[] = "○ 已初始化: visitor_total";
}

// 5. 创建 photo_images 相册图片表（规范化 photo.img 换行分隔存储）
$createPhotoImages = "
CREATE TABLE IF NOT EXISTS photo_images (
    id INT(11) NOT NULL AUTO_INCREMENT,
    photo_id INT(11) NOT NULL COMMENT '相册ID',
    photo_code VARCHAR(20) NOT NULL DEFAULT '' COMMENT '相册code',
    img_url VARCHAR(1000) NOT NULL COMMENT '图片/视频URL',
    img_thumb VARCHAR(1000) NOT NULL DEFAULT '' COMMENT '缩略图URL',
    img_type TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0:图片 1:视频',
    img_text VARCHAR(500) NOT NULL DEFAULT '' COMMENT '图片描述',
    location VARCHAR(100) NOT NULL DEFAULT '' COMMENT '拍摄地点',
    lng DECIMAL(11,6) NOT NULL DEFAULT 0 COMMENT '经度',
    lat DECIMAL(10,6) NOT NULL DEFAULT 0 COMMENT '纬度',
    sort_order INT(11) NOT NULL DEFAULT 0 COMMENT '排序',
    views INT(11) NOT NULL DEFAULT 0 COMMENT '浏览数',
    likes INT(11) NOT NULL DEFAULT 0 COMMENT '点赞数',
    date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '拍摄时间',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_photo_images_photo_id (photo_id),
    KEY idx_photo_images_photo_code (photo_code),
    KEY idx_photo_images_sort (sort_order),
    KEY idx_photo_images_date (date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='相册图片明细表'
";

if (mysqli_query($connect, $createPhotoImages)) {
    $messages[] = "✓ 创建表: photo_images";
    // 迁移现有 photo.img 中的换行分隔图片
    $photoResult = mysqli_query($connect, "SELECT id, code, img, date, location, lng, lat FROM photo WHERE img != ''");
    if ($photoResult && mysqli_num_rows($photoResult) > 0) {
        $migratedCount = 0;
        while ($photoRow = mysqli_fetch_assoc($photoResult)) {
            $imgLines = explode("\n", $photoRow['img']);
            $sortOrder = 1;
            foreach ($imgLines as $line) {
                $line = trim($line);
                if (!empty($line)) {
                    $isVideo = preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', $line) ? 1 : 0;
                    $imgUrl = mysqli_real_escape_string($connect, $line);
                    $photoCode = mysqli_real_escape_string($connect, $photoRow['code']);
                    $photoDate = !empty($photoRow['date']) ? "'" . mysqli_real_escape_string($connect, $photoRow['date']) . "'" : 'NOW()';
                    $location = mysqli_real_escape_string($connect, $photoRow['location'] ?? '');
                    $lng = $photoRow['lng'] ?? 0;
                    $lat = $photoRow['lat'] ?? 0;
                    // 避免重复插入
                    $checkExist = mysqli_query($connect, "SELECT id FROM photo_images WHERE photo_id = {$photoRow['id']} AND img_url = '$imgUrl' LIMIT 1");
                    if ($checkExist && mysqli_num_rows($checkExist) == 0) {
                        mysqli_query($connect, "INSERT INTO photo_images (photo_id, photo_code, img_url, img_type, location, lng, lat, sort_order, date) VALUES ({$photoRow['id']}, '$photoCode', '$imgUrl', $isVideo, '$location', $lng, $lat, $sortOrder, $photoDate)");
                        $migratedCount++;
                    }
                    $sortOrder++;
                }
            }
        }
        if ($migratedCount > 0) {
            $messages[] = "✓ 迁移 photo.img 到 photo_images：共 $migratedCount 条记录";
        }
    }
} else {
    $err = mysqli_error($connect);
    if (strpos($err, 'already exists') !== false) {
        $messages[] = "○ 表已存在: photo_images";
    } else {
        $messages[] = "✗ 创建表失败: photo_images - $err";
    }
}

// 6. 创建 little_images 点滴图片表（规范化 little.text 中的 img 标签）
$createLittleImages = "
CREATE TABLE IF NOT EXISTS little_images (
    id INT(11) NOT NULL AUTO_INCREMENT,
    little_id INT(11) NOT NULL COMMENT '点滴ID',
    img_url VARCHAR(1000) NOT NULL COMMENT '图片URL',
    img_alt VARCHAR(500) NOT NULL DEFAULT '' COMMENT '图片alt文本',
    sort_order INT(11) NOT NULL DEFAULT 0 COMMENT '在正文中的顺序',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_little_images_little_id (little_id),
    KEY idx_little_images_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='点滴文章图片索引表'
";

if (mysqli_query($connect, $createLittleImages)) {
    $messages[] = "✓ 创建表: little_images";
    // 迁移 little.text 中的 <img> 标签
    $littleResult = mysqli_query($connect, "SELECT id, text FROM little WHERE text LIKE '%<img%'");
    if ($littleResult && mysqli_num_rows($littleResult) > 0) {
        $migratedCount = 0;
        while ($littleRow = mysqli_fetch_assoc($littleResult)) {
            if (preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*alt=[\'"]([^\'"]*)[\'"]/i', $littleRow['text'], $matches, PREG_SET_ORDER)) {
                $sortOrder = 1;
                foreach ($matches as $match) {
                    $imgUrl = mysqli_real_escape_string($connect, $match[1]);
                    $imgAlt = isset($match[2]) ? mysqli_real_escape_string($connect, $match[2]) : '';
                    $checkExist = mysqli_query($connect, "SELECT id FROM little_images WHERE little_id = {$littleRow['id']} AND img_url = '$imgUrl' LIMIT 1");
                    if ($checkExist && mysqli_num_rows($checkExist) == 0) {
                        mysqli_query($connect, "INSERT INTO little_images (little_id, img_url, img_alt, sort_order) VALUES ({$littleRow['id']}, '$imgUrl', '$imgAlt', $sortOrder)");
                        $migratedCount++;
                    }
                    $sortOrder++;
                }
            }
        }
        if ($migratedCount > 0) {
            $messages[] = "✓ 迁移 little.text 中的图片到 little_images：共 $migratedCount 张图片";
        }
    }
} else {
    $err = mysqli_error($connect);
    if (strpos($err, 'already exists') !== false) {
        $messages[] = "○ 表已存在: little_images";
    } else {
        $messages[] = "✗ 创建表失败: little_images - $err";
    }
}

echo "<!DOCTYPE html>";
echo "<html><head>";
echo "<meta charset='UTF-8'>";
echo "<title>数据库更新</title>";
echo "<style>
    body { font-family: 'Segoe UI', sans-serif; background: #f5f5f5; padding: 40px; }
    .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h1 { color: #333; margin-bottom: 20px; }
    .msg { padding: 10px 15px; margin: 5px 0; border-radius: 6px; font-size: 14px; }
    .success { background: #d4edda; color: #155724; }
    .error { background: #f8d7da; color: #721c24; }
    .info { background: #fff3cd; color: #856404; }
    .back { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; }
</style>";
echo "</head><body>";
echo "<div class='container'>";
echo "<h1>📊 数据库更新</h1>";
foreach ($messages as $msg) {
    $class = strpos($msg, '✗') !== false ? 'error' : (strpos($msg, '✓') !== false ? 'success' : 'info');
    echo "<div class='msg $class'>$msg</div>";
}
echo "<a href='index.php' class='back'>返回后台首页</a>";
echo "</div></body></html>";
?>