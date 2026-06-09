<?php
/*
 * @Version：Like Girl 5.2.1-Stable
 * @Author: Ki.
 * @Date: 2025-09-03 00:00:00
 * @LastEditTime: 2025-09-03
 * @Description: 愿得一心人 白头不相离
 * @Document：https://blog.kikiw.cn/index.php/archives/52/
 * @Copyright (c) 2023 - 2025 by Ki All Rights Reserved.
 * @Warning：禁止以任何方式出售本项目 如有发现一切后果自行负责
 * @Warning：禁止以任何方式出售本项目 如有发现一切后果自行负责
 * @Warning：禁止以任何方式出售本项目 如有发现一切后果自行负责
 * @Message：开发不易 版权信息请保留 (删除/修改作者版权的Dog请勿使用 感谢配合)
 */
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
// include ("ipjc.php");
// include_once ("ip.php");
include_once 'admin/connect.php';
include_once 'admin/Function.php';

$version = '5.2.1';

$text = [];
$diy = [];
if ($connect) {
    $sql = "SELECT boy, girl, title, logo, writing, boyimg, girlimg, startTime, icp, Copyright, card1, card2, card3, deci1, deci2, deci3, bgimg, userQQ, userName, Animation, boyCity, girlCity, boyLat, boyLng, girlLat, girlLng FROM text";
    $result = mysqli_query($connect, $sql);
    if ($result) {
        $text = mysqli_fetch_array($result) ?: [];
        mysqli_free_result($result);
    }

    $sql = "SELECT id, headCon, footerCon, cssCon, Pjaxkg, Blurkg FROM diySet";
    $result = mysqli_query($connect, $sql);
    if ($result && mysqli_num_rows($result)) {
        $diy = mysqli_fetch_array($result) ?: [];
        mysqli_free_result($result);
    }
}

$copy = $text['Copyright'] ?? '';
$icp = $text['icp'] ?? '';
$Animation = $text['Animation'] ?? '';

// 初始化头像URL变量（提前到meta标签之前）
$boyimg_val = $text['boyimg'] ?? '';
$girlimg_val = $text['girlimg'] ?? '';
if ($boyimg_val && !preg_match('/^https?:\/\//', $boyimg_val)) {
    $boyimg_val = 'https://q1.qlogo.cn/g?b=qq&nk=' . $boyimg_val . '&s=640';
} elseif (!$boyimg_val) {
    $boyimg_val = '/Style/img/boy.png';
}
if ($girlimg_val && !preg_match('/^https?:\/\//', $girlimg_val)) {
    $girlimg_val = 'https://q1.qlogo.cn/g?b=qq&nk=' . $girlimg_val . '&s=640';
} elseif (!$girlimg_val) {
    $girlimg_val = '/Style/img/girl.png';
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta charset="UTF-8">
<link rel="icon" href="/favicon.png" />
<link rel="canonical" href="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '/') ?>" />
<title><?php
    $siteName = $text['title'] ?: 'Like Girl';
    $siteSlogan = $text['writing'] ?: '愿得一心人 白头不相离';
    if (!empty($pageTitle)) {
        echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') . ' — ' . htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8');
    } else {
        echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') . ' — ' . htmlspecialchars($siteSlogan, ENT_QUOTES, 'UTF-8');
    }
?></title>
<meta name="keywords"
    content="<?php echo htmlspecialchars($text['title'], ENT_QUOTES, 'UTF-8') ?>,Like Girl 5.2.1-Stable,LGNeUi,情侣小站,开源情侣网站,PHP情侣网站,情侣记录,情侣网站,情侣项目,情侣小窝,Love,LikeGirl,Ki,PHP情侣小站,情侣小站使用教程,情侣小站使用文档">
<meta name="description" content="<?php echo htmlspecialchars($text['writing'], ENT_QUOTES, 'UTF-8') ?> - Like Girl 5.2.1-Stable">
<meta name="author" content="Ki">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="robots" content="index, follow">

<!-- Open Graph (Facebook/微信/QQ) -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?php echo htmlspecialchars($text['title'], ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($text['title'], ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($text['writing'], ENT_QUOTES, 'UTF-8') ?>">
<meta property="og:url" content="https://love.54oimx.top/">
<meta property="og:image" content="<?php echo htmlspecialchars($boyimg_val, ENT_QUOTES, 'UTF-8') ?>">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($text['title'], ENT_QUOTES, 'UTF-8') ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($text['writing'], ENT_QUOTES, 'UTF-8') ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($boyimg_val, ENT_QUOTES, 'UTF-8') ?>">
<meta name="x-lg-license-instance" content="858ee1d099b9">

    <!-- Google Fonts CDN 版本 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Pacifico&family=Inter:wght@200;300;400;500;600;700&family=Noto+Serif+SC:wght@400;600;700&family=Noto+Sans+SC:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@1,500&family=Oswald:wght@400;600;700&family=Dancing+Script:wght@400;700&family=Crimson+Pro:wght@300;400;500;600;700&family=Libre+Baskerville:wght@400;700&family=Montserrat:wght@400&family=Niconne&family=Ma+Shan+Zheng&family=Liu+Jian+Mao+Cao&display=swap"
        rel="stylesheet">
<!-- ===== CSS（按参考站顺序排列） ===== -->
<link rel="stylesheet" href="/Style/vendor/google-fonts/fonts-non-google.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/vendor/fontawesome/css/all.min.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/leaving.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/leav.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-message.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/index.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/little.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/loveImg.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/list.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/Font/font_list/iconfont.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/toastify/toastify.min.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/APlayer.min.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/aplayer.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/loadinglike.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/vendor/aos/aos.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/plyr.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/kicode.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/phosphor-regular.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/phosphor-icons.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/phosphor-fill.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/phosphor-duotone.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/vendor/qweather-icons/qweather-icons.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/nprogress.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/vendor/remixicon/remixicon.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-tooltip.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-interaction.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lgnewui-home-style.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lgnewui-detail.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-mobile-nav.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-header.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-context-menu.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-map.css?LikeGirl=<?php echo $version ?>">
<!-- ===== JS（按参考站顺序排列） ===== -->
<script src="/Style/jquery/jquery.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/Font/font_leav/iconfont.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/jquery.pjax.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/plyr.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/vendor/aos/aos.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/highlight.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/lazyload.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/masonry.pkgd.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/imagesloaded.pkgd.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/loading.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/LGNewUiOwO.js?LikeGirl=<?php echo $version ?>"></script>
<!-- 全局滚动锁工具（所有弹窗共用，防止滚动条消失时布局跳动） -->
<script>
(function(){
    var _count = 0;
    window.lgScrollLock = function(){
        _count++;
        if (_count === 1) {
            var w = window.innerWidth - document.documentElement.clientWidth;
            document.documentElement.style.setProperty('--lg-scrollbar-compensate', w + 'px');
            document.documentElement.classList.add('lg-scroll-locked');
        }
    };
    window.lgScrollUnlock = function(){
        _count = Math.max(0, _count - 1);
        if (_count === 0) {
            document.documentElement.classList.remove('lg-scroll-locked');
            document.documentElement.style.removeProperty('--lg-scrollbar-compensate');
        }
    };
    window.lgScrollReset = function(){
        _count = 0;
        document.documentElement.classList.remove('lg-scroll-locked');
        document.documentElement.style.removeProperty('--lg-scrollbar-compensate');
    };
})();
</script>
<link rel="stylesheet" href="/Style/dplayer/DPlayer.min.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/video-modal.css?LikeGirl=<?php echo $version ?>">
<script src="/Style/dplayer/DPlayer.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/video-modal.js?LikeGirl=<?php echo $version ?>"></script>
<script src="https://static.geetest.com/v4/gt4.js"></script>
<script src="/Style/js/geetest-helper.js?LikeGirl=<?php echo $version ?>"></script>
<script>if (typeof GeetestHelper !== 'undefined') GeetestHelper.setCaptchaId("");</script>
<script src="/Style/js/nprogress.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/vendor/confetti/confetti.browser.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/vendor/qrcode/qrcode.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/vendor/qr-code-styling/qr-code-styling.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-app.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-components.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-pjax.js?LikeGirl=<?php echo $version ?>"></script>
<script>if(window.LGPjax&&typeof window.LGPjax.init==="function")window.LGPjax.init();</script>
<script src="/Style/vendor/confetti/confetti.browser.min.js?LikeGirl=<?php echo $version ?>"></script>
<link rel="stylesheet" href="/Style/Font/font_footer/iconfont.css?LikeGirl=<?php echo $version ?>">
<script src="/assets/js/page-messages.js?LikeGirl=<?php echo $version ?>" defer></script>
<script src="/Style/toastify/lucide.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/toastify/toastify.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/clipboard.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-clipboard.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-tooltip.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/view-image.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/mian.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/LoveListStyle/carousel.umd.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/LoveListStyle/carousel.thumbs.umd.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/LoveListStyle/fancybox.umd.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/page-lovelist.js?LikeGirl=<?php echo $version ?>" defer></script>
<script src="/assets/js/page-index.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/page-detail.js?LikeGirl=<?php echo $version ?>" defer></script>
<script src="/assets/js/page-album-detail.js?LikeGirl=<?php echo $version ?>" defer></script>
<script src="/assets/js/html2canvas.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-chat.js?LikeGirl=<?php echo $version ?>" defer></script>
<script src="/assets/js/lg-visitor-hash.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-map.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-interaction.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-context-menu.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/APlayer.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/color-thief.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/meting.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/music-player.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-mobile-nav.js?LikeGirl=<?php echo $version ?>"></script>
<!-- LG_NewUI 核心框架配置 -->
<script>
    <?php
    $lg_config = [
        'title' => $text['title'] ?? '情侣小站',
        'boy' => $text['boy'] ?? '男方',
        'girl' => $text['girl'] ?? '女方',
        'startTime' => str_replace('T', ' ', $text['startTime'] ?? '2022-06-05 00:00:00'),
        'version' => '5.2.1',
        'pcCarouselHeight' => '80vh',
        'mobileCarouselHeight' => '50vh',
        'pcPhotoCoverHeight' => '80vh',
        'mobilePhotoCoverHeight' => '60vh',
        'pcImgMaxHeight' => '450px',
        'mobileImgMaxHeight' => '260px',
        'maleName' => $text['boy'] ?? '男方',
        'maleAvatar' => $boyimg_val,
        'femaleName' => $text['girl'] ?? '女方',
        'femaleAvatar' => $girlimg_val,
        'siteBase' => '',
        'assetBase' => '',
        'imageErrorFallback' => '/Style/img/file-placeholder.svg',
        'owoBase' => '/OwO',
        'soloMode' => false,
        'weatherEnabled' => true,
        'weatherToken' => '',
        'bannedChars' => '',
        'endpoints' => [
            'mapApi' => '/assets/map-api.php',
            'weatherNow' => '/services/weather.php',
            'interaction' => '/services/interaction.php',
        ],
    ];
    ?>
    window.LG_CONFIG = Object.assign(window.LG_CONFIG || {}, <?php echo json_encode($lg_config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>);

    // AOS 动画配置
    window.LG_AOS_CONFIG = {"enabled":true,"animation":"fade-up","duration":800,"delay":0,"interval":50,"maxDelay":300,"easing":"ease-out-cubic","offset":50,"once":true,"mirror":true,"anchorPlacement":"top-bottom"};

    // 访客地理缓存
    window.LGVisitorGeoCache = window.LGVisitorGeoCache || (function () {
        var storageKey = 'lgnewui_visitor_geo_v1';
        var cookieKey = 'lg_visitor_geo';
        var maxAgeMs = 6 * 60 * 60 * 1000;
        function normalize(p) {
            if (!p || typeof p !== 'object') return null;
            var lat = Number(p.lat), lng = Number(p.lng), ts = Number(p.ts || Date.now()), city = typeof p.city === 'string' ? p.city.trim() : '';
            if (!isFinite(lat) || !isFinite(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180 || (lat === 0 && lng === 0)) return null;
            if (!isFinite(ts) || ts <= 0) ts = Date.now();
            return { lat: Number(lat.toFixed(6)), lng: Number(lng.toFixed(6)), ts: ts, city: city };
        }
        function writeCookie(p) { var n = normalize(p); if (!n) return; document.cookie = cookieKey + '=' + encodeURIComponent(JSON.stringify(n)) + '; path=/; max-age=' + String(Math.floor(maxAgeMs / 1000)) + '; SameSite=Lax'; }
        function clear() { try { window.localStorage.removeItem(storageKey); } catch(e) {} document.cookie = cookieKey + '=; path=/; max-age=0; SameSite=Lax'; }
        function getCached() { try { var raw = window.localStorage.getItem(storageKey); if (!raw) return null; var n = normalize(JSON.parse(raw)); if (!n) return null; if (Date.now() - n.ts > maxAgeMs) { clear(); return null; } return n; } catch(e) { return null; } }
        function setCached(p) { var n = normalize(p); if (!n) return; try { window.localStorage.setItem(storageKey, JSON.stringify(n)); } catch(e) {} writeCookie(n); }
        function syncCookieFromCache() { var c = getCached(); if (c) writeCookie(c); }
        return { get: getCached, set: setCached, clear: clear, syncCookieFromCache: syncCookieFromCache };
    })();
    window.LGVisitorGeoCache.syncCookieFromCache();

    // 高德地图安全配置
    window._AMapSecurityConfig = {"securityJsCode":""};

    // 地图配置
    window.LGMAP_CONFIG = {"amapKey":"","modeConfig":{"lovers":{"title":"情侣模式","desc":"无论相隔多远，心始终在一起"},"moments":{"title":"点点滴滴","desc":"记录我们的每一个美好瞬间"},"messages":{"title":"留言模式","desc":"来自世界各地的温暖祝福"},"albums":{"title":"相册模式","desc":"用照片定格我们的回忆"},"events":{"title":"事件清单","desc":"一起完成的每一个小目标"}},"lovers":[],"milestones":[],"events":[],"albums":[],"messages":[],"moments":[],"loveStartDate":"","hsla":"345deg,70%,55%","mapStyle":"amap://styles/grey","soloMode":false,"_apiBase":"/assets/map-api.php"};

    // 地图数据
    window.LGMapData = window.LGMapData || {
        assign: function (data) {
            if (data.lovers) window.LGMAP_CONFIG.lovers = data.lovers;
            if (typeof data.loveStartDate !== 'undefined') window.LGMAP_CONFIG.loveStartDate = data.loveStartDate;
            if (data.milestones) window.LGMAP_CONFIG.milestones = data.milestones;
            if (data.moments) window.LGMAP_CONFIG.moments = data.moments;
            if (data.messages) window.LGMAP_CONFIG.messages = data.messages;
            if (data.albums) window.LGMAP_CONFIG.albums = data.albums;
            if (data.events) window.LGMAP_CONFIG.events = data.events;
            return data;
        },
        fetchAll: function () {
            var apiUrl = new URL(window.LGMAP_CONFIG._apiBase, window.location.origin);
            apiUrl.searchParams.set('module', 'all');
            return fetch(apiUrl.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
                .then(function (r) { if (!r.ok) throw new Error(r.status); return r.json(); })
                .then(this.assign.bind(this));
        }
    };

    window.LGMAP_DATA_READY = window.LGMapData.fetchAll()
        .catch(function (err) {
            if (window.LG_CONFIG && window.LG_CONFIG.debugMap && window.console && typeof window.console.warn === 'function') {
                window.console.warn('地图数据加载失败:', err);
            }
        });
</script>

<script>
    // 倒计时、高度调整、轮播图、导航栏等功能已迁移到 lg-app.js 和 lg-components.js
    // 保留必要的全局变量供旧代码兼容
    var pcCarouselHeight = "80vh";
    var mobileCarouselHeight = "50vh";
    var pcPhotoCoverHeight = "80vh";
    var mobilePhotoCoverHeight = "60vh";
    var pcImgMaxHeight = "450px";
    var mobileImgMaxHeight = "260px";
</script>

<?php
echo htmlspecialchars_decode($diy['headCon'], ENT_QUOTES);
?>
</head>
<body class="bg-pdot-vignette" onload="document.body.classList.add('loaded')" data-aos-easing="ease-out-cubic" data-aos-duration="800" data-aos-delay="0">
<script>

    console.log("%c Q & V | [已隐藏]", "color:#fff;background:#000;padding:8px 15px;font-weight: 700;border-radius:15px");
    console.log("%c Like Girl 5.2.1-Stable | Powered by Ki", "color:#fff;font-weight: 700;background:linear-gradient(270deg,#986fee,#8695e6,#68b7dd,#18d7d3);padding:8px 15px;border-radius:15px");
    

    function setupVideoPlayer(video) {
        var videoContainer = $('<div class="video-container"></div>');
        var playPauseBtn = $('<div class="play-pause-btn"></div>');
    
        var playPauseIcon = `
            <svg t="1730884474730" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg"
                p-id="7671" width="200" height="200">
                <path
                    d="M861.829969 330.562413L391.150271 33.456576A214.465233 214.465233 0 0 0 62.30358 214.751187V809.248813a214.465233 214.465233 0 0 0 328.846691 181.294611l470.679698-297.105837a214.751187 214.751187 0 0 0 0-362.875174z"
                    fill="#ffffff" p-id="7672"></path>
            </svg>
        `;
        playPauseBtn.html(playPauseIcon);
    
        video.wrap(videoContainer);
        video.parent().append(playPauseBtn);
    
        video.attr('controls', false);
    
        video.css({
            'width': '100%',
            'height': 'auto'
        });
    
        playPauseBtn.show();
    
        playPauseBtn.on('click', function(e) {
            e.stopPropagation();
    
            if (video[0].paused) {
                video[0].play();
                playPauseBtn.hide();
            } else {
                video[0].pause();
                playPauseBtn.show();
            }
        });
    
        video.on('click', function() {
            if (video[0].paused) {
                video[0].play();
                playPauseBtn.hide();
            } else {
                video[0].pause();
                playPauseBtn.show();
            }
        });
    }

    function show_date_time() {
        setTimeout(show_date_time, 1000);
        var BirthDay = new Date(<?php echo json_encode($text['startTime']); ?>);
        var today = new Date();
        var timeold = (today.getTime() - BirthDay.getTime());
        var msPerDay = 24 * 60 * 60 * 1000;
        var e_daysold = timeold / msPerDay;
        var daysold = Math.floor(e_daysold);
        var e_hrsold = (e_daysold - daysold) * 24;
        var hrsold = Math.floor(e_hrsold);
        var e_minsold = (e_hrsold - hrsold) * 60;
        var minsold = Math.floor((e_hrsold - hrsold) * 60);
        var seconds = Math.floor((e_minsold - minsold) * 60);
        var timeKi = document.getElementById('span_dt_dt');
        if (timeKi !== null) {
            document.getElementById('span_dt_dt').innerHTML = "这是我们一起走过的";
            document.getElementById('tian').innerHTML = daysold + '天';
            document.getElementById('shi').innerHTML = hrsold + '时';
            document.getElementById('fen').innerHTML = minsold + '分';
            document.getElementById('miao').innerHTML = (seconds < 10 ? "0" : "") + seconds + '秒';
        }
    }

    show_date_time();
    
let currentPage = 1;
const limit = 6;
let total = 0;

function createPhotoElement(photo) {
    return `

<div class="img_card col-lg-4 col-md-6 col-sm-12 col-sm-x-12 photo-item">
    <div class="love_img">
        <img class="spotlight" data-funlazy="${photo.img}" alt="${photo.text}" 
             data-description="${photo.date}">
        
        <div class="words" data-tip="${photo.text}" data-tip-position="top">
            <i>${photo.date}</i>
            <span>${photo.text}</span>
        </div>
    </div>
</div>

    `;
}

function showPhotos(photos) {
    const $gallery = $('#photoGallery');
    const startIndex = $gallery.children().length;

    photos.forEach(photo => {
        const photoElement = createPhotoElement(photo);
        $gallery.append(photoElement);
    });

    // 逐张显示动画
    const newItems = $('.photo-item').slice(startIndex);
    newItems.each(function(index) {
        const $item = $(this);
        setTimeout(function() {
            $item.addClass('show');
        }, index * 300);
    });
}

// 加载照片
function loadPhotos() {
    const $loading = $('#loading');
    const $loadBtn = $('#loadMoreBtn');

    $loading.show();
    $loadBtn.prop('disabled', true);

    $.post('getPhotos.php', { page: currentPage, limit: limit }, function(res) {
        if (res.code === 200) {
            total = res.total;
            showPhotos(res.data);
            
            FunLazy({
                placeholder: "Style/img/Loading2.gif",
                effect: "show",
                strictLazyMode: false,
                useErrorImagePlaceholder: "https://img.gejiba.com/images/dbc7f2562e051afc3c39f916689ba5f0.png"
            });

            currentPage++;

            $loading.hide();

            if ($('#photoGallery .photo-item').length >= total) {
                $loadBtn.html(`
                <svg t="1756817423631" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="20662" width="256" height="256"><path d="M866.944 256.768c-95.488-95.488-250.496-95.488-345.984 0l-13.312 13.312-9.472-9.472c-93.824-93.824-246.656-100.736-343.68-10.368-101.888 94.976-104.064 254.592-6.4 352.256l13.568 13.568 299.264 299.264c25.728 25.728 67.584 25.728 93.44 0l312.576-312.576c95.488-95.488 95.488-250.368 0-345.984zM335.36 352.64c-20.48 0-40.832 6.016-56.704 18.944a85.4912 85.4912 0 0 0-6.912 126.976c9.984 9.984 9.984 26.24 0 36.224l-3.2 3.2c-8.192 8.192-21.632 8.192-29.952 0-52.608-52.608-57.216-138.496-6.528-192.896 26.112-28.032 61.952-43.52 100.096-43.52 14.08 0 25.6 11.52 25.6 25.6v3.072c0 12.416-9.984 22.4-22.4 22.4z" fill="#333333" p-id="20663"></path></svg>
                暂无更多数据
                `).prop('disabled', true);
            } else {
                $loadBtn.prop('disabled', false);
            }
        } else {
            $loading.hide();
            $loadBtn.prop('disabled', false);
        }
    }, 'json');
}

    
    
    function initLoveAlbum() {
        const $gallery = $('#photoGallery');
        if ($gallery.length === 0) {
            return;
        }

        // 重置
        currentPage = 1;
        total = 0;
        $('#photoGallery').empty();
            $('#loadMoreBtn').html(`
                            <svg t="1756817125714" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="4311" width="256" height="256"><path d="M849.799529 168.357647A481.882353 481.882353 0 1 0 993.882353 512a90.352941 90.352941 0 0 0-180.705882 0 301.176471 301.176471 0 1 1-90.051765-214.799059 90.352941 90.352941 0 1 0 126.674823-128.843294z" p-id="4312"></path></svg>
              加载更多
              `).prop('disabled', false);
    
        // 首次加载
        loadPhotos();
    
        $('#loadMoreBtn').off('click').on('click', loadPhotos);
    }
    
    
    
    function initScrollButton(btnSelector, targetSelector, tolerance = 800, duration = 800) {
    const $btn = $(btnSelector);
    const $target = $(targetSelector);

    if ($btn.length && $target.length) {
        // 点击按钮滚动到目标
        $btn.on('click', () => {
            const targetOffset = $target.offset().top;
            $('html, body').animate({ scrollTop: targetOffset }, duration);
        });

        // 根据滚动位置显示/隐藏按钮
        $(window).on('scroll resize', () => {
            const scrollTop = $(window).scrollTop();
            const targetOffset = $target.offset().top;

            if (Math.abs(scrollTop - targetOffset) <= tolerance) {
                $btn.fadeOut();
            } else {
                $btn.fadeIn();
            }
        }).trigger('scroll');
    }
}

</script>
<a href="#pjax-container" class="lg-skip-link">跳到主要内容</a>
<div class="lg-aria-live" id="lgAriaLive" aria-live="polite" aria-atomic="true"></div>

<!-- 加载动画 -->
<div id="loader-wrapper">
    <div class="loader-section"></div>
    <div id="loader"></div>
</div>
<script>
window.addEventListener('load', function() {
    setTimeout(function() {
        var lw = document.getElementById('loader-wrapper');
        if (lw) { lw.classList.add('fade-out'); setTimeout(function() { lw.style.display = 'none'; }, 500); }
    }, 300);
});
</script>

<!-- 灵动岛导航栏 -->
<div class="lgnewui-nav-placeholder" id="lgnewuiNavPlaceholder"></div>
<div class="lgnewui-nav-wrapper" id="lgnewuiNavWrapper">
    <nav class="lgnewui-nav-island-container" id="lgnewuiNavIsland">
        <div class="lgnewui-nav-indicator" id="lgnewuiNavIndicator"></div>

        <a href="articles.php"
           class="lgnewui-nav-island-item"
           draggable="false"
           data-desc="记录在一起的点滴时光"
           data-meta="Memory Notes"
           data-page="articles">
            <i class="ph-fill ph-notebook"></i>
            <span>点滴</span>
        </a>
        <a href="messages.php"
           class="lgnewui-nav-island-item"
           draggable="false"
           data-desc="留下想说的话与温柔回应"
           data-meta="Kind Messages"
           data-page="messages">
            <i class="ph-fill ph-chat-teardrop-dots"></i>
            <span>留言</span>
        </a>
        <a href="timeline.php"
           class="lgnewui-nav-island-item"
           draggable="false"
           data-desc="回看我们一路走来的轨迹"
           data-meta="Steps of Us"
           data-page="timeline">
            <i class="ph-fill ph-clock-countdown"></i>
            <span>轨迹</span>
        </a>
        <a href="index.php"
           class="lgnewui-nav-island-item active nav-home"
           draggable="false"
           data-desc="收好我们的日常与心动"
           data-meta="Our Cozy Place"
           data-page="index">
            <i class="ph-fill ph-house"></i>
        </a>
        <a href="albums.php"
           class="lgnewui-nav-island-item"
           draggable="false"
           data-desc="收藏见面与出游的闪亮瞬间"
           data-meta="Photo Keepsakes"
           data-page="albums">
            <i class="ph-fill ph-camera"></i>
            <span>相册</span>
        </a>
        <a href="lovelist.php"
           class="lgnewui-nav-island-item"
           draggable="false"
           data-desc="记下想一起完成的心愿"
           data-meta="Plans Together"
           data-page="lovelist">
            <i class="ph-fill ph-list-checks"></i>
            <span>清单</span>
        </a>
        <a href="about.php"
           class="lgnewui-nav-island-item"
           draggable="false"
           data-desc="用对话回放我们的故事"
           data-meta="Story Replay"
           data-page="about">
            <i class="ph-fill ph-book-open-text"></i>
            <span>关于</span>
        </a>
    </nav>
</div>

<!-- 移动端导航栏已移至 footer.php（参考站标准位置：footer-warp 之后） -->

<!-- 页面标题栏 -->
<div class="lgnewui-page-header">
    <div class="lgnewui-meta-container">
        <div class="lgnewui-meta-tag" id="lgnewuiMetaTag">
            <i class="ph-fill ph-star-of-life lgnewui-meta-icon"></i>
            <span id="lgnewuiMetaText">Sanctuary of Us</span>
            <i class="ph-fill ph-star-of-life lgnewui-meta-icon"></i>
        </div>
        <div class="lgnewui-meta-line" id="lgnewuiMetaLine"></div>
    </div>
    <h2 class="lgnewui-hero-title" id="lgnewuiHeroTitle"></h2>
</div>

<!-- 头部 -->
<div class="header-wrap">
    <div class="header">
        <!-- 吸顶 Logo -->
        <div class="lgnewui-header-left-avatar">
            <div class="stuck-logo stuck-logo--en-v7">
                <span class="stuck-logo__name" data-lg-tip="<?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?>"><?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="stuck-logo__redline-l"></span>
                <span class="stuck-logo__heart"><svg width="20" height="20" viewBox="0 0 256 256" fill="currentColor"><path d="M240,94c0,70-103.79,126.66-108.21,129a8,8,0,0,1-7.58,0C119.79,220.66,16,164,16,94A62.07,62.07,0,0,1,78,32c20.65,0,38.73,8.88,50,23.89C139.27,40.88,157.35,32,178,32A62.07,62.07,0,0,1,240,94Z" /></svg></span>
                <span class="stuck-logo__redline-r"></span>
                <span class="stuck-logo__name" data-lg-tip="<?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?>"><?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>

        <!-- 返回按钮 -->
        <div class="lg-capsule-back">
            <a href="javascript:void(0);" class="lg-capsule-back__btn lg-capsule-back__prev" title="返回">
                <i data-lucide="chevron-left"></i>
            </a>
            <a href="/index.php" class="lg-capsule-back__btn lg-capsule-back__home" title="首页">
                <i data-lucide="house"></i>
            </a>
        </div>

        <div class="logo">
            <h1><a class="alogo" href="index.php"><?php echo preg_replace('/\{([^}]+)\}/', '<b>$1</b>', htmlspecialchars($text['logo'], ENT_QUOTES, 'UTF-8')) ?></a></h1>
        </div>

        <!-- 吸顶时显示的右侧区域: 天气 + 地图 + 情侣头像 -->
        <div class="lgnewui-header-actions" id="lgnewuiHeaderActions">
            <!-- 天气按钮 -->
            <div class="lgnewui-header-weather is-loading" id="lgHeaderVisitorWeather" title="点击查看当前天气信息" role="button" tabindex="0" aria-expanded="false">
                <span class="lgnewui-header-weather-loading" id="lgHeaderVisitorWeatherLoading" aria-label="天气加载中">
                    <i data-lucide="loader-circle"></i>
                </span>
                <span class="lgnewui-header-weather-icon-wrap">
                    <i class="qi-999-fill lgnewui-header-weather-icon" id="lgHeaderVisitorWeatherIcon"></i>
                </span>
                <span class="lgnewui-header-weather-text" id="lgHeaderVisitorWeatherText"></span>
            </div>

            <!-- 地图按钮 -->
            <a href="javascript:void(0);" class="lgnewui-header-map" id="lgMapOpenBtn" title="足迹地图">
                <span class="lgnewui-header-map-icon-wrap">
                    <i class="ph-fill ph-globe-hemisphere-west"></i>
                </span>
                <span class="lgnewui-header-map-text">足迹</span>
            </a>

            <div class="lgnewui-header-divider"></div>

            <div class="lgnewui-couple-avatars-right">
                <div class="lgnewui-avatar-group">
                    <img src="<?php echo htmlspecialchars($girlimg_val, ENT_QUOTES, 'UTF-8') ?>" class="avatar-male" alt="<?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?>">
                    <img src="<?php echo htmlspecialchars($boyimg_val, ENT_QUOTES, 'UTF-8') ?>" class="avatar-female" alt="<?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <span class="lgnewui-right-heart"></span>
            </div>

            <!-- 移动端更多按钮 -->
            <button type="button" class="lg-header-more-btn" id="lgHeaderMoreBtn" aria-label="更多信息">
                <i data-lucide="ellipsis"></i>
            </button>
        </div>
        <div class="word" data-tip="<?php echo htmlspecialchars($text['writing'], ENT_QUOTES, 'UTF-8') ?>" data-tip-position="bottom">
            <span class="wenan"><?php echo htmlspecialchars($text['writing'], ENT_QUOTES, 'UTF-8') ?></span>
        </div>
    </div>
</div>
<div class="lgnewui-sticky-sentinel" id="lgnewuiStickySentinel"></div>

<!-- 旧版音乐播放器元素 -->
<div class="music_info">
    <div class="music_info_time" id="musicInfoTime"></div>
    <div class="music_info_btn">
        <span class="music_info_btn_play" data-music></span>
        <span class="music_info_btn_close"></span>
    </div>
</div>
<audio id="music"></audio>

<!-- 移动端更多面板（毛玻璃磨砂效果） -->
<div class="lg-header-more-panel" id="lgHeaderMorePanel">
    <div class="lg-header-more-overlay" data-close-panel></div>
    <div class="lg-header-more-sheet">
        <button type="button" class="lg-header-more-close" data-close-panel aria-label="关闭">
            <i data-lucide="x"></i>
        </button>

        <!-- stuck-logo 展示 -->
        <div class="lg-header-more-identity">
            <div class="stuck-logo stuck-logo--en-v7">
                <span class="stuck-logo__name" data-lg-tip="<?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?>"><?php echo htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') ?></span>
                <span class="stuck-logo__redline-l"></span>
                <span class="stuck-logo__heart"><svg width="20" height="20" viewBox="0 0 256 256" fill="currentColor"><path d="M240,94c0,70-103.79,126.66-108.21,129a8,8,0,0,1-7.58,0C119.79,220.66,16,164,16,94A62.07,62.07,0,0,1,78,32c20.65,0,38.73,8.88,50,23.89C139.27,40.88,157.35,32,178,32A62.07,62.07,0,0,1,240,94Z"/></svg></span>
                <span class="stuck-logo__redline-r"></span>
                <span class="stuck-logo__name" data-lg-tip="<?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?>"><?php echo htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>

        <!-- 功能入口：天气、地图 -->
        <div class="lg-header-more-actions">
            <a href="javascript:void(0);" class="lg-header-more-action-item" id="lgMorePanelWeather" data-close-panel>
                <span class="lg-header-more-action-icon">
                    <i class="qi-999-fill" id="lgMorePanelWeatherIcon"></i>
                </span>
                <span class="lg-header-more-action-label" id="lgMorePanelWeatherText">天气</span>
            </a>

            <a href="javascript:void(0);" class="lg-header-more-action-item" id="lgMorePanelMap" data-close-panel>
                <span class="lg-header-more-action-icon">
                    <i class="ph-fill ph-globe-hemisphere-west"></i>
                </span>
                <span class="lg-header-more-action-label">足迹地图</span>
            </a>
        </div>
    </div>
</div>

<!-- bg-wrap 移至各页面独立控制 -->


<style>
    /* 导航栏吸顶效果 */
    .lgnewui-nav-wrapper.is-fixed {
        position: fixed;
        top: 14px;
        left: 0;
        right: 0;
        z-index: 9999;
    }

    .lgnewui-nav-island-container.lgnewui-is-stuck {
        padding: 5px;
        background: rgba(248, 248, 248, 0.65);
        border-radius: 50px;
        box-shadow: none;
        transform: scale(0.88);
    }
</style>
<style>
    <?php echo preg_replace('/<\/style/i', '<\\/style', $diy['cssCon']) ?>
</style>

<script>
// 导航栏灵动岛效果 - 基于LG-NewUi研究成果
(function() {
    'use strict';

    // 配置
    var PAGE_MAPPING = {
        'index.php': 'index.php',
        'articles.php': 'articles.php',
        'messages.php': 'messages.php',
        'lovelist.php': 'lovelist.php',
        'albums.php': 'albums.php',
        'loveImg.php': 'albums.php',
        'about.php': 'about.php',
        'timeline.php': 'timeline.php'
    };

    // DOM元素
    var navWrapper = document.getElementById('lgnewuiNavWrapper');
    var navIsland = document.getElementById('lgnewuiNavIsland');
    var navIndicator = document.getElementById('lgnewuiNavIndicator');
    var navItems = document.querySelectorAll('.lgnewui-nav-island-item');
    var navPlaceholder = document.getElementById('lgnewuiNavPlaceholder');

    if (!navWrapper || !navIsland || !navItems.length) {
        return;
    }

    // 状态变量
    var navOriginalTop = null;
    var navHeight = null;
    var headerHeight = 72;

    // 工具函数：节流
    function throttle(func, limit) {
        var inThrottle = false;
        return function() {
            var context = this;
            var args = arguments;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(function() { inThrottle = false; }, limit);
            }
        };
    }

    // 根据当前路径设置活动状态
    function setActiveByPath() {
        var currentPath = window.location.pathname;
        var currentPage = currentPath.split('/').pop() || 'index.php';
        var targetPage = PAGE_MAPPING[currentPage] || currentPage;

        navItems.forEach(function(item) {
            item.classList.remove('active');
            var href = item.getAttribute('href');

            if (href === targetPage ||
                currentPage === href ||
                (href === 'index.php' && (currentPath === '/' || currentPath.endsWith('/') || currentPath.endsWith('index.php')))) {
                item.classList.add('active');
            }
        });

        // 默认首页
        if (!document.querySelector('.lgnewui-nav-island-item.active')) {
            var homeItem = document.querySelector('.lgnewui-nav-island-item.nav-home');
            if (homeItem) homeItem.classList.add('active');
        }
    }

    // 更新导航位置（吸顶效果）
    function updateNavPosition() {
        if (navOriginalTop === null) {
            navOriginalTop = navWrapper.getBoundingClientRect().top + window.scrollY;
            navHeight = navWrapper.offsetHeight;
        }

        var scrollTop = window.scrollY;
        var triggerPoint = navOriginalTop - headerHeight;
        var shouldStick = scrollTop > triggerPoint;

        if (shouldStick) {
            navWrapper.classList.add('is-fixed');
            navIsland.classList.add('lgnewui-is-stuck');
            if (navPlaceholder) {
                navPlaceholder.style.height = navHeight + 'px';
                navPlaceholder.style.display = 'block';
            }
        } else {
            navWrapper.classList.remove('is-fixed');
            navIsland.classList.remove('lgnewui-is-stuck');
            if (navPlaceholder) {
                navPlaceholder.style.display = 'none';
            }
        }
    }

    // 更新指示器位置
    function updateIndicator() {
        var activeItem = document.querySelector('.lgnewui-nav-island-item.active');
        if (activeItem && navIndicator) {
            var itemRect = activeItem.getBoundingClientRect();
            var navRect = navIsland.getBoundingClientRect();
            navIndicator.style.left = (itemRect.left - navRect.left) + 'px';
            navIndicator.style.width = itemRect.width + 'px';
        }
    }

    // 绑定事件
    function bindEvents() {
        // 滚动事件（节流）
        window.addEventListener('scroll', throttle(function() {
            updateNavPosition();
        }, 16));

        // 窗口大小变化
        var resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                navOriginalTop = null;
                updateNavPosition();
            }, 200);
        });

        // 导航项点击
        navItems.forEach(function(item) {
            item.addEventListener('click', function(e) {
                navItems.forEach(function(i) { i.classList.remove('active'); });
                this.classList.add('active');
                updateIndicator();
            });
        });
    }

    // 初始化
    function init() {
        setActiveByPath();
        updateNavPosition();
        updateIndicator();
        bindEvents();

        // 延迟更新指示器（等待字体加载）
        setTimeout(function() {
            navOriginalTop = null;
            updateNavPosition();
            updateIndicator();
        }, 100);
    }

    // 回到顶部函数
    window.scrollToTop = function() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    // 页面加载完成后初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
<!-- 移动端导航指示器脚本已移至 footer.php（与移动端导航DOM同位置） -->