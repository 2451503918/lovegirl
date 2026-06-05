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
            min-height: 100vh;
        }
        
        /* ============================================
           完整的 Bento Grid 布局修复
           ============================================ */
        
        /* 修复Grid容器 */
        .lgnewui-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* 修复Grid主容器 */
        .lgnewui-grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 20px !important;
            margin-bottom: 20px !important;
            align-items: stretch !important;
        }
        
        /* 修复列跨度 */
        .lgnewui-col-2 { 
            grid-column: span 2 !important; 
        }
        .lgnewui-col-4 { 
            grid-column: span 4 !important; 
        }
        .lgnewui-col-md-1 { 
            grid-column: span 2 !important; 
        }
        
        /* 修复行跨度 */
        .lgnewui-row-2 { 
            grid-row: span 2 !important; 
        }
        
        /* 确保Grid项目高度一致 */
        .lgnewui-grid > * {
            min-height: 100%;
        }
        
        /* ============================================
           响应式布局修复
           ============================================ */
        @media (max-width: 1024px) {
            .lgnewui-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 16px !important;
            }
            .lgnewui-col-2 {
                grid-column: span 2 !important;
            }
            .lgnewui-col-md-1 {
                grid-column: span 1 !important;
            }
            .lgnewui-row-2 {
                grid-row: span 1 !important;
            }
        }
        
        @media (max-width: 768px) {
            .lgnewui-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 12px !important;
            }
            .lgnewui-container {
                padding: 0 16px;
            }
        }
        
        @media (max-width: 480px) {
            .lgnewui-grid {
                grid-template-columns: 1fr !important;
                gap: 12px !important;
            }
            .lgnewui-col-2, 
            .lgnewui-col-4,
            .lgnewui-col-md-1 {
                grid-column: span 1 !important;
            }
            .lgnewui-row-2 {
                grid-row: span 1 !important;
            }
        }
        
        /* ============================================
           卡片样式修复
           ============================================ */
        
        /* 智能媒体卡片 */
        .lgnewui-smart-card {
            height: 100%;
            min-height: 400px;
            position: relative;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 24px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            color: white;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.3);
        }
        
        .lgnewui-smart-card__media {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y=".9em" font-size="80" opacity="0.1">❤️</text></svg>') center/cover;
        }
        
        .lgnewui-smart-card__overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(180deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.1) 100%);
        }
        
        .lgnewui-smart-card__header {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: auto;
        }
        
        .lgnewui-smart-card__capsule {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 8px 16px 8px 8px;
            border-radius: 50px;
        }
        
        .lgnewui-smart-card__avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .lgnewui-smart-card__user-info {
            display: flex;
            flex-direction: column;
        }
        
        .lgnewui-smart-card__name {
            font-weight: 700;
            font-size: 14px;
        }
        
        .lgnewui-smart-card__time {
            font-size: 11px;
            opacity: 0.8;
        }
        
        .lgnewui-smart-card__album-link {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
        }
        
        .lgnewui-smart-card__content {
            position: relative;
            z-index: 2;
            margin-top: auto;
        }
        
        .lgnewui-smart-card__location-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            margin-bottom: 12px;
        }
        
        .lgnewui-smart-card__title {
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 8px 0;
        }
        
        .lgnewui-smart-card__desc {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }
        
        .lgnewui-smart-card__switch-btn-container {
            position: absolute;
            bottom: 24px;
            right: 24px;
            z-index: 2;
        }
        
        .lgnewui-smart-card__switch-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: none;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }
        
        /* 天气卡片 */
        .lgnewui-home-weather-card {
            height: 100%;
            min-height: 200px;
            position: relative;
            border-radius: 24px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .lgnewui-home-weather-card.blue {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .lgnewui-home-weather-card.orange {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .lgnewui-home-weather-bg-decoration {
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }
        
        .lgnewui-home-weather-row-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
        }
        
        .lgnewui-home-weather-user-pill {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .lgnewui-home-weather-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .lgnewui-home-weather-username {
            font-weight: 600;
            font-size: 14px;
        }
        
        .lgnewui-home-weather-time-tag {
            background: rgba(255,255,255,0.2);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        
        .lgnewui-home-weather-row-main {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
        }
        
        .lgnewui-home-weather-text-temp {
            font-size: 42px;
            font-weight: 800;
        }
        
        .lgnewui-home-weather-icon-main {
            font-size: 48px;
            opacity: 0.8;
        }
        
        .lgnewui-home-weather-row-location {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 12px;
            position: relative;
            z-index: 1;
            font-size: 13px;
        }
        
        .lgnewui-home-weather-dot-divider {
            opacity: 0.6;
        }
        
        .lgnewui-home-weather-grid-stats {
            display: flex;
            gap: 8px;
            position: relative;
            z-index: 1;
        }
        
        .lgnewui-home-weather-stat-pill {
            flex: 1;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 6px 8px;
            border-radius: 10px;
            font-size: 11px;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        
        /* 统计卡片 */
        .lgnewui-widget {
            height: 100%;
            min-height: 140px;
            position: relative;
            border-radius: 24px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .lgnewui-widget:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }
        
        .lgnewui-widget__bg-icon {
            position: absolute;
            top: -20px;
            right: -20px;
            font-size: 100px;
            opacity: 0.15;
        }
        
        .lgnewui-widget--stats-vibrant-1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .lgnewui-widget--stats-vibrant-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        
        .lgnewui-widget--stats-vibrant-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .lgnewui-widget--stats-vibrant-4 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
        }
        
        .lgnewui-widget--stats-vibrant-5 {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            color: white;
        }
        
        .lgnewui-widget--stats-vibrant-6 {
            background: linear-gradient(135deg, #0c3483 0%, #a2b6df 100%);
            color: white;
        }
        
        .lgnewui-flex-col-between-1 {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
            position: relative;
            z-index: 1;
        }
        
        .lgnewui-flex-col-runtime {
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
            z-index: 1;
        }
        
        .lgnewui-stats-header-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .lgnewui-icon-circle-glass {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .lgnewui-stats-title {
            font-weight: 700;
            font-size: 16px;
        }
        
        .lgnewui-mt-1rem {
            margin-top: 1rem;
        }
        
        .lgnewui-mt-auto {
            margin-top: auto;
        }
        
        .lgnewui-font-num {
            font-weight: 900;
        }
        
        .lgnewui-stats-num {
            font-size: 42px;
            line-height: 1;
        }
        
        .lgnewui-runtime-num {
            font-size: 36px;
            line-height: 1;
        }
        
        .lgnewui-stats-label {
            font-size: 12px;
            opacity: 0.8;
            margin-top: 4px;
        }
        
        .lgnewui-runtime-values {
            display: flex;
            align-items: baseline;
            gap: 12px;
        }
        
        .lgnewui-runtime-meta {
            display: flex;
            flex-direction: column;
        }
        
        .lgnewui-runtime-days {
            font-weight: 700;
            font-size: 14px;
        }
        
        .lgnewui-runtime-divider {
            display: none;
        }
        
        .lgnewui-runtime-text {
            font-size: 12px;
            opacity: 0.8;
        }
        
        /* 留言卡片 */
        .lgnewui-home-message-card {
            height: 100%;
            min-height: 120px;
            background: white;
            border-radius: 20px;
            padding: 20px;
            display: block;
            text-decoration: none;
            color: inherit;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .lgnewui-home-message-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        
        .lgnewui-home-message-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .lgnewui-home-message-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .lgnewui-home-message-name-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .lgnewui-home-message-user-name {
            font-weight: 700;
            font-size: 14px;
            color: #333;
        }
        
        .lgnewui-home-message-post-time {
            font-size: 12px;
            color: #999;
        }
        
        .lgnewui-home-message-content {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }
        
        /* 日记卡片 */
        .lgnewui-journal-card {
            height: 100%;
            min-height: 200px;
            background: white;
            border-radius: 20px;
            padding: 20px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .lgnewui-journal-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }
        
        .lgnewui-watermark {
            position: absolute;
            top: -20px;
            right: -20px;
            font-size: 80px;
            font-weight: 900;
            color: rgba(102, 126, 234, 0.08);
            transform: rotate(-15deg);
            pointer-events: none;
        }
        
        .lgnewui-journal-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }
        
        .lgnewui-journal-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .lgnewui-font-sm-bold {
            font-weight: 700;
            font-size: 14px;
            color: #333;
        }
        
        .lgnewui-journal-meta {
            font-size: 12px;
            color: #999;
        }
        
        .lgnewui-journal-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin: 0 0 8px 0;
        }
        
        .lgnewui-journal-body {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
            margin: 0 0 12px 0;
        }
        
        .lgnewui-journal-body-clamp {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .lgnewui-journal-footer {
            margin-top: auto;
        }
        
        .lgnewui-flex-gap-sm {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .lgnewui-chip {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .lgnewui-chip--light {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }
        
        /* Fix for icons */
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
            margin-bottom: 40px;
            background: rgba(255,255,255,0.9);
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
    
    <main class="lgnewui-container">
        
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
                
                <!-- 智能媒体卡片 - 占2列2行 -->
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
                <div class="lgnewui-col-md-1">
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
                <div class="lgnewui-col-md-1">
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
                <div class="lgnewui-col-md-1">
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
                <div class="lgnewui-col-md-1">
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
                <div class="lgnewui-col-md-1">
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
                <div class="lgnewui-col-md-1">
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
