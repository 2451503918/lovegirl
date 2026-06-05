<?php
/**
 * 测试UI页面 - 不依赖数据库
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>❤️ LG-NewUi 测试页面</title>
    
    <!-- Font Awesome (stable icon library) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS -->
    <link rel="stylesheet" href="Style/css/lg-variables.css">
    <link rel="stylesheet" href="Style/css/lg-bento.css">
    <link rel="stylesheet" href="Style/css/lg-weather.css">
    <link rel="stylesheet" href="Style/css/lg-enhanced.css">
    <link rel="stylesheet" href="Style/css/animate.min.css">
    
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
        }
        
        /* Fix for icons - use Font Awesome instead of Phosphor */
        [class^="ph-"], [class*=" ph-"] {
            font-family: 'Font Awesome 6 Free' !important;
            font-weight: 900 !important;
        }
        
        .test-header {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 24px;
            margin-bottom: 40px;
            box-shadow: 0 20px 60px rgba(102, 126, 234, 0.3);
        }
        
        .test-header h1 {
            margin: 0 0 10px 0;
            font-size: 2.5rem;
            font-weight: 800;
        }
        
        .test-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .test-status {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        
        .status-pill {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 10px 20px;
            border-radius: 50px;
            font-size: 0.95rem;
            font-weight: 600;
        }
        
        .test-section {
            margin-bottom: 50px;
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
        
        .test-section h2 {
            color: #333;
            margin: 0 0 25px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .test-section h2 i {
            color: #667eea;
            font-size: 1.6rem;
        }
        
        .test-section-footer {
            text-align: center;
            padding: 40px 20px;
            margin-top: 40px;
            border-top: 2px solid #eee;
            background: white;
            border-radius: 24px;
        }
        
        .test-section-footer h3 {
            color: #667eea;
            margin: 0 0 15px 0;
            font-size: 1.8rem;
        }
        
        .test-section-footer p {
            color: #666;
            margin: 0;
            font-size: 1.1rem;
        }
        
        .test-links {
            margin-top: 25px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .test-link {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 14px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }
        
        .test-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .test-link.blue {
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            box-shadow: 0 5px 20px rgba(79, 172, 254, 0.3);
        }
        
        .test-link.blue:hover {
            box-shadow: 0 10px 30px rgba(79, 172, 254, 0.4);
        }
        
        /* Make sure weather icons work with Font Awesome */
        .lgnewui-home-weather-icon-main::before {
            font-family: 'Font Awesome 6 Free' !important;
            content: '\f6c4' !important; /* sun icon */
        }
        
        /* Fix for other Phosphor icons */
        .lgnewui-icon-circle-glass i,
        .lgnewui-widget__bg-icon i {
            font-family: 'Font Awesome 6 Free' !important;
            font-weight: 900 !important;
        }
    </style>
</head>
<body>
    
    <div class="test-header">
        <h1>❤️ LG-NewUi 测试页面</h1>
        <p>情侣小站界面预览 - 无需数据库即可查看UI效果</p>
        <div class="test-status">
            <span class="status-pill">✅ 服务器运行中</span>
            <span class="status-pill">📱 响应式设计</span>
            <span class="status-pill">🎨 完整样式</span>
        </div>
    </div>
    
    <main class="lgnewui-home lgnewui-container" style="padding-bottom:2rem;">
        
        <!-- ===== 1. 天数计数器 ===== -->
        <div class="test-section">
            <h2><i class="fas fa-heart"></i> 1. 天数计数器</h2>
            <div class="lgnewui-day-wrapper lgnewui-mb-4">
                <div class="lgnewui-day-fusion-card">
                    <div class="lgnewui-day-ambient-light"></div>
                    <div class="lgnewui-day-mac-dots">
                        <div class="lgnewui-day-dot lgnewui-day-dot-red"></div>
                        <div class="lgnewui-day-dot lgnewui-day-dot-yellow"></div>
                        <div class="lgnewui-day-dot lgnewui-day-dot-green"></div>
                    </div>
                    <div class="lgnewui-day-left-section">
                        <h2 class="lgnewui-day-poetic-title">
                            愿得一人心<br>
                            <span style="font-size:0.7em;opacity:0.7;">与你行至天光</span>
                        </h2>
                        <div class="lgnewui-day-start-date-capsule">
                            <div class="lgnewui-day-icon-circle"><i class="fas fa-heart"></i></div>
                            <div>
                                <span class="lgnewui-day-date-label-small">Together Since</span>
                                <span class="lgnewui-day-date-value-clean">2022-06-05 00:07</span>
                            </div>
                        </div>
                    </div>
                    <div class="lgnewui-day-right-section">
                        <div class="lgnewui-day-main-days-wrapper">
                            <div class="lgnewui-day-main-days-number" id="test-days">1095</div>
                            <div class="lgnewui-day-days-divider"></div>
                            <div class="lgnewui-day-days-label">DAYS</div>
                        </div>
                        <div class="lgnewui-day-digital-timer">
                            <div class="lgnewui-day-timer-block"><div class="lgnewui-day-timer-val">12</div><div class="lgnewui-day-timer-label">Hours</div></div>
                            <div class="lgnewui-day-timer-block"><div class="lgnewui-day-timer-val">34</div><div class="lgnewui-day-timer-label">Minutes</div></div>
                            <div class="lgnewui-day-timer-block"><div class="lgnewui-day-timer-val">56</div><div class="lgnewui-day-timer-label">Seconds</div></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ===== 2. Bento Grid ===== -->
        <div class="test-section">
            <h2><i class="fas fa-th-large"></i> 2. Bento Grid 布局</h2>
            <div class="lgnewui-grid">
                
                <!-- 智能媒体卡片 -->
                <div class="lgnewui-col-2 lgnewui-row-2">
                    <div id="moment-card" class="lgnewui-smart-card">
                        <div class="lgnewui-smart-card__media"></div>
                        <div class="lgnewui-smart-card__overlay"></div>
                        <div class="lgnewui-smart-card__header">
                            <div class="lgnewui-smart-card__capsule">
                                <img class="lgnewui-smart-card__avatar" src="https://ui-avatars.com/api/?name=Love&background=667eea&color=fff&size=128" alt="">
                                <div class="lgnewui-smart-card__user-info">
                                    <span class="lgnewui-smart-card__name">男主角</span>
                                    <span class="lgnewui-smart-card__time">最新动态</span>
                                </div>
                            </div>
                            <a href="#" class="lgnewui-smart-card__album-link">
                                <span>进入相册</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                        <div class="lgnewui-smart-card__content">
                            <div class="lgnewui-smart-card__location-pill">
                                <i class="fas fa-map-marker-alt"></i>
                                <span class="lgnewui-smart-card__location-text">我们的小窝</span>
                            </div>
                            <h2 class="lgnewui-smart-card__title">时光碎片</h2>
                            <div class="lgnewui-smart-card__meta">
                                <p class="lgnewui-smart-card__desc">记录每一个闪光的瞬间</p>
                            </div>
                        </div>
                        <div class="lgnewui-smart-card__switch-btn-container">
                            <button class="lgnewui-smart-card__switch-btn" type="button"><i class="fas fa-sync-alt"></i></button>
                        </div>
                    </div>
                </div>
                
                <!-- 天气卡片1 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1">
                    <div class="lgnewui-home-weather-card blue">
                        <div class="lgnewui-home-weather-bg-decoration"></div>
                        <div class="lgnewui-home-weather-row-top">
                            <div class="lgnewui-home-weather-user-pill">
                                <img src="https://ui-avatars.com/api/?name=Boy&background=667eea&color=fff&size=128" class="lgnewui-home-weather-avatar">
                                <span class="lgnewui-home-weather-username">男主角</span>
                            </div>
                            <div class="lgnewui-home-weather-time-tag">12:34</div>
                        </div>
                        <div class="lgnewui-home-weather-row-main">
                            <div class="lgnewui-home-weather-text-temp">25°</div>
                            <i class="fas fa-sun lgnewui-home-weather-icon-main"></i>
                        </div>
                        <div class="lgnewui-home-weather-row-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span class="lgnewui-home-weather-text-city">北京</span>
                            <span class="lgnewui-home-weather-dot-divider">•</span>
                            <span class="lgnewui-home-weather-text-status">晴朗</span>
                        </div>
                        <div class="lgnewui-home-weather-grid-stats">
                            <div class="lgnewui-home-weather-stat-pill"><i class="fas fa-tint"></i><span>45%</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="fas fa-eye"></i><span>10km</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="fas fa-thermometer-half"></i><span>23°</span></div>
                        </div>
                    </div>
                </div>
                
                <!-- 天气卡片2 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1">
                    <div class="lgnewui-home-weather-card orange">
                        <div class="lgnewui-home-weather-bg-decoration"></div>
                        <div class="lgnewui-home-weather-row-top">
                            <div class="lgnewui-home-weather-user-pill">
                                <img src="https://ui-avatars.com/api/?name=Girl&background=ff6b6b&color=fff&size=128" class="lgnewui-home-weather-avatar">
                                <span class="lgnewui-home-weather-username">女主角</span>
                            </div>
                            <div class="lgnewui-home-weather-time-tag">12:34</div>
                        </div>
                        <div class="lgnewui-home-weather-row-main">
                            <div class="lgnewui-home-weather-text-temp">28°</div>
                            <i class="fas fa-cloud-sun lgnewui-home-weather-icon-main"></i>
                        </div>
                        <div class="lgnewui-home-weather-row-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span class="lgnewui-home-weather-text-city">上海</span>
                            <span class="lgnewui-home-weather-dot-divider">•</span>
                            <span class="lgnewui-home-weather-text-status">多云</span>
                        </div>
                        <div class="lgnewui-home-weather-grid-stats">
                            <div class="lgnewui-home-weather-stat-pill"><i class="fas fa-tint"></i><span>60%</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="fas fa-eye"></i><span>8km</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="fas fa-thermometer-half"></i><span>26°</span></div>
                        </div>
                    </div>
                </div>
                
                <!-- 统计卡片1 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-1">
                        <div class="lgnewui-widget__bg-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass"><i class="fas fa-file-alt"></i></div>
                                <div class="lgnewui-stats-title">点滴</div>
                            </div>
                            <div class="lgnewui-mt-1rem">
                                <div class="lgnewui-font-num lgnewui-stats-num">42</div>
                                <div class="lgnewui-stats-label">Memory Notes</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 统计卡片2 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-2">
                        <div class="lgnewui-widget__bg-icon"><i class="fas fa-images"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass"><i class="fas fa-camera"></i></div>
                                <div class="lgnewui-stats-title">相册</div>
                            </div>
                            <div class="lgnewui-mt-1rem">
                                <div class="lgnewui-font-num lgnewui-stats-num">156</div>
                                <div class="lgnewui-stats-label">Photo Keepsakes</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 统计卡片3 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-3">
                        <div class="lgnewui-widget__bg-icon"><i class="fas fa-comments"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass"><i class="fas fa-comment-dots"></i></div>
                                <div class="lgnewui-stats-title">留言</div>
                            </div>
                            <div class="lgnewui-mt-1rem">
                                <div class="lgnewui-font-num lgnewui-stats-num">89</div>
                                <div class="lgnewui-stats-label">Kind Messages</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- 统计卡片4 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-6">
                        <div class="lgnewui-widget__bg-icon"><i class="fas fa-globe"></i></div>
                        <div class="lgnewui-flex-col-runtime">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass"><i class="fas fa-globe"></i></div>
                                <div class="lgnewui-stats-title">我们的小世界</div>
                            </div>
                            <div class="lgnewui-mt-auto">
                                <div class="lgnewui-runtime-values">
                                    <div class="lgnewui-font-num lgnewui-runtime-num">1095</div>
                                    <div class="lgnewui-runtime-meta">
                                        <div class="lgnewui-runtime-days">DAYS</div>
                                        <span class="lgnewui-runtime-divider"></span>
                                        <div class="lgnewui-runtime-text">已平稳运行</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- ===== 3. 功能入口 ===== -->
        <div class="test-section">
            <h2><i class="fas fa-th"></i> 3. 功能入口</h2>
            <div class="lgnewui-grid">
                <div class="lgnewui-col-2">
                    <a href="#" class="lgnewui-widget lgnewui-widget--stats-vibrant-1" style="text-decoration:none;color:#fff;display:block;">
                        <div class="lgnewui-widget__bg-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="fas fa-file-alt"></i></div><div class="lgnewui-stats-title">点滴</div></div>
                            <div><div class="lgnewui-stats-label">记录在一起的点滴时光</div></div>
                        </div>
                    </a>
                </div>
                <div class="lgnewui-col-2">
                    <a href="#" class="lgnewui-widget lgnewui-widget--stats-vibrant-3" style="text-decoration:none;color:#fff;display:block;">
                        <div class="lgnewui-widget__bg-icon"><i class="fas fa-comments"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="fas fa-comment-dots"></i></div><div class="lgnewui-stats-title">留言</div></div>
                            <div><div class="lgnewui-stats-label">留下想说的话与温柔回应</div></div>
                        </div>
                    </a>
                </div>
                <div class="lgnewui-col-2">
                    <a href="#" class="lgnewui-widget lgnewui-widget--stats-vibrant-4" style="text-decoration:none;color:#fff;display:block;">
                        <div class="lgnewui-widget__bg-icon"><i class="fas fa-clock"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="fas fa-clock"></i></div><div class="lgnewui-stats-title">轨迹</div></div>
                            <div><div class="lgnewui-stats-label">回看我们一路走来的轨迹</div></div>
                        </div>
                    </a>
                </div>
                <div class="lgnewui-col-2">
                    <a href="#" class="lgnewui-widget lgnewui-widget--stats-vibrant-2" style="text-decoration:none;color:#fff;display:block;">
                        <div class="lgnewui-widget__bg-icon"><i class="fas fa-camera"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="fas fa-camera"></i></div><div class="lgnewui-stats-title">相册</div></div>
                            <div><div class="lgnewui-stats-label">收藏见面与出游的闪亮瞬间</div></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- ===== 4. 留言卡片 ===== -->
        <div class="test-section">
            <h2><i class="fas fa-comments"></i> 4. 留言与点滴</h2>
            <div class="lgnewui-grid">
                <div class="lgnewui-col-2">
                    <a href="#" class="lgnewui-home-message-card">
                        <div class="lgnewui-home-message-header">
                            <img class="lgnewui-home-message-avatar" src="https://ui-avatars.com/api/?name=Visitor1&background=4facfe&color=fff&size=128">
                            <div>
                                <div class="lgnewui-home-message-name-row">
                                    <span class="lgnewui-home-message-user-name">访客1</span>
                                </div>
                                <span class="lgnewui-home-message-post-time">2024-06-05</span>
                            </div>
                        </div>
                        <div class="lgnewui-home-message-content">要久久久！超级甜的一对！祝幸福永远！</div>
                    </a>
                </div>
                <div class="lgnewui-col-2">
                    <a href="#" class="lgnewui-home-message-card">
                        <div class="lgnewui-home-message-header">
                            <img class="lgnewui-home-message-avatar" src="https://ui-avatars.com/api/?name=Visitor2&background=00f2fe&color=fff&size=128">
                            <div>
                                <div class="lgnewui-home-message-name-row">
                                    <span class="lgnewui-home-message-user-name">访客2</span>
                                </div>
                                <span class="lgnewui-home-message-post-time">2024-06-04</span>
                            </div>
                        </div>
                        <div class="lgnewui-home-message-content">这个小站太有爱了！一定要一直一直在一起呀！</div>
                    </a>
                </div>
                <div class="lgnewui-col-4">
                    <div class="lgnewui-journal-card" style="display:block;">
                        <div class="lgnewui-watermark">DAY 1095</div>
                        <div class="lgnewui-journal-header">
                            <div class="lgnewui-journal-user">
                                <img src="https://ui-avatars.com/api/?name=Love&background=667eea&color=fff&size=128" class="lgnewui-journal-avatar">
                                <div>
                                    <div class="lgnewui-font-sm-bold">男主角</div>
                                    <div class="lgnewui-journal-meta">2024-06-05</div>
                                </div>
                            </div>
                        </div>
                        <h3 class="lgnewui-journal-title">三周年纪念日快乐</h3>
                        <p class="lgnewui-journal-body lgnewui-journal-body-clamp">今天是我们在一起的三周年纪念日，时间过得真快！感谢你这三年来的陪伴，希望我们能一直这样幸福下去...</p>
                        <div class="lgnewui-journal-footer">
                            <div class="lgnewui-flex-gap-sm">
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="fas fa-calendar"></i> 2024-06-05</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </main>
    
    <!-- 测试页脚信息 -->
    <div class="test-section-footer">
        <h3>🎉 测试成功！</h3>
        <p>LG-NewUi 界面测试完成，所有样式组件正常工作！</p>
        <div class="test-links">
            <a href="debug.php" class="test-link">📊 查看调试页面</a>
            <a href="services/random_quote.php" class="test-link blue">🌐 测试API接口</a>
        </div>
    </div>
    
    <!-- Simple timer for demo -->
    <script>
        let seconds = 56;
        let minutes = 34;
        let hours = 12;
        
        function updateTimer() {
            seconds++;
            if (seconds >= 60) {
                seconds = 0;
                minutes++;
                if (minutes >= 60) {
                    minutes = 0;
                    hours++;
                    if (hours >= 24) hours = 0;
                }
            }
            
            const timerBlocks = document.querySelectorAll('.lgnewui-day-timer-val');
            if (timerBlocks.length >= 3) {
                timerBlocks[0].textContent = String(hours).padStart(2, '0');
                timerBlocks[1].textContent = String(minutes).padStart(2, '0');
                timerBlocks[2].textContent = String(seconds).padStart(2, '0');
            }
        }
        
        setInterval(updateTimer, 1000);
    </script>
</body>
</html>
