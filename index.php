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
    $r = mysqli_query($connect, "SELECT COUNT(*) as c FROM little");
    if ($r) { $row = mysqli_fetch_array($r); $statsArticles = $row['c']; }
    $r = mysqli_query($connect, "SELECT COUNT(*) as c FROM photo");
    if ($r) { $row = mysqli_fetch_array($r); $statsPhotos = $row['c']; }
    $r = mysqli_query($connect, "SELECT COUNT(*) as c FROM leaving");
    if ($r) { $row = mysqli_fetch_array($r); $statsMessages = $row['c']; }
    $r = mysqli_query($connect, "SELECT COUNT(*) as c FROM timeline");
    if ($r) { $row = mysqli_fetch_array($r); $statsTimeline = $row['c']; }
    $r = mysqli_query($connect, "SELECT COUNT(*) as c FROM lovelist");
    if ($r) { $row = mysqli_fetch_array($r); $listTotal = $row['c']; }
    $r = mysqli_query($connect, "SELECT COUNT(*) as c FROM lovelist WHERE is_done = 1");
    if ($r) { $row = mysqli_fetch_array($r); $listCompleted = $row['c']; }
    
    // 获取访问统计数据
    $today = date('Y-m-d');
    $r = mysqli_query($connect, "SELECT * FROM visitor_stats WHERE visit_date = '$today'");
    if ($r && mysqli_num_rows($r) > 0) {
        $row = mysqli_fetch_assoc($r);
        $todayVisits = intval($row['visit_count']);
        $todayVisitors = intval($row['visitor_count']);
    }
    $r = mysqli_query($connect, "SELECT * FROM visitor_total WHERE id = 1");
    if ($r && mysqli_num_rows($r) > 0) {
        $row = mysqli_fetch_assoc($r);
        $totalVisits = intval($row['total_visits']);
        $totalVisitors = intval($row['total_visitors']);
    }
}

$listPercent = $listTotal > 0 ? round(($listCompleted / $listTotal) * 100) : 0;
$startTs = strtotime(str_replace('T', ' ', $text['startTime'] ?? '2022-06-05 00:07:00'));
$runtimeDays = floor((time() - $startTs) / 86400);
?>

    <div id="pjax-container">

    <!-- ===== 头像区域 ===== -->
    <div class="bg-wrap central limg">
        <div class="bg-img">
            <div class="middle Blurkg">
                <div class="img-male">
                    <div class="avatarArea lgewui-head-avatar-boy">
                        <img draggable="false" class="avatarFrame" src="https://s1.locimg.com/2024/10/18/db01c99842e69.png" style="transform: scale(1.6);top: 2px;left: 2px;">
                        <img draggable="false" class="aiv_touxiang" src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo htmlspecialchars($text['boyimg'], ENT_QUOTES, 'UTF-8') ?>&s=640">
                        <div class="lgnewui-head-avatar-mask">
                            <div class="lgnewui-head-avatar-top lgnewui-head-avatar-anim-item">
                                <div class="lgnewui-head-avatar-gender-icon" data-gender="male"><i class="ph-fill ph-gender-male"></i></div>
                            </div>
                            <div class="lgnewui-head-avatar-middle lgnewui-head-avatar-anim-item">
                                <div class="lgnewui-head-avatar-status-text lgnewui-head-avatar-status-online">
                                    <i class="ph-fill ph-wifi-high"></i>
                                    <em>在线</em>
                                </div>
                                <div class="lgnewui-head-avatar-divider"></div>
                            </div>
                        </div>
                    </div>
                    <span class="shadow-blur"><?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="love-icon">
                    <div class="love-info-wrapper"></div>
                    <img draggable="false" src="/Style/img/like.svg">
                </div>
                <div class="img-female">
                    <div class="avatarArea lgewui-head-avatar-girl">
                        <img draggable="false" class="avatarFrame" src="https://s1.locimg.com/2024/10/18/db01c99842e69.png" style="transform: scale(1.6);top: 2px;left: 2px;">
                        <img draggable="false" class="aiv_touxiang" src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo htmlspecialchars($text['girlimg'], ENT_QUOTES, 'UTF-8') ?>&s=640">
                        <div class="lgnewui-head-avatar-mask">
                            <div class="lgnewui-head-avatar-top lgnewui-head-avatar-anim-item">
                                <div class="lgnewui-head-avatar-gender-icon" data-gender="female"><i class="ph-fill ph-gender-female"></i></div>
                            </div>
                            <div class="lgnewui-head-avatar-middle lgnewui-head-avatar-anim-item">
                                <div class="lgnewui-head-avatar-status-text lgnewui-head-avatar-status-online">
                                    <i class="ph-fill ph-wifi-high"></i>
                                    <em>在线</em>
                                </div>
                                <div class="lgnewui-head-avatar-divider"></div>
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
                                <img src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo htmlspecialchars($text['boyimg'], ENT_QUOTES, 'UTF-8') ?>&s=640" class="lgnewui-home-weather-avatar" alt="<?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?>">
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
                                <img src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo htmlspecialchars($text['girlimg'], ENT_QUOTES, 'UTF-8') ?>&s=640" class="lgnewui-home-weather-avatar" alt="<?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?>">
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

                <!-- 双方头像卡片 -->
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
                                        <img src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo htmlspecialchars($text['boyimg'], ENT_QUOTES, 'UTF-8') ?>&s=640" class="lgnewui-presence-avatar" alt="<?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?>">
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
                                        <img src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo htmlspecialchars($text['girlimg'], ENT_QUOTES, 'UTF-8') ?>&s=640" class="lgnewui-presence-avatar" alt="<?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?>">
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
                    $recentEvents = mysqli_query($connect, "SELECT * FROM lovelist ORDER BY id DESC LIMIT 4");
                }
                if ($recentEvents && mysqli_num_rows($recentEvents) > 0):
                    $eidx = 0; while ($evt = mysqli_fetch_array($recentEvents)):
                        $hasImg = !empty($evt['imgurl']) && $evt['imgurl'] !== '0';
                        $isDone = intval($evt['is_done']) === 1;
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
                            </div>
                            <div class="<?php echo $hasImg ? 'lgnewui-event-footer-glass' : 'lgnewui-event-footer-light'; ?>">
                                <span class="lgnewui-chip <?php echo $hasImg ? 'lgnewui-chip--glass' : 'lgnewui-chip--light'; ?>">
                                    <i class="ph-<?php echo $hasImg ? 'fill' : 'bold'; ?> ph-check-circle"></i>
                                    <?php echo $isDone ? '已完成' : '未完成'; ?>
                                </span>
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
                        <button class="lgnewui-ios-tab active" data-filter="all" onclick="filterLoveDays('all', this)">全部</button>
                        <button class="lgnewui-ios-tab" data-filter="future" onclick="filterLoveDays('future', this)">未来</button>
                        <button class="lgnewui-ios-tab" data-filter="past" onclick="filterLoveDays('past', this)">已过</button>
                    </div>
                </div>
            </div>
            <div class="lgnewui-grid lgnewui-loveday-grid">
                <?php
                $lovedays = [];
                if ($connect) {
                    $ldResult = mysqli_query($connect, "SELECT * FROM timeline ORDER BY date ASC");
                    if ($ldResult) {
                        while ($ld = mysqli_fetch_array($ldResult)) {
                            $ldDate = $ld['date'];
                            $ldTs = strtotime($ldDate);
                            $diffDays = floor((time() - $ldTs) / 86400);
                            $isFuture = $diffDays < 0;
                            $lovedays[] = [
                                'title' => $ld['title'],
                                'date' => $ldDate,
                                'days' => abs($diffDays),
                                'isFuture' => $isFuture
                            ];
                        }
                    }
                }
                // 如果数据库没有数据，用默认
                if (empty($lovedays)) {
                    $lovedays[] = ['title' => '在一起', 'date' => date('Y-m-d', $startTs), 'days' => $runtimeDays, 'isFuture' => false];
                }
                $ldIdx = 0;
                foreach ($lovedays as $ld):
                ?>
                <div data-aos="fade-up" data-aos-delay="<?php echo $ldIdx * 50 ?>">
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
                    $recentArticles = mysqli_query($connect, "SELECT * FROM little ORDER BY id DESC LIMIT 6");
                }
                if ($recentArticles && mysqli_num_rows($recentArticles) > 0):
                    $idx = 0; while ($art = mysqli_fetch_array($recentArticles)):
                        $dayNum = floor((time() - strtotime($art['date'])) / 86400);
                ?>
                <div data-aos="fade-up" data-aos-delay="<?php echo $idx * 50 ?>">
                    <a href="page.php?id=<?php echo $art['id'] ?>" class="lgnewui-journal-card lgnewui-journal-card--link">
                        <div class="lgnewui-watermark">DAY <?php echo $dayNum ?></div>
                        <div class="lgnewui-journal-header">
                            <div class="lgnewui-journal-user">
                                <img data-src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo htmlspecialchars($text['boyimg'], ENT_QUOTES, 'UTF-8') ?>&s=640" class="lgnewui-journal-avatar lazy">
                                <div>
                                    <div class="lgnewui-font-sm-bold"><?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="lgnewui-journal-meta"><?php echo $art['date'] ?></div>
                                </div>
                            </div>
                        </div>
                        <h3 class="lgnewui-journal-title"><?php echo htmlspecialchars(mb_substr($art['title'], 0, 30, 'UTF-8')) ?></h3>
                        <p class="lgnewui-journal-body lgnewui-journal-body-clamp"><?php echo htmlspecialchars(strip_tags(mb_substr($art['text'], 0, 100, 'UTF-8'))) ?></p>
                        <div class="lgnewui-journal-footer">
                            <div class="lgnewui-flex-gap-sm">
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-calendar-blank"></i> <?php echo date('Y-m-d', strtotime($art['date'])) ?></span>
                                <?php if (!empty($art['type'])): ?>
                                <span class="lgnewui-chip lgnewui-chip--light"><i class="ph-bold ph-smiley"></i> 心情</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
                <?php $idx++; endwhile; endif; ?>
            </div>
        </section>

        <!-- ===== 6. 相册 ===== -->
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
                    <a href="loveImg.php" class="lgnewui-link-more">
                        <i class="ph-bold ph-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="lgnewui-photo-grid">
                <?php
                $recentPhotos = null;
                if ($connect) {
                    $recentPhotos = mysqli_query($connect, "SELECT * FROM loveImg ORDER BY id DESC LIMIT 6");
                }
                if ($recentPhotos && mysqli_num_rows($recentPhotos) > 0):
                    $idx = 0; while ($photo = mysqli_fetch_array($recentPhotos)):
                ?>
                <div data-aos="fade-up" data-aos-delay="<?php echo $idx * 50 ?>">
                    <div class="lgnewui-photo-card">
                        <div class="lgnewui-photo-wrapper">
                            <img data-src="<?php echo htmlspecialchars($photo['imgUrl']) ?>" class="lgnewui-photo-img lazy" alt="<?php echo htmlspecialchars($photo['imgText'] ?? '') ?>">
                            <div class="lgnewui-photo-overlay">
                                <p class="lgnewui-photo-text"><?php echo htmlspecialchars($photo['imgText'] ?? '') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php $idx++; endwhile; else: ?>
                <div class="lgnewui-col-4" data-aos="fade-up">
                    <div class="lgnewui-widget" style="text-align:center;padding:2rem;">
                        <div style="font-size:2rem;margin-bottom:0.5rem;">📸</div>
                        <p style="color:#94a3b8;">暂无照片</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- ===== 7. 留言 ===== -->
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
            <div class="lgnewui-grid">
                <?php
                $recentMsgs = null;
                if ($connect) {
                    $recentMsgs = mysqli_query($connect, "SELECT * FROM leaving ORDER BY id DESC LIMIT 4");
                }
                if ($recentMsgs && mysqli_num_rows($recentMsgs) > 0):
                    $idx = 0; while ($msg = mysqli_fetch_array($recentMsgs)):
                ?>
                <div class="lgnewui-col-2" data-aos="fade-up" data-aos-delay="<?php echo $idx * 50 ?>">
                    <a href="messages.php#comment-<?php echo $msg['id'] ?>" class="lgnewui-home-message-card">
                        <div class="lgnewui-home-message-header">
                            <img class="lgnewui-home-message-avatar" src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo htmlspecialchars($msg['qqimg'], ENT_QUOTES, 'UTF-8') ?>&s=640">
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
                <?php $idx++; endwhile; endif; ?>
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
        // 初始化访客追踪
        if (typeof AccessBeacon !== 'undefined') {
            AccessBeacon.init('', '');
        }
        // 纪念日筛选
        function filterLoveDays(filter, btn) {
            document.querySelectorAll('.lgnewui-ios-tab').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }
    </script>

    </div>

    <?php include_once 'footer.php'; ?>

</body>
</html>
