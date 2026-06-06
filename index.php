<?php
include_once 'head.php';

// 获取统计数据
$statsArticles = 0;
$statsPhotos = 0;
$statsMessages = 0;

if ($connect) {
    $r = mysqli_query($connect, "SELECT COUNT(*) as c FROM little");
    if ($r) { $row = mysqli_fetch_array($r); $statsArticles = $row['c']; }
    $r = mysqli_query($connect, "SELECT COUNT(*) as c FROM photo");
    if ($r) { $row = mysqli_fetch_array($r); $statsPhotos = $row['c']; }
    $r = mysqli_query($connect, "SELECT COUNT(*) as c FROM leaving");
    if ($r) { $row = mysqli_fetch_array($r); $statsMessages = $row['c']; }
}

$startTs = strtotime(str_replace('T', ' ', $text['startTime'] ?? '2022-06-05 00:07:00'));
$runtimeDays = floor((time() - $startTs) / 86400);
?>

    <div id="pjax-container">
    <main class="lgnewui-home lgnewui-container" style="padding-bottom:2rem;">

        <!-- ===== 1. 天数计数器 ===== -->
        <div class="lgnewui-day-wrapper lgnewui-mb-4" data-aos="fade-up" data-aos-delay="0">
            <div class="lgnewui-day-fusion-card">
                <div class="lgnewui-day-ambient-light"></div>
                <div class="lgnewui-day-mac-dots">
                    <div class="lgnewui-day-dot lgnewui-day-dot-red"></div>
                    <div class="lgnewui-day-dot lgnewui-day-dot-yellow"></div>
                    <div class="lgnewui-day-dot lgnewui-day-dot-green"></div>
                </div>
                <div class="lgnewui-day-left-section">
                    <h2 class="lgnewui-day-poetic-title">
                        <?php echo preg_replace('/\{([^}]+)\}/', '<b>$1</b>', $text['logo']) ?><br>
                        <span style="font-size:0.7em;opacity:0.7;">与你行至天光</span>
                    </h2>
                    <div class="lgnewui-day-start-date-capsule">
                        <div class="lgnewui-day-icon-circle"><i class="ph-fill ph-heart"></i></div>
                        <div>
                            <span class="lgnewui-day-date-label-small">Together Since</span>
                            <span class="lgnewui-day-date-value-clean" id="lgnewui-day-start-date-display"><?php echo str_replace('T', ' ', $text['startTime']) ?></span>
                        </div>
                    </div>
                </div>
                <div class="lgnewui-day-right-section">
                    <div class="lgnewui-day-main-days-wrapper">
                        <div class="lgnewui-day-main-days-number" id="lgnewui-day-counter-days">0</div>
                        <div class="lgnewui-day-days-divider"></div>
                        <div class="lgnewui-day-days-label">DAYS</div>
                    </div>
                    <div class="lgnewui-day-digital-timer">
                        <div class="lgnewui-day-timer-block"><div class="lgnewui-day-timer-val" id="lgnewui-day-counter-hours">00</div><div class="lgnewui-day-timer-label">Hours</div></div>
                        <div class="lgnewui-day-timer-block"><div class="lgnewui-day-timer-val" id="lgnewui-day-counter-minutes">00</div><div class="lgnewui-day-timer-label">Minutes</div></div>
                        <div class="lgnewui-day-timer-block"><div class="lgnewui-day-timer-val" id="lgnewui-day-counter-seconds">00</div><div class="lgnewui-day-timer-label">Seconds</div></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== 2. Bento Grid 主区域 ===== -->
        <section class="lgnewui-section">
            <div class="lgnewui-grid">

                <!-- 智能媒体卡片（时光碎片）- 占2x2 -->
                <div class="lgnewui-col-2 lgnewui-row-2" data-aos="fade-up" data-aos-delay="0">
                    <div id="moment-card" class="lgnewui-smart-card">
                        <div class="lgnewui-smart-card__media"></div>
                        <div class="lgnewui-smart-card__overlay"></div>
                        <div class="lgnewui-smart-card__header">
                            <div class="lgnewui-smart-card__capsule">
                                <img class="lgnewui-smart-card__avatar" src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $text['boyimg'] ?>&s=640" alt="">
                                <div class="lgnewui-smart-card__user-info">
                                    <span class="lgnewui-smart-card__name"><?php echo $text['boy'] ?></span>
                                    <span class="lgnewui-smart-card__time">最新动态</span>
                                </div>
                            </div>
                            <a href="loveImg.php" class="lgnewui-smart-card__album-link">
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

                <!-- 天气卡片 - 男方 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1" data-aos="fade-up" data-aos-delay="50">
                    <div class="lgnewui-home-weather-card blue" data-weather-slot="1">
                        <div class="lgnewui-home-weather-bg-decoration"></div>
                        <div class="lgnewui-home-weather-row-top">
                            <div class="lgnewui-home-weather-user-pill">
                                <img src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $text['boyimg'] ?>&s=640" class="lgnewui-home-weather-avatar">
                                <span class="lgnewui-home-weather-username"><?php echo $text['boy'] ?></span>
                            </div>
                            <div class="lgnewui-home-weather-time-tag">--:--</div>
                        </div>
                        <div class="lgnewui-home-weather-row-main">
                            <div class="lgnewui-home-weather-text-temp">--°</div>
                            <i class="qi-100-fill lgnewui-home-weather-icon-main"></i>
                        </div>
                        <div class="lgnewui-home-weather-row-location">
                            <i class="ph-fill ph-map-pin"></i>
                            <span class="lgnewui-home-weather-text-city">--</span>
                            <span class="lgnewui-home-weather-dot-divider">•</span>
                            <span class="lgnewui-home-weather-text-status">--</span>
                        </div>
                        <div class="lgnewui-home-weather-grid-stats">
                            <div class="lgnewui-home-weather-stat-pill"><i class="ph-fill ph-drop"></i><span class="stat-humidity">--%</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="ph-fill ph-eye"></i><span class="stat-vis">--km</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="ph-fill ph-thermometer"></i><span class="stat-feels">--°</span></div>
                        </div>
                    </div>
                </div>

                <!-- 天气卡片 - 女方 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1" data-aos="fade-up" data-aos-delay="100">
                    <div class="lgnewui-home-weather-card orange" data-weather-slot="2">
                        <div class="lgnewui-home-weather-bg-decoration"></div>
                        <div class="lgnewui-home-weather-row-top">
                            <div class="lgnewui-home-weather-user-pill">
                                <img src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $text['girlimg'] ?>&s=640" class="lgnewui-home-weather-avatar">
                                <span class="lgnewui-home-weather-username"><?php echo $text['girl'] ?></span>
                            </div>
                            <div class="lgnewui-home-weather-time-tag">--:--</div>
                        </div>
                        <div class="lgnewui-home-weather-row-main">
                            <div class="lgnewui-home-weather-text-temp">--°</div>
                            <i class="qi-100-fill lgnewui-home-weather-icon-main"></i>
                        </div>
                        <div class="lgnewui-home-weather-row-location">
                            <i class="ph-fill ph-map-pin"></i>
                            <span class="lgnewui-home-weather-text-city">--</span>
                            <span class="lgnewui-home-weather-dot-divider">•</span>
                            <span class="lgnewui-home-weather-text-status">--</span>
                        </div>
                        <div class="lgnewui-home-weather-grid-stats">
                            <div class="lgnewui-home-weather-stat-pill"><i class="ph-fill ph-drop"></i><span class="stat-humidity">--%</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="ph-fill ph-eye"></i><span class="stat-vis">--km</span></div>
                            <div class="lgnewui-home-weather-stat-pill"><i class="ph-fill ph-thermometer"></i><span class="stat-feels">--°</span></div>
                        </div>
                    </div>
                </div>

                <!-- 点滴统计 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1" data-aos="fade-up" data-aos-delay="150">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-1">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-article"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-newspaper-clipping"></i></div>
                                <div class="lgnewui-stats-title">点滴</div>
                            </div>
                            <div class="lgnewui-mt-1rem">
                                <div class="lgnewui-font-num lgnewui-stats-num"><?php echo $statsArticles ?></div>
                                <div class="lgnewui-stats-label">Memory Notes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 相册统计 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1" data-aos="fade-up" data-aos-delay="200">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-2">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-images"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-camera"></i></div>
                                <div class="lgnewui-stats-title">相册</div>
                            </div>
                            <div class="lgnewui-mt-1rem">
                                <div class="lgnewui-font-num lgnewui-stats-num"><?php echo $statsPhotos ?></div>
                                <div class="lgnewui-stats-label">Photo Keepsakes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 留言统计 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1" data-aos="fade-up" data-aos-delay="250">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-3">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-chat-circle-dots"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-chat-teardrop-dots"></i></div>
                                <div class="lgnewui-stats-title">留言</div>
                            </div>
                            <div class="lgnewui-mt-1rem">
                                <div class="lgnewui-font-num lgnewui-stats-num"><?php echo $statsMessages ?></div>
                                <div class="lgnewui-stats-label">Kind Messages</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 运行天数 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1" data-aos="fade-up" data-aos-delay="300">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-6">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-planet"></i></div>
                        <div class="lgnewui-flex-col-runtime">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-planet"></i></div>
                                <div class="lgnewui-stats-title">我们的小世界</div>
                            </div>
                            <div class="lgnewui-mt-auto">
                                <div class="lgnewui-runtime-values">
                                    <div class="lgnewui-font-num lgnewui-runtime-num"><?php echo $runtimeDays ?></div>
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
        </section>

        <!-- ===== 地图显示 ===== -->
        <?php include_once 'components/lg-map-card.php'; ?>

        <!-- ===== 访客统计 ===== -->
        <?php include_once 'components/lg-visitor-stats.php'; ?>

        <!-- ===== 3. 清单事件 ===== -->
        <section class="lgnewui-section">
            <div class="lgnewui-section-header lgnewui-section-header--rose" data-aos="fade-up">
                <div class="lgnewui-section-header__left">
                    <h2 class="lgnewui-section-title">
                        <div class="lgnewui-section-icon-box lgnewui-section-icon-box--rose"><i class="ph-fill ph-heart"></i></div>
                        <span>清单</span>
                    </h2>
                </div>
                <div class="lgnewui-section-header__right">
                    <a href="lovelist.php" class="lgnewui-link-more"><i class="ph-bold ph-arrow-right"></i></a>
            </div>
            <div class="lgnewui-grid">
                <div class="lgnewui-col-4" data-aos="fade-up">
                    <a href="lovelist.php" class="lgnewui-widget" style="background:linear-gradient(135deg,#0f172a,#334155);color:#fff;text-decoration:none;">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-shooting-star"></i></div>
                        <div class="lgnewui-flex-between-center lgnewui-mb-4">
                            <div class="lgnewui-flex-center-gap" style="display:flex;align-items:center;gap:0.6rem;">
                                <div class="lgnewui-icon-box-glass"><i class="ph-bold ph-list-heart lgnewui-icon-md-white"></i></div>
                                <div style="font-size:1.1rem;font-weight:700;">恋爱清单</div>
                            </div>
                            <div style="font-size:0.8rem;opacity:0.7;">Plans Together</div>
                        </div>
                        <div style="display:flex;align-items:baseline;gap:1rem;margin-top:auto;">
                            <div style="font-size:2.5rem;font-weight:900;font-family:'Inter',monospace;">💕</div>
                            <div style="font-size:0.85rem;opacity:0.7;">点击查看更多心愿</div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- ===== 4. 功能入口 ===== -->
        <section class="lgnewui-section">
            <div class="lgnewui-section-header" data-aos="fade-up">
                <div class="lgnewui-section-header__left">
                    <h2 class="lgnewui-section-title">
                        <div class="lgnewui-section-icon-box lgnewui-section-icon-box--rose"><i class="ph-fill ph-squares-four"></i></div>
                        <span>我们的角落</span>
                    </h2>
                </div>
            </div>
            <div class="lgnewui-grid">
                <div class="lgnewui-col-2" data-aos="fade-up" data-aos-delay="0">
                    <a href="articles.php" class="lgnewui-widget lgnewui-widget--stats-vibrant-1" style="text-decoration:none;color:#fff;">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-notebook"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-notebook"></i></div><div class="lgnewui-stats-title">点滴</div></div>
                            <div><div class="lgnewui-stats-label">记录在一起的点滴时光</div></div>
                        </div>
                    </a>
                </div>
                <div class="lgnewui-col-2" data-aos="fade-up" data-aos-delay="50">
                    <a href="messages.php" class="lgnewui-widget lgnewui-widget--stats-vibrant-3" style="text-decoration:none;color:#fff;">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-chat-teardrop-dots"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-chat-teardrop-dots"></i></div><div class="lgnewui-stats-title">留言</div></div>
                            <div><div class="lgnewui-stats-label">留下想说的话与温柔回应</div></div>
                        </div>
                    </a>
                </div>
                <div class="lgnewui-col-2" data-aos="fade-up" data-aos-delay="100">
                    <a href="timeline.php" class="lgnewui-widget lgnewui-widget--stats-vibrant-4" style="text-decoration:none;color:#fff;">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-clock-countdown"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-clock-countdown"></i></div><div class="lgnewui-stats-title">轨迹</div></div>
                            <div><div class="lgnewui-stats-label">回看我们一路走来的轨迹</div></div>
                        </div>
                    </a>
                </div>
                <div class="lgnewui-col-2" data-aos="fade-up" data-aos-delay="150">
                    <a href="loveImg.php" class="lgnewui-widget lgnewui-widget--stats-vibrant-2" style="text-decoration:none;color:#fff;">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-camera"></i></div>
                        <div class="lgnewui-flex-col-between-1">
                            <div class="lgnewui-stats-header-row"><div class="lgnewui-icon-circle-glass"><i class="ph-bold ph-camera"></i></div><div class="lgnewui-stats-title">相册</div></div>
                            <div><div class="lgnewui-stats-label">收藏见面与出游的闪亮瞬间</div></div>
                        </div>
                    </a>
                </div>
            </div>
        </section>

        <!-- ===== 5. 最新点滴 ===== -->
        <?php
        $recentArticles = null;
        if ($connect) {
            $recentArticles = mysqli_query($connect, "SELECT * FROM little ORDER BY id DESC LIMIT 3");
        }
        if ($recentArticles && mysqli_num_rows($recentArticles) > 0):
        ?>
        <section class="lgnewui-section">
            <div class="lgnewui-section-header lgnewui-section-header--purple" data-aos="fade-up">
                <div class="lgnewui-section-header__left">
                    <h2 class="lgnewui-section-title">
                        <div class="lgnewui-section-icon-box" style="background:linear-gradient(135deg,#667eea,#764ba2);"><i class="ph-fill ph-notebook lgnewui-icon-md-white"></i></div>
                        <span>最新点滴</span>
                    </h2>
                </div>
                <div class="lgnewui-section-header__right">
                    <a href="articles.php" class="lgnewui-link-more"><i class="ph-bold ph-arrow-right"></i></a>
                </div>
            </div>
            <div class="lgnewui-grid">
                <?php $idx = 0; while ($art = mysqli_fetch_array($recentArticles)):
                    $dayNum = floor((time() - strtotime($art['date'])) / 86400);
                ?>
                <div class="lgnewui-col-2" data-aos="fade-up" data-aos-delay="<?php echo $idx * 50 ?>">
                    <a href="articles.php" class="lgnewui-journal-card">
                        <div class="lgnewui-watermark">DAY <?php echo $dayNum ?></div>
                        <div class="lgnewui-journal-header">
                            <div class="lgnewui-journal-user">
                                <img src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $text['boyimg'] ?>&s=640" class="lgnewui-journal-avatar">
                                <div>
                                    <div class="lgnewui-font-sm-bold"><?php echo $text['boy'] ?></div>
                                    <div class="lgnewui-journal-meta"><?php echo $art['date'] ?></div>
                                </div>
                            </div>
                        </div>
                        <h3 class="lgnewui-journal-title"><?php echo htmlspecialchars(mb_substr($art['title'], 0, 30, 'UTF-8')) ?></h3>
                        <p class="lgnewui-journal-body lgnewui-journal-body-clamp"><?php echo htmlspecialchars(strip_tags(mb_substr($art['text'], 0, 100, 'UTF-8'))) ?></p>
                        <div class="lgnewui-journal-footer">
                            <div class="lgnewui-flex-gap-sm">
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-calendar-blank"></i> <?php echo date('Y-m-d', strtotime($art['date'])) ?></span>
                            </div>
                        </div>
                    </a>
                </div>
                <?php $idx++; endwhile; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- ===== 6. 最新留言 ===== -->
        <?php
        $recentMsgs = null;
        if ($connect) {
            $recentMsgs = mysqli_query($connect, "SELECT * FROM leaving ORDER BY id DESC LIMIT 3");
        }
        if ($recentMsgs && mysqli_num_rows($recentMsgs) > 0):
        ?>
        <section class="lgnewui-section">
            <div class="lgnewui-section-header" data-aos="fade-up">
                <div class="lgnewui-section-header__left">
                    <h2 class="lgnewui-section-title">
                        <div class="lgnewui-section-icon-box" style="background:linear-gradient(135deg,#4facfe,#00f2fe);"><i class="ph-fill ph-chat-teardrop-dots lgnewui-icon-md-white"></i></div>
                        <span>最新留言</span>
                    </h2>
                </div>
                <div class="lgnewui-section-header__right">
                    <a href="messages.php" class="lgnewui-link-more"><i class="ph-bold ph-arrow-right"></i></a>
            </div>
            <div class="lgnewui-grid">
                <?php $idx = 0; while ($msg = mysqli_fetch_array($recentMsgs)): ?>
                <div class="lgnewui-col-2" data-aos="fade-up" data-aos-delay="<?php echo $idx * 50 ?>">
                    <a href="messages.php" class="lgnewui-home-message-card">
                        <div class="lgnewui-home-message-header">
                            <img class="lgnewui-home-message-avatar" src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $msg['qqimg'] ?>&s=640">
                            <div>
                                <div class="lgnewui-home-message-name-row">
                                    <span class="lgnewui-home-message-user-name"><?php echo htmlspecialchars($msg['name']) ?></span>
                                </div>
                                <span class="lgnewui-home-message-post-time"><?php echo $msg['date'] ?></span>
                            </div>
                        </div>
                        <div class="lgnewui-home-message-content"><?php echo htmlspecialchars(mb_substr($msg['text'], 0, 80, 'UTF-8')) ?></div>
                    </a>
                </div>
                <?php $idx++; endwhile; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- ===== 恋爱纪念日 ===== -->
        <section class="lgnewui-section" id="loveday-list">
            <div class="lgnewui-section-header lgnewui-section-header--purple" data-aos="fade-up">
                <div class="lgnewui-section-header__left">
                    <h2 class="lgnewui-section-title">
                        <div class="lgnewui-section-icon-box lgnewui-section-icon-box--purple"><i class="ph-fill ph-calendar-heart"></i></div>
                        <span>纪念日</span>
                    </h2>
                </div>
            </div>
            <div class="lgnewui-grid">
                <div class="lgnewui-col-2" data-aos="fade-up">
                    <div class="lgnewui-widget lgnewui-widget--loveday-vibrant" style="background:linear-gradient(135deg,#f472b6,#ec4899);color:#fff;padding:1.5rem;">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-heart"></i></div>
                        <div style="font-size:0.85rem;opacity:0.8;">在一起</div>
                        <div style="font-size:2rem;font-weight:900;font-family:'Inter',monospace;"><?php echo $runtimeDays; ?> 天</div>
                        <div style="font-size:0.8rem;opacity:0.7;margin-top:0.3rem;"><?php echo date('Y年m月d日', $startTs); ?></div>
                    </div>
                </div>
                <div class="lgnewui-col-2" data-aos="fade-up">
                    <div class="lgnewui-widget lgnewui-widget--loveday-vibrant" style="background:linear-gradient(135deg,#818cf8,#6366f1);color:#fff;padding:1.5rem;">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-gift"></i></div>
                        <div style="font-size:0.85rem;opacity:0.8;">下一个纪念日</div>
                        <div style="font-size:1.2rem;font-weight:700;">恋爱纪念日</div>
                        <div style="font-size:0.8rem;opacity:0.7;margin-top:0.3rem;">每年 <?php echo date('m月d日', $startTs); ?></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ===== 最新留言 ===== -->
        <section class="lgnewui-section" id="messages">
            <div class="lgnewui-section-header lgnewui-section-header--blue" data-aos="fade-up">
                <div class="lgnewui-section-header__left">
                    <h2 class="lgnewui-section-title">
                        <div class="lgnewui-section-icon-box lgnewui-section-icon-box--blue"><i class="ph-fill ph-chat-teardrop-dots"></i></div>
                        <span>留言</span>
                    </h2>
                </div>
                <div class="lgnewui-section-header__right">
                    <a href="messages.php" class="lgnewui-link-more"><i class="ph-bold ph-arrow-right"></i></a>
                </div>
            </div>
            <div class="lgnewui-grid">
                <div class="lgnewui-col-4" data-aos="fade-up">
                    <a href="messages.php" class="lgnewui-widget" style="text-align:center;padding:2rem;text-decoration:none;color:inherit;">
                        <div style="font-size:2rem;margin-bottom:0.5rem;">💌</div>
                        <p style="color:#667eea;font-weight:600;">查看全部留言</p>
                    </a>
                </div>
            </div>
        </section>

        <!-- ===== 结尾区 ===== -->
        <section class="lgnewui-epilogue" data-aos="fade-up">
            <div class="lgnewui-epilogue__holes">
                <div class="lgnewui-epilogue__hole"></div>
                <div class="lgnewui-epilogue__hole"></div>
                <div class="lgnewui-epilogue__hole"></div>
            </div>
            <div class="lgnewui-epilogue__content">
                <p class="lgnewui-epilogue__text">朝暮与年岁并往，与你行至天光。</p>
                <p class="lgnewui-epilogue__subtext">我们的故事，未完待续...</p>
            </div>
            <div class="lgnewui-epilogue__actions">
                <a href="messages.php" class="lgnewui-epilogue__btn">
                    <i class="ph-fill ph-chat-circle-dots"></i> 留下祝福
                </a>
                <a href="loveImg.php" class="lgnewui-epilogue__btn">
                    <i class="ph-fill ph-images"></i> 随机光影
                </a>
                <a href="articles.php" class="lgnewui-epilogue__btn">
                    <i class="ph-fill ph-notebook"></i> 随机碎片
                </a>
            </div>
        </section>

    </main>

    <!-- AOS + 模块初始化 -->
    <script>
        if (typeof AOS !== 'undefined') {
            AOS.init({ duration: 800, easing: 'ease-out-cubic', once: true, offset: 50 });
        }
        if (typeof initLGHome === 'function') {
            initLGHome({
                startTime: '<?php echo $text['startTime'] ?>',
                weatherToken: 'b65cfa0c849145c283dfdf9cc6b87dd1'
            });
        }
        if (typeof initLGHomeApp === 'function') {
            initLGHomeApp({
                startTime: '<?php echo $text['startTime'] ?>',
                weatherToken: 'b65cfa0c849145c283dfdf9cc6b87dd1'
            });
        }
        // 初始化礼花效果
        if (typeof ConfettiEffect !== 'undefined') {
            ConfettiEffect.init();
        }
        // 初始化访客追踪
        if (typeof AccessBeacon !== 'undefined') {
            AccessBeacon.init('', '');
        }
    </script>

    </div>

    <?php include_once 'footer.php'; ?>

</body>
</html>
