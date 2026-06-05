<?php
/**
 * 测试UI页面 - 完整移动端适配版
 */
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>❤️ LG-NewUi 测试页面</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS -->
    <link rel="stylesheet" href="Style/css/lg-variables.css">
    <link rel="stylesheet" href="Style/css/lg-bento.css">
    <link rel="stylesheet" href="Style/css/lg-weather.css">
    <link rel="stylesheet" href="Style/css/lg-enhanced.css">
    
    <style>
        * {
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f7fa !important;
            background-image: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%) !important;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* ============================================
           移动端优化的 Grid 布局
           ============================================ */
        .lgnewui-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 16px;
        }
        
        .lgnewui-grid {
            display: grid !important;
            grid-template-columns: repeat(4, 1fr) !important;
            gap: 20px !important;
            align-items: stretch !important;
        }
        
        .lgnewui-col-2 { grid-column: span 2 !important; }
        .lgnewui-col-4 { grid-column: span 4 !important; }
        .lgnewui-col-md-1 { grid-column: span 2 !important; }
        .lgnewui-row-2 { grid-row: span 2 !important; }
        
        .lgnewui-grid > * {
            min-height: 100%;
        }
        
        /* ============================================
           响应式断点 - 平板和手机
           ============================================ */
        
        /* 大平板 (1024px以下) */
        @media (max-width: 1024px) {
            .lgnewui-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 16px !important;
            }
            .lgnewui-col-2,
            .lgnewui-col-md-1 {
                grid-column: span 2 !important;
            }
            .lgnewui-row-2 {
                grid-row: span 1 !important;
            }
        }
        
        /* 小平板 (768px以下) */
        @media (max-width: 768px) {
            .lgnewui-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 12px !important;
            }
            .lgnewui-col-2,
            .lgnewui-col-4,
            .lgnewui-col-md-1 {
                grid-column: span 2 !important;
            }
            .lgnewui-row-2 {
                grid-row: span 1 !important;
            }
            .lgnewui-container {
                padding: 12px;
            }
        }
        
        /* 手机 (480px以下) */
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
            .lgnewui-container {
                padding: 10px;
            }
        }
        
        /* 超小手机 (360px以下) */
        @media (max-width: 360px) {
            .lgnewui-grid {
                gap: 10px !important;
            }
        }
        
        /* ============================================
           移动端头部优化
           ============================================ */
        .mobile-header {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 16px;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .mobile-header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .mobile-logo {
            font-size: 18px;
            font-weight: 700;
        }
        
        .mobile-menu-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
        }
        
        /* 移动端导航菜单 */
        .mobile-nav {
            display: none;
            position: fixed;
            top: 50px;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            z-index: 999;
            padding: 10px 0;
            border-radius: 0 0 16px 16px;
        }
        
        .mobile-nav.active {
            display: block;
        }
        
        .mobile-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 20px;
            color: #333;
            text-decoration: none;
            font-size: 15px;
            transition: background 0.2s;
        }
        
        .mobile-nav-item:hover {
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
        }
        
        .mobile-nav-item i {
            width: 24px;
            text-align: center;
            color: #667eea;
        }
        
        @media (max-width: 768px) {
            .mobile-header {
                display: block;
            }
            .test-header {
                margin-top: 60px !important;
            }
        }
        
        /* ============================================
           测试头部优化
           ============================================ */
        .test-header {
            text-align: center;
            padding: 40px 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 24px;
            margin-bottom: 30px;
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
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        
        .status-pill {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        /* ============================================
           天数计数器 - 移动端优化
           ============================================ */
        .lgnewui-day-fusion-card {
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            border-radius: 24px;
            padding: 24px;
            color: white;
            position: relative;
            overflow: hidden;
            min-height: 300px;
        }
        
        .lgnewui-day-ambient-light {
            position: absolute;
            top: -50%;
            right: -30%;
            width: 80%;
            height: 150%;
            background: radial-gradient(circle, rgba(102,126,234,0.3) 0%, transparent 60%);
            pointer-events: none;
        }
        
        .lgnewui-day-mac-dots {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }
        
        .lgnewui-day-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }
        
        .lgnewui-day-dot-red { background: #ff5f56; }
        .lgnewui-day-dot-yellow { background: #ffbd2e; }
        .lgnewui-day-dot-green { background: #27c93f; }
        
        .lgnewui-day-left-section {
            position: relative;
            z-index: 2;
            flex: 1;
        }
        
        .lgnewui-day-poetic-title {
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 20px 0;
            line-height: 1.3;
        }
        
        .lgnewui-day-start-date-capsule {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            padding: 12px 16px;
            border-radius: 50px;
        }
        
        .lgnewui-day-icon-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        
        .lgnewui-day-date-label-small {
            display: block;
            font-size: 10px;
            opacity: 0.7;
        }
        
        .lgnewui-day-date-value-clean {
            display: block;
            font-size: 13px;
            font-weight: 600;
        }
        
        .lgnewui-day-right-section {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-top: 20px;
        }
        
        .lgnewui-day-main-days-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .lgnewui-day-main-days-number {
            font-size: 72px;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1;
        }
        
        .lgnewui-day-days-divider {
            width: 4px;
            height: 80px;
            background: linear-gradient(180deg, #667eea, #764ba2);
            border-radius: 2px;
        }
        
        .lgnewui-day-days-label {
            font-size: 24px;
            font-weight: 700;
            color: rgba(255,255,255,0.6);
        }
        
        .lgnewui-day-digital-timer {
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        
        .lgnewui-day-timer-block {
            text-align: center;
        }
        
        .lgnewui-day-timer-val {
            font-size: 28px;
            font-weight: 800;
            background: rgba(255,255,255,0.1);
            padding: 8px 12px;
            border-radius: 10px;
            min-width: 60px;
            display: block;
        }
        
        .lgnewui-day-timer-label {
            font-size: 10px;
            color: rgba(255,255,255,0.6);
            margin-top: 4px;
            display: block;
        }
        
        /* 移动端天数计数器 */
        @media (max-width: 480px) {
            .lgnewui-day-fusion-card {
                padding: 20px;
                min-height: auto;
            }
            .lgnewui-day-poetic-title {
                font-size: 22px;
            }
            .lgnewui-day-main-days-number {
                font-size: 56px;
            }
            .lgnewui-day-days-divider {
                height: 60px;
            }
            .lgnewui-day-days-label {
                font-size: 18px;
            }
            .lgnewui-day-timer-val {
                font-size: 22px;
                padding: 6px 10px;
                min-width: 50px;
            }
        }
        
        @media (max-width: 360px) {
            .lgnewui-day-main-days-number {
                font-size: 48px;
            }
            .lgnewui-day-timer-val {
                font-size: 18px;
                padding: 5px 8px;
                min-width: 45px;
            }
        }
        
        /* ============================================
           智能媒体卡片 - 移动端优化
           ============================================ */
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
            flex-wrap: wrap;
            gap: 10px;
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
        
        /* 移动端智能卡片 */
        @media (max-width: 768px) {
            .lgnewui-smart-card {
                min-height: 350px;
                padding: 20px;
            }
            .lgnewui-smart-card__title {
                font-size: 24px;
            }
            .lgnewui-smart-card__switch-btn-container {
                bottom: 20px;
                right: 20px;
            }
        }
        
        @media (max-width: 480px) {
            .lgnewui-smart-card {
                min-height: 300px;
                padding: 16px;
            }
            .lgnewui-smart-card__title {
                font-size: 20px;
            }
            .lgnewui-smart-card__desc {
                font-size: 13px;
            }
            .lgnewui-smart-card__switch-btn {
                width: 36px;
                height: 36px;
                font-size: 16px;
            }
        }
        
        /* ============================================
           天气卡片 - 移动端优化
           ============================================ */
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
        
        /* 移动端天气卡片 */
        @media (max-width: 480px) {
            .lgnewui-home-weather-card {
                min-height: 180px;
                padding: 16px;
            }
            .lgnewui-home-weather-text-temp {
                font-size: 36px;
            }
            .lgnewui-home-weather-icon-main {
                font-size: 40px;
            }
            .lgnewui-home-weather-stat-pill {
                font-size: 10px;
                padding: 5px 6px;
            }
        }
        
        /* ============================================
           统计卡片 - 移动端优化
           ============================================ */
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
        
        .lgnewui-widget--stats-vibrant-1 { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .lgnewui-widget--stats-vibrant-2 { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white; }
        .lgnewui-widget--stats-vibrant-3 { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .lgnewui-widget--stats-vibrant-4 { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white; }
        .lgnewui-widget--stats-vibrant-5 { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white; }
        .lgnewui-widget--stats-vibrant-6 { background: linear-gradient(135deg, #0c3483 0%, #a2b6df 100%); color: white; }
        
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
        
        .lgnewui-mt-1rem { margin-top: 1rem; }
        .lgnewui-mt-auto { margin-top: auto; }
        .lgnewui-font-num { font-weight: 900; }
        .lgnewui-stats-num { font-size: 42px; line-height: 1; }
        .lgnewui-runtime-num { font-size: 36px; line-height: 1; }
        .lgnewui-stats-label { font-size: 12px; opacity: 0.8; margin-top: 4px; }
        
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
        
        .lgnewui-runtime-text {
            font-size: 12px;
            opacity: 0.8;
        }
        
        /* 移动端统计卡片 */
        @media (max-width: 480px) {
            .lgnewui-widget {
                min-height: 120px;
                padding: 16px;
            }
            .lgnewui-stats-num {
                font-size: 36px;
            }
            .lgnewui-runtime-num {
                font-size: 30px;
            }
            .lgnewui-widget__bg-icon {
                font-size: 80px;
                top: -15px;
                right: -15px;
            }
            .lgnewui-icon-circle-glass {
                width: 32px;
                height: 32px;
            }
        }
        
        /* ============================================
           留言卡片 - 移动端优化
           ============================================ */
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
        
        @media (max-width: 480px) {
            .lgnewui-home-message-card {
                padding: 16px;
            }
            .lgnewui-home-message-avatar {
                width: 36px;
                height: 36px;
            }
            .lgnewui-home-message-content {
                font-size: 13px;
            }
        }
        
        /* ============================================
           日记卡片 - 移动端优化
           ============================================ */
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
        
        @media (max-width: 480px) {
            .lgnewui-journal-card {
                padding: 16px;
            }
            .lgnewui-journal-title {
                font-size: 16px;
            }
            .lgnewui-journal-body {
                font-size: 13px;
            }
            .lgnewui-watermark {
                font-size: 60px;
            }
        }
        
        /* ============================================
           Section 标题优化
           ============================================ */
        .test-section {
            margin-bottom: 30px;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
            padding: 24px;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
        }
        
        .test-section h2 {
            color: #333;
            margin: 0 0 20px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.3rem;
            font-weight: 700;
        }
        
        .test-section h2 i {
            color: #667eea;
            font-size: 1.4rem;
        }
        
        @media (max-width: 480px) {
            .test-section {
                padding: 20px;
                border-radius: 20px;
            }
            .test-section h2 {
                font-size: 1.1rem;
            }
            .test-header h1 {
                font-size: 2rem;
            }
            .test-header p {
                font-size: 1rem;
            }
        }
        
        /* ============================================
           底部链接优化
           ============================================ */
        .test-section-footer {
            text-align: center;
            padding: 30px 20px;
            margin-top: 30px;
            border-top: 2px solid #eee;
            background: white;
            border-radius: 24px;
        }
        
        .test-section-footer h3 {
            color: #667eea;
            margin: 0 0 15px 0;
            font-size: 1.6rem;
        }
        
        .test-section-footer p {
            color: #666;
            margin: 0;
            font-size: 1rem;
        }
        
        .test-links {
            margin-top: 20px;
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .test-link {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
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
        
        @media (max-width: 480px) {
            .test-links {
                flex-direction: column;
                align-items: center;
            }
            .test-link {
                width: 100%;
                max-width: 250px;
            }
        }
        
        /* ============================================
           Font Awesome 图标修复
           ============================================ */
        [class^="ph-"], [class*=" ph-"] {
            font-family: 'Font Awesome 6 Free' !important;
            font-weight: 900 !important;
        }
        
        /* ============================================
           地图卡片样式
           ============================================ */
        .lgnewui-map-card {
            background: white;
            border-radius: 24px;
            padding: 24px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .lgnewui-map-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .lgnewui-map-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 18px;
            font-weight: 700;
            color: #333;
        }

        .lgnewui-map-title i {
            color: #667eea;
            font-size: 24px;
        }

        .lgnewui-map-distance {
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .lgnewui-map-distance-value {
            font-size: 36px;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .lgnewui-map-distance-unit {
            font-size: 16px;
            color: #999;
            font-weight: 600;
        }

        .lgnewui-map-visual {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .lgnewui-map-bg {
            position: relative;
        }

        .lgnewui-map-svg {
            width: 100%;
            height: auto;
            display: block;
        }

        .lgnewui-map-locations {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .lgnewui-map-location {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            background: #f8f9fa;
            border-radius: 16px;
        }

        .lgnewui-map-location-boy {
            background: linear-gradient(135deg, rgba(102,126,234,0.1), rgba(118,75,162,0.1));
        }

        .lgnewui-map-location-girl {
            background: linear-gradient(135deg, rgba(245,87,108,0.1), rgba(240,147,251,0.1));
        }

        .lgnewui-map-location-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        .lgnewui-map-location-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .lgnewui-map-location-info {
            flex: 1;
            min-width: 0;
        }

        .lgnewui-map-location-name {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 4px;
        }

        .lgnewui-map-location-city {
            font-size: 13px;
            color: #666;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .lgnewui-map-location-city i {
            color: #667eea;
        }

        .lgnewui-map-connector {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea, #f5576c);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
        }
        
        /* ============================================
           访客统计卡片样式
           ============================================ */
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
        
        /* 地图和访客统计移动端适配 */
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
            .lgnewui-map-card {
                padding: 16px;
                border-radius: 20px;
            }
            
            .lgnewui-map-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            
            .lgnewui-map-distance-value {
                font-size: 28px;
            }
            
            .lgnewui-map-locations {
                flex-direction: column;
                gap: 12px;
            }
            
            .lgnewui-map-connector {
                transform: rotate(90deg);
            }
            
            .lgnewui-map-location {
                width: 100%;
            }
            
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
</head>
<body>
    
    <!-- 移动端头部 -->
    <div class="mobile-header">
        <div class="mobile-header-content">
            <div class="mobile-logo">❤️ LG-NewUi</div>
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
    
    <!-- 移动端导航 -->
    <nav class="mobile-nav" id="mobileNav">
        <a href="little.php" class="mobile-nav-item"><i class="fas fa-file-alt"></i> 点滴</a>
        <a href="leaving.php" class="mobile-nav-item"><i class="fas fa-comments"></i> 留言</a>
        <a href="timeline.php" class="mobile-nav-item"><i class="fas fa-clock"></i> 轨迹</a>
        <a href="loveImg.php" class="mobile-nav-item"><i class="fas fa-images"></i> 相册</a>
        <a href="list.php" class="mobile-nav-item"><i class="fas fa-list"></i> 清单</a>
        <a href="about.php" class="mobile-nav-item"><i class="fas fa-heart"></i> 关于</a>
    </nav>
    
    <div class="test-header">
        <h1>❤️ LG-NewUi 测试页面</h1>
        <p>情侣小站界面预览 - 完整移动端适配</p>
        <div class="test-status">
            <span class="status-pill">✅ 服务器运行中</span>
            <span class="status-pill">📱 响应式设计</span>
            <span class="status-pill">🎨 移动端优化</span>
        </div>
    </div>
    
    <main class="lgnewui-container">
        
        <!-- ===== 1. 天数计数器 ===== -->
        <div class="test-section">
            <h2><i class="fas fa-heart"></i> 1. 天数计数器</h2>
            <div class="lgnewui-day-wrapper">
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
                            <div class="lgnewui-day-main-days-number">1095</div>
                            <div class="lgnewui-day-days-divider"></div>
                            <div class="lgnewui-day-days-label">DAYS</div>
                        </div>
                        <div class="lgnewui-day-digital-timer">
                            <div class="lgnewui-day-timer-block"><div class="lgnewui-day-timer-val">12</div><span class="lgnewui-day-timer-label">Hours</span></div>
                            <div class="lgnewui-day-timer-block"><div class="lgnewui-day-timer-val">34</div><span class="lgnewui-day-timer-label">Minutes</span></div>
                            <div class="lgnewui-day-timer-block"><div class="lgnewui-day-timer-val">56</div><span class="lgnewui-day-timer-label">Seconds</span></div>
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
                    <div class="lgnewui-smart-card">
                        <div class="lgnewui-smart-card__media"></div>
                        <div class="lgnewui-smart-card__overlay"></div>
                        <div class="lgnewui-smart-card__header">
                            <div class="lgnewui-smart-card__capsule">
                                <img class="lgnewui-smart-card__avatar" src="https://ui-avatars.com/api/?name=Love&background=667eea&color=fff&size=128" alt="">
                                <div>
                                    <span class="lgnewui-smart-card__name">男主角</span>
                                    <span class="lgnewui-smart-card__time">最新动态</span>
                                </div>
                            </div>
                            <a href="#" class="lgnewui-smart-card__album-link"><span>进入相册</span><i class="fas fa-arrow-right"></i></a>
                        </div>
                        <div class="lgnewui-smart-card__content">
                            <div class="lgnewui-smart-card__location-pill"><i class="fas fa-map-marker-alt"></i><span>我们的小窝</span></div>
                            <h2 class="lgnewui-smart-card__title">时光碎片</h2>
                            <p class="lgnewui-smart-card__desc">记录每一个闪光的瞬间</p>
                        </div>
                        <div class="lgnewui-smart-card__switch-btn-container">
                            <button class="lgnewui-smart-card__switch-btn"><i class="fas fa-sync-alt"></i></button>
                        </div>
                    </div>
                </div>
                
                <!-- 天气卡片 -->
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
                            <span>北京</span><span class="lgnewui-home-weather-dot-divider">•</span><span>晴朗</span>
                        </div>
                        <div class="lgnewui-home-weather-grid-stats">
                            <div class="lgnewui-home-weather-stat-pill"><i class="fas fa-tint"></i><span>45%</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="fas fa-eye"></i><span>10km</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="fas fa-thermometer-half"></i><span>23°</span></div>
                        </div>
                    </div>
                </div>
                
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
                            <span>上海</span><span class="lgnewui-home-weather-dot-divider">•</span><span>多云</span>
                        </div>
                        <div class="lgnewui-home-weather-grid-stats">
                            <div class="lgnewui-home-weather-stat-pill"><i class="fas fa-tint"></i><span>60%</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="fas fa-eye"></i><span>8km</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="fas fa-thermometer-half"></i><span>26°</span></div>
                        </div>
                    </div>
                </div>
                
                <!-- 统计卡片 -->
                <div class="lgnewui-col-md-1">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-1">
                        <div class="lgnewui-widget__bg-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="fas fa-file-alt"></i></div><div class="lgnewui-stats-title">点滴</div></div>
                            <div class="lgnewui-mt-1rem"><div class="lgnewui-font-num lgnewui-stats-num">42</div><div class="lgnewui-stats-label">Memory Notes</div></div>
                        </div>
                    </div>
                </div>
                
                <div class="lgnewui-col-md-1">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-2">
                        <div class="lgnewui-widget__bg-icon"><i class="fas fa-images"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="fas fa-camera"></i></div><div class="lgnewui-stats-title">相册</div></div>
                            <div class="lgnewui-mt-1rem"><div class="lgnewui-font-num lgnewui-stats-num">156</div><div class="lgnewui-stats-label">Photo Keepsakes</div></div>
                        </div>
                    </div>
                </div>
                
                <div class="lgnewui-col-md-1">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-3">
                        <div class="lgnewui-widget__bg-icon"><i class="fas fa-comments"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="fas fa-comment-dots"></i></div><div class="lgnewui-stats-title">留言</div></div>
                            <div class="lgnewui-mt-1rem"><div class="lgnewui-font-num lgnewui-stats-num">89</div><div class="lgnewui-stats-label">Kind Messages</div></div>
                        </div>
                    </div>
                </div>
                
                <div class="lgnewui-col-md-1">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-6">
                        <div class="lgnewui-widget__bg-icon"><i class="fas fa-globe"></i></div>
                        <div class="lgnewui-flex-col-runtime">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="fas fa-globe"></i></div><div class="lgnewui-stats-title">我们的小世界</div></div>
                            <div class="lgnewui-mt-auto">
                                <div class="lgnewui-runtime-values">
                                    <div class="lgnewui-font-num lgnewui-runtime-num">1095</div>
                                    <div class="lgnewui-runtime-meta">
                                        <div class="lgnewui-runtime-days">DAYS</div>
                                        <div class="lgnewui-runtime-text">已平稳运行</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <!-- ===== 地图显示卡片 ===== -->
        <div class="test-section">
            <h2><i class="fas fa-map-marked-alt"></i> 地图显示</h2>
            <div class="lgnewui-map-card">
                <div class="lgnewui-map-header">
                    <div class="lgnewui-map-title">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>我们的距离</span>
                    </div>
                    <div class="lgnewui-map-distance">
                        <span class="lgnewui-map-distance-value">1068.9</span>
                        <span class="lgnewui-map-distance-unit">km</span>
                    </div>
                </div>
                
                <div class="lgnewui-map-visual">
                    <div class="lgnewui-map-bg">
                        <svg viewBox="0 0 400 200" class="lgnewui-map-svg">
                            <path d="M100,50 Q150,30 200,50 Q250,40 300,60 L320,100 Q300,150 250,170 L200,180 Q150,170 100,150 L80,100 Z" 
                                  fill="none" stroke="rgba(102, 126, 234, 0.2)" stroke-width="1"/>
                            
                            <g class="lgnewui-map-marker-boy">
                                <circle cx="150" cy="80" r="8" fill="#667eea" opacity="0.3">
                                    <animate attributeName="r" values="8;12;8" dur="2s" repeatCount="indefinite"/>
                                    <animate attributeName="opacity" values="0.3;0.1;0.3" dur="2s" repeatCount="indefinite"/>
                                </circle>
                                <circle cx="150" cy="80" r="4" fill="#667eea"/>
                                <text x="150" y="65" text-anchor="middle" fill="#667eea" font-size="12" font-weight="bold">男主角</text>
                            </g>
                            
                            <g class="lgnewui-map-marker-girl">
                                <circle cx="250" cy="120" r="8" fill="#f5576c" opacity="0.3">
                                    <animate attributeName="r" values="8;12;8" dur="2s" repeatCount="indefinite" begin="0.5s"/>
                                    <animate attributeName="opacity" values="0.3;0.1;0.3" dur="2s" repeatCount="indefinite" begin="0.5s"/>
                                </circle>
                                <circle cx="250" cy="120" r="4" fill="#f5576c"/>
                                <text x="250" y="140" text-anchor="middle" fill="#f5576c" font-size="12" font-weight="bold">女主角</text>
                            </g>
                            
                            <line x1="150" y1="80" x2="250" y2="120" 
                                  stroke="url(#distanceGradient)" stroke-width="2" stroke-dasharray="5,5">
                                <animate attributeName="stroke-dashoffset" from="0" to="10" dur="1s" repeatCount="indefinite"/>
                            </line>
                            
                            <defs>
                                <linearGradient id="distanceGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" style="stop-color:#667eea"/>
                                    <stop offset="100%" style="stop-color:#f5576c"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
                
                <div class="lgnewui-map-locations">
                    <div class="lgnewui-map-location lgnewui-map-location-boy">
                        <div class="lgnewui-map-location-avatar">
                            <img src="https://ui-avatars.com/api/?name=Boy&background=667eea&color=fff&size=128" alt="">
                        </div>
                        <div class="lgnewui-map-location-info">
                            <div class="lgnewui-map-location-name">男主角</div>
                            <div class="lgnewui-map-location-city">
                                <i class="fas fa-map-marker-alt"></i>北京
                            </div>
                        </div>
                    </div>
                    
                    <div class="lgnewui-map-connector">
                        <i class="fas fa-heart"></i>
                    </div>
                    
                    <div class="lgnewui-map-location lgnewui-map-location-girl">
                        <div class="lgnewui-map-location-avatar">
                            <img src="https://ui-avatars.com/api/?name=Girl&background=f5576c&color=fff&size=128" alt="">
                        </div>
                        <div class="lgnewui-map-location-info">
                            <div class="lgnewui-map-location-name">女主角</div>
                            <div class="lgnewui-map-location-city">
                                <i class="fas fa-map-marker-alt"></i>上海
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- ===== 访客统计卡片 ===== -->
        <div class="test-section">
            <h2><i class="fas fa-chart-line"></i> 访客统计</h2>
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
                                <span class="lgnewui-visitor-number" data-target="156">156</span>
                            </div>
                            <div class="lgnewui-visitor-stat-label">访问次数</div>
                        </div>
                        
                        <div class="lgnewui-visitor-stat">
                            <div class="lgnewui-visitor-stat-value">
                                <span class="lgnewui-visitor-number" data-target="48">48</span>
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
                                <span class="lgnewui-visitor-number lgnewui-visitor-number--large" data-target="3847">3.8k+</span>
                            </div>
                            <div class="lgnewui-visitor-stat-label">总访客数</div>
                        </div>
                        
                        <div class="lgnewui-visitor-stat">
                            <div class="lgnewui-visitor-stat-value">
                                <span class="lgnewui-visitor-number lgnewui-visitor-number--large" data-target="15234">15.2k+</span>
                            </div>
                            <div class="lgnewui-visitor-stat-label">总访问次</div>
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
                                <span class="lgnewui-home-message-user-name">访客1</span>
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
                                <span class="lgnewui-home-message-user-name">访客2</span>
                                <span class="lgnewui-home-message-post-time">2024-06-04</span>
                            </div>
                        </div>
                        <div class="lgnewui-home-message-content">这个小站太有爱了！一定要一直一直在一起呀！</div>
                    </a>
                </div>
                <div class="lgnewui-col-4">
                    <div class="lgnewui-journal-card">
                        <div class="lgnewui-watermark">DAY 1095</div>
                        <div class="lgnewui-journal-header">
                            <img src="https://ui-avatars.com/api/?name=Love&background=667eea&color=fff&size=128" class="lgnewui-journal-avatar">
                            <div>
                                <div class="lgnewui-font-sm-bold">男主角</div>
                                <div class="lgnewui-journal-meta">2024-06-05</div>
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
    
    <div class="test-section-footer">
        <h3>🎉 移动端适配完成！</h3>
        <p>所有组件已优化，可在手机、平板上完美展示！</p>
        <div class="test-links">
            <a href="debug.php" class="test-link">📊 查看调试页面</a>
            <a href="services/random_quote.php" class="test-link blue">🌐 测试API接口</a>
        </div>
    </div>
    
    <script>
        // 移动端菜单切换
        function toggleMobileMenu() {
            const nav = document.getElementById('mobileNav');
            nav.classList.toggle('active');
        }
        
        // 点击导航后关闭菜单
        document.querySelectorAll('.mobile-nav-item').forEach(item => {
            item.addEventListener('click', () => {
                document.getElementById('mobileNav').classList.remove('active');
            });
        });
        
        // 计时器
        let seconds = 56, minutes = 34, hours = 12;
        function updateTimer() {
            seconds++;
            if (seconds >= 60) { seconds = 0; minutes++; if (minutes >= 60) { minutes = 0; hours++; if (hours >= 24) hours = 0; } }
            document.querySelectorAll('.lgnewui-day-timer-val').forEach((el, i) => {
                el.textContent = String([hours, minutes, seconds][i]).padStart(2, '0');
            });
        }
        setInterval(updateTimer, 1000);
        
        // 访客统计数字动画
        document.addEventListener('DOMContentLoaded', function() {
            const numbers = document.querySelectorAll('.lgnewui-visitor-number');
            
            numbers.forEach(num => {
                const target = parseInt(num.dataset.target);
                if (isNaN(target)) return; // 跳过无效数据
                
                const duration = 1500;
                const startTime = performance.now();
                
                // 先设置初始值为0
                num.textContent = '0';
                
                function updateNumber(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    
                    // 使用easeOutExpo缓动
                    const easeOutExpo = 1 - Math.pow(2, -10 * progress);
                    const current = Math.floor(target * easeOutExpo);
                    
                    num.textContent = current.toLocaleString();
                    
                    if (progress < 1) {
                        requestAnimationFrame(updateNumber);
                    } else {
                        // 动画完成，确保最终值精确
                        num.textContent = target.toLocaleString();
                    }
                }
                
                requestAnimationFrame(updateNumber);
            });
            
            // 模拟实时更新 - 只更新今日访问次数
            setInterval(() => {
                const todayVisitNum = document.querySelector('.lgnewui-visitor-number[data-target="156"]');
                if (todayVisitNum) {
                    const current = parseInt(todayVisitNum.textContent.replace(/,/g, ''));
                    if (!isNaN(current)) {
                        todayVisitNum.textContent = (current + 1).toLocaleString();
                    }
                }
            }, 10000); // 每10秒增加1次访问
        });
    </script>
</body>
</html>
