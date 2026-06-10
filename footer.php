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

<!-- 随机一言确认弹窗（about.php 风格） -->
<div class="lgmsg-confirm-overlay" id="lgmsgConfirmOverlay">
    <div class="lgmsg-confirm-panel">
        <button class="lgmsg-confirm-close-btn" id="lgmsgConfirmClose" aria-label="关闭">
            <i class="ph ph-x"></i>
        </button>
        <div class="lgmsg-confirm-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.582a.5.5 0 0 1 0 .962L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/><path d="M20 3v4"/><path d="M22 5h-4"/></svg>
        </div>
        <h2 class="lgmsg-confirm-title">替换为随机一言？</h2>
        <p class="lgmsg-confirm-desc">当前输入框已有内容，确认后将清空并替换为一条随机文案</p>
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
    window.LG_CONFIG.userCity = "中国 · 中国";
    window.LG_CONFIG.anonymousAvatar = "/assets/img/avatars/default.png";
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

<script>if(typeof lucide!=='undefined')lucide.createIcons();</script>

<!-- ============ 音乐播放器 (pjax 外，不随页面刷新) ============ -->

<div id="nav-music">
    <div id="nav-music-hoverTips" onclick="lg_love.musicToggle()">
        <svg viewBox="0 0 1024 1024" class="lgnewui-nav-music-play-icon" aria-hidden="true">
            <path d="M324.085 95.787l500.422 300.664c82.373 50.453 79.284 136.946-1.03 186.37v0l-506.6 304.784c-41.187 23.683-87.522 37.068-131.798 9.267-36.037-22.653-46.335-58.691-46.335-97.819v-616.774c0-39.127 13.386-75.166 48.395-97.819 45.305-27.801 94.731-14.416 136.946 11.327v0z" fill="#ffffff" />
        </svg>
    </div>

    <!-- 本地音乐模式 -->
    <meting-js api="/services/music-player-data.php" server="local" type="song" id="0"
        mutex="true" preload="none" data-lrctype="3"
        volume="1" order="list"
        loop="all" data-expand="true">
    </meting-js>

    <div id="nav-music-progress">
        <div class="lgnewui-nav-music-progress-loaded"></div>
        <div class="lgnewui-nav-music-progress-played"></div>
        <div class="lgnewui-nav-music-progress-thumb"></div>
        <div class="lgnewui-nav-music-progress-loading">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" stroke-dasharray="31.4" stroke-dashoffset="10" />
            </svg>
        </div>
    </div>

    <div class="lgnewui-nav-music-controls">
        <button class="lgnewui-nav-music-btn lgnewui-nav-music-btn-back" type="button" onclick="lg_love.musicSkipBack()" aria-label="上一首">
            <svg viewBox="0 0 24 24" class="lgnewui-nav-music-icon" aria-hidden="true"><path d="M6 5v14" /><path d="M18 5L10 12l8 7" /></svg>
        </button>
        <button class="lgnewui-nav-music-btn lgnewui-nav-music-btn-toggle" type="button" onclick="lg_love.musicToggle()" aria-label="播放或暂停">
            <svg viewBox="0 0 24 24" class="lgnewui-nav-music-icon lgnewui-nav-music-icon-play" aria-hidden="true"><path d="M8 5v14l9-7z" /></svg>
            <svg viewBox="0 0 24 24" class="lgnewui-nav-music-icon lgnewui-nav-music-icon-pause" aria-hidden="true"><path d="M9 6v12" /><path d="M15 6v12" /></svg>
        </button>
        <button class="lgnewui-nav-music-btn lgnewui-nav-music-btn-forward" type="button" onclick="lg_love.musicSkipForward()" aria-label="下一首">
            <svg viewBox="0 0 24 24" class="lgnewui-nav-music-icon" aria-hidden="true"><path d="M18 5v14" /><path d="M6 5l8 7-8 7" /></svg>
        </button>
    </div>
</div>

<!-- 音乐列表面板 -->
<div class="lgnewui-music-playlist-panel" id="musicPlaylist">
    <div class="lgnewui-music-playlist-header">
        <div class="lgnewui-music-playlist-title">
            <svg viewBox="0 0 24 24" width="16" height="16" xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.2" />
                <circle cx="12" cy="12" r="4" fill="currentColor" />
                <circle cx="12" cy="12" r="1" fill="rgba(255,255,255,0.8)" />
            </svg>
            <span>播放列表</span>
            <span class="lgnewui-music-playlist-count" id="playlistCount"></span>
        </div>
        <div class="lgnewui-music-playlist-header-right">
            <div class="lgnewui-music-playlist-mode" id="playlistMode" title="播放模式">
                <svg class="mode-icon mode-random" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5M4 20L21 3M21 16v5h-5M15 15l6 6M4 4l5 5" /></svg>
                <svg class="mode-icon mode-loop" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none"><path d="M17 2l4 4-4 4M3 11V9a4 4 0 014-4h14M7 22l-4-4 4-4M21 13v2a4 4 0 01-4 4H3" /></svg>
                <svg class="mode-icon mode-single" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none"><path d="M17 2l4 4-4 4" /><path d="M3 11V9a4 4 0 014-4h14" /><path d="M7 22l-4-4 4-4" /><path d="M21 13v2a4 4 0 01-4 4H3" /><path d="M12 10v4" /></svg>
            </div>
            <button class="lgnewui-music-playlist-close" id="playlistLocate" aria-label="定位当前" title="定位当前播放" style="margin-left:8px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
            </button>
            <button class="lgnewui-music-playlist-close" id="playlistReset" aria-label="重置列表" title="重置播放列表" style="margin-left:4px;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"></path><path d="M21 3v5h-5"></path><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"></path><path d="M8 16H3v5"></path></svg>
            </button>
        </div>
    </div>
    <div class="lgnewui-music-playlist-content" id="playlistContent">
        <div class="lgnewui-music-playlist-empty">
            <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z" /></svg>
            <div>暂无音乐</div>
        </div>
    </div>
    <div class="lgnewui-music-playlist-volume">
        <div class="lgnewui-music-volume-value" id="volumeValue">50%</div>
        <div class="lgnewui-music-volume-segment-track" id="volumeTrack"></div>
        <button class="lgnewui-music-volume-icon-wrap" id="volumeBtn">
            <svg class="lgnewui-music-volume-icon-svg" viewBox="0 0 24 24">
                <g class="lgnewui-music-volume-icon-path lgnewui-path-mute"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><line x1="23" y1="9" x2="17" y2="15"></line><line x1="17" y1="9" x2="23" y2="15"></line></g>
                <g class="lgnewui-music-volume-icon-path lgnewui-path-low"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon></g>
                <g class="lgnewui-music-volume-icon-path lgnewui-path-med"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path></g>
                <g class="lgnewui-music-volume-icon-path lgnewui-path-high"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"></polygon><path d="M15.54 8.46a5 5 0 0 1 0 7.07"></path><path d="M19.07 4.93a10 10 0 0 1 0 14.14"></path></g>
            </svg>
        </button>
    </div>
</div>

<!-- 音乐播放确认弹窗 -->
<div class="music-modal-overlay" id="musicModal">
    <div class="music-modal">
        <div class="music-modal-content">
            <div class="music-modal-wave-box">
                <div class="music-modal-wave-bar"></div>
                <div class="music-modal-wave-bar"></div>
                <div class="music-modal-wave-bar"></div>
                <div class="music-modal-wave-bar"></div>
                <div class="music-modal-wave-bar"></div>
            </div>
            <h3 class="music-modal-title">沉浸体验</h3>
            <p class="music-modal-desc">为了获得最佳沉浸式体验，建议您开启背景音乐。您随时可以在设置中关闭。</p>
            <div class="music-modal-hints">
                <div class="music-hint-item">
                    <div class="music-hint-icon-box">
                        <svg class="music-hint-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle><circle cx="5" cy="12" r="1"></circle><circle cx="19" cy="12" r="1"></circle><path d="M5 9l-3 3 3 3M9 5l3-3 3 3M19 9l3 3-3 3M9 19l3 3 3-3" /></svg>
                    </div>
                    <span class="music-hint-text">长按拖动</span>
                </div>
                <div class="music-hint-item">
                    <div class="music-hint-icon-box">
                        <svg class="music-hint-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18" /><path d="M16 17l-4 4-4-4" /><path d="M16 7l-4-4-4 4" /></svg>
                    </div>
                    <span class="music-hint-text">歌名伸缩</span>
                </div>
                <div class="music-hint-item">
                    <div class="music-hint-icon-box">
                        <svg class="music-hint-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><line x1="9" y1="6" x2="20" y2="6"></line><line x1="9" y1="12" x2="20" y2="12"></line><line x1="9" y1="18" x2="20" y2="18"></line><circle cx="5" cy="6" r="1"></circle><circle cx="5" cy="12" r="1"></circle><circle cx="5" cy="18" r="1"></circle></svg>
                    </div>
                    <span class="music-hint-text">封面列表</span>
                </div>
            </div>
            <div class="music-modal-dismiss-row">
                <button type="button" id="musicModalDismiss" class="music-modal-dismiss-btn">
                    <i class="ph ph-bell-slash" style="width:13px;height:13px;font-size:13px"></i>
                    <span>下次不再提醒</span>
                    <span class="music-modal-dismiss-tick">
                        <i class="ph-bold ph-check" style="width:10px;height:10px;font-size:10px"></i>
                    </span>
                </button>
            </div>
            <div class="music-modal-buttons">
                <button class="music-modal-btn music-modal-btn-cancel" id="musicModalCancel">暂不需要</button>
                <button class="music-modal-btn music-modal-btn-confirm" id="musicModalConfirm">
                    <svg viewBox="0 0 24 24" width="18" height="18" xmlns="http://www.w3.org/2000/svg"><path d="M7 4.5c0-1.2 1.3-1.9 2.3-1.2l9.5 6.5c.9.6.9 1.8 0 2.4l-9.5 6.5c-1 .7-2.3 0-2.3-1.2V4.5z" fill="currentColor" /></svg>
                    立即播放
                </button>
            </div>
        </div>
    </div>
</div>
<script>
// getMusicSetting / setMusicSetting 已在 head.php 中提前定义，此处作为 fallback
if (typeof getMusicSetting === 'undefined') {
    function getMusicSetting(key, defaultValue) {
        try {
            var raw = localStorage.getItem('lg_music_' + key);
            if (raw !== null) return raw === 'true' ? true : raw === 'false' ? false : raw;
            raw = sessionStorage.getItem('lg_music_' + key);
            if (raw !== null) return raw === 'true' ? true : raw === 'false' ? false : raw;
        } catch (e) {}
        return defaultValue;
    }
}
if (typeof setMusicSetting === 'undefined') {
    function setMusicSetting(key, value) {
        try { localStorage.setItem('lg_music_' + key, value); } catch (e) {}
    }
}

// 音乐播放确认弹窗逻辑
(function () {
    var modal = document.getElementById('musicModal');
    var confirmBtn = document.getElementById('musicModalConfirm');
    var cancelBtn = document.getElementById('musicModalCancel');
    var dismissBtn = document.getElementById('musicModalDismiss');
    if (!modal || !confirmBtn || !cancelBtn) return;

    // 如果用户之前开启了"不再提醒"，直接跳过
    if (getMusicSetting('modalDismissed', false)) return;

    var dismissed = false;
    if (dismissBtn) {
        dismissBtn.addEventListener('click', function () {
            dismissed = !dismissed;
            dismissBtn.classList.toggle('is-active', dismissed);
        });
    }

    function showModalWhenReady() {
        var meting = document.querySelector('#nav-music meting-js');
        var ap = meting && meting.aplayer ? meting.aplayer : null;
        if (!ap || !ap.list || !ap.list.audios || ap.list.audios.length === 0) {
            setTimeout(showModalWhenReady, 500);
            return;
        }
        setTimeout(function () { modal.classList.add('show'); }, 300);
    }
    showModalWhenReady();

    confirmBtn.addEventListener('click', function () {
        if (dismissed) setMusicSetting('modalDismissed', true);
        modal.classList.remove('show');
        setTimeout(function () {
            if (window.lg_love && typeof lg_love.musicToggle === 'function') {
                lg_love.musicToggle();
            }
        }, 300);
    });
    cancelBtn.addEventListener('click', function () {
        if (dismissed) setMusicSetting('modalDismissed', true);
        modal.classList.remove('show');
    });
})();
</script>
<!-- ============ /音乐播放器 ============ -->

<!-- 足迹地图弹窗 -->
<!-- ============ 足迹地图弹窗 ============ -->
<div class="lg-map-overlay" id="lgMapOverlay" style="display:none;">
    <div class="lg-map-modal">
        <div class="lg-map">
            <section id="missing-pets-module">
                <div class="missing-pets-wrap">
                    <div id="missing-pets-map"></div>

                    <div class="ui-footer-container" id="ui-footer">
                        <div class="ui-footer-left">
                            <div class="ui-footer-title" id="footer-title">情侣模式</div>
                            <div class="ui-footer-sub" id="footer-sub">
                                <span class="status-dot"></span>
                                <span id="footer-desc">无论相隔多远，心始终在一起</span>
                            </div>
                        </div>
                        <div class="ui-footer-right">
                            <div class="lgnewui-badge">
                                <div class="lgnewui-icon-circle">LG</div>
                                <div class="lgnewui-text-thin">LGNewUi</div>
                            </div>
                            <div class="ui-footer-copy">
                                Powered by <span class="footer-amap-logo">
                                    <svg t="1767096719086" class="icon" viewBox="0 0 1024 1024" version="1.1"
                                        xmlns="http://www.w3.org/2000/svg" p-id="1907" width="256" height="256">
                                        <path d="M658.285714 621.714286h365.714286v256a146.285714 146.285714 0 0 1-146.285714 146.285714h-219.428572V621.714286z" fill="#B2D8FF" p-id="1908"></path>
                                        <path d="M1024 364.397714V218.624H0v145.773714z" fill="#FFFFFF" p-id="1909"></path>
                                        <path d="M649.142857 1024h145.773714V0H649.142857z" fill="#FFFFFF" p-id="1910"></path>
                                        <path d="M1024 729.417143v-145.773714H0v145.773714z" fill="#FFCF68" p-id="1911"></path>
                                        <path d="M0 218.624h649.179429V0H146.285714a146.285714 146.285714 0 0 0-146.285714 146.285714v72.338286z" fill="#AFE881" p-id="1912"></path>
                                        <path d="M195.803429 1024H341.577143V0H195.803429z" fill="#FFCF68" p-id="1913"></path>
                                        <path d="M103.862857 543.670857L349.622857 618.057143l302.628572-256.950857-234.569143 276.772571 262.765714 81.188572 135.314286-520.192z" fill="#0093FD" p-id="1914"></path>
                                        <path d="M652.251429 361.142857L349.586286 618.057143l68.096 19.821714z" fill="#0066BD" p-id="1915"></path>
                                        <path d="M349.622857 618.093714v143.908572l97.938286-114.834286-97.974857-29.074286z" fill="#0064BB" p-id="1916"></path>
                                    </svg>
                                    高德地图</span><br>
                                © <?php echo date('Y') ?> Ki All Rights Reserved.
                            </div>
                        </div>
                    </div>

                    <div class="full-screen-function">
                        <button id="map-zoom" type="button" class="control-icon-button" aria-label="重置缩放">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="control-icon">
                                <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <button id="full-screen-button" type="button" class="control-icon-button" aria-label="全屏切换">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="control-icon">
                                <path d="m13.28 7.78 3.22-3.22v2.69a.75.75 0 0 0 1.5 0v-4.5a.75.75 0 0 0-.75-.75h-4.5a.75.75 0 0 0 0 1.5h2.69l-3.22 3.22a.75.75 0 0 0 1.06 1.06ZM2 17.25v-4.5a.75.75 0 0 1 1.5 0v2.69l3.22-3.22a.75.75 0 0 1 1.06 1.06L4.56 16.5h2.69a.75.75 0 0 1 0 1.5h-4.5a.747.747 0 0 1-.75-.75ZM12.22 13.28l3.22 3.22h-2.69a.75.75 0 0 0 0 1.5h4.5a.747.747 0 0 0 .75-.75v-4.5a.75.75 0 0 0-1.5 0v2.69l-3.22-3.22a.75.75 0 1 0-1.06 1.06ZM3.5 4.56l3.22 3.22a.75.75 0 0 0 1.06-1.06L4.56 3.5h2.69a.75.75 0 0 0 0-1.5h-4.5a.75.75 0 0 0-.75.75v4.5a.75.75 0 0 0 1.5 0V4.56Z" />
                            </svg>
                        </button>
                    </div>

                    <!-- 缩放倍数显示器 -->
                    <div class="zoom-indicator" id="zoom-indicator">
                        <span class="zoom-current" id="zoom-current">5</span>
                        <span class="zoom-range">/ 2-20</span>
                    </div>

                    <!-- 模式切换器 -->
                    <div class="mode-switcher" id="mode-switcher">
                        <button class="mode-btn active" data-mode="lovers" title="情侣模式">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" /></svg>
                        </button>
                        <button class="mode-btn" data-mode="moments" title="点点滴滴">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path></svg>
                        </button>
                        <button class="mode-btn" data-mode="messages" title="留言模式">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        </button>
                        <button class="mode-btn" data-mode="albums" title="相册模式">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        </button>
                        <button class="mode-btn" data-mode="events" title="事件清单">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"></path><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                        </button>
                    </div>
                </div>
            </section>

            <!-- 情侣信息面板 -->
            <div class="lovers-panel" id="lovers-panel">
                <div class="lover-card lover-left" id="lover-left">
                    <div class="avatar-box"><img src="" alt="我" id="lover-left-avatar" class="avatar-img"></div>
                    <div class="lover-info">
                        <div class="lover-name" id="lover-left-name">我</div>
                        <div class="lover-meta" id="lover-left-meta">
                            <i class="ri-loader-4-line" id="lover-left-weather-icon"></i>
                            <span id="lover-left-location">加载中...</span>
                        </div>
                    </div>
                </div>
                <div class="love-distance-center">
                    <i class="ri-map-pin-fill distance-icon" id="distance-icon"></i>
                    <div class="distance-val" id="love-distance-text">计算中...</div>
                </div>
                <div class="lover-card lover-right" id="lover-right">
                    <div class="lover-info">
                        <div class="lover-name" id="lover-right-name">TA</div>
                        <div class="lover-meta" id="lover-right-meta">
                            <i class="ri-loader-4-line" id="lover-right-weather-icon"></i>
                            <span id="lover-right-location">加载中...</span>
                        </div>
                    </div>
                    <div class="avatar-box"><img src="" alt="TA" id="lover-right-avatar" class="avatar-img"></div>
                </div>
            </div>

            <div class="love-distance-panel" id="love-distance-panel">
                <div class="panel-title">我们之间的距离</div>
                <div class="panel-body" id="love-distance-text-panel">加载中...</div>
            </div>
        </div>
    </div>
</div>
<!-- ============ /足迹地图弹窗 ============ -->
<div id="pjax-container">

<div id="lgnewuiFloatingActions">
    <a href="javascript:void(0)" id="scrollTopBtn" title="回到顶部">
        <i class="ph-fill ph-arrow-circle-up"></i>
    </a>
</div>

<script>
// 加密免验提示点击
(function () {
    var btn = document.getElementById('lgnewuiEncryptHint');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var label = this.getAttribute('data-encrypt-label') || '加密';
        var msg = '当前处于「' + label + '」保护中，因管理员已登录自动免验通过';
        if (typeof Toastify !== 'undefined' && Toastify.showScenario) {
            Toastify.showScenario('info', { text: msg });
        } else {
            alert(msg);
        }
    });
})();
</script>

<script>
    // 滚动按钮和回到顶部功能已迁移到 lg-components.js 的 ScrollButtons 模块
    // 以下代码由 LGApp.init() 统一初始化，保留最小必要代码

    $(document).ready(function() {
        $('body').addClass('loaded');

        // 初始化 LGApp 核心框架
        if (window.LGApp && typeof window.LGApp.init === 'function') {
            window.LGApp.setConfig(window.LG_CONFIG || {});
            window.LGApp.init();
        }

        // 初始化组件（礼花、轮播、导航等）
        if (window.LGApp && window.LGApp.Components) {
            const {
                ConfettiEffect,
                Carousel,
                AvatarInteraction,
                Navigation,
                ScrollButtons,
                HeaderVisitorWeather
            } = window.LGApp.Components;

            // 初始化礼花效果
            if (ConfettiEffect) {
                ConfettiEffect.init();
                // 页面加载完成后延迟触发如影随形效果
                setTimeout(() => {
                    ConfettiEffect.loveWingEffect();
                }, 800);
            }

            // 初始化轮播图
            if (Carousel) Carousel.init();

            // 初始化头像交互
            if (AvatarInteraction) AvatarInteraction.init();

            // 初始化导航栏
            if (Navigation) Navigation.init();

            // 初始化滚动按钮
            if (ScrollButtons) ScrollButtons.init();

            // 初始化吸顶栏访客天气
            if (HeaderVisitorWeather) HeaderVisitorWeather.init();
        }

        // GetEm 函数调用（如果存在）
        if (typeof GetEm === 'function') GetEm();
    });
</script>

    <script>
    $(function () {
        initLoveAlbum();

        $('video').each(function() {
            var video = $(this);
            setupVideoPlayer(video);
        });

        $(".love_img img,.lovelist img,.little_texts img").addClass("spotlight").each(function () {
            this.onclick = function () {
                return hs.expand(this)
            }
        });

        if (typeof FunLazy === 'function') {
            FunLazy({
                placeholder: "Style/img/Loading2.gif",
                effect: "show",
                strictLazyMode: false,
                useErrorImagePlaceholder: "Style/img/error.svg"
            });
        }
    })
    </script>
    <style>
        .NotAbout {
            display: none;
        }

        .about_y {
            font-size: 2rem;
            background: #ffffff;
            padding: 0.8rem;
            margin-left: 1rem;
            border-radius: 1rem;
            color: #03A9F4;
            position: fixed;
            right: 1rem;
            bottom: 7.5rem;
            z-index: 100;
            box-shadow: 0 3px 10px #bdb7b78c;
            border: 1px solid #fff;
            transition: 0.1s all;
        }

        .about_y:hover {
            background: #03A9F4;
            color: #ffffff;
        }

        .icon {
            width: 1.5em;
            height: 1.5em;
            vertical-align: -0.3em;
            fill: currentColor;
            overflow: hidden;
        }

        li.cike {
            border-bottom: 1px solid #ddd;
        }

        li {
            list-style-type: none;
        }

        .cike:hover {
            cursor: pointer;
            cursor: url(/Style/cur/hover.cur), pointer;
        }

        button:disabled {
            background: #888;
            opacity: 0.6;
        }

        .avatar {
            width: 2.5em;
            height: 2.5em;
            border-radius: 50%;
            box-shadow: 0 2px 8px #a9a9a98c;
            border: 2px solid #fff;
            margin-right: 0.8rem;
        }

        .footer-warp {
            background: #ffffff;
            margin-top: 0;
            border-top: 1px solid #efefef;
            padding: 2rem 0;
        }

        .footer-warp .footer {
            padding-bottom: 0;
        }

        .footer-warp .footer p {
            line-height: 1.2rem;
            margin: 0.5rem auto 0;
        }

        .github-badge {
            display: inline-block;
            border-radius: 4px;
            text-shadow: none;
            font-size: 12px;
            color: #fff;
            line-height: 15px;
            background-color: #5d5d5d;
            margin-bottom: 5px;
            white-space: nowrap;
        }

        .footer .github-badge .badge-subject img {
            width: 12px;
            vertical-align: bottom;
            margin: 0 .3rem;
        }

        .github-badge:hover {
            color: #fafafa;
        }

        .github-badge .badge-subject {
            display: inline-block;
            background-color: #4d4d4d;
            padding: 4px 4px 4px 6px;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }

        .github-badge .badge-value {
            display: inline-block;
            padding: 4px 6px 4px 4px;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
        }

        .github-badge .bg-pink {
            background-image: linear-gradient(to right, #747474 0%, #ff7eb3 100%);
        }

        .github-badge .bg-DIY {
            background-image: linear-gradient(to right, #747474 0%, #ff7575 100%);
        }

        .github-badge .bg-DIY1 {
            background-color: #7f7f7f;
        }

        .github-badge .bg-blue {
            background-image: linear-gradient(120deg, #747474 0%, #66a6ff 100%);
        }

        #footer-animal {
            position: relative;
            user-select: none;
        }

        #footer-animal:before {
            content: '';
            position: absolute;
            bottom: 0;
            width: 100%;
            height: 36px;
            background: url(/Style/img/animalBg.jpg) repeat center / auto 100%;
            box-shadow: 0 4px 7px rgba(0, 0, 0, .15);
        }

        .animal {
            position: relative;
            max-width: min(974px, 100vw);
            margin: 0 auto;
            display: block;
        }

        @media (max-width: 768px) {
            .animal {
                bottom: 15px;
            }
        }
    </style>

</div>

<div id="footer-animal">
    <img class="animal" src="/Style/img/animals.png" draggable="false" alt="动物">
</div>

    <div class="div_marb_7rem-none">
    <div class="footer-warp">
        <div class="footer">
            <?php if ($icp): ?>
                <p><img src="/Style/img/icp.svg"><a href="https://beian.miit.gov.cn/#/Integrated/index" target="_blank"><?php echo $icp ?></a></p>
            <?php endif; ?>
            <?php if ($copy): ?>
                <p><?php echo $copy ?></p>
            <?php else: ?>
                <p><a href="javascript:void(0);" class="github-badge"><span class="badge-subject">Copyright</span><span class="badge-value bg-DIY1">&copy; <?php echo date('Y') ?> LG_NewUi Web All Rights Reserved.</span></a></p>
            <?php endif; ?>
        </div>
    </div>
    </div>

<div class="lgnewui-mobile-nav-root">
    <div class="lgnewui-tab-template-v5-container lgnewui-glass-panel" id="lgnewui-mobile-nav-v5">
        <div class="lgnewui-tab-template-v5-indicator"></div>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item" href="articles.php" data-page="articles">
            <i class="ph-fill ph-notebook"></i>
            <span>点滴</span>
        </a>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item" href="messages.php" data-page="messages">
            <i class="ph-fill ph-chat-teardrop-dots"></i>
            <span>留言</span>
        </a>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item" href="timeline.php" data-page="timeline">
            <i class="ph-fill ph-clock-countdown"></i>
            <span>轨迹</span>
        </a>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item active" href="index.php" data-page="index">
            <i class="ph-fill ph-house"></i>
            <span>首页</span>
        </a>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item" href="albums.php" data-page="albums">
            <i class="ph-fill ph-camera"></i>
            <span>相册</span>
        </a>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item" href="lovelist.php" data-page="lovelist">
            <i class="ph-fill ph-list-checks"></i>
            <span>清单</span>
        </a>
        <a class="lgnewui-base-nav-item js-lgnewui-v5-item" href="about.php" data-page="about">
            <i class="ph-fill ph-book-open-text"></i>
            <span>关于</span>
        </a>
    </div>
</div>

<script>
(function() {
    'use strict';
    var mobileNav = document.getElementById('lgnewui-mobile-nav-v5');
    var mobileIndicator = mobileNav ? mobileNav.querySelector('.lgnewui-tab-template-v5-indicator') : null;
    var mobileItems = document.querySelectorAll('.js-lgnewui-v5-item');
    if (!mobileNav || !mobileIndicator || !mobileItems.length) return;

    function setMobileActiveByPath() {
        var currentPath = window.location.pathname;
        var currentPage = currentPath.split('/').pop() || 'index.php';
        mobileItems.forEach(function(item) {
            item.classList.remove('active');
            var href = item.getAttribute('href');
            if (href === currentPage || (href === 'index.php' && (currentPath === '/' || currentPath.endsWith('/') || currentPath.endsWith('index.php')))) {
                item.classList.add('active');
            }
        });
        if (!document.querySelector('.js-lgnewui-v5-item.active')) {
            var homeItem = document.querySelector('.js-lgnewui-v5-item[href="index.php"]');
            if (homeItem) homeItem.classList.add('active');
        }
    }

    function updateMobileIndicator() {
        var activeItem = document.querySelector('.js-lgnewui-v5-item.active');
        if (activeItem && mobileIndicator) {
            var itemRect = activeItem.getBoundingClientRect();
            var navRect = mobileNav.getBoundingClientRect();
            mobileIndicator.style.left = (itemRect.left - navRect.left) + 'px';
        }
    }

    mobileItems.forEach(function(item) {
        item.addEventListener('click', function() {
            mobileItems.forEach(function(i) { i.classList.remove('active'); });
            this.classList.add('active');
            updateMobileIndicator();
        });
    });

    var resizeTimeout;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(updateMobileIndicator, 200);
    });

    setMobileActiveByPath();
    updateMobileIndicator();
    setTimeout(updateMobileIndicator, 100);
})();
</script>

<?php echo htmlspecialchars_decode($diy['footerCon'] ?? '', ENT_QUOTES) ?>

<script>
    window.LG_CONFIG = Object.assign(window.LG_CONFIG || {}, {
        endpoints: Object.assign({}, (window.LG_CONFIG && window.LG_CONFIG.endpoints) || {}, {
            accessBeacon: "/services/access-beacon.php"
        })
    });
</script>

<script>
(function(){
    var key='lg_stay_ts',dKey='lg_stay_dur';
    var ts=Date.now();
    try{sessionStorage.setItem(key,String(ts));}catch(e){}
    function sendBeacon(){
        var now=Date.now(),start=0;
        try{start=parseInt(sessionStorage.getItem(key),10)||0;}catch(e){}
        var dur=now-(start||now);
        if(dur<1000)return;
        try{localStorage.setItem(dKey,String(dur));}catch(e){}
        if(navigator.sendBeacon){
            navigator.sendBeacon('/services/visitor-stats.php','duration='+dur);
        }
    }
    window.addEventListener('beforeunload',sendBeacon);
    window.addEventListener('pagehide',sendBeacon);
})();
</script>

</body>
</html>