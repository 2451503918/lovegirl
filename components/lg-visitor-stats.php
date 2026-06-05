<?php
/**
 * 访问统计组件
 * 显示今日访问和累计统计
 */

// 模拟统计数据（实际应该从数据库读取）
// 今日访问统计
$todayVisits = isset($_SESSION['today_visits']) ? $_SESSION['today_visits'] : rand(50, 200);
$todayVisitors = isset($_SESSION['today_visitors']) ? $_SESSION['today_visitors'] : rand(20, 80);

// 累计统计
$totalVisits = isset($_SESSION['total_visits']) ? $_SESSION['total_visits'] : rand(10000, 20000);
$totalVisitors = isset($_SESSION['total_visitors']) ? $_SESSION['total_visitors'] : rand(2000, 5000);

// 格式化数字显示
function formatNumber($num) {
    if ($num >= 10000) {
        return number_format($num / 10000, 1) . 'w+';
    } elseif ($num >= 1000) {
        return number_format($num / 1000, 1) . 'k+';
    }
    return number_format($num);
}

// 递增今日访问（模拟）
if (!isset($_SESSION['last_visit_date']) || $_SESSION['last_visit_date'] !== date('Y-m-d')) {
    $_SESSION['today_visits'] = rand(50, 200);
    $_SESSION['today_visitors'] = rand(20, 80);
    $_SESSION['last_visit_date'] = date('Y-m-d');
} else {
    $_SESSION['today_visits']++;
    if (rand(1, 10) > 7) { // 10%概率增加访客
        $_SESSION['today_visitors']++;
    }
}

$todayVisits = $_SESSION['today_visits'];
$todayVisitors = $_SESSION['today_visitors'];
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

<style>
/* 访问统计卡片样式 */
.lgnewui-visitor-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 20px;
}

.lgnewui-visitor-card {
    background: white;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.lgnewui-visitor-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.1);
}

.lgnewui-visitor-card--accent {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.lgnewui-visitor-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.lgnewui-visitor-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 700;
    color: #333;
}

.lgnewui-visitor-card--accent .lgnewui-visitor-title {
    color: white;
}

.lgnewui-visitor-title i {
    color: #667eea;
    font-size: 18px;
}

.lgnewui-visitor-card--accent .lgnewui-visitor-title i {
    color: white;
}

.lgnewui-visitor-live {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #666;
}

.lgnewui-visitor-live-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #27c93f;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.2); }
}

.lgnewui-visitor-stats-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
}

.lgnewui-visitor-stat {
    text-align: center;
    padding: 12px;
    background: rgba(0,0,0,0.03);
    border-radius: 12px;
}

.lgnewui-visitor-card--accent .lgnewui-visitor-stat {
    background: rgba(255,255,255,0.15);
}

.lgnewui-visitor-stat-value {
    margin-bottom: 4px;
}

.lgnewui-visitor-number {
    font-size: 24px;
    font-weight: 900;
    color: #667eea;
    font-family: 'Inter', monospace;
}

.lgnewui-visitor-number--large {
    font-size: 32px;
}

.lgnewui-visitor-card--accent .lgnewui-visitor-number {
    color: white;
}

.lgnewui-visitor-stat-label {
    font-size: 12px;
    color: #999;
    font-weight: 600;
}

.lgnewui-visitor-card--accent .lgnewui-visitor-stat-label {
    color: rgba(255,255,255,0.8);
}

/* 移动端适配 */
@media (max-width: 768px) {
    .lgnewui-visitor-stats {
        grid-template-columns: 1fr;
    }
    
    .lgnewui-visitor-card {
        padding: 16px;
    }
    
    .lgnewui-visitor-number {
        font-size: 20px;
    }
    
    .lgnewui-visitor-number--large {
        font-size: 24px;
    }
}

@media (max-width: 480px) {
    .lgnewui-visitor-stats {
        gap: 12px;
    }
    
    .lgnewui-visitor-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .lgnewui-visitor-stats-grid {
        gap: 8px;
    }
    
    .lgnewui-visitor-stat {
        padding: 10px;
    }
}
</style>

<script>
// 数字动画效果
document.addEventListener('DOMContentLoaded', function() {
    const numbers = document.querySelectorAll('.lgnewui-visitor-number');
    
    numbers.forEach(num => {
        const target = parseInt(num.dataset.target);
        const duration = 1500;
        const start = 0;
        const startTime = performance.now();
        
        function updateNumber(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // 使用easeOutExpo缓动
            const easeOutExpo = 1 - Math.pow(2, -10 * progress);
            const current = Math.floor(start + (target - start) * easeOutExpo);
            
            num.textContent = current.toLocaleString();
            
            if (progress < 1) {
                requestAnimationFrame(updateNumber);
            }
        }
        
        requestAnimationFrame(updateNumber);
    });
    
    // 模拟实时更新
    setInterval(() => {
        const todayVisitsEl = document.querySelector('[data-target]');
        if (todayVisitsEl) {
            const current = parseInt(todayVisitsEl.textContent.replace(/,/g, ''));
            todayVisitsEl.textContent = (current + 1).toLocaleString();
        }
    }, 10000); // 每10秒增加1次访问
});
</script>
