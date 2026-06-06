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

</div><!-- /#pjax-container -->

<!-- 留言弹窗遮罩层 -->
<div class="mask" id="mask">
    <div class="close">
        <svg t="1682818912164" class="icon" viewBox="0 0 1024 1024" version="1.1"
            xmlns="http://www.w3.org/2000/svg" p-id="2416" width="200" height="200">
            <path
                d="M550.848 502.496l308.64-308.896a31.968 31.968 0 1 0-45.248-45.248l-308.608 308.896-308.64-308.928a31.968 31.968 0 1 0-45.248 45.248l308.64 308.896-308.64 308.896a31.968 31.968 0 1 0 45.248 45.248l308.64-308.896 308.608 308.896a31.968 31.968 0 1 0 45.248-45.248l-308.64-308.864z"
                p-id="2417"></path>
        </svg>
    </div>
</div>

<!-- 表情面板（全局可用，弹窗 & 抽屉共用） -->
<div class="lgnewui-message-emoji-panel" id="lgmsgEmojiPanel">
    <div class="lgnewui-message-emoji-tabs-wrap">
        <div class="lgnewui-message-emoji-tabs" id="lgmsgEmojiTabs"></div>
    </div>
    <div class="lgnewui-message-emoji-cat-title" id="lgmsgEmojiCatTitle"></div>
    <div class="lgnewui-message-emoji-list" id="lgmsgEmojiGrid"></div>
</div>

<div class="lgnewui-message-emoji-preview" id="lgmsgEmojiPreview">
    <img src="" id="lgmsgPreviewImg">
    <span id="lgmsgPreviewText"></span>
</div>

<!-- 留言触发按钮 -->
<div class="message_btn" id="mes">
    <span class="mesly shadow-blur">
        <i data-lucide="message-circle" style="width:2rem;height:2rem;fill:currentColor;stroke:none;"></i>
    </span>
</div>

<!-- 随机一言确认弹窗 -->
<div class="lgmsg-confirm-overlay" id="lgmsgConfirmOverlay" role="alertdialog" aria-modal="true" aria-labelledby="lgmsgConfirmTitle" aria-describedby="lgmsgConfirmDesc" aria-hidden="true">
    <div class="lgmsg-confirm-panel">
        <button class="lgmsg-confirm-close-btn" id="lgmsgConfirmClose" aria-label="关闭">
            <i class="ph ph-x" aria-hidden="true"></i>
        </button>
        <div class="lgmsg-confirm-icon-wrapper" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.582a.5.5 0 0 1 0 .962L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/><path d="M20 3v4"/><path d="M22 5h-4"/></svg>
        </div>
        <h2 class="lgmsg-confirm-title" id="lgmsgConfirmTitle">替换为随机一言？</h2>
        <p class="lgmsg-confirm-desc" id="lgmsgConfirmDesc">当前输入框已有内容，确认后将清空并替换为一条随机文案</p>
        <div class="lgmsg-confirm-actions">
            <button class="lgmsg-confirm-btn lgmsg-confirm-btn-secondary" id="lgmsgConfirmCancel">取消</button>
            <button class="lgmsg-confirm-btn lgmsg-confirm-btn-primary" id="lgmsgConfirmOk">确认替换</button>
        </div>
    </div>
</div>

<!-- 留言弹窗（全局可用） -->
<div class="lgnewui-message-modal-overlay" id="lgmsgCommentModal">
    <div class="lgnewui-message-modal-content" id="lgmsgModalContent">
        <div class="lgnewui-message-close-wrapper">
            <button class="lgnewui-message-close-btn" id="lgmsgModalCloseBtn">
                <i data-lucide="x" style="width:20px;height:20px;"></i>
            </button>
        </div>
        <div class="lgnewui-message-modal-body">
            <div class="lgnewui-message-head-titles">
                <div class="lgnewui-message-title">写一条留言</div>
                <div class="lgnewui-message-subtitle">在这里，留下属于你的印记</div>
            </div>
            <div class="lgnewui-message-ios-tabs-wrap">
                <div class="lgnewui-message-ios-tabs" id="lgmsgTabContainer">
                    <div class="lgnewui-message-ios-tab-slider" id="lgmsgTabSlider"></div>
                    <div class="lgnewui-message-ios-tab active" data-mode="qq">QQ留言</div>
                    <div class="lgnewui-message-ios-tab" data-mode="anonymous">匿名留言</div>
                </div>
            </div>
            <div class="lgnewui-message-visitor-tags" id="lgmsgVisitorTags">
                <div class="lgnewui-message-v-tag">
                    <div class="lgnewui-message-v-tag-icon lgnewui-message-icon-os">
                        <i data-lucide="monitor"></i>
                    </div>
                    <span id="lgmsgTagOS">--</span>
                </div>
                <div class="lgnewui-message-v-tag">
                    <div class="lgnewui-message-v-tag-icon lgnewui-message-icon-browser">
                        <i data-lucide="globe"></i>
                    </div>
                    <span id="lgmsgTagBrowser">--</span>
                </div>
                <div class="lgnewui-message-v-tag">
                    <div class="lgnewui-message-v-tag-icon lgnewui-message-icon-location">
                        <i data-lucide="map-pin"></i>
                    </div>
                    <span id="lgmsgTagLocation">--</span>
                </div>
                <div class="lgnewui-message-v-tag">
                    <div class="lgnewui-message-v-tag-icon lgnewui-message-icon-weather">
                        <i class="qi-100-fill" id="lgmsgWeatherIcon"></i>
                    </div>
                    <span id="lgmsgTagWeather">--</span>
                </div>
            </div>
            <div class="lgnewui-message-input-row" id="lgmsgInputRow"></div>
            <div class="lgnewui-message-privacy-hint" id="lgmsgPrivacyHint"><i data-lucide="lock"></i>QQ 信息经过加密脱敏处理，不会公开展示，请放心留言</div>
            <div class="lgnewui-message-editor-wrap">
                <div class="lgnewui-message-editor-content" id="lgmsgEditor" contenteditable="true" data-placeholder="想说点什么..."></div>
                <div class="lgnewui-message-emoji-bubbles" id="lgmsgEmojiBubbles"></div>
                <div class="lgnewui-message-editor-toolbar">
                    <div class="lgnewui-message-tb-left">
                        <button class="lgnewui-message-tb-btn" id="lgmsgBtnEmoji" title="表情">
                            <i data-lucide="smile"></i>
                        </button>
                        <button class="lgnewui-message-tb-btn" id="lgmsgBtnQuote" title="随机一言">
                            <i data-lucide="sparkles"></i>
                        </button>
                        <div class="lgnewui-message-switch-wrap" id="lgmsgEnterToSendWrap">
                            <div class="lgnewui-message-switch"></div>
                            <span class="lgnewui-message-switch-text">Enter 发送</span>
                        </div>
                    </div>
                    <span class="lgnewui-message-char-counter" id="lgmsgCharCounter">0/500</span>
                    <button class="lgnewui-message-submit-btn" id="lgmsgSubmitBtn">
                        <span class="lgnewui-message-submit-label">发送留言</span>
                        <i data-lucide="send" class="lgnewui-message-submit-icon" style="width:18px;height:18px;"></i>
                        <i data-lucide="loader" class="lgnewui-message-submit-loader lgnewui-message-lucide-loader" style="width:18px;height:18px;"></i>
                        <i data-lucide="check" class="lgnewui-message-submit-check" style="width:18px;height:18px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 留言弹窗配置输出 -->
<script>
    window.LG_CONFIG = window.LG_CONFIG || {};
    window.LG_CONFIG.userCity = "<?php echo htmlspecialchars($text['userCity'] ?? '未知', ENT_QUOTES, 'UTF-8') ?>";
    window.LG_CONFIG.anonymousAvatar = "";
</script>

<!-- 极验验证与留言提交绑定 -->
<script>
$(function() {
    if (typeof GeetestHelper !== 'undefined') {
        var siteTitle = (window.LG_CONFIG && window.LG_CONFIG.title) || '';

        GeetestHelper.init({
            toast: {
                success: function(msg) { Toastify.showScenario('success', { text: msg }); },
                error: function(msg) { Toastify.showScenario('error', { text: msg }); },
                warning: function(msg) { Toastify.showScenario('warning', { text: msg }); }
            },
            onClose: function() {
                $('#leavingPost').removeAttr('disabled').text('提交留言');
            },
            onSuccess: function(result) {
                if (typeof submitMessage === 'function') {
                    submitMessage(result);
                }
            }
        });

        $('#leavingPost').off('click.lgGeetest').on('click.lgGeetest', function() {
            var qq = $("input[name='qq']").val();
            var name = $("input[name='name']").val();
            var text = $("textarea[name='text']").val();

            if (!qq || !name || !text) {
                Toastify.showScenario('warning', { text: '留言提交失败 表单输入不完整！' });
                return false;
            }

            if (typeof containsBannedChar === 'function' && containsBannedChar((name || '') + ' ' + (text || ''))) {
                Toastify.showScenario('warning', { text: '留言包含违禁内容，请修改后重试' });
                return false;
            }

            $('#leavingPost').text('请完成验证...').attr('disabled', 'disabled');
            GeetestHelper.show();
        });
    } else {
        $('#leavingPost').off('click.lgGeetest').on('click.lgGeetest', function() {
            var qq = $("input[name='qq']").val();
            var name = $("input[name='name']").val();
            var text = $("textarea[name='text']").val();

            if (!qq || !name || !text) {
                Toastify.showScenario('warning', { text: '留言提交失败 表单输入不完整！' });
                return false;
            }

            if (typeof containsBannedChar === 'function' && containsBannedChar((name || '') + ' ' + (text || ''))) {
                Toastify.showScenario('warning', { text: '留言包含违禁内容，请修改后重试' });
                return false;
            }

            if (typeof submitMessage === 'function') {
                submitMessage({});
            }
        });
    }
});
</script>

<?php include_once 'footer.php'; ?>
