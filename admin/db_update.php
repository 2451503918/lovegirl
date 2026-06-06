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

$connect = mysqli_connect($db_address, $db_username, $db_password, $db_name);
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