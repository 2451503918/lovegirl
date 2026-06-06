<?php
include_once 'head.php';

if (!isset($text) || !is_array($text)) {
    $text = [
        'boy' => '男方', 'girl' => '女方', 'boyimg' => '', 'girlimg' => '',
        'startTime' => date('Y-m-d H:i:s', time() - 365 * 86400), 'logo' => '我们的故事',
        'writing' => '记录美好时光', 'Copyright' => '', 'icp' => '',
        'Animation' => '', 'title' => '情侣小站'
    ];
}

// 获取作者头像（优先使用上传的URL，否则使用QQ头像）
$boyimg = $text['boyimg'] ?? '';
$girlimg = $text['girlimg'] ?? '';
if ($boyimg && !preg_match('/^https?:\/\//', $boyimg)) {
    $boyimg = 'https://q1.qlogo.cn/g?b=qq&nk=' . $boyimg . '&s=640';
}
if ($girlimg && !preg_match('/^https?:\/\//', $girlimg)) {
    $girlimg = 'https://q1.qlogo.cn/g?b=qq&nk=' . $girlimg . '&s=640';
}
?>
<title><?php echo htmlspecialchars($text['title'] ?? '情侣小站', ENT_QUOTES, 'UTF-8') ?> — 恋爱轨迹</title>

<div id="pjax-container">
    <!-- 时间轴专用样式 -->
    <link rel="stylesheet" href="/Style/css/timeline.css">
    <!-- 迷你地图组件（地点卡片） -->
    <link rel="stylesheet" href="/assets/css/lg-mini-map.css">
    <script src="/assets/js/lg-mini-map.js"></script>

    <!-- 滚动提示 -->
    <div class="lgnewui-scroll-hint" id="timelineScrollHint">
        <div class="lgnewui-scroll-hint-inner">
            <!-- PC 端：鼠标图标 -->
            <div class="lgnewui-scroll-mouse">
                <div class="lgnewui-scroll-wheel"></div>
            </div>
            <!-- 移动端：手指滑动图标 -->
            <div class="lgnewui-scroll-touch">
                <div class="lgnewui-scroll-touch-hand">
                    <i class="ph-fill ph-hand-tap"></i>
                </div>
                <div class="lgnewui-scroll-touch-trail"></div>
            </div>
            <span class="lgnewui-scroll-text lgnewui-scroll-text-pc">向下滚动查看时间轴</span>
            <span class="lgnewui-scroll-text lgnewui-scroll-text-mobile">向上滑动查看时间轴</span>
        </div>
    </div>

    <style>
        /* 修复 pjax-container 内 sticky 失效问题 */
        #pjax-container {
            overflow: visible !important;
        }
        #pjax-container .lgnewui-time-line-main {
            overflow: visible !important;
        }

        /* 滚动提示样式 */
        .lgnewui-scroll-hint {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px 0 50px;
        }

        .lgnewui-scroll-hint-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        /* 鼠标图标 - PC 端 */
        .lgnewui-scroll-mouse {
            width: 26px;
            height: 42px;
            border: 2px solid rgba(0, 0, 0, 0.2);
            border-radius: 15px;
            position: relative;
            display: flex;
            justify-content: center;
        }

        .lgnewui-scroll-wheel {
            width: 4px;
            height: 8px;
            background: rgba(0, 0, 0, 0.3);
            border-radius: 2px;
            position: absolute;
            top: 8px;
            animation: scrollWheel 1.8s ease-in-out infinite;
        }

        @keyframes scrollWheel {
            0% { opacity: 1; transform: translateY(0); }
            50% { opacity: 0.5; transform: translateY(10px); }
            100% { opacity: 0; transform: translateY(16px); }
        }

        /* 手指滑动图标 - 移动端 */
        .lgnewui-scroll-touch {
            display: none;
            flex-direction: column;
            align-items: center;
            position: relative;
            height: 60px;
        }

        .lgnewui-scroll-touch-hand {
            font-size: 28px;
            color: rgba(0, 0, 0, 0.35);
            animation: touchSwipe 1.8s ease-in-out infinite;
        }

        .lgnewui-scroll-touch-trail {
            position: absolute;
            bottom: 0;
            width: 2px;
            height: 20px;
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.15), transparent);
            border-radius: 1px;
            animation: touchTrail 1.8s ease-in-out infinite;
        }

        @keyframes touchSwipe {
            0%, 100% { transform: translateY(20px); opacity: 0.3; }
            50% { transform: translateY(-5px); opacity: 1; }
        }

        @keyframes touchTrail {
            0%, 100% { opacity: 0; height: 0; }
            30%, 70% { opacity: 0.5; height: 20px; }
        }

        .lgnewui-scroll-text {
            font-size: 13px;
            color: rgba(0, 0, 0, 0.4);
            font-family: 'Noto Serif SC', serif;
            letter-spacing: 0.1em;
        }

        .lgnewui-scroll-text-mobile {
            display: none;
        }

        @media (max-width: 768px) {
            .lgnewui-scroll-hint { padding: 20px 0 40px; }
            .lgnewui-scroll-mouse { display: none; }
            .lgnewui-scroll-touch { display: flex; }
            .lgnewui-scroll-text-pc { display: none; }
            .lgnewui-scroll-text-mobile { display: block; }
            .lgnewui-scroll-text { font-size: 12px; }
        }
    </style>

    <main class="lgnewui-time-line-main">
        <div id="timeline-container" class="lgnewui-time-line-container"></div>
    </main>

    <!-- 双主角配置 -->
    <script>
        window.TIMELINE_AUTHORS = {};
        window.TIMELINE_AUTHORS[1] = {
            name: <?php echo json_encode($text['boy'], JSON_UNESCAPED_UNICODE); ?>,
            avatar: <?php echo json_encode($boyimg, JSON_UNESCAPED_UNICODE); ?>,
            gender: "male"
        };
        window.TIMELINE_AUTHORS[2] = {
            name: <?php echo json_encode($text['girl'], JSON_UNESCAPED_UNICODE); ?>,
            avatar: <?php echo json_encode($girlimg, JSON_UNESCAPED_UNICODE); ?>,
            gender: "female"
        };
        // 兼容旧数据 male/female 键
        window.TIMELINE_AUTHORS['male'] = window.TIMELINE_AUTHORS[1];
        window.TIMELINE_AUTHORS['female'] = window.TIMELINE_AUTHORS[2];
    </script>

    <!-- WaveSurfer 音频波形 -->
    <script src="/Style/js/wavesurfer.min.js"></script>
    <!-- 时间轴页面 JS -->
    <script src="/assets/js/page-timeline.js"></script>

    <script>
        // 通过 AJAX 从后端加载时间轴数据
        if (typeof window.LGTimelineModule !== 'undefined' && document.getElementById('timeline-container')) {
            window.LGTimelineModule.loadFromServer();
        }
    </script>
</div>

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
</body>
</html>
