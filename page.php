<?php
$pageTitle = '文章详情';
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

$time = gmdate("Y-m-d", time() + 8 * 3600);
$id = isset($_GET['id']) ? $_GET['id'] : '';

$articleData = [
    'id' => 0,
    'title' => '',
    'text' => '',
    'author' => '',
    'date' => '',
    'location' => '',
    'weather' => '',
    'mood' => ''
];

if (is_numeric($id) && $id > 0 && $connect) {
    $article = "SELECT id, title, text, author, date, location, weather, mood FROM little WHERE id=? limit 1";
    $stmt = mysqli_prepare($connect, $article);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $articleData = [
                'id' => $row['id'],
                'title' => $row['title'] ?? '',
                'text' => $row['text'] ?? '',
                'author' => $row['author'] ?? ($text['boy'] ?? '作者'),
                'date' => $row['date'] ?? '',
                'location' => $row['location'] ?? '未知',
                'weather' => $row['weather'] ?? '晴',
                'mood' => $row['mood'] ?? '开心'
            ];
        }
        mysqli_stmt_close($stmt);
    }
} else {
    echo ("<script>alert('参数错误或页面不存在！');history.back();</script>");
    exit;
}

// 计算天数
$dayNum = 0;
if ($articleData['date']) {
    $dayNum = floor((time() - strtotime($articleData['date'])) / 86400);
}

// 获取上一篇和下一篇
$prevArticle = null;
$nextArticle = null;

if ($connect) {
    $prevQuery = "SELECT id, title, date FROM little WHERE id > ? ORDER BY id ASC LIMIT 1";
    $stmt = mysqli_prepare($connect, $prevQuery);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $nextArticle = $row;
        }
        mysqli_stmt_close($stmt);
    }

    $prevQuery = "SELECT id, title, date FROM little WHERE id < ? ORDER BY id DESC LIMIT 1";
    $stmt = mysqli_prepare($connect, $prevQuery);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $prevArticle = $row;
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<div id="pjax-container">
    <!-- 页面标题栏 -->
    <div class="lgnewui-page-header">
        <div class="lgnewui-meta-container">
            <div class="lgnewui-meta-line"></div>
            <div class="lgnewui-meta-tag">
                <i class="ph-bold ph-article lgnewui-meta-icon"></i>
                Article
            </div>
            <div class="lgnewui-meta-line"></div>
        </div>
        <h2 class="lgnewui-hero-title"><?php echo htmlspecialchars($articleData['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
    </div>

    <div class="lgnewui-container">
        <!-- 文章内容卡片 -->
        <div class="lgnewui-article-detail-card" data-aos="fade-up">
            <!-- 文章头部 -->
            <div class="lgnewui-article-detail-header">
                <div class="lgnewui-article-detail-meta">
                    <span class="lgnewui-article-detail-date">
                        <i class="ph-fill ph-calendar"></i>
                        <?php echo date('Y年m月d日 H:i', strtotime($articleData['date'])); ?>
                    </span>
                    <span class="lgnewui-article-detail-day">DAY <?php echo $dayNum; ?></span>
                    <span class="lgnewui-article-detail-location">
                        <i class="ph-fill ph-map-pin"></i>
                        <?php echo htmlspecialchars($articleData['location'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </div>
                <div class="lgnewui-article-detail-tags">
                    <span class="lgnewui-article-detail-tag">
                        <i class="ph-fill ph-cloud-sun"></i>
                        <?php echo htmlspecialchars($articleData['weather'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <span class="lgnewui-article-detail-tag">
                        <i class="ph-fill ph-smiley"></i>
                        <?php echo htmlspecialchars($articleData['mood'], ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </div>
            </div>

            <!-- 文章内容 -->
            <div class="lgnewui-article-detail-content">
                <h1 class="lgnewui-article-detail-title"><?php echo htmlspecialchars($articleData['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                <div class="lgnewui-article-detail-body">
                    <?php echo htmlspecialchars($articleData['text'], ENT_QUOTES, 'UTF-8'); ?>
                </div>
            </div>

            <!-- 文章作者信息 -->
            <div class="lgnewui-article-detail-author">
                <img src="<?php echo htmlspecialchars($boyimg_val ?? '/Style/img/boy.png', ENT_QUOTES, 'UTF-8'); ?>" class="lgnewui-article-detail-avatar">
                <div class="lgnewui-article-detail-author-info">
                    <span class="lgnewui-article-detail-author-name"><?php echo htmlspecialchars($articleData['author'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <span class="lgnewui-article-detail-author-label">作者</span>
                </div>
            </div>
        </div>

        <!-- 导航按钮 -->
        <div class="lgnewui-article-nav" data-aos="fade-up">
            <?php if ($prevArticle): ?>
            <a href="page.php?id=<?php echo $prevArticle['id']; ?>" class="lgnewui-article-nav-btn lgnewui-article-nav-btn--prev">
                <i class="ph-bold ph-arrow-left"></i>
                <div class="lgnewui-article-nav-info">
                    <span class="lgnewui-article-nav-label">上一篇</span>
                    <span class="lgnewui-article-nav-title"><?php echo htmlspecialchars(mb_substr($prevArticle['title'], 0, 20, 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </a>
            <?php else: ?>
            <a href="articles.php" class="lgnewui-article-nav-btn lgnewui-article-nav-btn--home">
                <i class="ph-bold ph-house"></i>
                <span>回到首页</span>
            </a>
            <?php endif; ?>

            <?php if ($nextArticle): ?>
            <a href="page.php?id=<?php echo $nextArticle['id']; ?>" class="lgnewui-article-nav-btn lgnewui-article-nav-btn--next">
                <div class="lgnewui-article-nav-info">
                    <span class="lgnewui-article-nav-label">下一篇</span>
                    <span class="lgnewui-article-nav-title"><?php echo htmlspecialchars(mb_substr($nextArticle['title'], 0, 20, 'UTF-8'), ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
                <i class="ph-bold ph-arrow-right"></i>
            </a>
            <?php else: ?>
            <span class="lgnewui-article-nav-btn lgnewui-article-nav-btn--disabled">
                <i class="ph-bold ph-warning"></i>
                <span>已是最早一篇</span>
            </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // 确保视频播放器初始化
    $('video').each(function() {
        var video = $(this);
        if (!video.parent().hasClass('video-container')) {
            setupVideoPlayer(video);
        }
    });
});
</script>

<?php
include_once 'footer.php';
?>
