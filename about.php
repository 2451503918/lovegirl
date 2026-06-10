<?php
$pageTitle = '关于';
include_once 'head.php';

// Resolve avatar URLs for use in template
$boyAvatarUrl = $text['boyimg'] ?? '';
$girlAvatarUrl = $text['girlimg'] ?? '';
if ($boyAvatarUrl && !preg_match('/^https?:\/\//', $boyAvatarUrl)) {
    $boyAvatarUrl = '/services/avatar-proxy.php?type=qq&qq=' . urlencode($boyAvatarUrl) . '&s=640';
}
if ($girlAvatarUrl && !preg_match('/^https?:\/\//', $girlAvatarUrl)) {
    $girlAvatarUrl = '/services/avatar-proxy.php?type=qq&qq=' . urlencode($girlAvatarUrl) . '&s=640';
}
$boyName = $text['boy'] ?? 'Ta';
$girlName = $text['girl'] ?? 'Ta';
?>

<div id="pjax-container">
    <!-- 聊天页专用样式 -->
    <link rel="stylesheet" href="/Style/css/lg-chat.css?LikeGirl=<?php echo $version ?>">

    <!-- chat-demo 完整结构 -->
    <div class="lgnewui-chat-wrapper" style="opacity:0;transition:opacity .3s ease;">
        <div class="lgnewui-chat-device">
            <!-- 顶部导航 -->
            <div class="lgnewui-chat-header">
                <div class="lgnewui-chat-header-info">
                    <img src="<?php echo htmlspecialchars($boyAvatarUrl, ENT_QUOTES, 'UTF-8') ?>" alt="Avatar" class="lgnewui-chat-avatar" id="headerAvatar">
                    <div class="lgnewui-chat-user-info">
                        <div class="lgnewui-chat-user-name" id="headerName">加载中...</div>
                        <div class="lgnewui-chat-user-status"><span class="lgnewui-chat-status-dot"></span> 回忆连接中...</div>
                    </div>
                </div>
                <div class="lgnewui-chat-header-icon" id="menuToggle" title="菜单">
                    <i class="ph ph-dots-three-circle"></i>
                </div>
            </div>

            <!-- 下拉菜单 -->
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

            <!-- 聊天流容器 -->
            <div class="lgnewui-chat-container" id="chatBox">
                <!-- 全屏 Loading -->
                <div class="loading-container" id="page-loading" style="display:flex;flex-direction:column;align-items:center;justify-content:center;height:100%;gap:12px;">
                    <div style="width:40px;height:40px;border:3px solid rgba(0,0,0,0.1);border-top-color:#333;border-radius:50%;animation:exportSpin 0.8s linear infinite;"></div>
                    <div style="color:#999;font-size:13px;">正在连接回忆...</div>
                </div>
            </div>

            <!-- 悬浮控制台 -->
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
    </div><!-- /.lgnewui-chat-wrapper -->

    <!-- 导出遮罩与面板 -->
    <div class="lgnewui-chat-overlay-backdrop" id="exportOverlay" style="display:none">
        <div class="lgnewui-chat-export-panel" id="exportPanel">
            <!-- 关闭按钮 -->
            <button class="lgnewui-chat-export-close-btn" id="exportCloseBtn" aria-label="关闭">
                <i class="ph ph-x"></i>
            </button>

            <!-- 状态 A：生成中（骨架屏） -->
            <div class="lgnewui-chat-panel-content lgnewui-chat-state-generating" id="stateGenerating">
                <div class="lgnewui-chat-skeleton-chat">
                    <div class="lgnewui-chat-sk-row sk-other">
                        <div class="lgnewui-chat-sk-bubble" style="width: 75%;"></div>
                    </div>
                    <div class="lgnewui-chat-sk-row sk-self">
                        <div class="lgnewui-chat-sk-bubble" style="width: 50%;"></div>
                    </div>
                    <div class="lgnewui-chat-sk-row sk-other">
                        <div class="lgnewui-chat-sk-bubble lgnewui-chat-sk-media" style="width: 85%;"></div>
                    </div>
                    <div class="lgnewui-chat-sk-row sk-self">
                        <div class="lgnewui-chat-sk-bubble" style="width: 40%;"></div>
                    </div>
                </div>
                <div class="lgnewui-chat-gen-title-wrapper">
                    <i class="ph ph-spinner-gap lgnewui-chat-icon-spin"></i>
                    <div class="lgnewui-chat-gen-title" id="genTitle">正在收集聊天片段...</div>
                </div>
                <div class="lgnewui-chat-gen-subtitle" id="genSubtitle">文字、图片与语音正在归档</div>
            </div>

            <!-- 状态 B：预览 -->
            <div class="lgnewui-chat-panel-content lgnewui-chat-state-preview lgnewui-chat-hidden-state" id="statePreview">
                <div class="lgnewui-chat-preview-header">
                    <h2>长图已准备就绪</h2>
                    <p>这段聊天记录已整理完成，可保存至相册</p>
                </div>
                <div class="lgnewui-chat-preview-thumbnail-wrapper">
                    <div class="lgnewui-chat-scroll-window">
                        <img class="lgnewui-chat-scrolling-img" id="exportPreviewImg" src="" alt="长图预览">
                    </div>
                    <div class="lgnewui-chat-preview-badge">
                        <i class="ph ph-image"></i>
                        <span>长图预览</span>
                    </div>
                </div>
                <div class="lgnewui-chat-actions-group">
                    <button class="lgnewui-chat-export-btn lgnewui-chat-export-btn-primary" id="exportDownloadBtn">
                        <i class="ph ph-download-simple"></i>
                        保存长图
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div id="poster-export-zone"></div>

    <!-- PHP 配置注入 -->
    <script>
        window.__CHAT_CONFIG = {
            loadEndpoint: "/services/chat-data.php"
        };
    </script>

    <script>
    // 安全回退：如果聊天模块5秒内未初始化，强制显示容器
    setTimeout(function() {
        var w = document.querySelector('.lgnewui-chat-wrapper');
        if (w) w.style.opacity = '1';
    }, 5000);
    </script>

</div><!-- /#pjax-container -->

<?php include_once 'footer.php'; ?>
