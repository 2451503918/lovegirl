<?php
include_once 'head.php';
$time = gmdate("Y-m-d", time() + 8 * 3600);

// 获取在一起的时间
$startStr = $text['startTime'];
$startTs = strtotime(str_replace('T', ' ', $startStr));
$nowTs = time();
$days = floor(($nowTs - $startTs) / 86400);
?>

<title><?php echo $text['title'] ?> — 恋爱轨迹</title>
<style>
        .timeline-container {
            position: relative;
            padding: 2rem 0;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #667eea, #764ba2);
            transform: translateX(-50%);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 3rem;
            display: flex;
            justify-content: flex-end;
            padding-right: calc(50% + 30px);
        }

        .timeline-item:nth-child(even) {
            justify-content: flex-start;
            padding-right: 0;
            padding-left: calc(50% + 30px);
        }

        .timeline-dot {
            position: absolute;
            left: 50%;
            top: 20px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            border: 4px solid #667eea;
            transform: translateX(-50%);
            z-index: 1;
        }

        .timeline-dot.start {
            background: #ff6b6b;
            border-color: #ff6b6b;
            box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.3);
        }

        .timeline-dot.current {
            background: #667eea;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.3);
            animation: pulse 2s infinite;
        }

        .timeline-dot.festival {
            background: #ffd93d;
            border-color: #ffd93d;
            box-shadow: 0 0 0 4px rgba(255, 217, 61, 0.3);
        }

        .timeline-dot.stats {
            background: #6bcb77;
            border-color: #6bcb77;
            box-shadow: 0 0 0 4px rgba(107, 203, 119, 0.3);
        }

        .timeline-dot.future {
            background: #ff6b6b;
            border-color: #ff6b6b;
            box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.3);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.3);
            }
            50% {
                box-shadow: 0 0 0 8px rgba(102, 126, 234, 0.1);
            }
            100% {
                box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.3);
            }
        }

        .timeline-content {
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }

        .timeline-content:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .timeline-date {
            font-size: 0.9rem;
            color: #667eea;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .timeline-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
            font-family: 'Noto Serif SC', serif;
        }

        .timeline-text {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
        }

        .highlight {
            color: #ff6b6b;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .delay-03s {
            animation-delay: 0.3s;
        }

        .delay-06s {
            animation-delay: 0.6s;
        }

        .delay-09s {
            animation-delay: 0.9s;
        }

        .delay-12s {
            animation-delay: 1.2s;
        }

        @media (max-width: 768px) {
            .timeline-container {
                padding: 1rem 0;
            }

            .timeline-container::before {
                left: 20px;
            }

            .timeline-item,
            .timeline-item:nth-child(even) {
                padding-left: 50px;
                padding-right: 0;
                justify-content: flex-start;
                margin-bottom: 1.5rem;
            }

            .timeline-dot {
                left: 20px;
                width: 16px;
                height: 16px;
            }

            .timeline-content {
                padding: 1rem 1.2rem;
                border-radius: 0.8rem;
            }

            .timeline-title {
                font-size: 1.1rem;
            }

            .timeline-text {
                font-size: 0.9rem;
            }

            .highlight {
                font-size: 1.2rem;
            }

            .card {
                padding: 1rem;
                background: transparent;
                box-shadow: none;
                border: none !important;
            }

            .central .title h1 {
                font-size: 1.4em;
            }
        }
    </style>

    <div id="pjax-container">
    <style>
        .timeline-container {
            position: relative;
            padding: 2rem 0;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 3px;
            background: linear-gradient(to bottom, #667eea, #764ba2);
            transform: translateX(-50%);
        }

        .timeline-item {
            position: relative;
            margin-bottom: 3rem;
            display: flex;
            justify-content: flex-end;
            padding-right: calc(50% + 30px);
        }

        .timeline-item:nth-child(even) {
            justify-content: flex-start;
            padding-right: 0;
            padding-left: calc(50% + 30px);
        }

        .timeline-dot {
            position: absolute;
            left: 50%;
            top: 20px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #fff;
            border: 4px solid #667eea;
            transform: translateX(-50%);
            z-index: 1;
        }

        .timeline-dot.start {
            background: #ff6b6b;
            border-color: #ff6b6b;
            box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.3);
        }

        .timeline-dot.current {
            background: #667eea;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.3);
            animation: pulse 2s infinite;
        }

        .timeline-dot.festival {
            background: #ffd93d;
            border-color: #ffd93d;
            box-shadow: 0 0 0 4px rgba(255, 217, 61, 0.3);
        }

        .timeline-dot.stats {
            background: #6bcb77;
            border-color: #6bcb77;
            box-shadow: 0 0 0 4px rgba(107, 203, 119, 0.3);
        }

        .timeline-dot.future {
            background: #ff6b6b;
            border-color: #ff6b6b;
            box-shadow: 0 0 0 4px rgba(255, 107, 107, 0.3);
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.3); }
            50% { box-shadow: 0 0 0 8px rgba(102, 126, 234, 0.1); }
            100% { box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.3); }
        }

        .timeline-content {
            background: rgba(255, 255, 255, 0.95);
            padding: 1.5rem 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }

        .timeline-content:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .timeline-date {
            font-size: 0.9rem;
            color: #667eea;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .timeline-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
            font-family: 'Noto Serif SC', serif;
        }

        .timeline-text {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
        }

        .highlight {
            color: #ff6b6b;
            font-weight: 700;
            font-size: 1.5rem;
        }

        @media (max-width: 768px) {
            .timeline-container { padding: 1rem 0; }
            .timeline-container::before { left: 20px; }
            .timeline-item,
            .timeline-item:nth-child(even) {
                padding-left: 50px;
                padding-right: 0;
                justify-content: flex-start;
                margin-bottom: 1.5rem;
            }
            .timeline-dot { left: 20px; width: 16px; height: 16px; }
            .timeline-content { padding: 1rem 1.2rem; border-radius: 0.8rem; }
            .timeline-title { font-size: 1.1rem; }
            .timeline-text { font-size: 0.9rem; }
            .highlight { font-size: 1.2rem; }
            .card { padding: 1rem; background: transparent; box-shadow: none; border: none !important; }
            .central .title h1 { font-size: 1.4em; }
        }
    </style>
        <div class="central">
            <div class="title">
                <h1><?php echo $text['title'] ?> — 恋爱轨迹</h1>
            </div>
            <div class="row central central-800">
                <div class="card col-lg-12 col-md-12 col-sm-12 col-sm-x-12 <?php if ($text['Animation'] === "1") { ?>animated fadeInUp delay-03s<?php } ?>">
                    <div class="timeline-container">
                        <!-- 时间线起点 -->
                        <div class="timeline-item animated fadeInUp">
                            <div class="timeline-dot start"></div>
                            <div class="timeline-content">
                                <div class="timeline-date"><?php echo date('Y年m月d日', $startTs); ?></div>
                                <div class="timeline-title">在一起 ❤️</div>
                                <div class="timeline-text">从这一天开始，我们的故事开始了...</div>
                            </div>
                        </div>

                        <!-- 在一起天数 -->
                        <div class="timeline-item animated fadeInUp delay-03s">
                            <div class="timeline-dot current"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">今天</div>
                                <div class="timeline-title">已经在一起 <span class="highlight"><?php echo $days; ?></span> 天</div>
                                <div class="timeline-text">每一天都是珍贵的回忆</div>
                            </div>
                        </div>

                        <!-- 重要节日提醒 -->
                        <div class="timeline-item animated fadeInUp delay-06s">
                            <div class="timeline-dot festival"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">即将到来</div>
                                <div class="timeline-title">节日提醒 🎉</div>
                                <div class="timeline-text">
                                    <?php
                                    // 计算下一个纪念日
                                    $nextAnniversary = date('Y', $nowTs) . '-' . date('m-d', $startTs);
                                    $nextTs = strtotime($nextAnniversary);
                                    if ($nextTs < $nowTs) {
                                        $nextTs = strtotime((date('Y', $nowTs) + 1) . '-' . date('m-d', $startTs));
                                    }
                                    $daysUntil = floor(($nextTs - $nowTs) / 86400);
                                    ?>
                                    距离下一个纪念日还有 <span class="highlight"><?php echo $daysUntil; ?></span> 天
                                </div>
                            </div>
                        </div>

                        <!-- 统计信息 -->
                        <div class="timeline-item animated fadeInUp delay-09s">
                            <div class="timeline-dot stats"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">统计</div>
                                <div class="timeline-title">我们的数据 📊</div>
                                <div class="timeline-text">
                                    共 <?php echo $days; ?> 天 = <?php echo $days * 24; ?> 小时 = <?php echo $days * 24 * 60; ?> 分钟
                                </div>
                            </div>
                        </div>

                        <!-- 时间线终点 -->
                        <div class="timeline-item animated fadeInUp delay-12s">
                            <div class="timeline-dot future"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">未来</div>
                                <div class="timeline-title">永远在一起 💕</div>
                                <div class="timeline-text">未来的每一天，都想和你一起度过</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include_once 'footer.php'; ?>

</body>

</html>
