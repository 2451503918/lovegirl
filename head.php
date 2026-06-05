<!--
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
-->
<?php
error_reporting(0);
include_once 'db_mock.php';
// include ("ipjc.php");
// include_once ("ip.php");
// include_once 'admin/connect.php';
// include_once 'admin/Function.php';

/*
$sql = "select * from text";
$result = mysqli_query($connect, $sql);
$text = mysqli_fetch_array($result);

$sql = "select * from diySet";
$result = mysqli_query($connect, $sql);
if (mysqli_num_rows($result)) {
    $diy = mysqli_fetch_array($result);
}

$copy = $text['Copyright'];
$icp = $text['icp'];
$Animation = $text['Animation'];
*/
?>

<script>

    console.log("%c Q & V | 3439780232", "color:#fff;background:#000;padding:8px 15px;font-weight: 700;border-radius:15px");
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
        var BirthDay = new Date("<?php echo $text['startTime'] ?>");
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
<head>
<link rel="shortcut icon" href="/favicon.ico" />
<meta name="keywords"
    content="<?php echo $text['title'] ?>,Like Girl 5.2.1-Stable,LGNeUi,情侣小站,开源情侣网站,PHP情侣网站,情侣记录,情侣网站,情侣项目,情侣小窝,Love,LikeGirl,Ki,PHP情侣小站,情侣小站使用教程,情侣小站使用文档">
<meta name="description" content="<?php echo htmlspecialchars($text['writing']) ?> - Like Girl 5.2.1-Stable">
<meta name="author" content="Ki">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="robots" content="index, follow">

<!-- Open Graph (Facebook/微信/QQ) -->
<meta property="og:type" content="website">
<meta property="og:site_name" content="<?php echo htmlspecialchars($text['title']) ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($text['title']) ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($text['writing']) ?>">
<meta property="og:url" content="https://love.54oimx.top/">
<meta property="og:image" content="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $text['boyimg'] ?>&s=640">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars($text['title']) ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($text['writing']) ?>">
<meta name="twitter:image" content="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $text['boyimg'] ?>&s=640">

<link href="https://fonts.googleapis.com/css?family=Concert+One|Pacifico" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Noto+Serif+SC:wght@400&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Niconne&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/Style/css/lg-variables.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-newui-nav.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-mobile-nav.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/phosphor-icons.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/phosphor-fill.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/leaving.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/index.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/little.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/about.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/animate.min.css">
<link rel="stylesheet" href="/Botui/botui.min.css">
<link rel="stylesheet" href="/Botui/botui-theme-default.css">
<link rel="stylesheet" href="/Style/Font/font_list/iconfont.css">
<link rel="stylesheet" href="/Style/css/loveImg.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/list.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/toastr/toastr.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/loadinglike.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/vendor/aos/aos.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-bento.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-weather.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-home-components.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-tooltip.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-context-menu.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/lg-enhanced.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/css/APlayer.min.css?LikeGirl=<?php echo $version ?>">
<link rel="stylesheet" href="/Style/vendor/qweather-icons/qweather-icons.css?LikeGirl=<?php echo $version ?>">
<script src="/Style/Font/font_leav/iconfont.js"></script>
<script src="/Botui/botui.min.js"></script>
<script src="/Style/js/vue.min.js"></script>
<script src="/Style/jquery/jquery.min.js"></script>
<script src="/Style/js/jquery.pjax.js" type="text/javascript"></script>
<script src="/Style/pagelir/spotlight.bundle.js"></script>
<script src="/Style/js/funlazy.min.js"></script>
<script src="/Style/js/loading.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/vendor/aos/aos.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/lg-home.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/lg-home-app.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/vendor/confetti/confetti.browser.min.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/lg-effects.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/lg-tooltip.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/lg-context-menu.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/Style/js/APlayer.min.js?LikeGirl=<?php echo $version ?>"></script>
<!-- LG-NewUi 核心功能模块 -->
<script src="/assets/js/lg-home-app.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/music-player.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-interaction.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-map.js?LikeGirl=<?php echo $version ?>"></script>
<script src="/assets/js/lg-init.js?LikeGirl=<?php echo $version ?>"></script>

<?php
echo htmlspecialchars_decode($diy['headCon'], ENT_QUOTES);
if ($diy['Pjaxkg'] === "1"):
    ?>
    <script>
        $(document).pjax('a[target!=_blank]', '#pjax-container', { fragment: '#pjax-container', timeout: 15000 });
        $(document).on('pjax:send', function () {
            NProgress.start();
        });
        $(document).on('pjax:complete', function () {
            $(".love_img img,.lovelist img,.little_texts img").addClass("spotlight");
            NProgress.done();
            
            FunLazy({
                placeholder: "Style/img/Loading2.gif",
                effect: "show",
                strictLazyMode: false,
                useErrorImagePlaceholder: "Style/img/error.svg"
            })
            
            $('.card, .card-b').click(function() {
                var link = $(this).find('a').get(0);
                if (link) {
                    link.click();
                }
            });
            
            $('#MessageBtn').click(function() {
                var targetOffset = $('#MessageArea').offset().top;
                if ($(window).scrollTop() !== targetOffset) {
                    $('html, body').animate({
                        scrollTop: targetOffset
                    }, 800);
                }
            });
            
            
            $('video').each(function() {
                var video = $(this);
                setupVideoPlayer(video);
            });
            
            initLoveAlbum();

            initScrollButton('#MessageBtn', '#MessageArea', 800, 800);

        });
        
        
    </script>
<?php endif; ?>
<script src="/Style/js/nprogress.js?LikeGirl=<?php echo $version ?>"></script>
<link href="/Style/css/nprogress.css?LikeGirl=<?php echo $version ?>" rel="stylesheet" type="text/css">
</head>
<body>

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

        <a href="little.php"
           class="lgnewui-nav-island-item"
           draggable="false"
           data-desc="记录在一起的点滴时光"
           data-meta="Memory Notes"
           data-page="little">
            <i class="ph-fill ph-notebook"></i>
            <span>点滴</span>
        </a>
        <a href="leaving.php"
           class="lgnewui-nav-island-item"
           draggable="false"
           data-desc="留下想说的话与温柔回应"
           data-meta="Kind Messages"
           data-page="leaving">
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
        <a href="loveImg.php"
           class="lgnewui-nav-island-item"
           draggable="false"
           data-desc="收藏见面与出游的闪亮瞬间"
           data-meta="Photo Keepsakes"
           data-page="loveImg">
            <i class="ph-fill ph-camera"></i>
            <span>相册</span>
        </a>
        <a href="list.php"
           class="lgnewui-nav-island-item"
           draggable="false"
           data-desc="记下想一起完成的心愿"
           data-meta="Plans Together"
           data-page="list">
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

<!-- 移动端导航栏 -->
<div class="lgnewui-mobile-nav-root">
    <!-- 极简包裹点阵 (v5) -->
    <div class="lgnewui-tab-template-v5-container lgnewui-glass-panel" id="lgnewui-mobile-nav-v5">
        <div class="lgnewui-tab-template-v5-indicator"></div>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item" href="little.php" data-page="little">
            <i class="ph-fill ph-notebook"></i>
            <span>点滴</span>
        </a>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item" href="leaving.php" data-page="leaving">
            <i class="ph-fill ph-chat-teardrop-dots"></i>
            <span>留言</span>
        </a>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item" href="timeline.php" data-page="timeline">
            <i class="ph-fill ph-clock-countdown"></i>
            <span>轨迹</span>
        </a>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item active" href="index.php" data-page="index">
            <i class="ph-fill ph-house"></i>
            <span>首页</span>
        </a>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item" href="loveImg.php" data-page="loveImg">
            <i class="ph-fill ph-camera"></i>
            <span>相册</span>
        </a>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item" href="list.php" data-page="list">
            <i class="ph-fill ph-list-checks"></i>
            <span>清单</span>
        </a>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item" href="about.php" data-page="about">
            <i class="ph-fill ph-book-open-text"></i>
            <span>关于</span>
        </a>
    </div>
</div>

<!-- 页面标题栏 -->
<div class="lgnewui-page-header">
    <div class="lgnewui-meta-container">
        <div class="lgnewui-meta-tag">
            <span>Sanctuary of Us</span>
        </div>
    </div>
</div>

<!-- 原始头部（备用） -->
<div class="header-wrap" style="display:none;">
    <div class="header">
        <div class="logo">
            <h1><a class="alogo" href="index.php"><?php echo preg_replace('/\{([^}]+)\}/', '<b>$1</b>', $text['logo']) ?></a></h1>
        </div>
        <div class="word" data-tip="<?php echo $text['writing'] ?>" data-tip-position="bottom">
            <span class="wenan"><?php echo $text['writing'] ?></span>
        </div>
    </div>
</div>

<!-- 头像内容 -->
<div class="bg-wrap">
    <div class="bg-img">
        <div class="central central-800">
            <!-- 情侣面板 -->
            <div class="lovers-panel <?php if ($text['Animation'] === "1") { ?>animated fadeInDown<?php } ?> <?php if ($diy['Blurkg'] === "2") { ?>Blurkg<?php } ?>">
                <div class="lover-card lover-left">
                    <div class="avatar-box avatarArea" style="position:relative;">
                        <img src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $text['boyimg'] ?>&s=640"
                             alt="<?php echo $text['boy'] ?>"
                             class="avatar-img aiv_touxiang"
                             onerror="this.src='Style/img/default-avatar.svg'">
                        <div class="lgnewui-head-avatar-mask">
                            <div class="lgnewui-head-avatar-anim-item">
                                <div class="lgnewui-head-avatar-gender-icon">♂</div>
                            </div>
                            <div class="lgnewui-head-avatar-anim-item">
                                <div class="lgnewui-head-avatar-status-text lgnewui-head-avatar-status-online">
                                    <em>在线</em>
                                </div>
                            </div>
                            <div class="lgnewui-head-avatar-divider"></div>
                            <div class="lgnewui-head-avatar-anim-item">
                                <div class="lgnewui-head-avatar-location">
                                    <em><?php echo $text['boy'] ?></em>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lover-info">
                        <div class="lover-name"><?php echo $text['boy'] ?></div>
                    </div>
                </div>

                <div class="love-distance-center">
                    <div class="love-icon-wrapper">
                        <img src="Style/img/like.svg" draggable="false" class="love-heartbeat">
                    </div>
                    <div class="distance-bubble" id="love-distance-text">
                        <div class="distance-icon-box">📍</div>
                        <div class="distance-text">
                            <span class="distance-text-sm">相距</span>
                            <span class="km-value" id="km-value">--</span>
                            <span class="distance-text-sm">km</span>
                        </div>
                    </div>
                </div>

                <div class="lover-card lover-right">
                    <div class="lover-info">
                        <div class="lover-name"><?php echo $text['girl'] ?></div>
                    </div>
                    <div class="avatar-box avatarArea" style="position:relative;">
                        <img src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $text['girlimg'] ?>&s=640"
                             alt="<?php echo $text['girl'] ?>"
                             class="avatar-img aiv_touxiang"
                             onerror="this.src='Style/img/default-avatar.svg'">
                        <div class="lgnewui-head-avatar-mask">
                            <div class="lgnewui-head-avatar-anim-item">
                                <div class="lgnewui-head-avatar-gender-icon">♀</div>
                            </div>
                            <div class="lgnewui-head-avatar-anim-item">
                                <div class="lgnewui-head-avatar-status-text lgnewui-head-avatar-status-offline">
                                    <em>离线</em>
                                </div>
                            </div>
                            <div class="lgnewui-head-avatar-divider"></div>
                            <div class="lgnewui-head-avatar-anim-item">
                                <div class="lgnewui-head-avatar-location">
                                    <em><?php echo $text['girl'] ?></em>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
            viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
            <defs>
                <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
            </defs>
            <g class="parallax">
                <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.7" />
                <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
                <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,0.3)" />
                <use xlink:href="#gentle-wave" x="48" y="7" fill="#fff" />
            </g>
        </svg>
    </div>
</div>


<style>
    .bg-img {
        background: url(<?php echo $text['bgimg'] ?>) no-repeat center !important;
        background-size: cover !important;
    }

    .wenan {
        color: rgb(97 97 97);
        transition: all 0.2s linear;
    }

    .alogo {
        color: rgb(97 97 97);
        transition: all 0.2s linear;
    }

    /* webkit, opera, IE9 （谷歌浏览器）*/
    ::selection {
        background: #6f6f6fc7;
        color: #ffffff;
    }

    /* mozilla firefox（火狐浏览器） */
    ::-moz-selection {
        background: #6f6f6fc7;
        color: #ffffff;
    }

    .delay-03s {
        -webkit-animation-delay: .3s;
        animation-delay: .3s;
    }

    /* ========== 情侣面板样式 ========== */
    .lovers-panel {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 2rem;
        padding: 3rem 4rem;
        backdrop-filter: blur(25px) saturate(180%);
        -webkit-backdrop-filter: blur(25px) saturate(180%);
        background: rgba(255, 255, 255, 0.25);
        border-radius: 2rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        transition: all 0.3s ease;
    }

    .lovers-panel:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(31, 38, 135, 0.25);
        background: rgba(255, 255, 255, 0.35);
    }

    .lover-card {
        display: flex;
        align-items: center;
        gap: 1.2rem;
    }

    .lover-card .avatar-box {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .lover-card:hover .avatar-box {
        transform: scale(1.08);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
    }

    .lover-card .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .lover-info {
        text-align: center;
    }

    .lover-name {
        font-size: 1.4rem;
        font-weight: 700;
        color: #fff;
        font-family: 'Noto Serif SC', serif;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        margin-bottom: 0.5rem;
    }

    .lover-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.9);
    }

    .lover-weather-icon {
        font-size: 1.2rem;
    }

    .lover-location {
        font-size: 0.85rem;
        opacity: 0.9;
    }

    .love-distance-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.8rem;
    }

    .love-icon-wrapper {
        width: 80px;
        height: 80px;
    }

    .love-heartbeat {
        width: 100%;
        height: 100%;
        animation: heartbeat 1.5s ease-in-out infinite;
    }

    @keyframes heartbeat {
        0%, 100% { transform: scale(1); }
        14% { transform: scale(1.1); }
        28% { transform: scale(1); }
        42% { transform: scale(1.1); }
        70% { transform: scale(1); }
    }

    .distance-val {
        font-size: 1.1rem;
        color: #fff;
        font-weight: 600;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        background: rgba(255, 255, 255, 0.2);
        padding: 0.4rem 1rem;
        border-radius: 2rem;
        backdrop-filter: blur(10px);
    }

    /* 移动端适配 */
    @media (max-width: 768px) {
        .lovers-panel {
            flex-direction: row;
            gap: 1rem;
            padding: 1.5rem;
        }

        .lover-card {
            flex-direction: column;
            text-align: center;
            flex: 1;
        }

        .lover-card .avatar-box {
            width: 70px;
            height: 70px;
        }

        .lover-name {
            font-size: 1.1rem;
        }

        .lover-meta {
            font-size: 0.8rem;
        }

        .love-distance-center {
            flex-direction: column;
            gap: 0.5rem;
        }

        .love-icon-wrapper {
            width: 50px;
            height: 50px;
        }

        .distance-val {
            font-size: 0.9rem;
            padding: 0.3rem 0.8rem;
        }
    }

    .Blurkg {
        backdrop-filter: blur(0px) !important;
        -webkit-backdrop-filter: blur(0px) !important;
        background: transparent !important;
    }

    .cpt-loading-mask.column {
        background: transparent !important;
    }

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

    .lgnewui-nav-island-container.lgnewui-is-stuck .lgnewui-nav-stuck-logo,
    .lgnewui-nav-island-container.lgnewui-is-stuck .lgnewui-nav-stuck-actions {
        display: flex;
        opacity: 1;
    }
</style>
<style>
    <?php echo $diy['cssCon'] ?>
</style>

<script>
// 导航栏灵动岛效果 - 基于LG-NewUi研究成果
(function() {
    'use strict';

    // 配置
    var PAGE_MAPPING = {
        'index.php': 'index.php',
        'little.php': 'little.php',
        'leaving.php': 'leaving.php',
        'list.php': 'list.php',
        'loveImg.php': 'loveImg.php',
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
<script>
// 移动端导航指示器 - 基于LG-NewUi研究成果
(function() {
    'use strict';

    var mobileNav = document.getElementById('lgnewui-mobile-nav-v5');
    var mobileIndicator = mobileNav ? mobileNav.querySelector('.lgnewui-tab-template-v5-indicator') : null;
    var mobileItems = document.querySelectorAll('.js-lgnewui-v5-item');

    if (!mobileNav || !mobileIndicator || !mobileItems.length) {
        return;
    }

    // 根据当前路径设置活动状态
    function setMobileActiveByPath() {
        var currentPath = window.location.pathname;
        var currentPage = currentPath.split('/').pop() || 'index.php';

        mobileItems.forEach(function(item) {
            item.classList.remove('active');
            var href = item.getAttribute('href');

            if (href === currentPage ||
                (href === 'index.php' && (currentPath === '/' || currentPath.endsWith('/') || currentPath.endsWith('index.php')))) {
                item.classList.add('active');
            }
        });

        // 默认首页
        if (!document.querySelector('.js-lgnewui-v5-item.active')) {
            var homeItem = document.querySelector('.js-lgnewui-v5-item[href="index.php"]');
            if (homeItem) homeItem.classList.add('active');
        }
    }

    // 更新指示器位置
    function updateMobileIndicator() {
        var activeItem = document.querySelector('.js-lgnewui-v5-item.active');
        if (activeItem && mobileIndicator) {
            var itemRect = activeItem.getBoundingClientRect();
            var navRect = mobileNav.getBoundingClientRect();
            mobileIndicator.style.left = (itemRect.left - navRect.left) + 'px';
        }
    }

    // 绑定事件
    function bindMobileEvents() {
        mobileItems.forEach(function(item) {
            item.addEventListener('click', function(e) {
                mobileItems.forEach(function(i) { i.classList.remove('active'); });
                this.classList.add('active');
                updateMobileIndicator();
            });
        });

        // 窗口大小变化
        var resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(updateMobileIndicator, 200);
        });
    }

    // 初始化
    function initMobile() {
        setMobileActiveByPath();
        updateMobileIndicator();
        bindMobileEvents();

        // 延迟更新指示器
        setTimeout(updateMobileIndicator, 100);
    }

    // 页面加载完成后初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMobile);
    } else {
        initMobile();
    }
})();
</script>