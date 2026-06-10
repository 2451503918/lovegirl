<?php
include_once 'head.php';

// 为text数组提供默认值，防止未定义
if (!isset($text) || !is_array($text)) {
    $text = [
        'boy' => '男方',
        'girl' => '女方',
        'boyimg' => '',
        'girlimg' => '',
        'startTime' => date('Y-m-d H:i:s', time() - 365 * 86400),
        'logo' => '我们的故事',
        'writing' => '记录美好时光',
        'Copyright' => '',
        'icp' => ''
    ];
}

// 获取统计数据
$statsArticles = 0;
$statsPhotos = 0;
$statsMessages = 0;
$statsTimeline = 0;
$listCompleted = 0;
$listTotal = 0;
$todayVisits = 0;
$todayVisitors = 0;
$totalVisits = 0;
$totalVisitors = 0;

if ($connect) {
    // Combined query: 6 COUNT subqueries + visitor stats in one round-trip
    $today = date('Y-m-d');
    $combinedSql = "SELECT "
        . "(SELECT COUNT(*) FROM little) AS articles, "
        . "(SELECT COUNT(*) FROM photo) AS photos, "
        . "(SELECT COUNT(*) FROM leaving) AS messages, "
        . "(SELECT COUNT(*) FROM timeline) AS timeline_count, "
        . "(SELECT COUNT(*) FROM lovelist) AS list_total, "
        . "(SELECT COUNT(*) FROM lovelist WHERE is_done = 1) AS list_completed, "
        . "(SELECT visit_count FROM visitor_stats WHERE visit_date = ? LIMIT 1) AS today_visits, "
        . "(SELECT visitor_count FROM visitor_stats WHERE visit_date = ? LIMIT 1) AS today_visitors, "
        . "(SELECT total_visits FROM visitor_total WHERE id = 1 LIMIT 1) AS total_visits, "
        . "(SELECT total_visitors FROM visitor_total WHERE id = 1 LIMIT 1) AS total_visitors";

    $stmt = mysqli_prepare($connect, $combinedSql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $today, $today);
        mysqli_stmt_execute($stmt);
        $r = mysqli_stmt_get_result($stmt);
        if ($r) {
            $row = mysqli_fetch_assoc($r);
            $statsArticles = intval($row['articles'] ?? 0);
            $statsPhotos = intval($row['photos'] ?? 0);
            $statsMessages = intval($row['messages'] ?? 0);
            $statsTimeline = intval($row['timeline_count'] ?? 0);
            $listTotal = intval($row['list_total'] ?? 0);
            $listCompleted = intval($row['list_completed'] ?? 0);
            $todayVisits = intval($row['today_visits'] ?? 0);
            $todayVisitors = intval($row['today_visitors'] ?? 0);
            $totalVisits = intval($row['total_visits'] ?? 0);
            $totalVisitors = intval($row['total_visitors'] ?? 0);
        }
        mysqli_stmt_close($stmt);
    }

    // Fallback: if combined query failed, run individual queries
    if ($statsArticles === 0 && $statsPhotos === 0 && $statsMessages === 0 && $statsTimeline === 0) {
        $r = @mysqli_query($connect, "SELECT COUNT(*) as c FROM little");
        if ($r) { $row = mysqli_fetch_array($r); $statsArticles = intval($row['c']); }
        $r = @mysqli_query($connect, "SELECT COUNT(*) as c FROM photo");
        if ($r) { $row = mysqli_fetch_array($r); $statsPhotos = intval($row['c']); }
        $r = @mysqli_query($connect, "SELECT COUNT(*) as c FROM leaving");
        if ($r) { $row = mysqli_fetch_array($r); $statsMessages = intval($row['c']); }
        $r = @mysqli_query($connect, "SELECT COUNT(*) as c FROM timeline");
        if ($r) { $row = mysqli_fetch_array($r); $statsTimeline = intval($row['c']); }
        $r = @mysqli_query($connect, "SELECT COUNT(*) as c FROM lovelist");
        if ($r) { $row = mysqli_fetch_array($r); $listTotal = intval($row['c']); }
        // Try is_done column first (exists after migration), fall back to icon
        $r = @mysqli_query($connect, "SELECT COUNT(*) as c FROM lovelist WHERE is_done = 1");
        if ($r) {
            $row = mysqli_fetch_array($r);
            $listCompleted = intval($row['c']);
        } else {
            $r = @mysqli_query($connect, "SELECT COUNT(*) as c FROM lovelist WHERE icon = 1");
            if ($r) { $row = mysqli_fetch_array($r); $listCompleted = intval($row['c']); }
        }

        // Fallback visitor stats with prepared statement
        $stmt2 = mysqli_prepare($connect, "SELECT visit_count, visitor_count FROM visitor_stats WHERE visit_date = ?");
        if ($stmt2) {
            mysqli_stmt_bind_param($stmt2, 's', $today);
            mysqli_stmt_execute($stmt2);
            $vr = mysqli_stmt_get_result($stmt2);
            if ($vr && mysqli_num_rows($vr) > 0) {
                $vrow = mysqli_fetch_assoc($vr);
                $todayVisits = intval($vrow['visit_count'] ?? 0);
                $todayVisitors = intval($vrow['visitor_count'] ?? 0);
            }
            mysqli_stmt_close($stmt2);
        }
        $r = @mysqli_query($connect, "SELECT total_visits, total_visitors FROM visitor_total WHERE id = 1");
        if ($r && mysqli_num_rows($r) > 0) {
            $row = mysqli_fetch_assoc($r);
            $totalVisits = intval($row['total_visits'] ?? $row['count'] ?? 0);
            $totalVisitors = intval($row['total_visitors'] ?? 0);
        }
    }
}

$listPercent = $listTotal > 0 ? round(($listCompleted / $listTotal) * 100) : 0;
$startTs = @strtotime(str_replace('T', ' ', $text['startTime'] ?? '2022-06-05 00:07:00'));
$runtimeDays = floor((time() - $startTs) / 86400);
?>

    <div id="pjax-container">

    <!-- ===== 1. 轮播横幅区域 ===== -->
    <div id="homePage" class="wrap" data-Fullscreen>
        <ul class="list mask_black">
            <li class="item active"><img class="CarouselImage" src="/Style/img/banner/1.jpg" alt="" fetchpriority="high" decoding="sync"></li>
            <li class="item"><img class="CarouselImage lazy" data-src="/Style/img/banner/2.jpg" alt="" draggable="false"></li>
            <li class="item"><img class="CarouselImage lazy" data-src="/Style/img/banner/3.jpg" alt="" draggable="false"></li>
            <li class="item"><img class="CarouselImage lazy" data-src="/Style/img/banner/4.jpg" alt="" draggable="false"></li>
            <li class="item"><img class="CarouselImage lazy" data-src="/Style/img/banner/5.jpg" alt="" draggable="false"></li>
            <li class="item"><img class="CarouselImage lazy" data-src="/Style/img/banner/6.jpg" alt="" draggable="false"></li>
            <li class="item"><img class="CarouselImage lazy" data-src="/Style/img/banner/7.jpg" alt="" draggable="false"></li>
            <li class="item"><img class="CarouselImage lazy" data-src="/Style/img/banner/8.jpg" alt="" draggable="false"></li>
        </ul>
        <div class="bg-wrap central limg" data-avatar-swap="1">
            <div class="bg-img">
                <div class="middle Blurkg">
                <!-- 男方头像 -->
                <div class="img-male">
                    <div class="avatarArea lgewui-head-avatar-boy">
                        <img draggable="false" class="avatarFrame" src="/Style/img/avatar-frame.png" style="transform: scale(1.6);top: 2px;left: 2px;">
                        <img draggable="false" class="aiv_touxiang" src="<?php echo htmlspecialchars($boyimg_val ?? '/Style/img/boy.png', ENT_QUOTES, 'UTF-8') ?>">
                        <div class="lgewui-head-avatar-mask">
                            <div class="lgewui-head-avatar-top lgewui-head-avatar-anim-item">
                                <div class="lgewui-head-avatar-gender-icon" data-gender="male"><i data-lucide="mars"></i></div>
                            </div>
                            <div class="lgewui-head-avatar-middle lgewui-head-avatar-anim-item">
                                <div class="lgewui-head-avatar-status-text lgewui-head-avatar-status-away">
                                    <i data-lucide="clock" class="lgewui-head-avatar-icon-away"></i>
                                    <em>离线</em>
                                </div>
                                <div class="lgewui-head-avatar-divider"></div>
                            </div>
                            <div class="lgewui-head-avatar-bottom lgewui-head-avatar-anim-item">
                                <div class="lgewui-head-avatar-location">
                                    <i class="ph-fill ph-map-pin"></i>
                                    <span id="lgnewui-male-location">-- · --</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span class="shadow-blur"><?php echo htmlspecialchars($text['boy'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <!-- 爱心图标 -->
                <div class="love-icon">
                    <div class="love-info-wrapper"></div>
                    <img draggable="false" src="/Style/img/like.svg">
                </div>
                <!-- 女方头像 -->
                <div class="img-female">
                    <div class="avatarArea lgewui-head-avatar-girl">
                        <img draggable="false" class="avatarFrame" src="/Style/img/avatar-frame.png" style="transform: scale(1.6);top: 2px;left: 2px;">
                        <img draggable="false" class="aiv_touxiang" src="<?php echo htmlspecialchars($girlimg_val ?? '/Style/img/girl.png', ENT_QUOTES, 'UTF-8') ?>">
                        <div class="lgewui-head-avatar-mask">
                            <div class="lgewui-head-avatar-top lgewui-head-avatar-anim-item">
                                <div class="lgewui-head-avatar-gender-icon" data-gender="female"><i data-lucide="venus"></i></div>
                            </div>
                            <div class="lgewui-head-avatar-middle lgewui-head-avatar-anim-item">
                                <div class="lgewui-head-avatar-status-text lgewui-head-avatar-status-away">
                                    <i data-lucide="clock" class="lgewui-head-avatar-icon-away"></i>
                                    <em>离线</em>
                                </div>
                                <div class="lgewui-head-avatar-divider"></div>
                            </div>
                            <div class="lgewui-head-avatar-bottom lgewui-head-avatar-anim-item">
                                <div class="lgewui-head-avatar-location">
                                    <i class="ph-fill ph-map-pin"></i>
                                    <span id="lgnewui-female-location">-- · --</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <span class="shadow-blur"><?php echo htmlspecialchars($text['girl'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
            <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
                <defs>
                    <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
                </defs>
                <g class="parallax">
                    <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.7)" />
                    <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
                    <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.3)" />
                    <use xlink:href="#gentle-wave" x="48" y="7" fill="rgba(255,255,255,1)" />
                </g>
            </svg>
            </div>
        </div>
    </div>

    <!-- ===== 2. 主内容区域 ===== -->
    <main class="lgnewui-home lgnewui-container">

        <!-- ===== 天数计数器 ===== -->
        <div class="lgnewui-day-wrapper lgnewui-mb-4" data-aos="fade-up" data-aos-delay="0">
            <div class="lgnewui-day-fusion-card">
                <div class="lgnewui-day-ambient-light"></div>
                <div class="lgnewui-day-mac-dots">
                    <div class="lgnewui-day-dot lgnewui-day-dot-red"></div>
                    <div class="lgnewui-day-dot lgnewui-day-dot-yellow"></div>
                    <div class="lgnewui-day-dot lgnewui-day-dot-green"></div>
                </div>
                <div class="lgnewui-day-left-section">
                        <div class="lgnewui-day-title-container">
                            <h2 class="lgnewui-day-poetic-title">
                                <?php echo preg_replace('/\{([^}]+)\}/', '<b>$1</b>', htmlspecialchars($text['logo'] ?? '', ENT_QUOTES, 'UTF-8')) ?><br>
                                <span style="font-size:0.7em;opacity:0.7;">与你行至天光</span>
                            </h2>
                        </div>
                        <div class="lgnewui-day-start-date-capsule">
                            <div class="lgnewui-day-icon-circle"><i class="ph-fill ph-heart"></i></div>
                            <div class="lgnewui-day-date-text-group">
                                <span class="lgnewui-day-date-label-small">Together Since</span>
                                <span class="lgnewui-day-date-value-clean" id="lgnewui-day-start-date-display"><?php echo htmlspecialchars(str_replace('T', ' ', $text['startTime'] ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
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

        <!-- ===== Bento Grid 主区域 ===== -->
        <section class="lgnewui-section">
            <div class="lgnewui-grid">

                <!-- 智能媒体卡片（时光碎片）- 占2x2 -->
                <div class="lgnewui-col-2 lgnewui-row-2" data-aos="fade-up" data-aos-delay="0">
                    <div id="moment-card" class="lgnewui-smart-card">
                        <div class="lgnewui-smart-card__media"></div>
                        <div class="lgnewui-smart-card__overlay"></div>
                        <div class="lgnewui-smart-card__header">
                            <div class="lgnewui-smart-card__capsule">
                                <img class="lgnewui-smart-card__avatar lazy" src="" alt="">
                                <div class="lgnewui-smart-card__user-info">
                                    <span class="lgnewui-smart-card__name"></span>
                                    <span class="lgnewui-smart-card__time"></span>
                                </div>
                            </div>
                            <a href="albums.php" class="lgnewui-smart-card__album-link">
                                <span>进入相册</span>
                                <i class="ph-bold ph-arrow-right"></i>
                            </a>
                        </div>
                        <div class="lgnewui-smart-card__content">
                            <div class="lgnewui-smart-card__location-pill">
                                <i class="ph-fill ph-map-pin"></i>
                                <span class="lgnewui-smart-card__location-text"></span>
                            </div>
                            <h2 class="lgnewui-smart-card__title"></h2>
                            <div class="lgnewui-smart-card__meta">
                                <span class="lgnewui-smart-card__date"></span>
                                <p class="lgnewui-smart-card__desc"></p>
                            </div>
                        </div>
                        <div class="lgnewui-smart-card__switch-btn-container">
                            <button class="lgnewui-smart-card__switch-btn" type="button">
                                <i class="ph-bold ph-arrows-clockwise"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 天气卡片 - 男方 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1 lgnewui-weather-wrapper" data-aos="fade-up" data-aos-delay="50">
                    <div class="lgnewui-home-weather-card blue" data-weather-slot="1" data-location-name="--">
                        <div class="lgnewui-home-weather-bg-decoration"></div>
                        <div class="lgnewui-home-weather-row-top">
                            <div class="lgnewui-home-weather-user-pill">
                                <img src="" class="lgnewui-home-weather-avatar lg-male-avatar" alt="<?php echo htmlspecialchars($text['boy'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <span class="lgnewui-home-weather-username"><?php echo htmlspecialchars($text['boy'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="lgnewui-home-weather-time-tag">--</div>
                        </div>
                        <div class="lgnewui-home-weather-row-main">
                            <div class="lgnewui-home-weather-text-temp">--°</div>
                            <i class="qi-100-fill lgnewui-home-weather-icon-main"></i>
                        </div>
                        <div class="lgnewui-home-weather-row-location">
                            <i class="ph-fill ph-map-pin lgnewui-home-weather-icon-pin"></i>
                            <span class="lgnewui-home-weather-text-city">--</span>
                            <span class="lgnewui-home-weather-dot-divider">•</span>
                            <span class="lgnewui-home-weather-text-status">--</span>
                        </div>
                        <div class="lgnewui-home-weather-grid-stats">
                            <div class="lgnewui-home-weather-stat-pill">
                                <i class="ph-fill ph-drop lgnewui-home-weather-icon-stat"></i>
                                <span class="lgnewui-home-weather-text-stat stat-humidity">--%</span>
                            </div>
                            <div class="lgnewui-home-weather-stat-pill">
                                <i class="ph-fill ph-eye lgnewui-home-weather-icon-stat"></i>
                                <span class="lgnewui-home-weather-text-stat stat-vis">--km</span>
                            </div>
                            <div class="lgnewui-home-weather-stat-pill">
                                <i class="ph-fill ph-thermometer lgnewui-home-weather-icon-stat"></i>
                                <span class="lgnewui-home-weather-text-stat stat-feels">--°</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 天气卡片 - 女方 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1 lgnewui-weather-wrapper" data-aos="fade-up" data-aos-delay="100">
                    <div class="lgnewui-home-weather-card orange" data-weather-slot="2" data-location-name="--">
                        <div class="lgnewui-home-weather-bg-decoration"></div>
                        <div class="lgnewui-home-weather-row-top">
                            <div class="lgnewui-home-weather-user-pill">
                                <img src="" class="lgnewui-home-weather-avatar lg-female-avatar" alt="<?php echo htmlspecialchars($text['girl'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                <span class="lgnewui-home-weather-username"><?php echo htmlspecialchars($text['girl'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="lgnewui-home-weather-time-tag">--</div>
                        </div>
                        <div class="lgnewui-home-weather-row-main">
                            <div class="lgnewui-home-weather-text-temp">--°</div>
                            <i class="qi-100-fill lgnewui-home-weather-icon-main"></i>
                        </div>
                        <div class="lgnewui-home-weather-row-location">
                            <i class="ph-fill ph-map-pin lgnewui-home-weather-icon-pin"></i>
                            <span class="lgnewui-home-weather-text-city">--</span>
                            <span class="lgnewui-home-weather-dot-divider">•</span>
                            <span class="lgnewui-home-weather-text-status">--</span>
                        </div>
                        <div class="lgnewui-home-weather-grid-stats">
                            <div class="lgnewui-home-weather-stat-pill">
                                <i class="ph-fill ph-drop lgnewui-home-weather-icon-stat"></i>
                                <span class="lgnewui-home-weather-text-stat stat-humidity">--%</span>
                            </div>
                            <div class="lgnewui-home-weather-stat-pill">
                                <i class="ph-fill ph-eye lgnewui-home-weather-icon-stat"></i>
                                <span class="lgnewui-home-weather-text-stat stat-vis">--km</span>
                            </div>
                            <div class="lgnewui-home-weather-stat-pill">
                                <i class="ph-fill ph-thermometer lgnewui-home-weather-icon-stat"></i>
                                <span class="lgnewui-home-weather-text-stat stat-feels">--°</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 清单进度 -->
                <div class="lgnewui-col-2" data-aos="fade-up" data-aos-delay="150">
                    <div class="lgnewui-widget lgnewui-widget--lovelist">
                        <div class="lgnewui-widget__bg-icon lgnewui-lovelist-bg-icon">
                            <i class="ph-fill ph-shooting-star"></i>
                        </div>
                        <div class="lgnewui-flex-col-between-relative">
                            <div class="lgnewui-flex-between-center lgnewui-mb-4">
                                <div class="lgnewui-flex-center-gap">
                                    <div class="lgnewui-icon-box-glass">
                                        <i class="ph-bold ph-list-heart lgnewui-icon-md-white"></i>
                                    </div>
                                    <div class="lgnewui-card-title-lg">清单</div>
                                </div>
                                <div class="lgnewui-card-subtitle">Plans Together</div>
                            </div>
                            <div class="lgnewui-lovelist-bottom">
                                <div class="lgnewui-lovelist-stats">
                                    <div class="lgnewui-lovelist-fraction lgnewui-font-num">
                                        <span class="lgnewui-lovelist-completed"><?php echo $listCompleted ?></span>
                                        <span class="lgnewui-lovelist-divider">/</span>
                                        <span class="lgnewui-lovelist-total"><?php echo $listTotal ?></span>
                                    </div>
                                    <div class="lgnewui-font-num lgnewui-num-huge">
                                        <span><?php echo $listPercent ?></span><span class="lgnewui-num-suffix">%</span>
                                    </div>
                                </div>
                                <div class="lgnewui-progress lgnewui-progress-sm">
                                    <div class="lgnewui-progress__bar lgnewui-progress-fill-white" style="width: <?php echo $listPercent ?>%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 点滴统计 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1" data-aos="fade-up" data-aos-delay="200">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-1">
                        <div class="lgnewui-widget__bg-icon lgnewui-widget__bg-icon--tilted">
                            <i class="ph-fill ph-article"></i>
                        </div>
                        <div class="lgnewui-flex-col-between-1">
                            <div>
                                <div class="lgnewui-stats-header-row">
                                    <div class="lgnewui-icon-circle-glass">
                                        <i class="ph-bold ph-newspaper-clipping lgnewui-icon-sm-white"></i>
                                    </div>
                                    <div class="lgnewui-stats-title" data-lg-tip="点滴">点滴</div>
                                </div>
                            </div>
                            <div class="lgnewui-mt-1rem">
                                <div class="lgnewui-font-num lgnewui-stats-num"><?php echo $statsArticles ?></div>
                                <div class="lgnewui-stats-label lgnewui-stats-label--en">Memory Notes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 相册统计 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1" data-aos="fade-up" data-aos-delay="250">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-2">
                        <div class="lgnewui-widget__bg-icon lgnewui-widget__bg-icon--tilted">
                            <i class="ph-fill ph-images"></i>
                        </div>
                        <div class="lgnewui-flex-col-between-1">
                            <div>
                                <div class="lgnewui-stats-header-row">
                                    <div class="lgnewui-icon-circle-glass">
                                        <i class="ph-bold ph-camera lgnewui-icon-sm-white"></i>
                                    </div>
                                    <div class="lgnewui-stats-title" data-lg-tip="相册">相册</div>
                                </div>
                            </div>
                            <div class="lgnewui-mt-1rem">
                                <div class="lgnewui-font-num lgnewui-stats-num"><?php echo $statsPhotos ?></div>
                                <div class="lgnewui-stats-label lgnewui-stats-label--en">Photo Keepsakes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 留言统计 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1" data-aos="fade-up" data-aos-delay="300">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-3">
                        <div class="lgnewui-widget__bg-icon lgnewui-widget__bg-icon--tilted">
                            <i class="ph-fill ph-chat-circle-dots"></i>
                        </div>
                        <div class="lgnewui-flex-col-between-1">
                            <div>
                                <div class="lgnewui-stats-header-row">
                                    <div class="lgnewui-icon-circle-glass">
                                        <i class="ph-bold ph-chat-teardrop-dots lgnewui-icon-sm-white"></i>
                                    </div>
                                    <div class="lgnewui-stats-title" data-lg-tip="留言">留言</div>
                                </div>
                            </div>
                            <div class="lgnewui-mt-1rem">
                                <div class="lgnewui-font-num lgnewui-stats-num"><?php echo $statsMessages ?></div>
                                <div class="lgnewui-stats-label lgnewui-stats-label--en">Kind Messages</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 轨迹统计 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1" data-aos="fade-up" data-aos-delay="300">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-4">
                        <div class="lgnewui-widget__bg-icon lgnewui-widget__bg-icon--tilted">
                            <i class="ph-fill ph-hourglass-medium"></i>
                        </div>
                        <div class="lgnewui-flex-col-between-1">
                            <div>
                                <div class="lgnewui-stats-header-row">
                                    <div class="lgnewui-icon-circle-glass">
                                        <i class="ph-bold ph-timer lgnewui-icon-sm-white"></i>
                                    </div>
                                    <div class="lgnewui-stats-title" data-lg-tip="轨迹">轨迹</div>
                                </div>
                            </div>
                            <div class="lgnewui-mt-1rem">
                                <div class="lgnewui-font-num lgnewui-stats-num"><?php echo $statsTimeline ?></div>
                                <div class="lgnewui-stats-label lgnewui-stats-label--en">Steps of Us</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 今日访问统计 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1" data-aos="fade-up" data-aos-delay="300">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-5">
                        <div class="lgnewui-widget__bg-icon lgnewui-widget__bg-icon--tilted">
                            <i class="ph-fill ph-heart"></i>
                        </div>
                        <div class="lgnewui-traffic-card">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass">
                                    <i class="ph-bold ph-chart-line-up lgnewui-icon-sm-white"></i>
                                </div>
                                <div class="lgnewui-stats-title" data-lg-tip="今日访问">今日访问</div>
                            </div>
                            <div class="lgnewui-traffic-metrics">
                                <div class="lgnewui-traffic-metric" data-lg-tip="访问次数：<?php echo $todayVisits ?>" data-lg-tip-force="true">
                                    <div class="lgnewui-font-num lgnewui-traffic-value"><?php echo $todayVisits ?></div>
                                    <div class="lgnewui-traffic-label">访问次数</div>
                                </div>
                                <div class="lgnewui-traffic-divider" aria-hidden="true"></div>
                                <div class="lgnewui-traffic-metric" data-lg-tip="今日访客：<?php echo $todayVisitors ?>" data-lg-tip-force="true">
                                    <div class="lgnewui-font-num lgnewui-traffic-value"><?php echo $todayVisitors ?></div>
                                    <div class="lgnewui-traffic-label">今日访客</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 累计访问统计 -->
                <div class="lgnewui-col-2 lgnewui-col-md-1" data-aos="fade-up" data-aos-delay="300">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-7">
                        <div class="lgnewui-widget__bg-icon lgnewui-widget__bg-icon--tilted">
                            <i class="ph-fill ph-eye"></i>
                        </div>
                        <div class="lgnewui-traffic-card">
                            <div class="lgnewui-stats-header-row">
                                <div class="lgnewui-icon-circle-glass">
                                    <i class="ph-bold ph-users-three lgnewui-icon-sm-white"></i>
                                </div>
                                <div class="lgnewui-stats-title" data-lg-tip="累计访问">累计访问</div>
                            </div>
                            <div class="lgnewui-traffic-metrics">
                                <div class="lgnewui-traffic-metric" data-lg-tip="总访客数：<?php echo $totalVisitors ?>" data-lg-tip-force="true">
                                    <div class="lgnewui-font-num lgnewui-traffic-value"><?php echo $totalVisitors ?></div>
                                    <div class="lgnewui-traffic-label">总访客数</div>
                                </div>
                                <div class="lgnewui-traffic-divider" aria-hidden="true"></div>
                                <div class="lgnewui-traffic-metric" data-lg-tip="总访问次：<?php echo $totalVisits ?>" data-lg-tip-force="true">
                                    <div class="lgnewui-font-num lgnewui-traffic-value"><?php echo $totalVisits ?></div>
                                    <div class="lgnewui-traffic-label">总访问次</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 我们的小世界 -->
                <div class="lgnewui-col-2" data-aos="fade-up" data-aos-delay="300">
                    <div class="lgnewui-widget lgnewui-widget--stats-vibrant-6">
                        <div class="lgnewui-widget__bg-icon lgnewui-runtime-bg-icon">
                            <i class="ph-fill ph-planet"></i>
                        </div>
                        <div class="lgnewui-flex-col-runtime">
                            <div>
                                <div class="lgnewui-header-row-sm">
                                    <div class="lgnewui-icon-circle-glass">
                                        <i class="ph-bold ph-planet lgnewui-icon-sm-white"></i>
                                    </div>
                                    <div class="lgnewui-stats-title" data-lg-tip="我们的小世界">我们的小世界</div>
                                </div>
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

        <!-- ===== 3. 清单事件 ===== -->
        <section id="events" class="lgnewui-section">
            <div class="lgnewui-section-header lgnewui-section-header--rose" data-aos="fade-up" data-aos-delay="0">
                <div class="lgnewui-section-header__left">
                    <h2 class="lgnewui-section-title lgnewui-section-title-color-rose lgnewui-flex-center">
                        <div class="lgnewui-section-icon-box lgnewui-section-icon-box--rose">
                            <i class="ph-fill ph-heart lgnewui-icon-md-white"></i>
                        </div>
                        <span>清单</span>
                        <span class="lgnewui-badge-new">NEW</span>
                    </h2>
                </div>
                <div class="lgnewui-section-header__right">
                    <a href="lovelist.php" class="lgnewui-link-more">
                        <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="lgnewui-events-grid">
                <?php
                $recentEvents = null;
                if ($connect) {
                    $recentEvents = @mysqli_query($connect, "SELECT id, eventname, imgurl, is_done, icon, note, location, date FROM lovelist ORDER BY id DESC LIMIT 4");
                }
                if ($recentEvents && mysqli_num_rows($recentEvents) > 0):
                    $eidx = 0; while ($evt = mysqli_fetch_array($recentEvents)):
                        $hasImg = !empty($evt['imgurl']) && $evt['imgurl'] !== '0';
                        $isDone = (isset($evt['is_done']) && intval($evt['is_done']) === 1) || (!isset($evt['is_done']) && intval($evt['icon'] ?? 0) === 1);
                        $evtNote = $evt['note'] ?? $evt['content'] ?? '';
                        $evtLocation = $evt['location'] ?? '';
                        $evtDate = $evt['date'] ?? '';
                ?>
                <div data-aos="fade-up" data-aos-delay="<?php echo $eidx * 50 ?>">
                    <a href="lovelist.php#event-<?php echo $evt['id'] ?>"
                        class="lgnewui-event-card <?php echo $hasImg ? 'lgnewui-event-card--has-img' : ($isDone ? '' : 'lgnewui-event-card--locked'); ?> lgnewui-event-card--link">
                        <?php if ($hasImg): ?>
                        <img class="lgnewui-event-bg-img" src="<?php echo htmlspecialchars($evt['imgurl']) ?>" alt="<?php echo htmlspecialchars($evt['eventname']) ?>">
                        <div class="lgnewui-event-overlay"></div>
                        <?php endif; ?>
                        <div class="lgnewui-event-content">
                            <?php if (!$isDone && !$hasImg): ?>
                            <div class="lgnewui-flex-between-start">
                                <div class="lgnewui-event-icon">
                                    <i class="ph-duotone ph-lock-key"></i>
                                </div>
                                <i class="ph-fill ph-lock-key lgnewui-event-seal"></i>
                            </div>
                            <?php else: ?>
                            <div>
                                <div class="lgnewui-event-icon">
                                    <i class="ph-fill ph-heart"></i>
                                </div>
                            </div>
                            <?php endif; ?>
                            <div class="lgnewui-event-content-mt">
                                <h3 class="lgnewui-event-title <?php echo $hasImg ? 'lgnewui-text-white' : '' ?> lgnewui-text-xl lgnewui-font-bold lgnewui-mb-1">
                                    <?php echo htmlspecialchars($evt['eventname']) ?>
                                </h3>
                                <?php if (!empty($evtNote)): ?>
                                <p class="lgnewui-event-note <?php echo $hasImg ? 'lgnewui-text-white lgnewui-opacity-80 lgnewui-event-note-sm' : (!$isDone ? 'lgnewui-event-note-color' : 'lgnewui-text-muted'); ?>">
                                    <?php echo htmlspecialchars(mb_substr($evtNote, 0, 50, 'UTF-8')) ?>
                                </p>
                                <?php endif; ?>
                            </div>
                            <div class="<?php echo $hasImg ? 'lgnewui-event-footer-glass' : 'lgnewui-event-footer-light'; ?>">
                                <span class="lgnewui-chip <?php echo $hasImg ? 'lgnewui-chip--glass' : 'lgnewui-chip--light'; ?>">
                                    <i class="ph-<?php echo $hasImg ? 'fill' : 'bold'; ?> ph-check-circle"></i>
                                    <?php echo $isDone ? '已完成' : '未完成'; ?>
                                </span>
                                <?php if (!empty($evtLocation)): ?>
                                <span class="lgnewui-chip <?php echo $hasImg ? 'lgnewui-chip--glass' : 'lgnewui-chip--light'; ?>">
                                    <i class="ph-<?php echo $hasImg ? 'fill' : 'bold'; ?> ph-map-pin"></i>
                                    <?php echo htmlspecialchars($evtLocation) ?>
                                </span>
                                <?php endif; ?>
                                <?php if (!empty($evtDate)): ?>
                                <span class="lgnewui-chip <?php echo $hasImg ? 'lgnewui-chip--glass' : 'lgnewui-chip--light'; ?>">
                                    <i class="ph-fill ph-calendar-blank"></i>
                                    <?php echo (!$isDone && !$hasImg) ? '待解锁' : htmlspecialchars($evtDate); ?>
                                </span>
                                <?php elseif (!$isDone && !$hasImg): ?>
                                <span class="lgnewui-chip lgnewui-chip--light">
                                    <i class="ph-bold ph-calendar-blank"></i>
                                    待解锁
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
                <?php $eidx++; endwhile; else: ?>
                <div data-aos="fade-up">
                    <a href="lovelist.php" class="lgnewui-widget" style="background:linear-gradient(135deg,#0f172a,#334155);color:#fff;text-decoration:none;display:block;padding:2rem;text-align:center;">
                        <div style="font-size:2rem;margin-bottom:0.5rem;">💕</div>
                        <div style="font-size:1.1rem;font-weight:700;">恋爱清单</div>
                        <div style="font-size:0.85rem;opacity:0.7;margin-top:0.5rem;">点击查看更多心愿</div>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ===== 4. 纪念日 ===== -->
        <section id="loveday-list" class="lgnewui-section">
            <div class="lgnewui-section-header lgnewui-section-header--purple" data-aos="fade-up" data-aos-delay="0">
                <div class="lgnewui-section-header__left">
                    <h2 class="lgnewui-section-title lgnewui-section-title-color-purple lgnewui-flex-center">
                        <div class="lgnewui-section-icon-box lgnewui-section-icon-box--purple">
                            <i class="ph-fill ph-calendar lgnewui-icon-md-white"></i>
                        </div>
                        <span>Love Day</span>
                        <span class="lgnewui-badge-new">FULL</span>
                    </h2>
                </div>
                <div class="lgnewui-section-header__right">
                    <div class="lgnewui-ios-tabs">
                        <div class="lgnewui-ios-tabs-slider"></div>
                        <button class="lgnewui-ios-tab active" data-filter="all" onclick="filterLoveDays('all', this)">
                            <i class="ph-fill ph-squares-four"></i> <span>全部</span>
                        </button>
                        <button class="lgnewui-ios-tab" data-filter="past" onclick="filterLoveDays('past', this)">
                            <i class="ph-fill ph-heart"></i> <span>纪念日</span>
                        </button>
                        <button class="lgnewui-ios-tab" data-filter="future" onclick="filterLoveDays('future', this)">
                            <i class="ph-fill ph-hourglass"></i> <span>倒计时</span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="lgnewui-grid lgnewui-loveday-grid">
                <?php
                $lovedays = [];
                if ($connect) {
                    $ldResult = @mysqli_query($connect, "SELECT id, type, title, content, date, location FROM timeline ORDER BY date ASC");
                    if ($ldResult) {
                        while ($ld = mysqli_fetch_array($ldResult)) {
                            $ldDate = $ld['date'];
                            $ldTs = strtotime($ldDate);
                            $diffDays = floor((time() - $ldTs) / 86400);
                            $isFuture = $diffDays < 0;
                            $isAnniversary = !$isFuture;
                            $isCountdown = $isFuture;
                            // 农历日期（简化显示）
                            $lunarDate = '';
                            if (function_exists('lunarDateInfo')) {
                                $lunarDate = lunarDateInfo($ldDate);
                            }
                            $lovedays[] = [
                                'title' => $ld['title'],
                                'date' => $ldDate,
                                'days' => abs($diffDays),
                                'isFuture' => $isFuture,
                                'isAnniversary' => $isAnniversary,
                                'isCountdown' => $isCountdown,
                                'lunarDate' => $lunarDate,
                            ];
                        }
                    }
                }
                if (empty($lovedays)) {
                    $lovedays[] = ['title' => '在一起', 'date' => date('Y-m-d', $startTs), 'days' => $runtimeDays, 'isFuture' => false, 'isAnniversary' => true, 'isCountdown' => false, 'lunarDate' => ''];
                }
                $ldIdx = 0;
                foreach ($lovedays as $ld):
                ?>
                <div data-aos="fade-up" data-aos-delay="<?php echo $ldIdx * 50 ?>" data-loveday-type="<?php echo $ld['isFuture'] ? 'future' : 'past'; ?>">
                    <div class="lgnewui-widget lgnewui-widget--loveday-vibrant <?php echo $ld['isFuture'] ? 'lgnewui-widget--loveday-future' : 'lgnewui-widget--loveday-past' ?>">
                        <div class="lgnewui-loveday-sup-label"><?php echo $ld['isFuture'] ? '还有' : '已经' ?></div>
                        <?php if ($ld['isFuture']): ?>
                        <svg class="lgnewui-loveday-bg-icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <path d="M792 120H232a40 40 0 0 0-40 40v56c0 88.4 71.6 160 160 160 5.2 0 10.4-.2 15.6-.6 4.4 11.8 4.4 24.8 0 36.6-5.2-.4-10.4-.6-15.6-.6-88.4 0-160 71.6-160 160v56a40 40 0 0 0 40 40h560a40 40 0 0 0 40-40v-56c0-88.4-71.6-160-160-160-5.2 0-10.4.2-15.6.6-4.4-11.8-4.4-24.8 0-36.6 5.2.4 10.4.6 15.6.6 88.4 0 160-71.6 160-160v-56a40 40 0 0 0-40-40z" fill="currentColor"></path>
                        </svg>
                        <?php else: ?>
                        <svg class="lgnewui-loveday-bg-icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <path d="M923 283.6a260.04 260.04 0 0 0-56.9-82.6c-64.5-70-170.8-84-245.5-32.9L512 216.7l-108.6-48.6c-74.7-51.1-181-37.1-245.5 32.9-64.5 70-79.9 174.6-44.1 262.8 33.3 82.3 98.7 151.7 185.3 227.1L512 884.2l212.9-193.3c86.6-75.4 152-144.8 185.3-227.1 35.8-88.2 20.4-192.8-44.1-262.8z" fill="currentColor"></path>
                        </svg>
                        <?php endif; ?>
                        <div class="lgnewui-flex-between-center lgnewui-loveday-content">
                            <div class="lgnewui-flex-center-gap" tabindex="0">
                                <div class="lgnewui-icon-box-glass-white">
                                    <?php if ($ld['isFuture']): ?>
                                    <svg viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M810 249.5c-38.6-38.6-83.5-68.8-133.5-90-51.8-21.9-106.8-33-163.5-33s-111.7 11.1-163.5 33c-50 21.2-94.9 51.4-133.5 90-38.6 38.6-68.8 83.5-90 133.5-21.9 51.8-33 106.8-33 163.5s11.1 111.7 33 163.5c21.2 50 51.4 94.9 90 133.5s83.5 68.8 133.5 90c51.8 21.9 106.8 33 163.5 33s111.7-11.1 163.5-33c50-21.2 94.9-51.4 133.5-90S878.8 760 900 710c21.9-51.8 33-106.8 33-163.5S921.9 434.8 900 383c-21.2-50-51.5-94.9-90-133.5z m-297 657c-198.5 0-360-161.5-360-360s161.5-360 360-360 360 161.5 360 360-161.5 360-360 360zM357 96.5c-42.3-49.6-141-53.3-208.1 4s-77.3 153.9-35 203.5L357 96.5zM877.2 100.5C810 43.2 711.3 47 669 96.5L912.2 304c42.3-49.6 32.1-146.2-35-203.5z"></path>
                                        <path d="M667.1 558.6H543V351c0-17.9-14.5-32.4-32.4-32.4-15.2 0-27.6 12.3-27.6 27.6v272.4h182.2c17.1 0 30.9-13.8 30.9-30.9 0-16.1-13-29.1-29-29.1z"></path>
                                    </svg>
                                    <?php else: ?>
                                    <svg viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M470.4 204.8l44.8 44.8 44.8-44.8c99.2-99.2 262.4-99.2 361.6 0 48 48 73.6 112 73.6 179.2 0 19.2-12.8 32-32 32s-32-12.8-32-32c0-51.2-19.2-99.2-57.6-134.4-73.6-73.6-195.2-73.6-272 0l-67.2 67.2c-12.8 12.8-32 12.8-44.8 0l-67.2-67.2c-73.6-73.6-195.2-73.6-272 0-73.6 73.6-73.6 195.2 0 272L512 883.2c12.8 12.8 12.8 32 0 44.8s-32 12.8-44.8 0L105.6 566.4c-99.2-99.2-99.2-262.4 0-361.6 102.4-102.4 262.4-102.4 364.8 0z m176 710.4L425.6 694.4c-57.6-57.6-57.6-147.2 0-204.8 57.6-57.6 147.2-57.6 204.8 0l57.6 57.6 57.6-57.6c57.6-57.6 147.2-57.6 204.8 0 57.6 57.6 57.6 147.2 0 204.8L729.6 915.2c-9.6 9.6-25.6 16-38.4 16-19.2 0-32-6.4-44.8-16z m256-265.6c32-32 32-83.2 0-112-32-32-83.2-32-112 0l-80 80c-12.8 12.8-32 12.8-44.8 0l-80-80c-32-32-83.2-32-112 0-32 32-32 83.2 0 112L688 864l214.4-214.4z" fill="#ffffff"></path>
                                    </svg>
                                    <?php endif; ?>
                                </div>
                                <div class="lgnewui-loveday-copy">
                                    <div class="lgnewui-loveday-title" data-lg-tip="<?php echo htmlspecialchars($ld['title']) ?>"><?php echo htmlspecialchars($ld['title']) ?></div>
                                    <div class="lgnewui-loveday-date">
                                        <span class="lgnewui-loveday-date-line"><?php echo ($ld['isFuture'] ? '目标日：' : '起始日：') . $ld['date'] ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="lgnewui-text-right">
                                <div class="lgnewui-loveday-count">
                                    <?php echo $ld['days'] ?><span class="lgnewui-loveday-unit">天</span>
                                </div>
                                <?php if (!empty($ld['lunarDate'])): ?>
                                <div class="lgnewui-loveday-lunar-inline"><?php echo htmlspecialchars($ld['lunarDate']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $ldIdx++; endforeach; ?>
            </div>
        </section>

        <!-- ===== 5. 点滴 ===== -->
        <section id="updates" class="lgnewui-section">
            <div class="lgnewui-section-header lgnewui-section-header--blue" data-aos="fade-up" data-aos-delay="0">
                <div class="lgnewui-section-header__left">
                    <h2 class="lgnewui-section-title lgnewui-section-title-color-blue lgnewui-flex-center">
                        <div class="lgnewui-section-icon-box lgnewui-section-icon-box--blue">
                            <i class="ph-fill ph-star lgnewui-icon-md-white"></i>
                        </div>
                        <span>点滴</span>
                        <span class="lgnewui-badge-new">NEW</span>
                    </h2>
                </div>
                <div class="lgnewui-section-header__right">
                    <a href="articles.php" class="lgnewui-link-more">
                        <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="lgnewui-journal-grid">
                <?php
                $recentArticles = null;
                if ($connect) {
                    $recentArticles = @mysqli_query($connect, "SELECT id, title, text, author, date, weather, mood, location, views, likes FROM little ORDER BY id DESC LIMIT 6");
                }
                if ($recentArticles && mysqli_num_rows($recentArticles) > 0):
                    $idx = 0; while ($art = mysqli_fetch_array($recentArticles)):
                        $dayNum = floor((time() - strtotime($art['date'])) / 86400);
                        $artLocation = $art['location'] ?? '';
                        $artWeather = $art['weather'] ?? '';
                        $artMood = $art['mood'] ?? '';
                        $artViews = $art['views'] ?? 0;
                        $artLikes = $art['likes'] ?? 0;
                        $artAuthor = !empty($art['author']) ? $art['author'] : $text['boy'];
                        $isMaleAuthor = ($artAuthor === $text['boy']);
                ?>
                <div data-aos="fade-up" data-aos-delay="<?php echo $idx * 50 ?>">
                    <a href="page.php?id=<?php echo $art['id'] ?>" class="lgnewui-journal-card lgnewui-journal-card--link">
                        <div class="lgnewui-watermark">DAY <?php echo $dayNum ?></div>
                        <div class="lgnewui-journal-header">
                            <div class="lgnewui-journal-user">
                                <img data-src="" class="lgnewui-journal-avatar lazy">
                                <div>
                                    <div class="lgnewui-font-sm-bold"><?php echo htmlspecialchars($artAuthor, ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="lgnewui-journal-meta"><?php echo htmlspecialchars($art['date'], ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="lgnewui-journal-content">
                            <h3 class="lgnewui-journal-title lgnewui-journal-title-text"><?php echo htmlspecialchars(mb_substr($art['title'], 0, 30, 'UTF-8')) ?></h3>
                            <p class="lgnewui-journal-body lgnewui-journal-body-clamp"><?php echo htmlspecialchars(strip_tags(mb_substr($art['text'], 0, 100, 'UTF-8'))) ?></p>
                        </div>
                        <div class="lgnewui-journal-footer">
                            <div class="lgnewui-flex-gap-sm">
                                <?php if (!empty($artLocation)): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-map-pin"></i> <?php echo htmlspecialchars($artLocation, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                                <?php if (!empty($artWeather)): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-cloud-sun"></i> <?php echo htmlspecialchars($artWeather, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                                <?php if (!empty($artMood)): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-smiley"></i> <?php echo htmlspecialchars($artMood, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                                <?php if ($artViews > 0): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-eye"></i> <?php echo intval($artViews) ?></span>
                                <?php endif; ?>
                                <?php if ($artLikes > 0): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-heart"></i> <?php echo intval($artLikes) ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
                <?php $idx++; endwhile; endif; ?>
            </div>
        </section>

        <!-- ===== 6. 相册（马赛克网格） ===== -->
        <section id="album" class="lgnewui-section">
            <div class="lgnewui-section-header lgnewui-section-header--orange" data-aos="fade-up" data-aos-delay="0">
                <div class="lgnewui-section-header__left">
                    <h2 class="lgnewui-section-title lgnewui-section-title-color-orange lgnewui-flex-center">
                        <div class="lgnewui-section-icon-box lgnewui-section-icon-box--orange">
                            <i class="ph-fill ph-image lgnewui-icon-md-white"></i>
                        </div>
                        <span>相册</span>
                        <span class="lgnewui-badge-new">NEW</span>
                    </h2>
                </div>
                <div class="lgnewui-section-header__right">
                    <a href="albums.php" class="lgnewui-link-more">
                        <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="lgnewui-mosaic-grid<?php
                // 计算相册数量用于动态class
                $mosaicCount = 0;
                if ($connect) {
                    $countResult = @mysqli_query($connect, "SELECT COUNT(*) as cnt FROM photo");
                    if ($countResult) { $countRow = mysqli_fetch_array($countResult); $mosaicCount = intval($countRow['cnt']); }
                }
                echo ' lgnewui-mosaic-count-' . min(max($mosaicCount, 1), 6);
            ?>">
                <?php
                $recentAlbums = null;
                if ($connect) {
                    $recentAlbums = @mysqli_query($connect, "SELECT id, code, title, img, `desc`, author, location, date FROM photo ORDER BY id DESC LIMIT 6");
                }
                if ($recentAlbums && mysqli_num_rows($recentAlbums) > 0):
                    $idx = 0; while ($album = mysqli_fetch_array($recentAlbums)):
                        $albumTitle = $album['title'] ?? $album['imgText'] ?? '';
                        $albumCover = $album['imgUrl'] ?? $album['imgurl'] ?? $album['cover'] ?? '';
                        $albumDate = $album['date'] ?? '';
                        $albumCount = $album['count'] ?? $album['photo_count'] ?? 0;
                        $albumCode = $album['img_code'] ?? $album['code'] ?? '';
                        $albumAuthor = $album['author'] ?? '';
                        $albumLocation = $album['location'] ?? '';
                        // 作者头像
                        $authorImg = '';
                        if ($albumAuthor === ($text['boy'] ?? '')) {
                            $authorImg = $boyimg_val;
                        } elseif ($albumAuthor === ($text['girl'] ?? '')) {
                            $authorImg = $girlimg_val;
                        }
                        // 格式化日期为中文
                        $formattedDate = '';
                        if (!empty($albumDate)) {
                            $ts = strtotime($albumDate);
                            if ($ts) {
                                $y = date('Y', $ts); $m = (int)date('n', $ts); $d = (int)date('j', $ts);
                                $cnNums = ['〇','一','二','三','四','五','六','七','八','九','十','十一','十二'];
                                $formattedDate = $cnNums[0] . str_repeat($cnNums[0], 3) . '年' . $cnNums[$m] . '月' . ($d > 10 ? $cnNums[10] . $cnNums[$d-10] : $cnNums[$d]) . '日';
                            }
                        }
                ?>
                <div data-aos="fade-up" data-aos-delay="<?php echo $idx * 50 ?>">
                    <a href="<?php echo !empty($albumCode) ? 'album-detail.php?code=' . urlencode($albumCode) : 'albums.php'; ?>" class="lgnewui-mosaic-item">
                        <img data-src="<?php echo htmlspecialchars($albumCover, ENT_QUOTES, 'UTF-8') ?>" class="lgnewui-mosaic-img lazy">
                        <div class="lgnewui-mosaic-pos-tr">
                            <div class="lgnewui-chip--dark-glass">
                                <?php if (!empty($albumLocation)): ?>
                                <span class="lgnewui-flex-center-gap-xs"><i class="ph-fill ph-map-pin"></i> <?php echo htmlspecialchars($albumLocation, ENT_QUOTES, 'UTF-8') ?></span>
                                <span class="lgnewui-mosaic-divider"></span>
                                <?php endif; ?>
                                <span class="lgnewui-flex-center-gap-xs"><i class="ph-fill ph-image"></i> <?php echo intval($albumCount) ?></span>
                            </div>
                        </div>
                        <div class="lgnewui-mosaic-overlay">
                            <div class="lgnewui-mosaic-overlay-content">
                                <?php if (!empty($authorImg)): ?>
                                <div class="lgnewui-capsule lgnewui-capsule--avatar lgnewui-mosaic-avatar-mb">
                                    <img data-src="<?php echo htmlspecialchars($authorImg, ENT_QUOTES, 'UTF-8') ?>" class="lgnewui-capsule__img lazy">
                                    <span class="lgnewui-capsule__text lgnewui-text-white"><?php echo htmlspecialchars($albumAuthor, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <?php endif; ?>
                                <h3 class="u-font-serif lgnewui-mosaic-title"><?php echo htmlspecialchars($albumTitle, ENT_QUOTES, 'UTF-8') ?></h3>
                                <?php if (!empty($formattedDate)): ?>
                                <div class="u-font-serif lgnewui-mosaic-date"><?php echo $formattedDate ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
                <?php $idx++; endwhile; else: ?>
                <div data-aos="fade-up">
                    <div class="lgnewui-widget" style="text-align:center;padding:2rem;">
                        <div style="font-size:2rem;margin-bottom:0.5rem;">📸</div>
                        <p style="color:#94a3b8;">暂无相册</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ===== 7. 留言（横向滚动轮播） ===== -->
        <section id="messages" class="lgnewui-section">
            <div class="lgnewui-section-header lgnewui-section-header--teal" data-aos="fade-up" data-aos-delay="0">
                <div class="lgnewui-section-header__left">
                    <h2 class="lgnewui-section-title lgnewui-section-title-color-teal lgnewui-flex-center">
                        <div class="lgnewui-section-icon-box lgnewui-section-icon-box--teal">
                            <i class="ph-fill ph-chat-circle-text lgnewui-icon-md-white"></i>
                        </div>
                        <span>留言</span>
                        <span class="lgnewui-badge-new">NEW</span>
                    </h2>
                </div>
                <div class="lgnewui-section-header__right">
                    <a href="messages.php" class="lgnewui-link-more">
                        <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="lgnewui-home-message-container" id="messageCarousel">
                <div class="lgnewui-home-message-track">
                    <?php
                    $recentMsgs = null;
                    if ($connect) {
                        $recentMsgs = @mysqli_query($connect, "SELECT id, name, QQ, text, time, ip, city, device, browser, likes FROM leaving ORDER BY id DESC LIMIT 8");
                    }
                    if ($recentMsgs && mysqli_num_rows($recentMsgs) > 0):
                        while ($msg = mysqli_fetch_array($recentMsgs)):
                            $msgAvatar = $msg['avatar'] ?? '';
                            $msgQQ = $msg['qqimg'] ?? $msg['qq'] ?? '';
                            $msgName = $msg['name'] ?? '';
                            $msgIsAdmin = !empty($msg['is_admin']) || $msgName === $text['boy'] || $msgName === $text['girl'];
                            $msgLocation = $msg['location'] ?? '';
                            $msgDevice = $msg['device'] ?? '';
                            $msgBrowser = $msg['browser'] ?? '';
                            $msgLevel = $msg['level'] ?? 0;
                            // 头像：优先使用自定义头像，否则使用 weavatar
                            if (empty($msgAvatar) && !empty($msgQQ)) {
                                $msgAvatar = '/services/avatar-proxy.php?type=qq&qq=' . urlencode($msgQQ) . '&s=120';
                            } elseif (empty($msgAvatar)) {
                                $msgAvatar = '/assets/img/avatars/default.png';
                            }
                    ?>
                    <a href="messages.php#comment_<?php echo $msg['id'] ?>" class="lgnewui-home-message-card">
                        <div class="lgnewui-home-message-header">
                            <img class="lgnewui-home-message-avatar" src="<?php echo htmlspecialchars($msgAvatar, ENT_QUOTES, 'UTF-8') ?>" alt="<?php echo htmlspecialchars($msgName, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="lgnewui-home-message-user-info">
                                <div class="lgnewui-home-message-name-row">
                                    <span class="lgnewui-home-message-user-name"><?php echo htmlspecialchars($msgName, ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php if ($msgIsAdmin): ?>
                                    <span class="lgnewui-home-message-badge lgnewui-home-message-badge--admin">站长</span>
                                    <?php endif; ?>
                                    <?php if ($msgLevel > 0): ?>
                                    <span class="lgnewui-home-message-badge lgnewui-home-message-badge--level"><i class="ph ph-arrow-bend-down-right"></i> <?php echo $msgLevel <= 1 ? '一级' : ($msgLevel <= 2 ? '二级' : '三级'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="lgnewui-home-message-post-time"><?php echo htmlspecialchars($msg['date'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>
                        <div class="lgnewui-home-message-content"><?php echo htmlspecialchars(mb_substr($msg['text'], 0, 80, 'UTF-8')) ?></div>
                        <div class="lgnewui-home-message-divider"></div>
                        <div class="lgnewui-home-message-footer">
                            <?php if (!empty($msgLocation)): ?>
                            <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-fill ph-map-pin"></i> <?php echo htmlspecialchars($msgLocation, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                            <?php if (!empty($msgDevice)): ?>
                            <span class="lgnewui-chip lgnewui-chip--light lgnewui-chip--no-transform"><i class="ph-bold ph-devices"></i> <?php echo htmlspecialchars($msgDevice, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                            <?php if (!empty($msgBrowser)): ?>
                            <span class="lgnewui-chip lgnewui-chip--light lgnewui-chip--no-transform"><i class="ph-bold ph-globe"></i> <?php echo htmlspecialchars($msgBrowser, ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <?php endwhile; endif; ?>
                </div>
            </div>
        </section>

        <!-- ===== 8. 结尾区（笔记本风格） ===== -->
        <section class="lgnewui-epilogue" data-aos="fade-up">
            <div class="lgnewui-epilogue__holes">
                <?php for ($i = 0; $i < 12; $i++): ?>
                <div class="lgnewui-epilogue__hole"></div>
                <?php endfor; ?>
            </div>
            <div class="lgnewui-epilogue__content">
                <div class="lgnewui-epilogue__header">
                    <h3 class="lgnewui-epilogue__title">未完 · 待续</h3>
                </div>
                <div class="lgnewui-epilogue__quote-container">
                    <p class="lgnewui-epilogue__text" id="epilogue-quote-text">朝暮与年岁并往，与你行至天光。</p>
                </div>
            </div>
            <div class="lgnewui-epilogue__actions">
                <div class="lgnewui-epilogue__nav">
                    <a href="messages.php" class="lgnewui-epilogue__btn" id="epilogue-leaving-btn">
                        <i class="ph-fill ph-chat-circle-dots"></i> 留下祝福
                    </a>
                    <a href="albums.php" class="lgnewui-epilogue__btn" id="epilogue-random-album">
                        <i class="ph-fill ph-images"></i> 随机光影
                    </a>
                    <a href="articles.php" class="lgnewui-epilogue__btn" id="epilogue-random-article">
                        <i class="ph-fill ph-notebook"></i> 随机碎片
                    </a>
                </div>
                <div class="lgnewui-epilogue__tools">
                    <button class="lgnewui-epilogue__tool-btn" id="epilogue-btn-refresh" title="换一句">
                        <i class="ph-bold ph-shuffle"></i>
                    </button>
                    <button class="lgnewui-epilogue__tool-btn" id="epilogue-btn-copy" title="复制">
                        <i class="ph-bold ph-copy"></i>
                    </button>
                </div>
            </div>
        </section>

    </main>

    </div>

    <!-- 访问信标 -->
    <script>
    (function() {
        var beacon = new Image();
        beacon.src = 'services/visitor-stats.php?t=' + Date.now();
    })();
    </script>

    <?php include_once 'footer.php'; ?>
