<?php
include_once 'head.php';

if (!isset($text) || !is_array($text)) {
    $text = [
        'boy' => '男方', 'girl' => '女方', 'boyimg' => '', 'girlimg' => '',
        'startTime' => date('Y-m-d H:i:s', time() - 365 * 86400), 'logo' => '我们的故事',
        'writing' => '记录美好时光', 'Copyright' => '', 'icp' => ''
    ];
}

// 头像URL处理
$boyimg_val = $text['boyimg'] ?? '';
$girlimg_val = $text['girlimg'] ?? '';
if ($boyimg_val && !preg_match('/^https?:\/\//', $boyimg_val)) {
    $boyimg_val = 'https://q1.qlogo.cn/g?b=qq&nk=' . $boyimg_val . '&s=640';
}
if ($girlimg_val && !preg_match('/^https?:\/\//', $girlimg_val)) {
    $girlimg_val = 'https://q1.qlogo.cn/g?b=qq&nk=' . $girlimg_val . '&s=640';
}

// 从数据库获取相册数据
$albums = [];
if ($connect) {
    // 查询相册列表（按创建时间倒序）
    $res = mysqli_query($connect, "SELECT * FROM photo ORDER BY id DESC");
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $albums[] = $row;
        }
    }
}

/**
 * 根据照片数量返回 grid 类名
 */
function getGridClass($count) {
    if ($count <= 1) return 'grid-1';
    if ($count <= 3) return 'grid-3';
    if ($count <= 6) return 'grid-6';
    return 'grid-9';
}

/**
 * 格式化文件大小
 */
function formatFileSize($bytes) {
    if ($bytes >= 1048576) return round($bytes / 1048576, 1) . 'MB';
    if ($bytes >= 1024) return round($bytes / 1024, 1) . 'KB';
    return $bytes . 'B';
}
?>

<div id="pjax-container">
    <div class="lg-page-container">

        <!-- Masonry Grid Container -->
        <div class="lg-masonry-grid">
            <?php if (!empty($albums)):
                foreach ($albums as $album):
                    $albumId = intval($album['id'] ?? 0);
                    $albumCode = htmlspecialchars($album['code'] ?? $albumId, ENT_QUOTES, 'UTF-8');
                    $albumTitle = htmlspecialchars($album['title'] ?? '', ENT_QUOTES, 'UTF-8');
                    $albumDesc = htmlspecialchars($album['desc'] ?? $album['text'] ?? '', ENT_QUOTES, 'UTF-8');
                    $albumAuthor = $album['author'] ?? 'boy';
                    $albumDate = htmlspecialchars($album['date'] ?? '', ENT_QUOTES, 'UTF-8');
                    $albumLocation = htmlspecialchars($album['location'] ?? '', ENT_QUOTES, 'UTF-8');
                    $albumLng = htmlspecialchars($album['lng'] ?? '', ENT_QUOTES, 'UTF-8');
                    $albumLat = htmlspecialchars($album['lat'] ?? '', ENT_QUOTES, 'UTF-8');
                    $albumViews = intval($album['views'] ?? 0);
                    $albumLikes = intval($album['likes'] ?? 0);
                    $isPrivate = !empty($album['password']) || !empty($album['private']);
                    $isMaleAuthor = ($albumAuthor === 'boy' || $albumAuthor === 'male');
                    $authorAvatar = $isMaleAuthor ? $boyimg_val : $girlimg_val;
                    $authorName = $isMaleAuthor ? htmlspecialchars($text['boy'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($text['girl'], ENT_QUOTES, 'UTF-8');
                    $badgeClass = $isMaleAuthor ? 'male' : 'female';
                    $iconClass = $isMaleAuthor ? 'ph-bold ph-gender-male' : 'ph-bold ph-gender-female';

                    // 解析照片列表
                    $photos = [];
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
                    $gridClass = getGridClass($photoCount);
                    $isSquare = in_array($gridClass, ['grid-6', 'grid-9']);
                    $displayCount = ($gridClass === 'grid-9') ? min($photoCount, 9) : $photoCount;
                    $overflowCount = $photoCount - 9;
                    $hasDetail = $photoCount > 1;
                    $detailUrl = 'album-detail.php?code=' . $albumCode;
            ?>
            <!-- Masonry Column -->
            <div class="lg-masonry-col" data-aos="fade-up" data-aos-delay="0">

                <?php if ($isPrivate): ?>
                <!-- 私密相册卡片 -->
                <a href="<?php echo $detailUrl ?>" class="lg-card lg-private-card">
                    <!-- Header (私密相册版) -->
                    <div class="lg-header">
                        <div class="lg-author show-gender">
                            <div class="lg-author__ring">
                                <img class="lg-author__avatar" src="<?php echo htmlspecialchars($authorAvatar, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar">
                                <div class="lg-author__badge <?php echo $badgeClass ?>">
                                    <i class="<?php echo $iconClass ?>"></i>
                                </div>
                            </div>
                            <div class="lg-author__text">
                                <span class="lg-author__name"><?php echo $authorName ?></span>
                                <span class="lg-author__meta"><?php echo $albumDate ?></span>
                            </div>
                        </div>
                        <!-- 跳转按钮 -->
                        <div class="lg-header-action">
                            <i class="ph-bold ph-arrow-right"></i>
                        </div>
                    </div>
                    <!-- Private Content (点阵遮罩 + 指纹图标) -->
                    <div class="lg-private-content">
                        <div class="lg-private-icon-box">
                            <i class="ph-duotone ph-fingerprint lg-private-icon"></i>
                        </div>
                        <div>
                            <h3 class="lg-private-title">私密相册</h3>
                            <span class="lg-private-desc">点击查看</span>
                        </div>
                    </div>
                </a>
                <?php else: ?>
                <!-- 普通相册卡片 -->
                <div class="lg-card">
                    <!-- Header -->
                    <div class="lg-header">
                        <div class="lg-author show-gender">
                            <div class="lg-author__ring">
                                <img class="lg-author__avatar" src="<?php echo htmlspecialchars($authorAvatar, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar">
                                <div class="lg-author__badge <?php echo $badgeClass ?>">
                                    <i class="<?php echo $iconClass ?>"></i>
                                </div>
                            </div>
                            <div class="lg-author__text">
                                <span class="lg-author__name"><?php echo $authorName ?></span>
                                <span class="lg-author__meta"><?php echo $albumDate ?></span>
                            </div>
                        </div>
                        <!-- 跳转按钮 (多张图片时显示) -->
                        <?php if ($hasDetail): ?>
                        <a href="<?php echo $detailUrl ?>" class="lg-header-action">
                            <i class="ph-bold ph-arrow-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>

                    <!-- Content -->
                    <div class="lg-content">
                        <h3 class="lg-title"><?php echo $albumTitle ?></h3>
                        <?php if (!empty($albumDesc)): ?>
                        <p class="lg-desc"><?php echo $albumDesc ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- Media -->
                    <?php if ($photoCount > 0): ?>
                    <div class="lg-media <?php echo $gridClass ?>" view-image>
                        <?php for ($i = 0; $i < $displayCount; $i++):
                            $photoUrl = htmlspecialchars(trim($photos[$i]), ENT_QUOTES, 'UTF-8');
                            $isLastInGrid = ($i === $displayCount - 1);
                            $showOverlay = $isLastInGrid && $overflowCount > 0;
                            $isVideo = (preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', $photoUrl));
                            $fileSize = isset($album['filesize']) ? formatFileSize(intval($album['filesize'])) : '';
                        ?>
                        <div class="lg-photo-box<?php echo $isSquare ? ' square' : '' ?><?php echo $isVideo ? ' is-video' : '' ?>"
                            <?php if ($isVideo): ?>
                            data-video-url="<?php echo $photoUrl ?>"
                            <?php endif; ?>>
                            <img class="lg-photo lazy"
                                data-src="<?php echo $photoUrl ?>"
                                data-original="<?php echo $photoUrl ?>"
                                src="<?php echo $photoUrl ?>"
                                alt="Photo"
                                <?php if ($isVideo): ?>no-view<?php endif; ?>>
                            <?php if (!empty($fileSize)): ?>
                            <span class="lg-file-size"><?php echo $fileSize ?></span>
                            <?php endif; ?>
                            <?php if ($isVideo): ?>
                            <div class="lg-video-icon"><i class="ph-fill ph-play"></i></div>
                            <?php endif; ?>
                            <!-- +N 遮罩层 -->
                            <?php if ($showOverlay): ?>
                            <a href="<?php echo $detailUrl ?>" class="lg-overlay">
                                <span>+<?php echo $overflowCount ?></span>
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Footer -->
                    <div class="lg-footer">
                        <?php if (!empty($albumLocation)): ?>
                        <div class="lg-location-tag"
                            <?php if (!empty($albumLng) && !empty($albumLat)): ?>
                            data-lng="<?php echo $albumLng ?>"
                            data-lat="<?php echo $albumLat ?>"
                            onclick="LGMap.open({ mode: 'albums', coords: [<?php echo $albumLng ?>, <?php echo $albumLat ?>], zoom: 20 })"
                            <?php endif; ?>
                            data-tooltip="<?php echo $albumLocation ?>">
                            <i class="ph-fill ph-map-pin"></i>
                            <span><?php echo $albumLocation ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="lg-actions-left">
                            <div class="lg-action-item">
                                <i class="ph ph-eye"></i>
                                <span data-view-count="album:<?php echo $albumId ?>"><?php echo $albumViews ?></span>
                            </div>
                            <div class="lg-action-item" data-like-target="album" data-like-id="<?php echo $albumId ?>">
                                <i class="ph ph-heart"></i>
                                <span class="lg-interaction-like-num" data-like-count="album:<?php echo $albumId ?>"><?php echo $albumLikes ?></span>
                            </div>
                            <?php if ($photoCount > 1): ?>
                            <div class="lg-photo-count">
                                <span class="num"><?php echo str_pad($photoCount, 2, '0', STR_PAD_LEFT) ?></span>
                                <span class="label">PICS</span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            <?php endforeach; else: ?>
            <!-- 无数据 -->
            <div class="lgnewui-no-data" style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem;">
                <div class="lgnewui-no-data__icon"><i class="ph-fill ph-camera" style="font-size: 3rem; opacity: 0.3;"></i></div>
                <h3 style="margin-top: 1rem; color: var(--lg-text-secondary);">还没有相册</h3>
                <p style="color: var(--lg-text-muted);">开始上传你们的美好瞬间吧</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- 加载更多 -->
        <div class="load-more lgnewui-load-more">
            <button class="lg-btn-alt lgnewui-btn-primary" id="loadMoreBtn">
                <svg class="icon" viewBox="0 0 1024 1024" width="20" height="20"><path d="M849.799529 168.357647A481.882353 481.882353 0 1 0 993.882353 512a90.352941 90.352941 0 0 0-180.705882 0 301.176471 301.176471 0 1 1-90.051765-214.799059 90.352941 90.352941 0 1 0 126.674823-128.843294z" fill="currentColor"></path></svg>
                <span id="loadMoreText">加载更多</span>
            </button>
        </div>
    </div>
</div>

<!-- 相册页面 JS -->
<script>
var boyAvatar = <?php echo json_encode($boyimg_val); ?>;
var girlAvatar = <?php echo json_encode($girlimg_val); ?>;
var boyName = <?php echo json_encode($text['boy']); ?>;
var girlName = <?php echo json_encode($text['girl']); ?>;

// 初始化图片查看器
if (typeof ViewImage !== 'undefined') {
    ViewImage.init('[view-image] .lg-photo:not([no-view])');
}

// 初始化懒加载
if (typeof FunLazy === 'function') {
    FunLazy({
        placeholder: "Style/img/Loading2.gif",
        effect: "show",
        strictLazyMode: false,
        useErrorImagePlaceholder: "Style/img/error.svg"
    });
}

// 加载更多按钮（如果需要分页）
$('#loadMoreBtn').on('click', function() {
    // 目前为单页全量渲染，可后续扩展为 AJAX 分页
    Toastify.showScenario('info', { text: '已加载全部相册' });
});
</script>

<?php include_once 'footer.php'; ?>
</body>
</html>
