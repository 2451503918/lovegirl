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
                <!-- 距离气泡 -->
                <div class="love-info-wrapper">
                    <div class="distance-bubble" id="distanceBubble" onclick="if(window.LGMap&&LGMap.show){LGMap.show();}">
                        <div class="distance-icon-box"><i class="ph-fill ph-map-pin-line"></i></div>
                        <div class="distance-text">
                            <span class="distance-text-sm">相距</span>
                            <span class="km-value" id="distanceKm">--</span>
                            <span class="distance-text-sm">km</span>
                        </div>
                    </div>
                </div>
                <!-- 男方头像 -->
                <div class="img-male">
                    <div class="avatarArea lgewui-head-avatar-boy">
                        <img draggable="false" class="avatarFrame" src="https://s1.locimg.com/2024/10/18/db01c99842e69.png" style="transform: scale(1.6);top: 2px;left: 2px;">
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
                    <span class="shadow-blur"><?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <!-- 爱心图标 -->
                <div class="love-icon">
                    <div class="love-info-wrapper"></div>
                    <img draggable="false" src="/Style/img/like.svg">
                </div>
                <!-- 女方头像 -->
                <div class="img-female">
                    <div class="avatarArea lgewui-head-avatar-girl">
                        <img draggable="false" class="avatarFrame" src="https://s1.locimg.com/2024/10/18/db01c99842e69.png" style="transform: scale(1.6);top: 2px;left: 2px;">
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
                    <span class="shadow-blur"><?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?></span>
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
    <main class="lgnewui-home lgnewui-container" style="padding-bottom:2rem;">

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
                    <h2 class="lgnewui-day-poetic-title">
                        <?php echo preg_replace('/\{([^}]+)\}/', '<b>$1</b>', htmlspecialchars($text['logo'], ENT_QUOTES, 'UTF-8')) ?><br>
                        <span style="font-size:0.7em;opacity:0.7;">与你行至天光</span>
                    </h2>
                    <div class="lgnewui-day-start-date-capsule">
                        <div class="lgnewui-day-icon-circle"><i class="ph-fill ph-heart"></i></div>
                        <div>
                            <span class="lgnewui-day-date-label-small">Together Since</span>
                            <span class="lgnewui-day-date-value-clean" id="lgnewui-day-start-date-display"><?php echo htmlspecialchars(str_replace('T', ' ', $text['startTime']), ENT_QUOTES, 'UTF-8') ?></span>
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
                                <img src="" class="lgnewui-home-weather-avatar lg-male-avatar" alt="<?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?>">
                                <span class="lgnewui-home-weather-username"><?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?></span>
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
                                <img src="" class="lgnewui-home-weather-avatar lg-female-avatar" alt="<?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?>">
                                <span class="lgnewui-home-weather-username"><?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?></span>
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

                <!-- 双方头像卡片（我们） -->
                <div class="lgnewui-col-2" data-aos="fade-up" data-aos-delay="120">
                    <div class="lgnewui-widget lgnewui-widget--presence">
                        <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-heart"></i></div>
                        <div class="lgnewui-flex-col-between-relative">
                            <div class="lgnewui-flex-between-center lgnewui-mb-4">
                                <div class="lgnewui-flex-center-gap">
                                    <div class="lgnewui-icon-box-glass">
                                        <i class="ph-bold ph-users-three lgnewui-icon-md-white"></i>
                                    </div>
                                    <div class="lgnewui-card-title-lg">我们</div>
                                </div>
                                <div class="lgnewui-presence-badge">Together</div>
                            </div>
                            <div class="lgnewui-presence-card">
                                <div class="lgnewui-presence-people">
                                    <div class="lgnewui-presence-person">
                                        <img src="" class="lgnewui-presence-avatar lg-male-avatar" alt="<?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?>">
                                        <div class="lgnewui-presence-person__body">
                                            <div class="lgnewui-presence-person__name-row">
                                                <span class="lgnewui-presence-name"><?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?></span>
                                                <span class="lgnewui-presence-gender is-male">♂</span>
                                            </div>
                                            <div class="lgnewui-presence-state">
                                                <span class="lgnewui-presence-state__dot"></span>
                                                <span>在线</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="lgnewui-presence-person">
                                        <img src="" class="lgnewui-presence-avatar lg-female-avatar" alt="<?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?>">
                                        <div class="lgnewui-presence-person__body">
                                            <div class="lgnewui-presence-person__name-row">
                                                <span class="lgnewui-presence-name"><?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?></span>
                                                <span class="lgnewui-presence-gender is-female">♀</span>
                                            </div>
                                            <div class="lgnewui-presence-state">
                                                <span class="lgnewui-presence-state__dot"></span>
                                                <span>在线</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                            <div>
                                <div class="lgnewui-event-icon">
                                    <i class="ph-fill <?php echo $isDone ? 'ph-heart' : ($hasImg ? 'ph-heart' : 'ph-lock-key'); ?>"></i>
                                </div>
                            </div>
                            <div class="lgnewui-event-content-mt">
                                <h3 class="lgnewui-event-title <?php echo $hasImg ? 'lgnewui-text-white' : '' ?> lgnewui-text-xl lgnewui-font-bold lgnewui-mb-1">
                                    <?php echo htmlspecialchars($evt['eventname']) ?>
                                </h3>
                                <?php if (!empty($evtNote)): ?>
                                <p class="lgnewui-event-note <?php echo $hasImg ? 'lgnewui-text-white' : 'lgnewui-text-muted' ?>">
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
                                    <i class="ph-fill ph-map-pin"></i>
                                    <?php echo htmlspecialchars($evtLocation) ?>
                                </span>
                                <?php endif; ?>
                                <?php if (!empty($evtDate)): ?>
                                <span class="lgnewui-chip <?php echo $hasImg ? 'lgnewui-chip--glass' : 'lgnewui-chip--light'; ?>">
                                    <i class="ph-bold ph-calendar-blank"></i>
                                    <?php echo htmlspecialchars($evtDate) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php if (!$isDone && !$hasImg): ?>
                        <i class="ph-fill ph-lock-key lgnewui-event-seal"></i>
                        <?php endif; ?>
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
                            <i class="ph-fill ph-heart"></i> 全部
                        </button>
                        <button class="lgnewui-ios-tab" data-filter="anniversary" onclick="filterLoveDays('anniversary', this)">
                            <i class="ph-fill ph-calendar-heart"></i> 纪念日
                        </button>
                        <button class="lgnewui-ios-tab" data-filter="countdown" onclick="filterLoveDays('countdown', this)">
                            <i class="ph-fill ph-clock-countdown"></i> 倒计时
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
                <div data-aos="fade-up" data-aos-delay="<?php echo $ldIdx * 50 ?>" data-loveday-type="<?php echo $ld['isFuture'] ? 'countdown' : 'anniversary'; ?>">
                    <div class="lgnewui-widget lgnewui-widget--loveday-vibrant <?php echo $ld['isFuture'] ? 'lgnewui-widget--loveday-future' : 'lgnewui-widget--loveday-past' ?>">
                        <div class="lgnewui-loveday-sup-label"><?php echo $ld['isFuture'] ? '还有' : '已经' ?></div>
                        <svg class="lgnewui-loveday-bg-icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg">
                            <path d="M923 283.6a260.04 260.04 0 0 0-56.9-82.6c-64.5-70-170.8-84-245.5-32.9L512 216.7l-108.6-48.6c-74.7-51.1-181-37.1-245.5 32.9-64.5 70-79.9 174.6-44.1 262.8 33.3 82.3 98.7 151.7 185.3 227.1L512 884.2l212.9-193.3c86.6-75.4 152-144.8 185.3-227.1 35.8-88.2 20.4-192.8-44.1-262.8z" fill="currentColor"></path>
                        </svg>
                        <div class="lgnewui-flex-between-center lgnewui-loveday-content">
                            <div class="lgnewui-flex-center-gap" tabindex="0">
                                <div class="lgnewui-icon-box-glass-white">
                                    <i class="ph-fill <?php echo $ld['isFuture'] ? 'ph-clock-countdown' : 'ph-heart' ?>"></i>
                                </div>
                                <div class="lgnewui-loveday-copy">
                                    <div class="lgnewui-loveday-title"><?php echo htmlspecialchars($ld['title']) ?></div>
                                    <div class="lgnewui-loveday-date">
                                        <span class="lgnewui-loveday-date-line"><?php echo ($ld['isFuture'] ? '目标日：' : '起始日：') . $ld['date'] ?></span>
                                        <?php if (!empty($ld['lunarDate'])): ?>
                                        <span class="lgnewui-loveday-lunar"><?php echo htmlspecialchars($ld['lunarDate']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="lgnewui-text-right">
                                <div class="lgnewui-loveday-count">
                                    <?php echo $ld['days'] ?><span class="lgnewui-loveday-unit">天</span>
                                </div>
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
                                <img data-src="" class="lgnewui-journal-avatar lazy <?php echo $isMaleAuthor ? 'lg-male-avatar' : 'lg-female-avatar'; ?>">
                                <div>
                                    <div class="lgnewui-font-sm-bold"><?php echo htmlspecialchars($artAuthor, ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="lgnewui-journal-meta"><?php echo htmlspecialchars($art['date'], ENT_QUOTES, 'UTF-8') ?></div>
                                </div>
                            </div>
                        </div>
                        <h3 class="lgnewui-journal-title"><?php echo htmlspecialchars(mb_substr($art['title'], 0, 30, 'UTF-8')) ?></h3>
                        <p class="lgnewui-journal-body lgnewui-journal-body-clamp"><?php echo htmlspecialchars(strip_tags(mb_substr($art['text'], 0, 100, 'UTF-8'))) ?></p>
                        <div class="lgnewui-journal-footer">
                            <div class="lgnewui-flex-gap-sm">
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-calendar-blank"></i> <?php echo date('Y-m-d', strtotime($art['date'])) ?></span>
                                <?php if (!empty($artLocation)): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-fill ph-map-pin"></i> <?php echo htmlspecialchars($artLocation, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                                <?php if (!empty($artWeather)): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-fill ph-cloud"></i> <?php echo htmlspecialchars($artWeather, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                                <?php if (!empty($artMood)): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-smiley"></i> <?php echo htmlspecialchars($artMood, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                                <?php if ($artViews > 0): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-eye"></i> <?php echo intval($artViews) ?></span>
                                <?php endif; ?>
                                <?php if ($artLikes > 0): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-fill ph-heart"></i> <?php echo intval($artLikes) ?></span>
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
            <div class="lgnewui-mosaic-grid">
                <?php
                $recentAlbums = null;
                if ($connect) {
                    // 尝试从 photo 表获取相册数据
                    $recentAlbums = @mysqli_query($connect, "SELECT id, code, title, img, `desc`, author, location, date FROM photo ORDER BY id DESC LIMIT 6");
                }
                if ($recentAlbums && mysqli_num_rows($recentAlbums) > 0):
                    $idx = 0; while ($album = mysqli_fetch_array($recentAlbums)):
                        $albumTitle = $album['title'] ?? $album['imgText'] ?? '';
                        $albumCover = $album['imgUrl'] ?? $album['imgurl'] ?? $album['cover'] ?? '';
                        $albumDate = $album['date'] ?? '';
                        $albumCount = $album['count'] ?? $album['photo_count'] ?? 0;
                        $albumCode = $album['img_code'] ?? $album['code'] ?? '';
                ?>
                <div class="lgnewui-mosaic-item lgnewui-mosaic-item--<?php echo ($idx % 6 < 2) ? 'large' : 'small'; ?>" data-aos="fade-up" data-aos-delay="<?php echo $idx * 50 ?>">
                    <a href="<?php echo !empty($albumCode) ? 'album-detail.php?code=' . urlencode($albumCode) : 'albums.php'; ?>" class="lgnewui-album-card">
                        <div class="lgnewui-album-cover">
                            <img data-src="<?php echo htmlspecialchars($albumCover, ENT_QUOTES, 'UTF-8') ?>" class="lgnewui-album-img lazy" alt="<?php echo htmlspecialchars($albumTitle, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="lgnewui-album-overlay"></div>
                        </div>
                        <div class="lgnewui-album-info">
                            <h4 class="lgnewui-album-title"><?php echo htmlspecialchars($albumTitle, ENT_QUOTES, 'UTF-8') ?></h4>
                            <div class="lgnewui-album-meta">
                                <?php if ($albumCount > 0): ?>
                                <span class="lgnewui-chip lgnewui-chip--glass"><i class="ph-fill ph-images"></i> <?php echo intval($albumCount) ?>张</span>
                                <?php endif; ?>
                                <?php if (!empty($albumDate)): ?>
                                <span class="lgnewui-chip lgnewui-chip--glass"><i class="ph-bold ph-calendar-blank"></i> <?php echo htmlspecialchars($albumDate, ENT_QUOTES, 'UTF-8') ?></span>
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
            <div id="messageCarousel" class="lgnewui-home-message-carousel">
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
                                $msgAvatar = 'https://weavatar.com/avatar/' . md5(strtolower(trim($msgQQ))) . '?s=120&d=mp';
                            } elseif (empty($msgAvatar)) {
                                $msgAvatar = 'https://weavatar.com/avatar/?s=120&d=mp';
                            }
                    ?>
                    <div class="lgnewui-home-message-card">
                        <div class="lgnewui-home-message-header">
                            <img class="lgnewui-home-message-avatar" src="<?php echo htmlspecialchars($msgAvatar, ENT_QUOTES, 'UTF-8') ?>" alt="<?php echo htmlspecialchars($msgName, ENT_QUOTES, 'UTF-8') ?>">
                            <div class="lgnewui-home-message-user">
                                <div class="lgnewui-home-message-name-row">
                                    <span class="lgnewui-home-message-user-name"><?php echo htmlspecialchars($msgName, ENT_QUOTES, 'UTF-8') ?></span>
                                    <?php if ($msgIsAdmin): ?>
                                    <span class="lgnewui-badge lgnewui-badge--admin">站长</span>
                                    <?php endif; ?>
                                    <?php if (!empty($msg['is_developer'])): ?>
                                    <span class="lgnewui-badge lgnewui-badge--developer">开发者</span>
                                    <?php endif; ?>
                                    <?php if ($msgLevel > 0): ?>
                                    <span class="lgnewui-badge lgnewui-badge--level">Lv.<?php echo intval($msgLevel) ?></span>
                                    <?php endif; ?>
                                </div>
                                <span class="lgnewui-home-message-post-time"><?php echo htmlspecialchars($msg['date'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>
                        <div class="lgnewui-home-message-content"><?php echo htmlspecialchars(mb_substr($msg['text'], 0, 80, 'UTF-8')) ?></div>
                        <div class="lgnewui-home-message-divider"></div>
                        <div class="lgnewui-home-message-footer">
                            <div class="lgnewui-flex-gap-sm">
                                <?php if (!empty($msgLocation)): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-fill ph-map-pin"></i> <?php echo htmlspecialchars($msgLocation, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                                <?php if (!empty($msgDevice)): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-device-mobile"></i> <?php echo htmlspecialchars($msgDevice, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                                <?php if (!empty($msgBrowser)): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-globe"></i> <?php echo htmlspecialchars($msgBrowser, ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
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

    <!-- ===== 留言弹窗遮罩 ===== -->
    <div class="lgnewui-mask" id="lgnewuiMessageMask"></div>

    <!-- ===== 表情面板 ===== -->
    <div class="lgnewui-emoji-panel" id="lgnewuiEmojiPanel">
        <div class="lgnewui-emoji-panel__header">
            <span class="lgnewui-emoji-panel__title">表情</span>
            <button class="lgnewui-emoji-panel__close" onclick="closeEmojiPanel()"><i class="ph-bold ph-x"></i></button>
        </div>
        <div class="lgnewui-emoji-panel__body" id="lgnewuiEmojiGrid"></div>
    </div>

    <!-- 共享覆盖层元素（留言/音乐/地图/浮动按钮/footer/移动导航）已移至 footer.php -->

    <!-- ===== 访问信标脚本 ===== -->
    <div class="lgnewui-confirm-dialog" id="randomQuoteConfirm" style="display:none;">
        <div class="lgnewui-confirm-dialog__overlay"></div>
        <div class="lgnewui-confirm-dialog__content">
            <div class="lgnewui-confirm-dialog__icon"><i class="ph-fill ph-quote"></i></div>
            <h4 class="lgnewui-confirm-dialog__title">随机语录</h4>
            <p class="lgnewui-confirm-dialog__text" id="randomQuoteText"></p>
            <div class="lgnewui-confirm-dialog__actions">
                <button class="lgnewui-confirm-dialog__btn lgnewui-confirm-dialog__btn--cancel" onclick="closeRandomQuoteConfirm()">关闭</button>
                <button class="lgnewui-confirm-dialog__btn lgnewui-confirm-dialog__btn--confirm" id="randomQuoteCopyBtn">复制</button>
            </div>
        </div>
    </div>

    <!-- ===== 留言弹窗 ===== -->
    <div class="lgnewui-message-modal" id="lgnewuiMessageModal">
        <div class="lgnewui-message-modal__overlay" onclick="closeMessageModal()"></div>
        <div class="lgnewui-message-modal__content">
            <div class="lgnewui-message-modal__header">
                <h3 class="lgnewui-message-modal__title" id="lgnewuiMessageModalTitle">留下你的祝福</h3>
                <button class="lgnewui-message-modal__close" onclick="closeMessageModal()" aria-label="关闭留言弹窗"><i class="ph-bold ph-x" aria-hidden="true"></i></button>
            </div>
            <div class="lgnewui-message-modal__tabs">
                <button class="lgnewui-message-modal__tab active" data-tab="qq" onclick="switchMessageTab('qq', this)">QQ 登录</button>
                <button class="lgnewui-message-modal__tab" data-tab="anonymous" onclick="switchMessageTab('anonymous', this)">匿名留言</button>
            </div>
            <div class="lgnewui-message-modal__body">
                <!-- QQ 登录表单 -->
                <div class="lgnewui-message-modal__form" id="messageFormQQ">
                    <div class="lgnewui-message-modal__field">
                        <label class="lgnewui-message-modal__label" for="msgQQInput">QQ 号</label>
                        <input type="number" inputmode="numeric" class="lgnewui-message-modal__input" id="msgQQInput" name="qq" placeholder="请输入 QQ 号获取头像和昵称…" autocomplete="off" spellcheck="false" maxlength="12" aria-describedby="msgQQInputHint">
                    </div>
                    <div class="lgnewui-message-modal__field">
                        <label class="lgnewui-message-modal__label" for="msgQQContent">留言内容</label>
                        <textarea class="lgnewui-message-modal__textarea" id="msgQQContent" name="text" placeholder="写下你想说的话…" rows="4" maxlength="500"></textarea>
                    </div>
                    <div class="lgnewui-message-modal__field">
                        <label class="lgnewui-message-modal__label" id="msgQQTagsLabel">访客标签</label>
                        <div class="lgnewui-message-modal__tags" id="msgQQTags" role="group" aria-labelledby="msgQQTagsLabel">
                            <button type="button" class="lgnewui-message-modal__tag" data-tag="祝福" aria-pressed="false" onclick="toggleMsgTag(this)">祝福</button>
                            <button type="button" class="lgnewui-message-modal__tag" data-tag="喜欢" aria-pressed="false" onclick="toggleMsgTag(this)">喜欢</button>
                            <button type="button" class="lgnewui-message-modal__tag" data-tag="路过" aria-pressed="false" onclick="toggleMsgTag(this)">路过</button>
                            <button type="button" class="lgnewui-message-modal__tag" data-tag="打卡" aria-pressed="false" onclick="toggleMsgTag(this)">打卡</button>
                        </div>
                    </div>
                </div>
                <!-- 匿名留言表单 -->
                <div class="lgnewui-message-modal__form" id="messageFormAnonymous" style="display:none;">
                    <div class="lgnewui-message-modal__field">
                        <label class="lgnewui-message-modal__label" for="msgAnonName">昵称</label>
                        <input type="text" class="lgnewui-message-modal__input" id="msgAnonName" name="name" placeholder="匿名访客" autocomplete="nickname" maxlength="20">
                    </div>
                    <div class="lgnewui-message-modal__field">
                        <label class="lgnewui-message-modal__label" for="msgAnonContent">留言内容</label>
                        <textarea class="lgnewui-message-modal__textarea" id="msgAnonContent" name="text" placeholder="写下你想说的话…" rows="4" maxlength="500"></textarea>
                    </div>
                </div>
            </div>
            <div class="lgnewui-message-modal__footer">
                <button class="lgnewui-message-modal__emoji-btn" onclick="toggleEmojiPanel()" aria-label="切换表情面板">
                    <i class="ph-bold ph-smiley" aria-hidden="true"></i>
                </button>
                <button class="lgnewui-message-modal__submit" id="msgSubmitBtn" onclick="submitMessage()">发送留言</button>
            </div>
        </div>
    </div>

    <!-- ===== LG_CONFIG 城市和匿名头像输出 ===== -->
    <script>
    window.LG_CONFIG = window.LG_CONFIG || {};
    window.LG_CONFIG.userCity = <?php echo json_encode($text['city'] ?? '', JSON_UNESCAPED_UNICODE); ?>;
    window.LG_CONFIG.anonymousAvatar = <?php echo json_encode('https://weavatar.com/avatar/?s=120&d=mp', JSON_UNESCAPED_SLASHES); ?>;
    </script>

    <!-- ===== Geetest 验证绑定 ===== -->
    <script>
    if (typeof initGeetest === 'function') {
        // Geetest 初始化将在留言提交时触发
    }
    </script>

    <!-- ===== 用 LG_CONFIG 头像填充所有头像占位 ===== -->
    <script>
    (function() {
        var maleAvatar = (window.LG_CONFIG && window.LG_CONFIG.maleAvatar) || '';
        var femaleAvatar = (window.LG_CONFIG && window.LG_CONFIG.femaleAvatar) || '';
        if (maleAvatar) {
            document.querySelectorAll('.lg-male-avatar').forEach(function(el) {
                if (!el.src || el.src === '' || el.src === window.location.href) {
                    el.src = maleAvatar;
                }
            });
        }
        if (femaleAvatar) {
            document.querySelectorAll('.lg-female-avatar').forEach(function(el) {
                if (!el.src || el.src === '' || el.src === window.location.href) {
                    el.src = femaleAvatar;
                }
            });
        }
    })();
    </script>

    <!-- ===== AOS + 模块初始化 ===== -->
    <script>
        if (typeof AOS !== 'undefined') {
            AOS.init({ duration: 800, easing: 'ease-out-cubic', once: true, offset: 50 });
        }
        if (typeof initLGHome === 'function') {
            initLGHome({
                startTime: <?php echo json_encode($text['startTime']); ?>
            });
        }
        if (typeof initLGHomeApp === 'function') {
            initLGHomeApp({
                startTime: <?php echo json_encode($text['startTime']); ?>
            });
        }
        // 初始化礼花效果
        if (typeof ConfettiEffect !== 'undefined') {
            ConfettiEffect.init();
        }
        // 纪念日筛选
        function filterLoveDays(filter, btn) {
            var tabs = document.querySelectorAll('.lgnewui-ios-tab');
            var slider = document.querySelector('.lgnewui-ios-tabs-slider');
            tabs.forEach(function(b) { b.classList.remove('active'); });
            btn.classList.add('active');
            if (slider) {
                slider.style.width = btn.offsetWidth + 'px';
                slider.style.transform = 'translateX(' + btn.offsetLeft + 'px)';
            }
            var items = document.querySelectorAll('[data-loveday-type]');
            items.forEach(function(item) {
                var type = item.getAttribute('data-loveday-type');
                var card = item.querySelector('.lgnewui-widget--loveday-vibrant');
                if (filter === 'all') {
                    item.style.display = '';
                    if (card) card.classList.remove('lgnewui-skeleton-card');
                } else if (filter === type) {
                    item.style.display = '';
                    if (card) card.classList.remove('lgnewui-skeleton-card');
                } else {
                    item.style.display = 'none';
                }
            });
        }
        // 留言弹窗
        function openMessageModal() {
            var modal = document.getElementById('lgnewuiMessageModal');
            var mask = document.getElementById('lgnewuiMessageMask');
            if (modal) modal.classList.add('active');
            if (mask) mask.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeMessageModal() {
            var modal = document.getElementById('lgnewuiMessageModal');
            var mask = document.getElementById('lgnewuiMessageMask');
            if (modal) modal.classList.remove('active');
            if (mask) mask.classList.remove('active');
            document.body.style.overflow = '';
        }
        function switchMessageTab(tab, btn) {
            document.querySelectorAll('.lgnewui-message-modal__tab').forEach(function(t) { t.classList.remove('active'); });
            btn.classList.add('active');
            document.getElementById('messageFormQQ').style.display = tab === 'qq' ? '' : 'none';
            document.getElementById('messageFormAnonymous').style.display = tab === 'anonymous' ? '' : 'none';
        }
        function toggleMsgTag(el) {
            el.classList.toggle('active');
        }
        function toggleEmojiPanel() {
            var panel = document.getElementById('lgnewuiEmojiPanel');
            if (panel) panel.classList.toggle('active');
        }
        function closeEmojiPanel() {
            var panel = document.getElementById('lgnewuiEmojiPanel');
            if (panel) panel.classList.remove('active');
        }
        function submitMessage() {
            // 留言提交逻辑由 page-messages.js 处理
            if (typeof submitLeavingMessage === 'function') {
                submitLeavingMessage();
            }
        }
        function closeRandomQuoteConfirm() {
            var dialog = document.getElementById('randomQuoteConfirm');
            if (dialog) dialog.style.display = 'none';
        }
    </script>

    </div>

    <!-- ===== 底部脚本 ===== -->
    <!-- 所有脚本已在head.php中统一加载，此处无需重复引用 -->

    <!-- ===== 访问信标脚本 ===== -->
    <script>
    <!-- 音乐播放器/地图/浮动按钮/footer/移动导航已移至 footer.php -->


    <!-- ===== 访问信标脚本 ===== -->
    <script>
    (function() {
        // 访问统计信标
        var beacon = new Image();
        beacon.src = 'services/visitor-stats.php?t=' + Date.now();
    })();
    if (typeof AccessBeacon !== 'undefined') {
        AccessBeacon.init('', '');
    }
    </script>

    <?php include_once 'footer.php'; ?>