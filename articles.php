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
    $res = mysqli_query($connect, "SELECT id, title, text, author, date, weather, mood, location, views, likes, encrypted, password FROM little ORDER BY id DESC");
    if ($res) { while ($r = mysqli_fetch_array($res)) { $articles[] = $r; } }
}

$monthMap = ['01'=>'一月','02'=>'二月','03'=>'三月','04'=>'四月','05'=>'五月','06'=>'六月',
    '07'=>'七月','08'=>'八月','09'=>'九月','10'=>'十月','11'=>'十一月','12'=>'十二月'];

// 天气图标映射
$weatherIconMap = [
    '晴' => 'ph-sun', '多云' => 'ph-cloud-sun', '阴' => 'ph-cloud',
    '小雨' => 'ph-cloud-rain', '大雨' => 'ph-cloud-rain', '雷阵雨' => 'ph-cloud-lightning',
    '雪' => 'ph-snowflake', '雾' => 'ph-sun-dim', '霾' => 'ph-sun-dim',
];

// 心情图标映射
$moodIconMap = [
    '开心' => 'ph-smiley', '难过' => 'ph-smiley-sad', '生气' => 'ph-smiley-angry',
    '平静' => 'ph-smiley-mehe', '惊喜' => 'ph-smiley-wink', '思念' => 'ph-heart',
];
?>

<div id="pjax-container">
    <div class="Width_limit_10rem">
        <div class="central mar_t0">

            <!-- 文章卡片瀑布流 -->
            <div class="lgnewui-article-grid">
                <div class="lgnewui-article-masonry" id="lgnewui-article-masonry">
                    <?php if (!empty($articles)):
                        $idx = 0;
                        foreach ($articles as $info):
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
                            $artAuthor = !empty($info['author']) ? $info['author'] : $text['boy'];
                            $isMaleAuthor = ($artAuthor === $text['boy']);
                            $artWeather = $info['weather'] ?? '';
                            $artMood = $info['mood'] ?? '';
                            $artLocation = $info['location'] ?? '';
                            $artViews = intval($info['views'] ?? 0);
                            $artLikes = intval($info['likes'] ?? 0);
                            $artId = intval($info['id']);
                            $isEncrypted = !empty($info['encrypted']) || !empty($info['password']);

                            // 天气图标
                            $weatherIcon = 'ph-cloud-sun';
                            foreach ($weatherIconMap as $wk => $wi) {
                                if (mb_strpos($artWeather, $wk) !== false) { $weatherIcon = $wi; break; }
                            }
                            // 心情图标
                            $moodIcon = 'ph-smiley';
                            foreach ($moodIconMap as $mk => $mi) {
                                if (mb_strpos($artMood, $mk) !== false) { $moodIcon = $mi; break; }
                            }

                            $aosDelay = min(50 + $idx * 50, 300);
                    ?>
                    <div class="lgnewui-article-masonry-item" data-aos="fade-up" data-aos-delay="<?php echo $aosDelay ?>">
                        <div data-href="page.php?id=<?php echo $artId ?>"
                           class="lgnewui-article-card-base lgnewui-article-theme-light lgnewui-article-aurora-spot<?php echo $isEncrypted ? ' lgnewui-article-card-encrypted' : '' ?>"
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
                                    <?php if ($isEncrypted): ?>
                                    <span class="lgnewui-article-label">LOCKED</span>
                                    <?php else: ?>
                                    <span class="lgnewui-article-label">DAY</span>
                                    <?php endif; ?>
                                    <span class="lgnewui-article-num"><?php echo $dayNum ?></span>
                                </div>
                            </header>

                            <main class="lgnewui-article-card-content">
                                <div class="lgnewui-article-meta-info">
                                    <div class="lgnewui-article-meta-item">
                                        <i class="ph-duotone ph-clock"></i>
                                        <span><?php echo $time ?></span>
                                    </div>
                                    <?php if (!empty($artWeather)): ?>
                                    <div class="lgnewui-article-meta-item">
                                        <i class="ph-duotone <?php echo $weatherIcon ?>"></i>
                                        <span><?php echo htmlspecialchars($artWeather, ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <?php endif; ?>
                                    <?php if (!empty($artMood)): ?>
                                    <div class="lgnewui-article-meta-item">
                                        <i class="ph-duotone <?php echo $moodIcon ?>"></i>
                                        <span><?php echo htmlspecialchars($artMood, ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <?php if ($isEncrypted): ?>
                                <!-- 加密内容：点阵遮罩 -->
                                <div class="lgnewui-content-lock-wrapper">
                                    <div class="lgnewui-encrypted-mask">
                                        <div class="lgnewui-dot-pattern"></div>
                                        <div class="lgnewui-lock-btn">
                                            <i class="ph-duotone ph-fingerprint"></i>
                                        </div>
                                        <div class="lgnewui-lock-text">点击查看</div>
                                    </div>
                                </div>
                                <?php else: ?>
                                    <h3 class="lgnewui-article-card-title"><?php echo $artTitle ?></h3>
                                    <p class="lgnewui-article-card-desc"><?php echo $artText ?></p>
                                <?php endif; ?>
                            </main>

                            <footer class="lgnewui-article-card-footer">
                                <div class="lg-author show-gender">
                                    <div class="lg-author__ring">
                                        <div class="lg-author__avatar <?php echo $isMaleAuthor ? 'lg-male-avatar' : 'lg-female-avatar'; ?>"
                                             style="background-image: url()"></div>
                                        <?php if ($isMaleAuthor): ?>
                                        <div class="lg-author__badge male">
                                            <i class="ph-bold ph-gender-male"></i>
                                        </div>
                                        <?php else: ?>
                                        <div class="lg-author__badge female">
                                            <i class="ph-bold ph-gender-female"></i>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="lg-author__text">
                                        <span class="lg-author__name"><?php echo htmlspecialchars($artAuthor, ENT_QUOTES, 'UTF-8') ?></span>
                                        <?php if (!empty($artLocation)): ?>
                                        <span class="lg-author__meta" data-tooltip="<?php echo htmlspecialchars($artLocation, ENT_QUOTES, 'UTF-8') ?>">
                                            <i class="ph-fill ph-map-pin"></i>
                                            <span><?php echo htmlspecialchars($artLocation, ENT_QUOTES, 'UTF-8') ?></span>
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="lgnewui-article-interact-box">
                                    <div class="lgnewui-article-action-btn">
                                        <i class="ph ph-eye"></i>
                                        <span data-view-count="article:<?php echo $artId ?>"><?php echo $artViews ?></span>
                                    </div>
                                    <div class="lgnewui-article-action-btn" data-like-target="article" data-like-id="<?php echo $artId ?>">
                                        <i class="ph ph-heart"></i>
                                        <span class="lg-interaction-like-num" data-like-count="article:<?php echo $artId ?>"><?php echo $artLikes ?></span>
                                    </div>
                                </div>
                            </footer>
                        </div>
                    </div>
                    <?php $idx++; endforeach; else: ?>
                    <div class="lgnewui-no-data">
                        <div class="lgnewui-no-data__icon"><i class="ph-fill ph-notebook"></i></div>
                        <h3>还没有点滴记录</h3>
                        <p>开始记录你们的日常与心情吧</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 用 LG_CONFIG 头像填充所有头像占位 -->
<script>
(function() {
    var maleAvatar = (window.LG_CONFIG && window.LG_CONFIG.maleAvatar) || '';
    var femaleAvatar = (window.LG_CONFIG && window.LG_CONFIG.femaleAvatar) || '';
    if (maleAvatar) {
        document.querySelectorAll('.lg-male-avatar').forEach(function(el) {
            el.style.backgroundImage = 'url(' + maleAvatar + ')';
        });
    }
    if (femaleAvatar) {
        document.querySelectorAll('.lg-female-avatar').forEach(function(el) {
            el.style.backgroundImage = 'url(' + femaleAvatar + ')';
        });
    }
})();
</script>

<?php include_once 'footer.php'; ?>
