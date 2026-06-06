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
        'icp' => ''
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
?>

    <div id="pjax-container">
        <div class="lgnewui-page-header">
            <div class="lgnewui-meta-container">
                <div class="lgnewui-meta-line"></div>
                <div class="lgnewui-meta-tag">
                    <i class="ph-bold ph-camera lgnewui-meta-icon"></i>
                    Photo Gallery
                </div>
                <div class="lgnewui-meta-line"></div>
            </div>
            <h2 class="lgnewui-hero-title">记录下你的最美瞬间</h2>
        </div>

        <div class="lg-page-container">
            <div class="lg-masonry-grid" id="photoGallery">
            </div>

            <div class="loading lgnewui-loading-wrapper" id="loading">
                <div class="lgnewui-loading-spinner"></div>
                <span>加载中...</span>
            </div>

            <div class="load-more lgnewui-load-more">
                <button class="lg-btn-alt lgnewui-btn-primary" id="loadMoreBtn">
                    <svg class="icon" viewBox="0 0 1024 1024" width="20" height="20"><path d="M849.799529 168.357647A481.882353 481.882353 0 1 0 993.882353 512a90.352941 90.352941 0 0 0-180.705882 0 301.176471 301.176471 0 1 1-90.051765-214.799059 90.352941 90.352941 0 1 0 126.674823-128.843294z" fill="currentColor"></path></svg>
                    <span id="loadMoreText">加载更多</span>
                </button>
            </div>
        </div>
    </div>

    <script>
    var boyAvatar = <?php echo json_encode($boyimg_val); ?>;
    var girlAvatar = <?php echo json_encode($girlimg_val); ?>;
    var boyName = <?php echo json_encode($text['boy']); ?>;
    var girlName = <?php echo json_encode($text['girl']); ?>;

    var currentPage = 1;
    var limit = 6;
    var total = 0;

    function escapeHtml(str) {
        if (typeof str !== 'string') return '';
        return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function createPhotoCard(photo) {
        var author = photo.author || '';
        var isBoy = author === 'boy' || (author === '' && Math.random() > 0.5);
        var avatar = isBoy ? boyAvatar : girlAvatar;
        var name = isBoy ? boyName : girlName;
        var badgeClass = isBoy ? 'male' : 'female';
        var iconClass = isBoy ? 'ph-bold ph-gender-male' : 'ph-bold ph-gender-female';

        return '<div class="lg-masonry-col" data-aos="fade-up" data-aos-delay="0">' +
            '<div class="lg-card">' +
                '<div class="lg-header">' +
                    '<div class="lg-author show-gender">' +
                        '<div class="lg-author__ring">' +
                            '<img class="lg-author__avatar" src="' + avatar + '" alt="Avatar">' +
                            '<div class="lg-author__badge ' + badgeClass + '">' +
                                '<i class="' + iconClass + '"></i>' +
                            '</div>' +
                        '</div>' +
                        '<div class="lg-author__text">' +
                            '<span class="lg-author__name">' + escapeHtml(name) + '</span>' +
                            '<span class="lg-author__meta">' + escapeHtml(photo.date) + '</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="lg-content">' +
                    '<h3 class="lg-title">' + escapeHtml(photo.text) + '</h3>' +
                '</div>' +
                '<div class="lg-media grid-1" view-image>' +
                    '<div class="lg-photo-box">' +
                        '<img class="lg-photo lazy" data-src="' + escapeHtml(photo.img) + '" src="' + escapeHtml(photo.img) + '" alt="' + escapeHtml(photo.text) + '">' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    function showPhotos(photos) {
        var $gallery = $('#photoGallery');
        photos.forEach(function(photo) {
            $gallery.append(createPhotoCard(photo));
        });

        // Re-init lazy load for new images
        if (typeof FunLazy === 'function') {
            FunLazy({
                placeholder: "Style/img/Loading2.gif",
                effect: "show",
                strictLazyMode: false,
                useErrorImagePlaceholder: "Style/img/error.svg"
            });
        }

        // Re-init AOS for new elements
        if (typeof AOS !== 'undefined') {
            AOS.refresh();
        }
    }

    function loadPhotos() {
        var $loading = $('#loading');
        var $loadBtn = $('#loadMoreBtn');

        $loading.show();
        $loadBtn.prop('disabled', true);

        $.post('getPhotos.php', { page: currentPage, limit: limit }, function(res) {
            if (res.code === 200) {
                total = res.total;
                showPhotos(res.data);

                currentPage++;
                $loading.hide();

                if ($('#photoGallery .lg-masonry-col').length >= total) {
                    $loadBtn.html(
                        '<svg class="icon" viewBox="0 0 1024 1024" width="256" height="256"><path d="M866.944 256.768c-95.488-95.488-250.496-95.488-345.984 0l-13.312 13.312-9.472-9.472c-93.824-93.824-246.656-100.736-343.68-10.368-101.888 94.976-104.064 254.592-6.4 352.256l13.568 13.568 299.264 299.264c25.728 25.728 67.584 25.728 93.44 0l312.576-312.576c95.488-95.488 95.488-250.368 0-345.984zM335.36 352.64c-20.48 0-40.832 6.016-56.704 18.944a85.4912 85.4912 0 0 0-6.912 126.976c9.984 9.984 9.984 26.24 0 36.224l-3.2 3.2c-8.192 8.192-21.632 8.192-29.952 0-52.608-52.608-57.216-138.496-6.528-192.896 26.112-28.032 61.952-43.52 100.096-43.52 14.08 0 25.6 11.52 25.6 25.6v3.072c0 12.416-9.984 22.4-22.4 22.4z" fill="#333333"></path></svg> 暂无更多数据'
                    ).prop('disabled', true);
                } else {
                    $loadBtn.prop('disabled', false);
                }
            } else {
                $loading.hide();
                $loadBtn.prop('disabled', false);
            }
        }, 'json');
    }

    function initLoveAlbum() {
        var $gallery = $('#photoGallery');
        if ($gallery.length === 0) return;

        currentPage = 1;
        total = 0;
        $gallery.empty();
        $('#loadMoreBtn').html(
            '<svg class="icon" viewBox="0 0 1024 1024" width="256" height="256"><path d="M849.799529 168.357647A481.882353 481.882353 0 1 0 993.882353 512a90.352941 90.352941 0 0 0-180.705882 0 301.176471 301.176471 0 1 1-90.051765-214.799059 90.352941 90.352941 0 1 0 126.674823-128.843294z" fill="currentColor"></path></svg> 加载更多'
        ).prop('disabled', false);

        loadPhotos();
        $('#loadMoreBtn').off('click').on('click', loadPhotos);
    }
    </script>

    <?php
    include_once 'footer.php';
    ?>
</body>

</html>
