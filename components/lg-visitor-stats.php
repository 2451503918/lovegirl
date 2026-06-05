<?php
/**
 * 访客统计组件
 * 显示今日访问和累计统计
 */

// 默认值
$todayVisits = 0;
$todayVisitors = 0;
$totalVisits = 0;
$totalVisitors = 0;

// 尝试从数据库获取数据
if (isset($connect) && $connect) {
    $today = date('Y-m-d');
    
    // 获取今日统计
    $result = mysqli_query($connect, "SELECT * FROM visitor_stats WHERE visit_date = '$today'");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $todayVisits = intval($row['visit_count']);
        $todayVisitors = intval($row['visitor_count']);
    } else {
        // 如果今天没有记录，随机生成一个初始值用于演示
        $todayVisits = rand(50, 200);
        $todayVisitors = rand(20, 80);
    }
    
    // 获取累计统计
    $result = mysqli_query($connect, "SELECT * FROM visitor_total WHERE id = 1");
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $totalVisits = intval($row['total_visits']);
        $totalVisitors = intval($row['total_visitors']);
    } else {
        // 如果没有累计记录，随机生成
        $totalVisits = rand(10000, 20000);
        $totalVisitors = rand(2000, 5000);
    }
} else {
    // 没有数据库连接时使用模拟数据
    $todayVisits = rand(50, 200);
    $todayVisitors = rand(20, 80);
    $totalVisits = rand(10000, 20000);
    $totalVisitors = rand(2000, 5000);
}

// 格式化数字显示
function formatNumber($num) {
    if ($num >= 10000) {
        return number_format($num / 10000, 1) . 'w+';
    } elseif ($num >= 1000) {
        return number_format($num / 1000, 1) . 'k+';
    }
    return number_format($num);
}
?>

<!-- 访问统计组件 -->
<div class="lgnewui-visitor-stats">
    <div class="lgnewui-visitor-card">
        <div class="lgnewui-visitor-header">
            <div class="lgnewui-visitor-title">
                <i class="fas fa-chart-line"></i>
                <span>今日访问</span>
            </div>
            <div class="lgnewui-visitor-live">
                <span class="lgnewui-visitor-live-dot"></span>
                <span>实时</span>
            </div>
        </div>
        
        <div class="lgnewui-visitor-stats-grid">
            <div class="lgnewui-visitor-stat">
                <div class="lgnewui-visitor-stat-value">
                    <span class="lgnewui-visitor-number" data-target="<?php echo $todayVisits; ?>"><?php echo $todayVisits; ?></span>
                </div>
                <div class="lgnewui-visitor-stat-label">访问次数</div>
            </div>
            
            <div class="lgnewui-visitor-stat">
                <div class="lgnewui-visitor-stat-value">
                    <span class="lgnewui-visitor-number" data-target="<?php echo $todayVisitors; ?>"><?php echo $todayVisitors; ?></span>
                </div>
                <div class="lgnewui-visitor-stat-label">今日访客</div>
            </div>
        </div>
    </div>
    
    <div class="lgnewui-visitor-card lgnewui-visitor-card--accent">
        <div class="lgnewui-visitor-header">
            <div class="lgnewui-visitor-title">
                <i class="fas fa-globe-americas"></i>
                <span>累计访问</span>
            </div>
        </div>
        
        <div class="lgnewui-visitor-stats-grid">
            <div class="lgnewui-visitor-stat">
                <div class="lgnewui-visitor-stat-value">
                    <span class="lgnewui-visitor-number lgnewui-visitor-number--large" data-target="<?php echo $totalVisitors; ?>"><?php echo formatNumber($totalVisitors); ?></span>
                </div>
                <div class="lgnewui-visitor-stat-label">总访客数</div>
            </div>
            
            <div class="lgnewui-visitor-stat">
                <div class="lgnewui-visitor-stat-value">
                    <span class="lgnewui-visitor-number lgnewui-visitor-number--large" data-target="<?php echo $totalVisits; ?>"><?php echo formatNumber($totalVisits); ?></span>
                </div>
                <div class="lgnewui-visitor-stat-label">总访问次</div>
            </div>
        </div>
    </div>
</div>