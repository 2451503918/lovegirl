<?php
include_once 'admin/Database.php';
$time = gmdate("Y-m-d", time() + 8 * 3600);
@$id = $_GET['id'];

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

if (is_numeric($id) && $id > 0) {
    $article = "SELECT * FROM little WHERE id=? limit 1";
    $stmt = $conn->prepare($article);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
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
        $stmt->close();
    }
} else {
    echo ("<script>alert('参数错误或页面不存在！');history.back();</script>");
    exit;
}

include_once 'head.php';

// 计算天数
$dayNum = 0;
if ($articleData['date']) {
    $dayNum = floor((time() - strtotime($articleData['date'])) / 86400);
}

// 获取上一篇和下一篇
$prevArticle = null;
$nextArticle = null;

$prevQuery = "SELECT id, title, date FROM little WHERE id > ? ORDER BY id ASC LIMIT 1";
$stmt = $conn->prepare($prevQuery);
if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $nextArticle = $row;
    }
    $stmt->close();
}

$prevQuery = "SELECT id, title, date FROM little WHERE id < ? ORDER BY id DESC LIMIT 1";
$stmt = $conn->prepare($prevQuery);
if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $prevArticle = $row;
    }
    $stmt->close();
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
        <h2 class="lgnewui-hero-title"><?php echo htmlspecialchars($articleData['title']); ?></h2>
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
                        <?php echo htmlspecialchars($articleData['location']); ?>
                    </span>
                </div>
                <div class="lgnewui-article-detail-tags">
                    <span class="lgnewui-article-detail-tag">
                        <i class="ph-fill ph-cloud-sun"></i>
                        <?php echo htmlspecialchars($articleData['weather']); ?>
                    </span>
                    <span class="lgnewui-article-detail-tag">
                        <i class="ph-fill ph-smiley"></i>
                        <?php echo htmlspecialchars($articleData['mood']); ?>
                    </span>
                </div>
            </div>

            <!-- 文章内容 -->
            <div class="lgnewui-article-detail-content">
                <h1 class="lgnewui-article-detail-title"><?php echo htmlspecialchars($articleData['title']); ?></h1>
                <div class="lgnewui-article-detail-body">
                    <?php echo $articleData['text']; ?>
                </div>
            </div>

            <!-- 文章作者信息 -->
            <div class="lgnewui-article-detail-author">
                <img src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $text['boyimg']; ?>&s=640" class="lgnewui-article-detail-avatar">
                <div class="lgnewui-article-detail-author-info">
                    <span class="lgnewui-article-detail-author-name"><?php echo htmlspecialchars($articleData['author']); ?></span>
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
                    <span class="lgnewui-article-nav-title"><?php echo htmlspecialchars(mb_substr($prevArticle['title'], 0, 20, 'UTF-8')); ?></span>
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
                    <span class="lgnewui-article-nav-title"><?php echo htmlspecialchars(mb_substr($nextArticle['title'], 0, 20, 'UTF-8')); ?></span>
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
