<?php
include_once 'head.php';
$time = gmdate("Y-m-d", time() + 8 * 3600);

// 获取点滴数据
$articles = [];
if ($connect) {
    // 尝试使用 little 表（参考网站使用此表名）
    $article = "SELECT * FROM little ORDER BY id DESC";
    $resarticle = mysqli_query($connect, $article);
    if ($resarticle && mysqli_num_rows($resarticle) > 0) {
        while ($row = mysqli_fetch_array($resarticle)) {
            $articles[] = $row;
        }
    } else {
        // 回退到 article 表
        $article = "SELECT * FROM article ORDER BY id DESC";
        $resarticle = mysqli_query($connect, $article);
        if ($resarticle) {
            while ($row = mysqli_fetch_array($resarticle)) {
                $articles[] = $row;
            }
        }
    }
}
?>

<div id="pjax-container">
    <div class="lgnewui-page-header">
        <div class="lgnewui-meta-container">
            <div class="lgnewui-meta-line"></div>
            <div class="lgnewui-meta-tag">
                <i class="ph-bold ph-notebook lgnewui-meta-icon"></i>
                Memory Notes
            </div>
            <div class="lgnewui-meta-line"></div>
        </div>
        <h2 class="lgnewui-hero-title">写下日常、心情与想念</h2>
    </div>

    <div class="lgnewui-container">
        <?php if (!empty($articles)): ?>
        <div class="lgnewui-masonry-grid">
            <?php foreach ($articles as $info):
                $dayNum = floor((time() - strtotime($info['date'] ?? $info['articletime'] ?? '')) / 86400);
                $articleTitle = $info['title'] ?? $info['articletitle'] ?? '';
                $articleText = $info['text'] ?? $info['articletext'] ?? '';
                $articleName = $info['author'] ?? $info['articlename'] ?? ($text['boy'] ?? '作者');
                $articleTime = $info['date'] ?? $info['articletime'] ?? '';
                $articleLocation = $info['location'] ?? '未知';
                $articleWeather = $info['weather'] ?? '晴';
                $articleMood = $info['mood'] ?? '开心';
                $articleViews = $info['views'] ?? 0;
                $articleComments = $info['comments'] ?? 0;
            ?>
            <div class="lgnewui-masonry-item" data-aos="fade-up">
                <div class="lgnewui-article-card">
                    <?php if (isset($info['img']) && $info['img']): ?>
                    <div class="lgnewui-article-card__image">
                        <img src="<?php echo htmlspecialchars($info['img']); ?>" alt="" loading="lazy">
                    </div>
                    <?php endif; ?>
                    <div class="lgnewui-article-card__body">
                        <div class="lgnewui-article-card__meta">
                            <span class="lgnewui-article-card__date">
                                <?php echo date('d', strtotime($articleTime)); ?>
                                <small><?php echo date('m月', strtotime($articleTime)); ?></small>
                                <small><?php echo date('Y', strtotime($articleTime)); ?></small>
                            </span>
                            <span class="lgnewui-article-card__day">DAY<?php echo $dayNum; ?></span>
                            <span class="lgnewui-article-card__time"><?php echo date('H:i', strtotime($articleTime)); ?></span>
                        </div>
                        <div class="lgnewui-article-card__tags">
                            <span class="lgnewui-article-card__tag"><i class="ph-fill ph-cloud-sun"></i> <?php echo htmlspecialchars($articleWeather); ?></span>
                            <span class="lgnewui-article-card__tag"><i class="ph-fill ph-smiley"></i> <?php echo htmlspecialchars($articleMood); ?></span>
                        </div>
                        <h3 class="lgnewui-article-card__title"><?php echo htmlspecialchars($articleTitle); ?></h3>
                        <p class="lgnewui-article-card__text"><?php echo htmlspecialchars(mb_substr(strip_tags($articleText), 0, 120, 'UTF-8')); ?></p>
                        <div class="lgnewui-article-card__footer">
                            <div class="lgnewui-article-card__author">
                                <img src="https://q1.qlogo.cn/g?b=qq&nk=<?php echo $text['boyimg'] ?>&s=640" class="lgnewui-article-card__avatar">
                                <span><?php echo htmlspecialchars($articleName); ?></span>
                                <span class="lgnewui-article-card__location"><?php echo htmlspecialchars($articleLocation); ?></span>
                            </div>
                            <div class="lgnewui-article-card__stats">
                                <span><i class="ph-fill ph-eye"></i> <?php echo (int)$articleViews; ?></span>
                                <span><i class="ph-fill ph-chat-circle"></i> <?php echo (int)$articleComments; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="lgnewui-empty-state">
            <div class="lgnewui-empty-state__icon"><i class="ph-fill ph-notebook"></i></div>
            <h3>还没有点滴记录</h3>
            <p>开始记录你们的日常与心情吧</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
include_once 'footer.php';
?>

</body>
</html>