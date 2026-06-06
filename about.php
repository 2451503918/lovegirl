<?php
include_once 'head.php';

// Resolve avatar URLs for use in template
$boyAvatarUrl = $text['boyimg'] ?? '';
$girlAvatarUrl = $text['girlimg'] ?? '';
if ($boyAvatarUrl && !preg_match('/^https?:\/\//', $boyAvatarUrl)) {
    $boyAvatarUrl = 'https://q1.qlogo.cn/g?b=qq&nk=' . $boyAvatarUrl . '&s=640';
}
if ($girlAvatarUrl && !preg_match('/^https?:\/\//', $girlAvatarUrl)) {
    $girlAvatarUrl = 'https://q1.qlogo.cn/g?b=qq&nk=' . $girlAvatarUrl . '&s=640';
}
$boyName = $text['boy'] ?? 'Ta';
$girlName = $text['girl'] ?? 'Ta';
?>

<div id="pjax-container">
    <!-- chat replay page style -->
    <link rel="stylesheet" href="/Style/css/lg-chat.css?LikeGirl=<?php echo $version ?>">

    <!-- page header -->
    <div class="lgnewui-page-header">
        <div class="lgnewui-meta-container">
            <div class="lgnewui-meta-line"></div>
            <div class="lgnewui-meta-tag">
                <i class="ph-bold ph-book-open-text lgnewui-meta-icon"></i>
                Story Replay
            </div>
            <div class="lgnewui-meta-line"></div>
        </div>
        <h2 class="lgnewui-hero-title">用对话回放我们的故事</h2>
    </div>

    <!-- chat device frame -->
    <div class="lgnewui-chat-wrapper" style="opacity:0;transition:opacity .3s ease;">
        <div class="lgnewui-chat-device">
            <!-- header bar -->
            <div class="lgnewui-chat-header">
                <div class="lgnewui-chat-header-info">
                    <img src="<?php echo htmlspecialchars($boyAvatarUrl) ?>" alt="Avatar" class="lgnewui-chat-avatar" id="headerAvatar">
                    <div class="lgnewui-chat-user-info">
                        <div class="lgnewui-chat-user-name" id="headerName"><?php echo htmlspecialchars($boyName . ' & ' . $girlName) ?></div>
                        <div class="lgnewui-chat-user-status"><span class="lgnewui-chat-status-dot"></span> 回忆连接中...</div>
                    </div>
                </div>
                <div class="lgnewui-chat-header-icon" id="menuToggle" title="菜单">
                    <i class="ph ph-dots-three-circle"></i>
                </div>
            </div>

            <!-- dropdown menu -->
            <div class="lgnewui-chat-dropdown-menu" id="dropdownMenu">
                <div class="lgnewui-chat-menu-item" onclick="switchTheme('theme1'); event.stopPropagation();">
                    <i class="ph ph-sparkle"></i> 极简质感风
                </div>
                <div class="lgnewui-chat-menu-item" onclick="switchTheme('theme2'); event.stopPropagation();">
                    <i class="ph ph-leaf"></i> 清新拟物风
                </div>
                <div class="lgnewui-chat-menu-divider"></div>
                <div class="lgnewui-chat-menu-item" onclick="toggleMute(); event.stopPropagation();">
                    <i class="ph ph-speaker-slash" id="menuMuteIcon"></i> <span id="menuMuteText">关闭提示音</span>
                </div>
                <div class="lgnewui-chat-menu-item" onclick="exportChat(); event.stopPropagation();">
                    <i class="ph ph-download-simple"></i> 导出回忆长图
                </div>
            </div>

            <!-- chat container -->
            <div class="lgnewui-chat-container" id="chatBox">
                <!-- loading state -->
                <div class="loading-container" id="page-loading" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:12px;">
                    <div style="width:40px;height:40px;border:3px solid rgba(0,0,0,0.1);border-top-color:#333;border-radius:50%;animation:exportSpin 0.8s linear infinite;"></div>
                    <div style="color:#999;font-size:13px;">正在连接回忆...</div>
                </div>
            </div>

            <!-- floating controls -->
            <div class="lgnewui-chat-controls-wrapper">
                <div class="lgnewui-chat-controls">
                    <div class="lgnewui-chat-ctrl-btn" title="重新开始" onclick="resetDemo()">
                        <i class="ph ph-arrow-u-up-left"></i>
                    </div>
                    <div class="lgnewui-chat-ctrl-btn" title="切换视角" id="perspectiveBtn">
                        <i class="ph ph-arrows-left-right"></i>
                    </div>
                    <div class="lgnewui-chat-ctrl-btn lgnewui-chat-play-btn" id="playBtn" title="暂停/继续">
                        <i class="ph-fill ph-pause"></i>
                    </div>
                    <div class="lgnewui-chat-ctrl-btn" id="speedBtn" title="切换倍速">
                        <span class="lgnewui-chat-speed-btn-text">1.0x</span>
                    </div>
                    <div class="lgnewui-chat-ctrl-btn" title="跳过动画" id="skipBtn">
                        <i class="ph ph-skip-forward"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- chat config for JS -->
    <script>
        window.__CHAT_CONFIG = {
            loadEndpoint: "/services/chat-data.php"
        };
    </script>
    <script src="/assets/js/lg-chat.js?LikeGirl=<?php echo $version ?>"></script>

    <!-- fade-in animation -->
    <script>
    (function() {
        var wrapper = document.querySelector('.lgnewui-chat-wrapper');
        if (wrapper) {
            setTimeout(function() { wrapper.style.opacity = '1'; }, 100);
        }
    })();
    </script>
</div>

<?php include_once 'footer.php'; ?>
</body>
</html>
