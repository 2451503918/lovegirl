<?php
include_once 'head.php';

if (!isset($text) || !is_array($text)) {
    $text = ['boy' => '男方', 'girl' => '女方', 'boyimg' => '', 'girlimg' => '',
        'startTime' => date('Y-m-d H:i:s', time() - 365 * 86400), 'logo' => '我们的故事',
        'writing' => '记录美好时光', 'Copyright' => '', 'icp' => ''];
}

$startTs = strtotime(str_replace('T', ' ', $text['startTime'] ?? '2022-06-05 00:07:00'));

$articles = [];
if ($connect) {
    $res = mysqli_query($connect, "SELECT * FROM little ORDER BY id DESC");
    if ($res) { while ($r = mysqli_fetch_array($res)) { $articles[] = $r; } }
}

$monthMap = ['01'=>'一月','02'=>'二月','03'=>'三月','04'=>'四月','05'=>'五月','06'=>'六月',
    '07'=>'七月','08'=>'八月','09'=>'九月','10'=>'十月','11'=>'十一月','12'=>'十二月'];
?>

<div id="pjax-container">
    <div class="lgnewui-page-header">
        <div class="lgnewui-meta-container">
            <div class="lgnewui-meta-line"></div>
            <div class="lgnewui-meta-tag">
                <i class="ph-fill ph-notebook lgnewui-meta-icon"></i>
                <span>Memory Notes</span>
                <i class="ph-fill ph-notebook lgnewui-meta-icon"></i>
            </div>
            <div class="lgnewui-meta-line"></div>
        </div>
        <h2 class="lgnewui-hero-title">写下日常、心情与想念</h2>
    </div>

    <div id="pjax-container" class="lgnewui-container">
        <?php if (!empty($articles)): ?>
        <div class="lgnewui-article-grid">
            <div class="lgnewui-article-masonry" id="lgnewui-article-masonry">
                <?php foreach ($articles as $idx => $info):
                    $artDate = $info['date'] ?? '';
                    $artTs = strtotime($artDate);
                    $dayNum = floor((time() - $artTs) / 86400);
                    $day = date('d', $artTs);
                    $monthNum = date('m', $artTs);
                    $year = date('Y', $artTs);
                    $time = date('H:i', $artTs);
                    $monthCn = $monthMap[$monthNum] ?? $monthNum . '月';
                    $artTitle = htmlspecialchars($info['title'] ?? '', ENT_QUOTES, 'UTF-8');
                    $artText = htmlspecialchars(strip_tags(mb_substr($info['text'] ?? '', 0, 120, 'UTF-8')), ENT_QUOTES, 'UTF-8');
                    $artQQ = $info['qqimg'] ?? $text['boyimg'];
                    $artAuthor = $info['author'] ?? $text['boy'];
                    // Validate QQ number is numeric for safe use in CSS url()
                    if ($artQQ && !preg_match('/^https?:\/\//', $artQQ) && !ctype_digit($artQQ)) {
                        $artQQ = '';
                    }
                ?>
                <div class="lgnewui-article-masonry-item" data-aos="fade-up" data-aos-delay="<?php echo $idx * 50 ?>">
                    <div data-href="page.php?id=<?php echo $info['id'] ?>"
                       class="lgnewui-article-card-base lgnewui-article-theme-light lgnewui-article-aurora-spot"
                       style="cursor:pointer;">

                        <header class="lgnewui-article-card-header">
                            <div class="lgnewui-article-date-group">
                                <div class="lgnewui-article-big-day"><?php echo $day ?></div>
                                <div class="lgnewui-article-date-divider"></div>
                                <div class="lgnewui-article-month-year-group">
                                    <span class="lgnewui-article-month-chinese"><?php echo $monthCn ?></span>
                                    <span class="lgnewui-article-year-text"><?php echo $year ?></span>
                                </div>
                            </div>
                            <div class="lgnewui-article-badge-serial">
                                <span class="lgnewui-article-label">DAY</span>
                                <span class="lgnewui-article-num"><?php echo $dayNum ?></span>
                            </div>
                        </header>

                        <main class="lgnewui-article-card-content">
                            <div class="lgnewui-article-meta-info">
                                <div class="lgnewui-article-meta-item">
                                    <i class="ph-duotone ph-clock"></i>
                                    <span><?php echo $time ?></span>
                                </div>
                            </div>
                            <h3 class="lgnewui-article-card-title"><?php echo $artTitle ?></h3>
                            <p class="lgnewui-article-card-desc"><?php echo $artText ?></p>
                        </main>

                        <footer class="lgnewui-article-card-footer">
                            <div class="lg-author show-gender">
                                <div class="lg-author__ring">
                                    <div class="lg-author__avatar" style="background-image: url(https://q1.qlogo.cn/g?b=qq&nk=<?php echo $artQQ ?>&s=640)"></div>
                                </div>
                                <div class="lg-author__text">
                                    <span class="lg-author__name"><?php echo htmlspecialchars($artAuthor, ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>
                        </footer>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="lgnewui-no-data">
            <div class="lgnewui-no-data__icon"><i class="ph-fill ph-notebook"></i></div>
            <h3>还没有点滴记录</h3>
            <p>开始记录你们的日常与心情吧</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include_once 'footer.php'; ?>
</body>
</html>
