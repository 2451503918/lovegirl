<?php
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
        'icp' => '',
        'Animation' => ''
    ];
}

$time = gmdate("Y-m-d", time() + 8 * 3600);

// 计算统计
$totalItems = 0;
$completedItems = 0;
$pendingItems = 0;
$itemsArray = [];
$reslist = null;
if ($connect) {
    $reslist = mysqli_query($connect, "select * from lovelist order by id desc");
    if ($reslist) {
        while ($list = mysqli_fetch_array($reslist)) {
            $totalItems++;
            if ($list['is_done']) {
                $completedItems++;
            } else {
                $pendingItems++;
            }
            $itemsArray[] = $list;
        }
    }
}
?>

<div id="pjax-container">
    <link rel="stylesheet" href="/Style/css/lg-lovelist-inline.css">
    <!-- 页面标题栏 -->
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

    <div class="Width_limit_10rem">
        <div class="central mar_t0">
            <!-- 搜索区域：标签页 + 搜索框 -->
            <div class="Search_warp">
                <div class="Tab_Warp">
                    <div class="LgLoveList-tab-container">
                        <div class="LgLoveList-tab-slider"></div>
                        <div class="LgLoveList-tab" data-id="1">
                            <i class="ph-fill ph-check-circle"></i>
                            <span>已完成</span>
                            <span class="tab-badge"><?php echo $completedItems; ?></span>
                        </div>
                        <div class="LgLoveList-tab LgLoveList-tab-active" data-id="2">
                            <i class="ph-fill ph-heart"></i>
                            <span>全部</span>
                            <span class="tab-badge"><?php echo $totalItems; ?></span>
                        </div>
                        <div class="LgLoveList-tab" data-id="3">
                            <i class="ph-fill ph-clock"></i>
                            <span>未完成</span>
                            <span class="tab-badge"><?php echo $pendingItems; ?></span>
                        </div>
                    </div>
                </div>

                <div class="search-box">
                    <input id="search" name="search" type="text" placeholder="搜索甜蜜回忆...">
                    <button id="search_btn" class="shadow-blur">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>查询</span>
                    </button>
                </div>
            </div>

            <!-- 清单卡片列表 -->
            <div id="list_container">
                <div id="list_data">
                    <?php foreach ($itemsArray as $list) { ?>
                        <div class="love-card" id="event-<?php echo $list['id']; ?>">
                            <div class="card-header">
                                <div class="header-left">
                                    <span class="status-dot <?php echo $list['is_done'] ? 'com' : 'air'; ?>"></span>
                                    <span class="event-name"><?php echo htmlspecialchars($list['eventname'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php if ($list['imgurl']) { ?>
                                        <span class="event-tags">
                                            <span class="etag"><i class="ph-fill ph-image" title="有照片"></i></span>
                                        </span>
                                    <?php } ?>
                                </div>
                                <div class="header-right">
                                    <span class="toggle-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6 9l6 6 6-6" />
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="body-content">
                                    <?php if ($list['is_done']) { ?>
                                        <div class="achievement-watermark">
                                            <i class="ph-fill ph-seal-check"></i>
                                        </div>
                                    <?php } ?>

                                    <?php if ($list['imgurl']) { ?>
                                        <div class="body-gallery">
                                            <img data-funlazy="<?php echo htmlspecialchars($list['imgurl'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($list['eventname']); ?>" class="lovelist-gallery-img" onerror="this.src='Style/img/default-avatar.svg'">
                                        </div>
                                    <?php } ?>

                                    <div class="body-info">
                                        <div class="body-full-title"><?php echo htmlspecialchars($list['eventname'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        <div class="info-item">
                                            <span class="info-label">完成状态 / STATUS</span>
                                            <span class="info-value"><?php echo $list['is_done'] ? '已完成' : '未完成'; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function () {
            // 卡片折叠展开
            $(".card-body").hide();
            $(".card-header").on("click", function () {
                var $card = $(this).closest(".love-card");
                var $body = $card.find(".card-body");
                $body.slideToggle(400);
                $card.toggleClass("active");
                // 关闭其他已展开的卡片
                $(".love-card").not($card).removeClass("active").find(".card-body").slideUp(400);
            });

            // 标签页切换
            $(".LgLoveList-tab").on("click", function () {
                var tabId = $(this).data("id");
                $(".LgLoveList-tab").removeClass("LgLoveList-tab-active");
                $(this).addClass("LgLoveList-tab-active");

                // 更新滑块位置
                var $slider = $(".LgLoveList-tab-slider");
                var $container = $(".LgLoveList-tab-container");
                var tabRect = this.getBoundingClientRect();
                var containerRect = $container[0].getBoundingClientRect();
                $slider.css({
                    "width": tabRect.width + "px",
                    "transform": "translateX(" + (tabRect.left - containerRect.left - 3) + "px)"
                });

                // 筛选卡片
                $(".love-card").each(function () {
                    var $card = $(this);
                    var isDone = $card.find(".status-dot").hasClass("com");
                    if (tabId === 1) {
                        // 已完成
                        $card.toggle(isDone);
                    } else if (tabId === 3) {
                        // 未完成
                        $card.toggle(!isDone);
                    } else {
                        // 全部
                        $card.show();
                    }
                });

                // 关闭所有展开的卡片
                $(".love-card").removeClass("active").find(".card-body").slideUp(400);
            });

            // 搜索功能
            $("#search_btn").on("click", function () {
                var query = $("#search").val().toLowerCase().trim();
                $(".love-card").each(function () {
                    var name = $(this).find(".event-name").text().toLowerCase();
                    $(this).toggle(name.indexOf(query) > -1);
                });
            });

            $("#search").on("keyup", function (e) {
                if (e.key === "Enter") {
                    $("#search_btn").click();
                }
                if ($(this).val() === "") {
                    $(".love-card").show();
                }
            });

            // 初始化滑块位置
            var $activeTab = $(".LgLoveList-tab-active");
            if ($activeTab.length) {
                var $slider = $(".LgLoveList-tab-slider");
                var $container = $(".LgLoveList-tab-container");
                var tabRect = $activeTab[0].getBoundingClientRect();
                var containerRect = $container[0].getBoundingClientRect();
                $slider.css({
                    "width": tabRect.width + "px",
                    "transform": "translateX(" + (tabRect.left - containerRect.left - 3) + "px)"
                });
            }
        });
    </script>
</div>

<?php
include_once 'footer.php';
?>

</body>

</html>
