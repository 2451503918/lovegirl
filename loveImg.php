<?php
include_once 'head.php';

$resImg = null;
if ($connect) {
    $loveImg = "select * from loveImg order by id desc";
    $resImg = mysqli_query($connect, $loveImg);
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
        
        <div class="lgnewui-container">
            <div class="row central gallery" id="photoGallery">
                
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
    
    <?php
    include_once 'footer.php';
    ?>

</body>

</html>