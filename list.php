<?php
include_once 'head.php';

if (!isset($text) || !is_array($text)) {
    $text = [
        'boy' => '男方', 'girl' => '女方', 'boyimg' => '', 'girlimg' => '',
        'startTime' => date('Y-m-d H:i:s', time() - 365 * 86400), 'logo' => '我们的故事',
        'writing' => '记录美好时光', 'Copyright' => '', 'icp' => '', 'Animation' => ''
    ];
}

$time = gmdate("Y-m-d", time() + 8 * 3600);

// 计算统计
$totalItems = 0;
$completedItems = 0;
$pendingItems = 0;
$itemsArray = [];
if ($connect) {
    $reslist = mysqli_query($connect, "SELECT id, is_done, eventname, imgurl, location, lng, lat, note, donedate, date FROM lovelist ORDER BY id DESC");
    if ($reslist) {
        while ($list = mysqli_fetch_array($reslist)) {
            $totalItems++;
            if (!empty($list['is_done'])) {
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
    <link rel="stylesheet" href="/Style/LoveListStyle/styleCarousel.css">
    <style>
        :root {
            --main-accent: #71b7ff;
            --text-primary: #333;
            --text-secondary: #999;
            --bg-card: #fff;
            --shadow-soft: 0 8px 25px -5px rgba(0, 0, 0, 0.08);
            --radius-lg: 16px;
        }

        .Search_warp { margin-bottom: 2rem; }
        .search-box { position: relative; width: 90%; max-width: 550px; margin: 0 auto; display: flex; align-items: center; }
        .Search_warp #search { padding: 1.1rem 6.5rem 1.1rem 7rem; border: none; background: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-radius: 50px; width: 100%; outline: none; transition: 0.3s; box-sizing: border-box; font-family: 'Noto Serif SC', serif; color: #515151; }
        .Search_warp #search:focus { box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .Search_warp #search_btn { display: inline-flex; align-items: center; gap: 6px; padding: 0.7rem 1.5rem; border: none; border-radius: 50px; color: #fff; background: var(--main-accent); font-weight: 600; position: absolute; top: 50%; right: 8px; transform: translateY(-50%); cursor: pointer; font-family: 'Noto Serif SC', serif; transition: all .3s; }
        .Search_warp #search_btn i { font-size: 13px; }
        .Search_warp #search_btn:hover { background: #5aa8f5; }

        .Tab_Warp { font-family: 'Noto Serif SC', serif; width: fit-content; margin: 1rem auto 2rem; }
        .LgLoveList-tab-container { position: relative; display: inline-flex; background: rgb(164 164 164 / 12%); border-radius: 12px; padding: 3px; gap: 2px; backdrop-filter: blur(10px); }
        .LgLoveList-tab-slider { position: absolute; top: 3px; left: 0; height: calc(100% - 6px); background: #ffffff; border-radius: 10px; transition: transform 0.4s cubic-bezier(0.34,1.56,0.64,1), width 0.3s cubic-bezier(0.34,1.56,0.64,1); z-index: 0; pointer-events: none; }
        .LgLoveList-tab { position: relative; z-index: 1; text-align: center; padding: 8px 16px; border-radius: 10px; cursor: pointer; color: rgba(60,60,67,0.6); font-weight: 500; transition: color 0.3s ease; font-size: 0.85rem; background: transparent; border: none; white-space: nowrap; display: flex; align-items: center; justify-content: center; gap: 6px; }
        .LgLoveList-tab-active { color: #000; font-weight: 600; }
        .LgLoveList-tab:hover:not(.LgLoveList-tab-active) { color: rgba(60,60,67,0.85); }
        .LgLoveList-tab i { font-size: 16px; transition: all 0.3s ease; line-height: 1; vertical-align: middle; }
        .LgLoveList-tab-active i { transform: scale(1.1); color: #000; }

        .tab-badge { display: inline-flex; align-items: center; justify-content: center; min-width: 18px; height: 18px; padding: 0 5px; font-size: 0.65rem; font-weight: 600; font-family: 'Inter', sans-serif; color: #999; background: #f0f0f0; border-radius: 10px; margin-left: 4px; transition: all 0.3s ease; box-sizing: border-box; }
        .LgLoveList-tab-active .tab-badge { background: #58aaff; color: #ffffff; }

        .scope-tag { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; background: #f8f8f8; border-radius: 20px; cursor: pointer; font-family: 'Noto Serif SC', serif; font-size: 0.82rem; font-weight: 500; color: #999; transition: all 0.3s cubic-bezier(0.4,0,0.2,1); user-select: none; z-index: 2; white-space: nowrap; }
        .scope-tag:hover { background: #f0f0f0; color: #666; }
        .scope-tag.active { background: #1d1d1f; color: #fff; }
        .scope-tag.active:hover { background: #333; color: #fff; }
        .scope-tag i { font-size: 11px; }

        #list_container { max-width: 900px; margin: 0 auto; padding: 0 16px; position: relative; }

        .love-card { background: var(--bg-card); border-radius: var(--radius-lg); box-shadow: var(--shadow-soft); margin-bottom: 1.2rem; overflow: hidden; transition: transform 0.3s; }
        .love-card:hover { transform: translateY(-2px); }
        .love-card.lgnewui-highlight { position: relative; overflow: hidden; }
        .love-card.lgnewui-highlight::before { content: ''; position: absolute; inset: 0; border-radius: inherit; padding: 2px; background: conic-gradient(from var(--highlight-angle, 0deg), transparent 0%, rgba(113,183,255,0.8) 10%, rgba(167,130,255,0.6) 15%, transparent 25%); -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0); -webkit-mask-composite: xor; mask-composite: exclude; animation: highlightSweep 4s linear forwards; pointer-events: none; z-index: 10; }
        @property --highlight-angle { syntax: '<angle>'; initial-value: 0deg; inherits: false; }
        @keyframes highlightSweep { 0% { --highlight-angle: 0deg; opacity: 1; } 90% { --highlight-angle: 1080deg; opacity: 1; } 100% { --highlight-angle: 1080deg; opacity: 0; } }

        .card-header { padding: 1.2rem 1.5rem; display: flex; align-items: center; justify-content: space-between; cursor: pointer; background: #fff; position: relative; z-index: 2; user-select: none; }
        .header-left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; overflow: hidden; padding-left: 4px; }
        .status-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
        .status-dot.com { background: var(--main-accent); box-shadow: 0 0 0 4px rgba(113,183,255,0.2); }
        .status-dot.air { background: #e0e0e0; box-shadow: 0 0 0 4px rgba(204,204,204,0.2); }
        .event-name { font-size: 1.1rem; font-weight: 600; color: var(--text-primary); font-family: 'Noto Serif SC', serif; transition: color 0.3s; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0; }
        .status-dot.air + .event-name { color: #bbb; font-weight: 500; }
        .event-tags { display: inline-flex; align-items: center; gap: 5px; flex-shrink: 0; margin-left: 6px; }
        .event-tags .etag { width: 22px; height: 22px; border-radius: 50%; background: #f0f0f0; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .toggle-icon { font-size: 1rem; color: #ccc; transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; }
        .love-card.active .toggle-icon { transform: rotate(180deg); color: var(--main-accent); }
        .love-card.active .card-header { background: #fafafa; }

        .card-body { display: none; border-top: 1px solid #f0f0f0; background: #fff; }
        .body-content { display: flex; flex-direction: column; position: relative; overflow: hidden; }
        .body-full-title { font-size: 1.25rem; font-weight: 300; color: #9f9f9f; line-height: 1.6; word-break: break-word; padding-bottom: 1rem; margin-bottom: 0.5rem; border-bottom: 1px solid transparent; background-image: linear-gradient(to right, #e8e8e8, transparent); background-size: 100% 1px; background-position: bottom; background-repeat: no-repeat; }

        .achievement-watermark { position: absolute; bottom: 16px; right: 16px; pointer-events: none; z-index: 2; }
        .achievement-watermark i { font-size: 4.5rem; color: #71b7ff; }
        .achievement-watermark.wm-pending i { font-size: 3.5rem; color: #e0e0e0; }

        .body-gallery { width: 100%; height: 240px; background: #f8f8f8; position: relative; overflow: hidden; }
        .ConventionPhoto, .f-carousel__viewport, .f-carousel__track, .f-carousel__slide { height: 100% !important; }
        .f-carousel__slide img.lazy-loaded, .f-carousel__slide img { width: 100%; height: 100% !important; object-fit: cover; display: block; }
        .f-carousel__nav, .f-button.is-prev, .f-button.is-next, .f-carousel__button { display: none !important; visibility: hidden !important; opacity: 0 !important; pointer-events: none !important; }
        .img-counter { position: absolute; bottom: 15px; right: 15px; background: rgba(0,0,0,0.5); color: #fff; font-size: 12px; padding: 4px 10px; border-radius: 20px; z-index: 10; pointer-events: none; backdrop-filter: blur(4px); font-family: Arial, sans-serif; font-weight: 500; letter-spacing: 1px; }

        .no-img-placeholder { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #ddd; gap: 8px; font-size: 0.9rem; }
        .no-img-placeholder svg { width: 32px; height: 32px; fill: #eee; }

        .body-info { padding: 1.5rem; display: flex; flex-direction: column; gap: 2rem; }
        .info-item { display: flex; flex-direction: column; gap: 4px; }
        .info-label { font-size: 0.8rem; color: #aaa; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }
        .info-value { font-size: 1rem; color: #444; font-weight: 500; line-height: 1.5; }
        .info-item.remark-item .info-value { font-size: 1rem; color: #504b4b; margin-top: 5px; font-weight: 300; }

        .lovelist-location-link { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px 4px 4px; border-radius: 999px; background: #f3f4f6; font-size: 12px; font-weight: 500; color: #c5c5c5; transition: all 0.2s ease; max-width: 180px; width: fit-content; }
        .lovelist-location-link i { display: flex; align-items: center; justify-content: center; width: 22px; height: 22px; border-radius: 50%; background: #ffffff; font-size: 11px; color: #9ca3af; flex-shrink: 0; transition: all 0.2s ease; }
        .lovelist-location-link span { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1; min-width: 0; }
        .lovelist-location-link.has-coords { cursor: pointer; }
        .lovelist-location-link.has-coords:hover { background: #f0f9ff; color: #3b82f6; }
        .lovelist-location-link.has-coords:hover i { background: #dbeafe; color: #3b82f6; }
        .lovelist-location-link.has-coords:active { transform: scale(0.95); }

        @media (min-width: 768px) {
            .body-content { flex-direction: row; height: 360px; }
            .body-gallery { flex: 6; height: 100%; border-right: 1px solid #f0f0f0; }
            .body-info { flex: 4; padding: 2.5rem 2rem; justify-content: center; overflow-y: auto; }
        }

        /* 骨架屏 */
        @keyframes lgnewui-skeleton-screen-shimmer { 0% { background-position: -1000px 0; } 100% { background-position: 1000px 0; } }
        .lgnewui-skeleton-screen-wrap { display: grid; gap: 16px; opacity: 0; visibility: hidden; position: absolute; top: 0; left: 16px; right: 16px; z-index: 8; pointer-events: none; transform: translateY(4px); transition: opacity 0.24s ease, transform 0.24s ease, visibility 0s linear 0.24s; }
        .lgnewui-skeleton-screen-wrap.is-active { opacity: 1; visibility: visible; transform: translateY(0); transition-delay: 0s; }
        .lgnewui-skeleton-screen-anim { background-color: #e2e5e7; background-image: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.6) 20%, rgba(255,255,255,0) 40%); background-size: 1000px 100%; background-repeat: no-repeat; animation: lgnewui-skeleton-screen-shimmer 2s infinite linear forwards; overflow: hidden; position: relative; }
        .lgnewui-skeleton-screen-text { border-radius: 4px; }
        .lgnewui-skeleton-screen-circle { border-radius: 50%; }
        .lgnewui-skeleton-screen-flex { display: flex; }
        .lgnewui-skeleton-screen-items-center { align-items: center; }
        .lgnewui-skeleton-screen-justify-between { justify-content: space-between; }
        .lgnewui-skeleton-screen-gap-3 { gap: 12px; }
        .lgnewui-skeleton-screen-gap-4 { gap: 16px; }
        .lgnewui-skeleton-screen-card { box-sizing: border-box; background-color: #ffffff; padding: 16px 24px; border-radius: 16px; width: 100%; box-shadow: none; border: none; margin: 0; }
        .lgnewui-skeleton-screen-left-wrap { flex: 1; }
        .lgnewui-skeleton-screen-dot { width: 22px; height: 22px; flex-shrink: 0; }
        .lgnewui-skeleton-screen-line { width: 100%; max-width: 240px; height: 18px; }
        .lgnewui-skeleton-screen-arrow { width: 16px; height: 16px; border-radius: 3px; flex-shrink: 0; }

        /* 列表淡入动画 */
        .lg-list-fade-in { animation: lgListFadeIn 0.4s ease-out forwards; }
        .lg-list-fade-in .love-card { animation: lgCardSlideIn 0.35s ease-out backwards; }
        .lg-list-fade-in .love-card:nth-child(1) { animation-delay: 0ms; }
        .lg-list-fade-in .love-card:nth-child(2) { animation-delay: 40ms; }
        .lg-list-fade-in .love-card:nth-child(3) { animation-delay: 80ms; }
        .lg-list-fade-in .love-card:nth-child(4) { animation-delay: 120ms; }
        .lg-list-fade-in .love-card:nth-child(5) { animation-delay: 160ms; }
        .lg-list-fade-in .love-card:nth-child(6) { animation-delay: 200ms; }
        .lg-list-fade-in .love-card:nth-child(7) { animation-delay: 240ms; }
        .lg-list-fade-in .love-card:nth-child(8) { animation-delay: 280ms; }
        .lg-list-fade-in .love-card:nth-child(9) { animation-delay: 320ms; }
        .lg-list-fade-in .love-card:nth-child(10) { animation-delay: 360ms; }
        .lg-list-fade-in .love-card:nth-child(n+11) { animation-delay: 400ms; }
        @keyframes lgListFadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes lgCardSlideIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        @media (max-width: 480px) {
            .scope-tag { padding: 6px 10px; font-size: 0.75rem; gap: 5px; left: 6px; }
            .scope-tag i { font-size: 10px; }
            .Search_warp #search { padding: 1rem 5.5rem 1rem 6rem; font-size: 0.85rem; }
            .Search_warp #search_btn { padding: 0.6rem 1rem; font-size: 0.8rem; }
            .Tab_Warp { max-width: 100%; margin: 0.8rem auto 1.2rem; }
            .LgLoveList-tab-container { padding: 2px; gap: 2px; border-radius: 10px; }
            .LgLoveList-tab { padding: 7px 10px; font-size: 0.8rem; gap: 4px; }
            .LgLoveList-tab i { font-size: 14px; }
            .tab-badge { min-width: 16px; height: 16px; padding: 0 4px; font-size: 0.6rem; border-radius: 8px; }
        }
    </style>

    <div class="Width_limit_10rem">
        <div class="central mar_t0">

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
                    <div class="scope-tag" id="scopeTag" data-scope="all">
                        <i class="fa-solid fa-heart"></i>
                        <span class="scope-tag-text">全部</span>
                    </div>
                    <input id="search" name="search" type="text" placeholder="搜索甜蜜回忆...">
                    <button id="search_btn" class="shadow-blur">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <span>查询</span>
                    </button>
                </div>
            </div>

            <div id="list_container">
                <!-- 骨架屏 -->
                <div id="lgListSkeleton" class="lgnewui-skeleton-screen-wrap" aria-hidden="true">
                    <?php for ($i = 0; $i < 10; $i++): ?>
                    <div class="lgnewui-skeleton-screen-card lgnewui-skeleton-screen-flex lgnewui-skeleton-screen-items-center lgnewui-skeleton-screen-justify-between lgnewui-skeleton-screen-gap-4">
                        <div class="lgnewui-skeleton-screen-flex lgnewui-skeleton-screen-items-center lgnewui-skeleton-screen-gap-3 lgnewui-skeleton-screen-left-wrap">
                            <div class="lgnewui-skeleton-screen-anim lgnewui-skeleton-screen-circle lgnewui-skeleton-screen-dot"></div>
                            <div class="lgnewui-skeleton-screen-anim lgnewui-skeleton-screen-text lgnewui-skeleton-screen-line"></div>
                        </div>
                        <div class="lgnewui-skeleton-screen-anim lgnewui-skeleton-screen-text lgnewui-skeleton-screen-arrow"></div>
                    </div>
                    <?php endfor; ?>
                </div>

                <div class="query_data"></div>

                <div id="list_data" class="lg-list-fade-in">
                    <?php
                    $idx = 0;
                    foreach ($itemsArray as $list):
                        $itemId = intval($list['id']);
                        $isDone = !empty($list['is_done']);
                        $eventName = htmlspecialchars($list['eventname'] ?? '', ENT_QUOTES, 'UTF-8');
                        $imgUrl = $list['imgurl'] ?? '';
                        $location = htmlspecialchars($list['location'] ?? '', ENT_QUOTES, 'UTF-8');
                        $lng = htmlspecialchars($list['lng'] ?? '', ENT_QUOTES, 'UTF-8');
                        $lat = htmlspecialchars($list['lat'] ?? '', ENT_QUOTES, 'UTF-8');
                        $note = htmlspecialchars($list['note'] ?? $list['remark'] ?? '', ENT_QUOTES, 'UTF-8');
                        $doneDate = htmlspecialchars($list['donedate'] ?? $list['date'] ?? '', ENT_QUOTES, 'UTF-8');
                        $hasImg = !empty($imgUrl);
                        $hasLocation = !empty($location);
                        $hasNote = !empty($note);
                        $hasCoords = !empty($lng) && !empty($lat);
                        $aosDelay = min(50 + $idx * 50, 300);

                        // 解析多图
                        $photos = [];
                        if ($hasImg) {
                            $imgLines = explode("\n", $imgUrl);
                            foreach ($imgLines as $line) {
                                $line = trim($line);
                                if (!empty($line)) $photos[] = $line;
                            }
                        }
                        $photoCount = count($photos);
                    ?>
                    <div class="love-card" id="event-<?php echo $itemId ?>" data-aos="fade-up" data-aos-delay="<?php echo $aosDelay ?>">
                        <div class="card-header">
                            <div class="header-left">
                                <span class="status-dot <?php echo $isDone ? 'com' : 'air'; ?>"></span>
                                <span class="event-name"><?php echo $eventName ?></span>
                                <span class="event-tags">
                                    <?php if ($hasImg): ?><span class="etag"><i data-lucide="image" title="有照片"></i></span><?php endif; ?>
                                    <?php if ($hasLocation): ?><span class="etag"><i data-lucide="map-pin" title="有定位"></i></span><?php endif; ?>
                                    <?php if ($hasNote): ?><span class="etag"><i data-lucide="notebook-pen" title="有备注"></i></span><?php endif; ?>
                                </span>
                            </div>
                            <div class="header-right">
                                <span class="toggle-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6" /></svg>
                                </span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="body-content">
                                <?php if ($isDone): ?>
                                <div class="achievement-watermark">
                                    <i class="ph-fill ph-seal-check"></i>
                                </div>
                                <?php else: ?>
                                <div class="achievement-watermark wm-pending">
                                    <i class="ph-fill ph-seal-check"></i>
                                </div>
                                <?php endif; ?>

                                <?php if ($photoCount > 0): ?>
                                <div class="body-gallery">
                                    <div class="img-counter">1 / <?php echo $photoCount ?></div>
                                    <div class="f-carousel ConventionPhoto" view-image>
                                        <?php foreach ($photos as $photoUrl): ?>
                                        <div class="f-carousel__slide">
                                            <img class="lazy" draggable="false"
                                                data-src="<?php echo htmlspecialchars(trim($photoUrl), ENT_QUOTES, 'UTF-8') ?>"
                                                data-original="<?php echo htmlspecialchars(trim($photoUrl), ENT_QUOTES, 'UTF-8') ?>" />
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="body-gallery">
                                    <div class="no-img-placeholder">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" />
                                        </svg>
                                        <span>暂无影像</span>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="body-info">
                                    <div class="body-full-title"><?php echo $eventName ?></div>
                                    <div class="info-item">
                                        <span class="info-label">完成时间 / TIME</span>
                                        <span class="info-value"><?php echo $isDone ? ($doneDate ?: '---') : '---' ?></span>
                                    </div>
                                    <div class="info-item">
                                        <span class="info-label">达成地点 / LOCATION</span>
                                        <?php if ($hasLocation && $hasCoords): ?>
                                        <span class="info-value lovelist-location-link has-coords"
                                            onclick="event.stopPropagation(); if(window.LGMap) LGMap.open({ mode:'events', coords:[<?php echo $lng ?>,<?php echo $lat ?>], zoom:15 });"
                                            data-tooltip="<?php echo $location ?>">
                                            <i class="ph-fill ph-map-pin"></i>
                                            <span><?php echo $location ?></span>
                                        </span>
                                        <?php elseif ($hasLocation): ?>
                                        <span class="info-value"><?php echo $location ?></span>
                                        <?php else: ?>
                                        <span class="info-value">---</span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($hasNote): ?>
                                    <div class="info-item remark-item">
                                        <span class="info-label">清单备注 / NOTE</span>
                                        <span class="info-value"><?php echo $note ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php $idx++; endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(function() {
        // 卡片折叠展开
        $(".card-body").hide();
        $(".card-header").on("click", function() {
            var $card = $(this).closest(".love-card");
            var $body = $card.find(".card-body");
            $body.slideToggle(400);
            $card.toggleClass("active");
            $(".love-card").not($card).removeClass("active").find(".card-body").slideUp(400);
        });

        // 标签页切换
        $(".LgLoveList-tab").on("click", function() {
            var tabId = $(this).data("id");
            $(".LgLoveList-tab").removeClass("LgLoveList-tab-active");
            $(this).addClass("LgLoveList-tab-active");

            var $slider = $(".LgLoveList-tab-slider");
            var $container = $(".LgLoveList-tab-container");
            var tabRect = this.getBoundingClientRect();
            var containerRect = $container[0].getBoundingClientRect();
            $slider.css({
                "width": tabRect.width + "px",
                "transform": "translateX(" + (tabRect.left - containerRect.left - 3) + "px)"
            });

            // 更新搜索范围胶囊
            var scopeText = tabId === 1 ? '已完成' : (tabId === 3 ? '未完成' : '全部');
            var scopeIcon = tabId === 1 ? 'fa-check-circle' : (tabId === 3 ? 'fa-clock' : 'fa-heart');
            $("#scopeTag").attr("data-scope", tabId === 1 ? "done" : (tabId === 3 ? "pending" : "all"));
            $("#scopeTag .scope-tag-text").text(scopeText);
            $("#scopeTag i").attr("class", "fa-solid " + scopeIcon);

            $(".love-card").each(function() {
                var $card = $(this);
                var isDone = $card.find(".status-dot").hasClass("com");
                if (tabId === 1) $card.toggle(isDone);
                else if (tabId === 3) $card.toggle(!isDone);
                else $card.show();
            });
            $(".love-card").removeClass("active").find(".card-body").slideUp(400);
        });

        // 搜索范围胶囊点击
        $("#scopeTag").on("click", function(e) {
            e.stopPropagation();
            var scope = $(this).data("scope");
            var tabId = scope === "done" ? 1 : (scope === "pending" ? 3 : 2);
            $(".LgLoveList-tab[data-id='" + tabId + "']").click();
        });

        // 搜索功能
        $("#search_btn").on("click", function() {
            var query = $("#search").val().toLowerCase().trim();
            $(".love-card").each(function() {
                var name = $(this).find(".event-name").text().toLowerCase();
                $(this).toggle(name.indexOf(query) > -1);
            });
        });
        $("#search").on("keyup", function(e) {
            if (e.key === "Enter") $("#search_btn").click();
            if ($(this).val() === "") $(".love-card").show();
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

        // 隐藏骨架屏
        setTimeout(function() {
            $("#lgListSkeleton").removeClass("is-active");
        }, 300);
    });
    </script>
</div>

<?php include_once 'footer.php'; ?>
