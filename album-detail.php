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

// 获取code参数
$code = isset($_GET['code']) ? trim($_GET['code']) : '';

if (empty($code)) {
    echo '<div id="pjax-container"><div class="lgnewui-no-data" style="text-align:center;padding:6rem 1rem;">
        <div class="lgnewui-no-data__icon"><i class="ph-fill ph-warning" style="font-size:3rem;opacity:0.3;"></i></div>
        <h3 style="margin-top:1rem;color:var(--lg-text-secondary);">404</h3>
        <p style="color:var(--lg-text-muted);">当前参数有误 请输入正确参数后访问</p>
        <a href="albums.php" style="display:inline-block;margin-top:1.5rem;padding:0.6rem 1.5rem;background:var(--lg-accent,#71b7ff);color:#fff;border-radius:50px;text-decoration:none;font-size:0.9rem;">返回相册</a>
    </div></div>';
    include_once 'footer.php';
    exit;
}

// 从数据库获取相册数据
$album = null;
if ($connect) {
    $stmt = mysqli_prepare($connect, "SELECT id, code, title, img, `desc`, author, location, lng, lat, views, likes, password, private, date FROM photo WHERE code = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $code);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $album = $row;
        }
        mysqli_stmt_close($stmt);
    }
}

if (!$album) {
    echo '<div id="pjax-container"><div class="lgnewui-no-data" style="text-align:center;padding:6rem 1rem;">
        <div class="lgnewui-no-data__icon"><i class="ph-fill ph-image-broken" style="font-size:3rem;opacity:0.3;"></i></div>
        <h3 style="margin-top:1rem;color:var(--lg-text-secondary);">相册不存在</h3>
        <p style="color:var(--lg-text-muted);">该相册可能已被删除或链接有误</p>
        <a href="albums.php" style="display:inline-block;margin-top:1.5rem;padding:0.6rem 1.5rem;background:var(--lg-accent,#71b7ff);color:#fff;border-radius:50px;text-decoration:none;font-size:0.9rem;">返回相册</a>
    </div></div>';
    include_once 'footer.php';
    exit;
}

// 检查私密相册密码
$isPrivate = !empty($album['password']) || !empty($album['private']);
$passwordInput = isset($_POST['password']) ? trim($_POST['password']) : '';
if ($isPrivate && !empty($album['password']) && $passwordInput !== $album['password']) {
    // 显示密码输入页面
    ?>
    <div id="pjax-container">
        <div class="lgnewui-container" style="max-width:500px;margin:4rem auto;padding:0 1rem;">
            <div class="lgnewui-article-detail-card" data-aos="fade-up" style="text-align:center;padding:3rem 2rem;">
                <div style="margin-bottom:1.5rem;">
                    <i class="ph-duotone ph-fingerprint" style="font-size:3rem;color:var(--lg-accent,#71b7ff);"></i>
                </div>
                <h2 style="margin-bottom:0.5rem;">私密相册</h2>
                <p style="color:var(--lg-text-muted,#999);margin-bottom:2rem;">请输入密码查看</p>
                <form method="POST" action="">
                    <input type="password" name="password" placeholder="输入访问密码"
                        style="width:100%;padding:0.8rem 1.2rem;border:1px solid #e5e7eb;border-radius:12px;font-size:1rem;outline:none;text-align:center;margin-bottom:1rem;"
                        onfocus="this.style.borderColor='var(--lg-accent,#71b7ff)'" onblur="this.style.borderColor='#e5e7eb'">
                    <button type="submit"
                        style="width:100%;padding:0.8rem;background:var(--lg-accent,#71b7ff);color:#fff;border:none;border-radius:12px;font-size:1rem;cursor:pointer;">
                        解锁查看
                    </button>
                </form>
                <a href="albums.php" style="display:inline-block;margin-top:1rem;color:var(--lg-text-muted,#999);text-decoration:none;font-size:0.85rem;">返回相册列表</a>
            </div>
        </div>
    </div>
    <?php include_once 'footer.php'; exit;
}

// 解析相册数据
$albumId = intval($album['id']);
$albumTitle = htmlspecialchars($album['title'] ?? '', ENT_QUOTES, 'UTF-8');
$albumDesc = htmlspecialchars($album['desc'] ?? '', ENT_QUOTES, 'UTF-8');
$albumAuthor = $album['author'] ?? 'boy';
$albumDate = htmlspecialchars($album['date'] ?? '', ENT_QUOTES, 'UTF-8');
$albumLocation = htmlspecialchars($album['location'] ?? '', ENT_QUOTES, 'UTF-8');
$albumViews = intval($album['views'] ?? 0);
$albumLikes = intval($album['likes'] ?? 0);
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
        if (!empty($line)) $photos[] = $line;
    }
}
$photoCount = count($photos);
?>

<div id="pjax-container">
    <link rel="stylesheet" href="/Style/css/loveImg.css?LikeGirl=<?php echo $version ?>">

    <div class="lgnewui-container" style="max-width:900px;margin:0 auto;padding:0 1rem;">

        <!-- 相册详情头部 -->
        <div class="lgnewui-article-detail-card" data-aos="fade-up">
            <div class="lgnewui-article-detail-header" style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;">
                <div style="display:flex;align-items:center;gap:12px;">
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
                </div>
                <div style="display:flex;align-items:center;gap:16px;color:var(--lg-text-muted,#999);font-size:0.85rem;">
                    <span><i class="ph ph-eye"></i> <?php echo $albumViews ?></span>
                    <span><i class="ph ph-heart"></i> <?php echo $albumLikes ?></span>
                    <span><i class="ph ph-images"></i> <?php echo $photoCount ?> 张</span>
                </div>
            </div>

            <div style="margin-top:1.5rem;">
                <h1 style="font-size:1.5rem;font-weight:600;margin-bottom:0.5rem;"><?php echo $albumTitle ?></h1>
                <?php if (!empty($albumDesc)): ?>
                <p style="color:var(--lg-text-secondary,#666);margin-bottom:0.5rem;"><?php echo $albumDesc ?></p>
                <?php endif; ?>
                <?php if (!empty($albumLocation)): ?>
                <div class="lg-location-tag" style="display:inline-flex;align-items:center;gap:4px;font-size:0.85rem;color:var(--lg-text-muted,#999);">
                    <i class="ph-fill ph-map-pin"></i>
                    <span><?php echo $albumLocation ?></span>
                </div>
                <?php endif; ?>
            </div>

            <div style="margin-top:0.5rem;font-size:0.8rem;color:var(--lg-text-muted,#aaa);">
                ALBUM REF. <?php echo htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>
            </div>
        </div>

        <!-- 照片瀑布流 -->
        <?php if ($photoCount > 0): ?>
        <div class="lg-masonry-grid" style="margin-top:1.5rem;" data-aos="fade-up">
            <?php foreach ($photos as $idx => $photoUrl):
                $photoUrl = htmlspecialchars(trim($photoUrl), ENT_QUOTES, 'UTF-8');
                $isVideo = (preg_match('/\.(mp4|mov|avi|webm)(\?|$)/i', $photoUrl));
            ?>
            <div class="lg-masonry-col">
                <div class="lg-card">
                    <div class="lg-media grid-1" view-image>
                        <div class="lg-photo-box<?php echo $isVideo ? ' is-video' : '' ?>"
                            <?php if ($isVideo): ?>data-video-url="<?php echo $photoUrl ?>"<?php endif; ?>>
                            <?php if ($isVideo): ?>
                            <video src="<?php echo $photoUrl ?>" controls preload="metadata" style="width:100%;border-radius:12px;"></video>
                            <?php else: ?>
                            <img class="lg-photo lazy" data-src="<?php echo $photoUrl ?>" data-original="<?php echo $photoUrl ?>" src="<?php echo $photoUrl ?>" alt="Photo">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="lgnewui-no-data" style="text-align:center;padding:4rem 1rem;">
            <div class="lgnewui-no-data__icon"><i class="ph-fill ph-camera" style="font-size:3rem;opacity:0.3;"></i></div>
            <h3 style="margin-top:1rem;color:var(--lg-text-secondary);">暂无照片</h3>
        </div>
        <?php endif; ?>

        <!-- 返回按钮 -->
        <div style="text-align:center;margin:2rem 0 3rem;">
            <a href="albums.php" style="display:inline-flex;align-items:center;gap:6px;padding:0.6rem 1.5rem;background:var(--lg-accent,#71b7ff);color:#fff;border-radius:50px;text-decoration:none;font-size:0.9rem;transition:all 0.2s;">
                <i class="ph-bold ph-arrow-left"></i>
                返回相册
            </a>
        </div>
    </div>
</div>

<!-- 相册详情 JS -->
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

// 视频播放器初始化
$('video').each(function() {
    var video = $(this);
    if (!video.parent().hasClass('video-container')) {
        setupVideoPlayer(video);
    }
});
</script>

<?php include_once 'footer.php'; ?>
