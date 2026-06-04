<?php
include_once 'head.php';
$time = gmdate("Y-m-d", time() + 8 * 3600);
$lovelist = "select * from lovelist order by id desc";
$reslist = mysqli_query($connect, $lovelist);

// 计算统计
$totalItems = 0;
$completedItems = 0;
$itemsArray = [];
while ($list = mysqli_fetch_array($reslist)) {
    $totalItems++;
    if ($list['icon']) $completedItems++;
    $itemsArray[] = $list;
}
// 重置指针
$lovelist = "select * from lovelist order by id desc";
$reslist = mysqli_query($connect, $lovelist);
$progressPercent = $totalItems > 0 ? round(($completedItems / $totalItems) * 100) : 0;
?>

    <div id="pjax-container">
        <div class="lgnewui-page-header">
            <div class="lgnewui-meta-container">
                <div class="lgnewui-meta-line"></div>
                <div class="lgnewui-meta-tag">
                    <i class="ph-bold ph-list-checks lgnewui-meta-icon"></i>
                    Bucket List
                </div>
                <div class="lgnewui-meta-line"></div>
            </div>
            <h2 class="lgnewui-hero-title">总有些惊奇的际遇 比方说当我遇见你</h2>
        </div>
        
        <div class="lgnewui-container">
            <div class="lgnewui-section">
                <div class="lgnewui-widget lgnewui-widget--lovelist lgnewui-mb-4">
                    <div class="lgnewui-widget__bg-icon"><i class="ph-fill ph-heart"></i></div>
                    <div class="lgnewui-lovelist-bottom">
                        <div class="lgnewui-lovelist-stats">
                            <span class="lgnewui-num-huge" id="lgnewui-lovelist-completed"><?php echo $completedItems; ?></span>
                            <span class="lgnewui-lovelist-fraction">/ <span id="lgnewui-lovelist-total"><?php echo $totalItems; ?></span></span>
                        </div>
                        <div class="lgnewui-progress">
                            <div class="lgnewui-progress__bar" style="width: <?php echo $progressPercent; ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row central central-800">
                <div class="card col-lg-12 col-md-12 col-sm-12 col-sm-x-12 lgnewui-card-enhanced">
                    <div class="list_texts <?php if ($text['Animation'] === "1") { ?>animated fadeInUp delay-03s<?php } ?>">
                        <div class="lovelist lgnewui-lovelist-enhanced">
                            <?php
                            foreach ($itemsArray as $list) {
                                ?>
                                <li class="cike lgnewui-lovelist-item" data-completed="<?php echo $list['icon'] ? '1' : '0'; ?>">
                                    <div class="lgnewui-lovelist-checkbox-wrapper">
                                        <?php if ($list['icon']) { ?>
                                            <div class="lgnewui-checkbox lgnewui-checkbox--checked">
                                                <i class="ph-fill ph-check"></i>
                                            </div>
                                        <?php } else { ?>
                                            <div class="lgnewui-checkbox"></div>
                                        <?php } ?>
                                    </div>
                                    <span class="lgnewui-lovelist-text <?php echo $list['icon'] ? 'lgnewui-lovelist-text--completed':'lgnewui-lovelist-text--pending' ?> "><?php echo htmlspecialchars($list['eventname']); ?></span>
                                    <?php if ($list['imgurl']) { ?>
                                        <div class="lgnewui-lovelist-icon-wrapper">
                                            <i class="ph-fill ph-image"></i>
                                        </div>
                                    <?php } ?>
                                    <div class="lgnewui-lovelist-arrow">
                                        <i class="ph-bold ph-caret-down"></i>
                                    </div>
                                </li>
                                <ul class="lgnewui-lovelist-image-container">
                                    <li>
                                        <?php if ($list['imgurl']) { ?>
                                            <img data-funlazy="<?php echo $list['imgurl']; ?>" alt="<?php echo htmlspecialchars($list['eventname']); ?>" class="lgnewui-lovelist-image" onerror="this.src='Style/img/default-avatar.svg'">
                                        <?php } ?>
                                    </li>
                                </ul>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
            $(function () {
                $(".lgnewui-lovelist-image-container").hide();
                $(".lgnewui-lovelist-item").bind("click", function () {
                    $(this).next(".lgnewui-lovelist-image-container").slideToggle(500).siblings(".lgnewui-lovelist-image-container").slideUp(500);
                    $(this).toggleClass("lgnewui-lovelist-item--expanded");
                });
                
                // 初始化动画
                if (typeof initStatsAnimation === "function") {
                    initStatsAnimation();
                }
                if (typeof initProgressAnimation === "function") {
                    initProgressAnimation();
                }
            });
        </script>
    </div>
    
    <?php
    include_once 'footer.php';
    ?>

</body>

</html>