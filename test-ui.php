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
    
    <!-- Phosphor Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS -->
    <link rel="stylesheet" href="Style/css/lg-variables.css">
    <link rel="stylesheet" href="Style/css/lg-bento.css">
    <link rel="stylesheet" href="Style/css/lg-weather.css">
    <link rel="stylesheet" href="Style/css/lg-enhanced.css">
    
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 20px;
        }
        
        .test-header {
            text-align: center;
            padding: 30px 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 20px;
            margin-bottom: 30px;
        }
        
        .test-header h1 {
            margin: 0 0 10px 0;
            font-size: 2.5rem;
        }
        
        .test-header p {
            margin: 0;
            opacity: 0.9;
        }
        
        .test-status {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
        }
        
        .status-pill {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.9rem;
        }
        
        .test-section {
            margin-bottom: 40px;
        }
        
        .test-section h2 {
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .test-section h2 i {
            color: #667eea;
        }
    </style>
</head>
<body>
    
    <div class="test-header">
        <h1>❤️ LG-NewUi 测试页面</h1>
        <p>情侣小站界面预览 - 无需数据库即可查看UI效果</p>
        <div class="test-status">
            <span class="status-pill">✅ 服务器运行中</span>
            <span class="status-pill">💡 测试模式</span>
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
                            <div class="lgnewui-day-icon-circle"><i class="ph-fill ph-heart"></i></div>
                            <div>
                                <span class="lgnewui-day-date-label-small">Together Since</span>
                                <span class="lgnewui-day-date-value-clean">2022-06-05 00:07</span>
                            </div>
                        </div>
                    </div>
                    <div class="lgnewui-day-right-section">
                        <div class="lgnewui-day-main-days-wrapper">
                            <div class="lgnewui-day-main-days-number">1095</div>
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
                                <img class="lgnewui-smart-card__avatar" src="https://q1.qlogo.cn/g?b=qq&nk=123456789&s=640" alt="">
                                <div class="lgnewui-smart-card__user-info">
                                    <span class="lgnewui-smart-card__name">男主角</span>
                                    <span class="lgnewui-smart-card__time">最新动态</span>
                                </div>
                            </div>
                            <a href="#" class="lgnewui-smart-card__album-link">
                                <span>进入相册</span>
                                <i class="ph-bold ph-arrow-right"></i>
                            </a>
                        </div>
                        <div class="lgnewui-smart-card__content">
                            <div class="lgnewui-smart-card__location-pill">
                                <i class="ph-fill ph-map-pin"></i>
                                <span class="lgnewui-smart-card__location-text">我们的小窝</span>
                            </div>
                            <h2 class="lgnewui-smart-card__title">时光碎片</h2>
                            <div class="lgnewui-smart-card__meta">
                                <p class="lgnewui-smart-card__desc">记录每一个闪光的瞬间</p>
                            </div>
                        </div>
                        <div class="lgnewui-smart-card__switch-btn-container">
                            <button class="lgnewui-smart-card__switch-btn" type="button"><i class="ph-bold ph-arrows-clockwise"></i></button>
                        </div>
                    </div>
                </div>
                
                <!-- 天气卡片1 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1">
                    <div class="lgnewui-home-weather-card blue">
                        <div class="lgnewui-home-weather-bg-decoration"></div>
                        <div class="lgnewui-home-weather-row-top">
                            <div class="lgnewui-home-weather-user-pill">
                                <img src="https://q1.qlogo.cn/g?b=qq&nk=123456789&s=640" class="lgnewui-home-weather-avatar">
                                <span class="lgnewui-home-weather-username">男主角</span>
                            </div>
                            <div class="lgnewui-home-weather-time-tag">12:34</div>
                        </div>
                        <div class="lgnewui-home-weather-row-main">
                            <div class="lgnewui-home-weather-text-temp">25°</div>
                            <i class="qi-100-fill lgnewui-home-weather-icon-main"></i>
                        </div>
                        <div class="lgnewui-home-weather-row-location">
                            <i class="ph-fill ph-map-pin"></i>
                            <span class="lgnewui-home-weather-text-city">北京</span>
                            <span class="lgnewui-home-weather-dot-divider">•</span>
                            <span class="lgnewui-home-weather-text-status">晴朗</span>
                        </div>
                        <div class="lgnewui-home-weather-grid-stats">
                            <div class="lgnewui-home-weather-stat-pill"><i class="ph-fill ph-drop"></i><span>45%</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="ph-fill ph-eye"></i><span>10km</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="ph-fill ph-thermometer"></i><span>23°</span></div>
                        </div>
                    </div>
                </div>
                
                <!-- 天气卡片2 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1">
                    <div class="lgnewui-home-weather-card orange">
                        <div class="lgnewui-home-weather-bg-decoration"></div>
                        <div class="lgnewui-home-weather-row-top">
                            <div class="lgnewui-home-weather-user-pill">
                                <img src="https://q1.qlogo.cn/g?b=qq&nk=987654321&s=640" class="lgnewui-home-weather-avatar">
                                <span class="lgnewui-home-weather-username">女主角</span>
                            </div>
                            <div class="lgnewui-home-weather-time-tag">12:34</div>
                        </div>
                        <div class="lgnewui-home-weather-row-main">
                            <div class="lgnewui-home-weather-text-temp">28°</div>
                            <i class="qi-100-fill lgnewui-home-weather-icon-main"></i>
                        </div>
                        <div class="lgnewui-home-weather-row-location">
                            <i class="ph-fill ph-map-pin"></i>
                            <span class="lgnewui-home-weather-text-city">上海</span>
                            <span class="lgnewui-home-weather-dot-divider">•</span>
                            <span class="lgnewui-home-weather-text-status">多云</span>
                        </div>
                        <div class="lgnewui-home-weather-grid-stats">
                            <div class="lgnewui-home-weather-stat-pill"><i class="ph-fill ph-drop"></i><span>60%</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="ph-fill ph-eye"></i><span>8km</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="ph-fill ph-thermometer"></i><span>26°</span></div>
                        </div>
                    </div>
                </div>
                
                <!-- 统计卡片1 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-1">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-article"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-newspaper-clipping"></i></div>
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
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-images"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-camera"></i></div>
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
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-chat-circle-dots"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-chat-teardrop-dots"></i></div>
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
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-planet"></i></div>
                        <div class="lgnewui-flex-col-runtime">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-planet"></i></div>
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
            <h2><i class="fas fa-th-large"></i> 3. 功能入口</h2>
            <div class="lgnewui-grid">
                <div class="lgnewui-col-2">
                    <a href="#" class="lgnewui-widget lgnewui-widget--stats-vibrant-1" style="text-decoration:none;color:#fff;">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-notebook"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-notebook"></i></div><div class="lgnewui-stats-title">点滴</div></div>
                            <div><div class="lgnewui-stats-label">记录在一起的点滴时光</div></div>
                        </div>
                    </a>
                </div>
                <div class="lgnewui-col-2">
                    <a href="#" class="lgnewui-widget lgnewui-widget--stats-vibrant-3" style="text-decoration:none;color:#fff;">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-chat-teardrop-dots"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-chat-teardrop-dots"></i></div><div class="lgnewui-stats-title">留言</div></div>
                            <div><div class="lgnewui-stats-label">留下想说的话与温柔回应</div></div>
                        </div>
                    </a>
                </div>
                <div class="lgnewui-col-2">
                    <a href="#" class="lgnewui-widget lgnewui-widget--stats-vibrant-4" style="text-decoration:none;color:#fff;">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-clock-countdown"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-clock-countdown"></i></div><div class="lgnewui-stats-title">轨迹</div></div>
                            <div><div class="lgnewui-stats-label">回看我们一路走来的轨迹</div></div>
                        </div>
                    </a>
                </div>
                <div class="lgnewui-col-2">
                    <a href="#" class="lgnewui-widget lgnewui-widget--stats-vibrant-2" style="text-decoration:none;color:#fff;">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-camera"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-camera"></i></div><div class="lgnewui-stats-title">相册</div></div>
                            <div><div class="lgnewui-stats-label">收藏见面与出游的闪亮瞬间</div></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- ===== 4. 留言卡片 ===== -->
        <div class="test-section">
            <h2><i class="fas fa-comments"></i> 4. 留言卡片</h2>
            <div class="lgnewui-grid">
                <div class="lgnewui-col-2">
                    <a href="#" class="lgnewui-home-message-card">
                        <div class="lgnewui-home-message-header">
                            <img class="lgnewui-home-message-avatar" src="https://q1.qlogo.cn/g?b=qq&nk=123456789&s=640">
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
                            <img class="lgnewui-home-message-avatar" src="https://q1.qlogo.cn/g?b=qq&nk=987654321&s=640">
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
                                <img src="https://q1.qlogo.cn/g?b=qq&nk=123456789&s=640" class="lgnewui-journal-avatar">
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
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-calendar-blank"></i> 2024-06-05</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </main>
    
    <!-- 测试页脚信息 -->
    <div style="text-align: center; padding: 40px 20px; margin-top: 40px; border-top: 1px solid #ddd; background: white; border-radius: 20px;">
        <h3 style="color: #667eea;">🎉 测试成功！</h3>
        <p style="color: #666;">LG-NewUi 界面测试完成，所有样式组件正常工作！</p>
        <p style="margin-top: 15px;">
            <a href="debug.php" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; margin: 0 10px;">📊 查看调试页面</a>
            <a href="services/random_quote.php" style="background: linear-gradient(135deg, #4facfe, #00f2fe); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none; margin: 0 10px;">🌐 测试API接口</a>
        </p>
    </div>
    
</body>
</html>
