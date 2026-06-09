<?php
$pageTitle = '轨迹';
include_once 'head.php';

if (!isset($text) || !is_array($text)) {
    $text = [
        'boy' => '男方', 'girl' => '女方', 'boyimg' => '', 'girlimg' => '',
        'startTime' => date('Y-m-d H:i:s', time() - 365 * 86400), 'logo' => '我们的故事',
        'writing' => '记录美好时光', 'Copyright' => '', 'icp' => '',
        'Animation' => '', 'title' => '情侣小站'
    ];
}

// 获取作者头像（优先使用上传的URL，否则使用QQ头像）
$boyimg = $text['boyimg'] ?? '';
$girlimg = $text['girlimg'] ?? '';
if ($boyimg && !preg_match('/^https?:\/\//', $boyimg)) {
    $boyimg = 'https://q1.qlogo.cn/g?b=qq&nk=' . $boyimg . '&s=640';
}
if ($girlimg && !preg_match('/^https?:\/\//', $girlimg)) {
    $girlimg = 'https://q1.qlogo.cn/g?b=qq&nk=' . $girlimg . '&s=640';
}
?>
<div id="pjax-container">
    <!-- 时间轴专用样式 -->
    <link rel="stylesheet" href="/Style/css/timeline.css">
    <!-- 迷你地图组件（地点卡片） -->
    <link rel="stylesheet" href="/assets/css/lg-mini-map.css">
    <script src="/assets/js/lg-mini-map.js"></script>

    <!-- 滚动提示 -->
    <div class="lgnewui-scroll-hint" id="timelineScrollHint">
        <div class="lgnewui-scroll-hint-inner">
            <!-- PC 端：鼠标图标 -->
            <div class="lgnewui-scroll-mouse">
                <div class="lgnewui-scroll-wheel"></div>
            </div>
            <!-- 移动端：手指滑动图标 -->
            <div class="lgnewui-scroll-touch">
                <div class="lgnewui-scroll-touch-hand">
                    <i class="ph-fill ph-hand-tap"></i>
                </div>
                <div class="lgnewui-scroll-touch-trail"></div>
            </div>
            <span class="lgnewui-scroll-text lgnewui-scroll-text-pc">向下滚动查看时间轴</span>
            <span class="lgnewui-scroll-text lgnewui-scroll-text-mobile">向上滑动查看时间轴</span>
        </div>
    </div>

    <style>
        /* 修复 pjax-container 内 sticky 失效问题 */
        #pjax-container {
            overflow: visible !important;
        }
        #pjax-container .lgnewui-time-line-main {
            overflow: visible !important;
        }

        /* 滚动提示样式 */
        .lgnewui-scroll-hint {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 0 50px;
        }

        .lgnewui-scroll-hint-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        /* 鼠标图标 - PC 端 */
        .lgnewui-scroll-mouse {
            width: 26px;
            height: 42px;
            border: 2px solid rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            position: relative;
            display: flex;
            justify-content: center;
        }

        .lgnewui-scroll-wheel {
            width: 4px;
            height: 8px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 2px;
            position: absolute;
            top: 8px;
            animation: scrollWheel 1.8s ease-in-out infinite;
        }

        @keyframes scrollWheel {
            0% { opacity: 1; transform: translateY(0); }
            50% { opacity: 0.5; transform: translateY(10px); }
            100% { opacity: 0; transform: translateY(16px); }
        }

        /* 手指滑动图标 - 移动端 */
        .lgnewui-scroll-touch {
            display: none;
            flex-direction: column;
            align-items: center;
            position: relative;
            height: 60px;
        }

        .lgnewui-scroll-touch-hand {
            font-size: 28px;
            color: rgba(0, 0, 0, 0.35);
            animation: touchSwipe 1.8s ease-in-out infinite;
        }

        .lgnewui-scroll-touch-trail {
            position: absolute;
            bottom: 0;
            width: 2px;
            height: 20px;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.15), transparent);
            border-radius: 1px;
            animation: touchTrail 1.8s ease-in-out infinite;
        }

        @keyframes touchSwipe {
            0%, 100% { transform: translateY(20px); opacity: 0.3; }
            50% { transform: translateY(-5px); opacity: 1; }
        }

        @keyframes touchTrail {
            0%, 100% { opacity: 0; height: 0; }
            30%, 70% { opacity: 0.5; height: 20px; }
        }

        .lgnewui-scroll-text {
            font-size: 13px;
            color: rgba(0, 0, 0, 0.4);
            font-family: 'Noto Serif SC', serif;
            letter-spacing: 0.1em;
        }

        .lgnewui-scroll-text-mobile {
            display: none;
        }

        @media (max-width: 768px) {
            .lgnewui-scroll-hint { padding: 20px 0 40px; }
            .lgnewui-scroll-mouse { display: none; }
            .lgnewui-scroll-touch { display: flex; }
            .lgnewui-scroll-text-pc { display: none; }
            .lgnewui-scroll-text-mobile { display: block; }
            .lgnewui-scroll-text { font-size: 12px; }
        }
    </style>

    <main class="lgnewui-time-line-main">
        <div id="timeline-container" class="lgnewui-time-line-container"></div>
    </main>

    <!-- 双主角配置 -->
    <script>
        window.TIMELINE_AUTHORS = {};
        window.TIMELINE_AUTHORS[1] = {
            name: <?php echo json_encode($text['boy'] ?? '', JSON_UNESCAPED_UNICODE); ?>,
            avatar: <?php echo json_encode($boyimg, JSON_UNESCAPED_UNICODE); ?>,
            gender: "male"
        };
        window.TIMELINE_AUTHORS[2] = {
            name: <?php echo json_encode($text['girl'] ?? '', JSON_UNESCAPED_UNICODE); ?>,
            avatar: <?php echo json_encode($girlimg, JSON_UNESCAPED_UNICODE); ?>,
            gender: "female"
        };
        // 兼容旧数据 male/female 键
        window.TIMELINE_AUTHORS['male'] = window.TIMELINE_AUTHORS[1];
        window.TIMELINE_AUTHORS['female'] = window.TIMELINE_AUTHORS[2];
    </script>

    <!-- WaveSurfer 音频波形 -->
    <script src="/Style/js/wavesurfer.min.js"></script>
    <!-- 时间轴页面 JS -->
    <script src="/assets/js/page-timeline.js"></script>

    <script>
        // 通过 AJAX 从后端加载时间轴数据
        if (typeof window.LGTimelineModule !== 'undefined' && document.getElementById('timeline-container')) {
            window.LGTimelineModule.loadFromServer();
        }
    </script>
</div>

<?php include_once 'footer.php'; ?>
