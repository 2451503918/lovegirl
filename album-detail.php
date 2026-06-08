<?php
$pageTitle = '相册详情';
include_once 'head.php';

// 获取相册code
$code = isset($_GET['code']) ? trim($_GET['code']) : '';

if (empty($code)) {
    echo '<div id="pjax-container"><div class="lgnewui-no-data" style="text-align:center;padding:6rem 1rem;">
        <div class="lgnewui-no-data__icon"><i class="ph-fill ph-warning" style="font-size:3rem;opacity:0.3;"></i></div>
        <h3 style="margin-top:1rem;color:var(--lg-text-secondary);">404</h3>
        <p style="color:var(--lg-text-muted);">当前参数有误 请输入正确参数后访问</p>
        <a href="albums.php" class="lgnewui-back-btn" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.5rem;background:var(--lg-primary);color:#fff;border-radius:2rem;margin-top:1rem;text-decoration:none;">
            <i class="ph-bold ph-arrow-left"></i> 返回相册
        </a>
    </div></div>';
    include_once 'footer.php';
    exit;
}

// 从数据库获取相册信息
$album = null;
$photos = [];
if ($connect) {
    $sql = "SELECT id, code, title, img, `desc`, author, location, lng, lat, views, likes, password, private, date FROM photo WHERE code = '" . mysqli_real_escape_string($connect, $code) . "'";
    $result = mysqli_query($connect, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $album = mysqli_fetch_assoc($result);
    }
}

if (!$album) {
    echo '<div id="pjax-container"><div class="lgnewui-no-data" style="text-align:center;padding:6rem 1rem;">
        <div class="lgnewui-no-data__icon"><i class="ph-fill ph-warning" style="font-size:3rem;opacity:0.3;"></i></div>
        <h3 style="margin-top:1rem;color:var(--lg-text-secondary);">404</h3>
        <p style="color:var(--lg-text-muted);">当前相册不存在或已被删除</p>
        <a href="albums.php" class="lgnewui-back-btn" style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.75rem 1.5rem;background:var(--lg-primary);color:#fff;border-radius:2rem;margin-top:1rem;text-decoration:none;">
            <i class="ph-bold ph-arrow-left"></i> 返回相册
        </a>
    </div></div>';
    include_once 'footer.php';
    exit;
}

// 解析照片列表
if (!empty($album['img'])) {
    $imgLines = explode("\n", $album['img']);
    foreach ($imgLines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $photos[] = $line;
        }
    }
}

$photoCount = count($photos);
$albumTitle = htmlspecialchars($album['title'] ?? '', ENT_QUOTES, 'UTF-8');
$albumDate = htmlspecialchars($album['date'] ?? '', ENT_QUOTES, 'UTF-8');
$albumLocation = htmlspecialchars($album['location'] ?? '', ENT_QUOTES, 'UTF-8');
$albumDesc = htmlspecialchars($album['desc'] ?? '', ENT_QUOTES, 'UTF-8');
$albumViews = intval($album['views'] ?? 0);
$albumLikes = intval($album['likes'] ?? 0);
$isPrivate = !empty($album['password']) || !empty($album['private']);

$isMaleAuthor = ($album['author'] === 'boy' || $album['author'] === 'male');
$authorAvatar = $isMaleAuthor ? $boyimg_val : $girlimg_val;
$authorName = $isMaleAuthor ? htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8');

// 格式化日期
$albumDateFormatted = '';
if (!empty($albumDate)) {
    try {
        $dt = new DateTime($albumDate);
        $albumDateFormatted = $dt->format('Y年n月j日');
    } catch (Exception $e) {
        $albumDateFormatted = $albumDate;
    }
}

// 计算日期相对时间
$albumDateAgo = '';
if (!empty($albumDate)) {
    try {
        $dt = new DateTime($albumDate);
        $now = new DateTime();
        $diff = $dt->diff($now);
        if ($diff->y > 0) {
            $albumDateAgo = $diff->y . '年前';
        } elseif ($diff->m > 0) {
            $albumDateAgo = $diff->m . '个月前';
        } elseif ($diff->d > 0) {
            $albumDateAgo = $diff->d . '天前';
        } else {
            $albumDateAgo = '今天';
        }
    } catch (Exception $e) {
        $albumDateAgo = '';
    }
}
?>

<div id="pjax-container">
    <!-- 相册详情页 -->
    <div class="lg-album-detail">
        <!-- 返回按钮 -->
        <div class="lg-album-detail-header">
            <a href="albums.php" class="lg-back-btn">
                <i class="ph-bold ph-arrow-left"></i>
                <span>返回相册</span>
            </a>
        </div>

        <!-- 相册信息 -->
        <div class="lg-album-info" data-aos="fade-up">
            <div class="lg-album-title-row">
                <h1 class="lg-album-title"><?php echo $albumTitle; ?></h1>
                <span class="lg-album-ref">ALBUM REF. <?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
            <div class="lg-album-stats">
                <span class="lg-stat-item">
                    <i class="ph ph-images"></i>
                    <span><?php echo $photoCount; ?></span>
                </span>
                <span class="lg-stat-item">
                    <i class="ph ph-heart"></i>
                    <span><?php echo $albumLikes; ?></span>
                </span>
            </div>
            <div class="lg-album-author">
                <img src="<?php echo htmlspecialchars($authorAvatar, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo $authorName; ?>" class="lg-author-avatar">
                <div class="lg-author-info">
                    <span class="lg-author-name"><?php echo $authorName; ?>.</span>
                    <?php if (!empty($albumLocation)): ?>
                    <span class="lg-author-location">
                        <i class="ph-fill ph-map-pin"></i>
                        <?php echo $albumLocation; ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($albumDateFormatted)): ?>
            <div class="lg-album-date">
                <?php echo $albumDateFormatted; ?>
                <?php if (!empty($albumDateAgo)): ?>
                <span class="lg-date-ago"> · <?php echo $albumDateAgo; ?></span>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <?php if (!empty($albumDesc)): ?>
            <div class="lg-album-desc">
                <p><?php echo $albumDesc; ?></p>
            </div>
            <?php endif; ?>
            <div class="lg-album-count">
                共 <?php echo $photoCount; ?> 项
            </div>
        </div>

        <!-- 照片瀑布流 -->
        <div class="lg-album-photos" id="photoGrid">
            <?php if (!$isPrivate && !empty($photos)): ?>
                <?php foreach ($photos as $index => $photoUrl): 
                    $photoUrl = htmlspecialchars(trim($photoUrl), ENT_QUOTES, 'UTF-8');
                    $isVideo = preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', $photoUrl);
                ?>
                <div class="lg-photo-item" data-aos="fade-up" data-aos-delay="<?php echo ($index % 9) * 50; ?>">
                    <div class="lg-photo-wrapper <?php echo $isVideo ? 'is-video' : ''; ?>">
                        <?php if ($isVideo): ?>
                        <video class="lg-photo-video" 
                               src="<?php echo $photoUrl; ?>" 
                               controls 
                               playsinline
                               preload="metadata"></video>
                        <div class="lg-video-play-icon">
                            <i class="ph-fill ph-play"></i>
                        </div>
                        <?php else: ?>
                        <img class="lg-photo-img spotlight" 
                             data-funlazy="<?php echo $photoUrl; ?>"
                             src="<?php echo $photoUrl; ?>"
                             alt="<?php echo $albumTitle; ?>"
                             loading="lazy">
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php elseif ($isPrivate): ?>
                <!-- 私密相册 -->
                <div class="lg-private-album">
                    <i class="ph-duotone ph-fingerprint lg-private-icon"></i>
                    <h3>私密相册</h3>
                    <p>输入密码后查看</p>
                </div>
            <?php else: ?>
                <div class="lg-album-empty">
                    <i class="ph-fill ph-camera" style="font-size:3rem;opacity:0.3;"></i>
                    <p>暂无照片</p>
                </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($albumLocation)): ?>
        <!-- 地点信息 -->
        <div class="lg-album-location" data-aos="fade-up">
            <div class="lg-location-info">
                <i class="ph-fill ph-map-pin"></i>
                <span><?php echo $albumLocation; ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- 相册详情页脚本 -->
<script>
(function() {
    'use strict';
    
    // 页面标题
    var pageTitle = document.querySelector('.lg-album-title');
    if (pageTitle) {
        document.title = pageTitle.textContent + ' - 相册 - ' + (window.LG_CONFIG && LG_CONFIG.title || '');
    }
    
    // 更新导航高亮
    if (window.LG_NewUi && LG_NewUi.setActiveNav) {
        LG_NewUi.setActiveNav('loveImg');
    }
    
    // 初始化视频播放
    document.querySelectorAll('.lg-photo-video').forEach(function(video) {
        var wrapper = video.closest('.lg-photo-wrapper');
        var playIcon = wrapper ? wrapper.querySelector('.lg-video-play-icon') : null;
        
        if (video.paused) {
            if (playIcon) playIcon.style.display = 'flex';
        }
        
        video.addEventListener('click', function(e) {
            if (video.paused) {
                video.play();
                if (playIcon) playIcon.style.display = 'none';
            } else {
                video.pause();
                if (playIcon) playIcon.style.display = 'flex';
            }
        });
        
        video.addEventListener('ended', function() {
            if (playIcon) playIcon.style.display = 'flex';
        });
    });
    
    // 瀑布流布局
    if (typeof Masonry !== 'undefined' && typeof imagesLoaded !== 'undefined') {
        var grid = document.getElementById('photoGrid');
        if (grid) {
            imagesLoaded(grid, function() {
                var msnry = new Masonry(grid, {
                    itemSelector: '.lg-photo-item',
                    percentPosition: true,
                    gutter: 16
                });
            });
        }
    }
    
    // ViewImage 初始化
    if (typeof ViewImage !== 'undefined') {
        ViewImage.init('.spotlight');
    }
    
    // FunLazy 初始化
    if (typeof FunLazy === 'function') {
        FunLazy({
            placeholder: "Style/img/Loading2.gif",
            effect: "show",
            strictLazyMode: false,
            useErrorImagePlaceholder: "Style/img/error.svg"
        });
    }
    
})();
</script>

<style>
/* 相册详情页样式 */
.lg-album-detail {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 1rem 4rem;
}

.lg-album-detail-header {
    margin-bottom: 2rem;
}

.lg-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--lg-card-bg);
    border-radius: 2rem;
    color: var(--lg-text-primary);
    text-decoration: none;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.lg-back-btn:hover {
    background: var(--lg-primary);
    color: #fff;
    transform: translateX(-4px);
}

.lg-album-info {
    background: var(--lg-card-bg);
    border-radius: 1.5rem;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
}

.lg-album-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 1rem;
    margin-bottom: 1rem;
}

.lg-album-title {
    font-size: 2rem;
    font-weight: 700;
    color: var(--lg-text-primary);
    margin: 0;
}

.lg-album-ref {
    font-size: 0.8rem;
    color: var(--lg-text-muted);
    font-family: monospace;
}

.lg-album-stats {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.lg-stat-item {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    color: var(--lg-text-secondary);
    font-size: 0.95rem;
}

.lg-album-author {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.lg-author-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--lg-primary-light, #e8e8ff);
}

.lg-author-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.lg-author-name {
    font-weight: 600;
    color: var(--lg-text-primary);
}

.lg-author-location {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.85rem;
    color: var(--lg-text-muted);
}

.lg-album-date {
    color: var(--lg-text-secondary);
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.lg-date-ago {
    color: var(--lg-text-muted);
}

.lg-album-desc {
    color: var(--lg-text-secondary);
    line-height: 1.6;
    margin-bottom: 1rem;
}

.lg-album-count {
    color: var(--lg-text-muted);
    font-size: 0.85rem;
}

.lg-album-photos {
    columns: 3;
    column-gap: 1rem;
}

@media (max-width: 768px) {
    .lg-album-photos {
        columns: 2;
    }
}

@media (max-width: 480px) {
    .lg-album-photos {
        columns: 1;
    }
}

.lg-photo-item {
    break-inside: avoid;
    margin-bottom: 1rem;
}

.lg-photo-wrapper {
    border-radius: 0.75rem;
    overflow: hidden;
    background: var(--lg-card-bg);
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.lg-photo-wrapper:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
}

.lg-photo-img {
    width: 100%;
    height: auto;
    display: block;
    cursor: pointer;
}

.lg-photo-video {
    width: 100%;
    height: auto;
    display: block;
    cursor: pointer;
    background: #000;
}

.lg-video-play-icon {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 50px;
    height: 50px;
    background: rgba(255,255,255,0.9);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--lg-primary);
    font-size: 1.5rem;
    pointer-events: none;
}

.lg-photo-wrapper.is-video {
    position: relative;
}

.lg-album-location {
    margin-top: 2rem;
    padding: 1rem 1.5rem;
    background: var(--lg-card-bg);
    border-radius: 1rem;
    display: inline-flex;
}

.lg-location-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--lg-text-secondary);
}

.lg-private-album,
.lg-album-empty {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--lg-text-muted);
}

.lg-private-icon {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 1rem;
}

/* 响应式调整 */
@media (max-width: 768px) {
    .lg-album-detail {
        padding: 1rem 0.75rem 3rem;
    }
    
    .lg-album-info {
        padding: 1.5rem 1rem;
    }
    
    .lg-album-title {
        font-size: 1.5rem;
    }
    
    .lg-album-title-row {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<?php include_once 'footer.php'; ?>
