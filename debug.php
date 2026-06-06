<?php
/**
 * LG-NewUi 项目调试工具
 */

session_start();
if(!isset($_SESSION['loginadmin'])){
    header("Location: admin/login.php");
    exit;
}

error_reporting(0);
ini_set('display_errors', 0);

echo "<h1>❤️ LG-NewUi 调试信息</h1>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;}h2{color:#667eea;border-bottom:2px solid #eee;padding-bottom:10px;}.success{color:green;}.error{color:red;}.info{color:#667eea;}</style>";

// 1. PHP信息
echo "<h2>📊 PHP环境</h2>";
echo "<p>PHP版本: <span class='info'>" . PHP_VERSION . "</span></p>";
echo "<p>当前时间: <span class='info'>" . date('Y-m-d H:i:s') . "</span></p>";

// 2. 数据库连接
echo "<h2>🗄️ 数据库连接</h2>";
try {
    require_once 'admin/connect.php';
    if ($connect) {
        echo "<p class='success'>✅ 数据库连接成功</p>";
        
        // 检查必需的表
        $requiredTables = ['text', 'little', 'photo', 'leaving', 'lovelist'];
        echo "<p>检查数据表:</p><ul>";
        foreach ($requiredTables as $table) {
            $result = mysqli_query($connect, "SHOW TABLES LIKE '$table'");
            if (mysqli_num_rows($result) > 0) {
                echo "<li class='success'>✅ $table</li>";
            } else {
                echo "<li class='error'>❌ $table (缺失)</li>";
            }
        }
        echo "</ul>";
        
        // 检查text表数据
        $textResult = mysqli_query($connect, "SELECT boy, girl, startTime FROM text LIMIT 1");
        if ($textResult && mysqli_num_rows($textResult) > 0) {
            $text = mysqli_fetch_assoc($textResult);
            echo "<p class='success'>✅ 网站配置存在</p>";
            echo "<ul>";
            echo "<li>男方: " . htmlspecialchars($text['boy']) . "</li>";
            echo "<li>女方: " . htmlspecialchars($text['girl']) . "</li>";
            echo "<li>开始时间: " . htmlspecialchars($text['startTime']) . "</li>";
            echo "</ul>";
        } else {
            echo "<p class='error'>⚠️ 网站配置不存在，请导入数据库或进入后台设置</p>";
        }
        
    } else {
        echo "<p class='error'>❌ 数据库连接失败</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ 错误: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 3. 文件权限检查
echo "<h2>📁 文件检查</h2>";
$filesToCheck = [
    'head.php' => '入口文件',
    'admin/connect.php' => '数据库连接',
    'admin/Config_DB.php' => '数据库配置',
    'Style/' => '静态资源',
    'assets/' => '新增资源',
    'services/' => '服务接口'
];

echo "<ul>";
foreach ($filesToCheck as $file => $desc) {
    if (file_exists($file)) {
        $exists = "<span class='success'>✅ 存在</span>";
    } else {
        $exists = "<span class='error'>❌ 不存在</span>";
    }
    echo "<li>$desc ($file): $exists</li>";
}
echo "</ul>";

// 4. 检查关键文件
echo "<h2>🔧 关键文件</h2>";
$criticalFiles = [
    'services/weather.php' => '天气接口',
    'services/random_quote.php' => '语录接口',
    'services/music-player-data.php' => '音乐数据接口',
    'assets/js/lg-home-app.js' => '首页应用',
    'assets/js/lg-init.js' => '初始化脚本'
];

echo "<ul>";
foreach ($criticalFiles as $file => $desc) {
    if (file_exists($file)) {
        $exists = "<span class='success'>✅ 存在</span>";
    } else {
        $exists = "<span class='error'>❌ 不存在</span>";
    }
    echo "<li>$desc ($file): $exists</li>";
}
echo "</ul>";

// 5. 测试服务接口
echo "<h2>🌐 服务接口</h2>";
echo "<p>点击测试各个API接口:</p>";
echo "<ul>";
echo "<li><a href='services/random_quote.php' target='_blank'>测试随机语录</a></li>";
echo "<li><a href='services/weather.php' target='_blank'>测试天气接口</a></li>";
echo "<li><a href='services/music-player-data.php' target='_blank'>测试音乐数据</a></li>";
echo "<li><a href='services/moments.php' target='_blank'>测试动态内容</a></li>";
echo "<li><a href='services/info-service.php' target='_blank'>测试信息服务</a></li>";
echo "</ul>";

echo "<hr><p class='info'>💡 提示：如果还没有安装数据库，请先导入 love_db.sql 文件！</p>";
?>
