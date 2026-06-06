<?php
include_once 'head.php';

// 获取留言配置
$jiequ = 10;
$shu = 0;

if ($connect) {
    $nub = "SELECT COUNT(id) AS shu FROM leaving";
    $res = mysqli_query($connect, $nub);
    if ($res) {
        $leav = mysqli_fetch_array($res);
        $shu = $leav['shu'] ?? 0;
    }

    $leavSet = "SELECT * FROM leavSet ORDER BY id DESC";
    $Set = mysqli_query($connect, $leavSet);
    if ($Set) {
        $Setinfo = mysqli_fetch_array($Set);
        $jiequ = $Setinfo['jiequ'] ?? 10;
    }
}

// 获取留言列表
$messages = [];
if ($connect) {
    $liuyan = "SELECT * FROM leaving ORDER BY id DESC LIMIT ?";
    $stmt = mysqli_prepare($connect, $liuyan);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $jiequ);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $messages[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<div id="pjax-container">
    <div class="Width_limit_10rem leav">

        <!-- 留言卡片容器 -->
        <div class="Message_Wrap">
            <div class="row Message_Main" id="lgmsgCardGrid">
                <?php if (!empty($messages)): ?>
                    <?php foreach ($messages as $i => $msg):
                        $msgId = $msg['id'];
                        $name = htmlspecialchars($msg['name']);
                        $qq = htmlspecialchars($msg['QQ']);
                        $text = htmlspecialchars($msg['text']);
                        $timestamp = intval($msg['time']);
                        $city = !empty($msg['city']) ? htmlspecialchars($msg['city']) : '中国';

                        // 头像
                        $avatarUrl = 'https://q1.qlogo.cn/g?b=qq&nk=' . $qq . '&s=100';

                        // 时间显示
                        $timeStr = date('Y-m-d H:i', $timestamp);

                        // 相对时间
                        $diff = time() - $timestamp;
                        if ($diff < 60) {
                            $timeAgo = '刚刚';
                        } elseif ($diff < 3600) {
                            $timeAgo = floor($diff / 60) . ' 分钟前';
                        } elseif ($diff < 86400) {
                            $timeAgo = floor($diff / 3600) . ' 小时前';
                        } elseif ($diff < 2592000) {
                            $timeAgo = floor($diff / 86400) . ' 天前';
                        } elseif ($diff < 31536000) {
                            $timeAgo = floor($diff / 2592000) . ' 个月前';
                        } else {
                            $timeAgo = floor($diff / 31536000) . ' 年前';
                        }

                        // AOS 动画延迟
                        $aosDelay = min($i * 50, 300);
                    ?>
                    <div class="MessageCard col-lg-6 col-md-6 col-sm-12 col-sm-x-12" data-msg-id="<?php echo $msgId; ?>" data-aos="fade-up" data-aos-delay="<?php echo $aosDelay; ?>">
                        <div class="MsgTime"><span><?php echo $timeStr; ?></span></div>
                        <div class="UserAvatar">
                            <img src="<?php echo $avatarUrl; ?>" alt="<?php echo $name; ?>" draggable="false" onerror="this.src='https://q1.qlogo.cn/g?b=qq&nk=10000&s=100'">
                        </div>
                        <div class="UserName"><h1><span class="lgmsg-card-name"><?php echo $name; ?></span></h1></div>
                        <div class="HeightCalc">
                            <div class="MsgContent"><p><?php echo $text; ?></p></div>
                            <div class="MsgFooter">
                                <div class="UserInfo">
                                    <span class="InfoItem" data-lg-tip="<?php echo $city; ?>"><i class="ph-fill ph-map-pin lgmsg-ico"></i><span class="lgmsg-info-text"><?php echo $city; ?></span></span>
                                    <span class="InfoItem lgmsg-info-hideable" data-lg-tip="<?php echo $timeAgo; ?>" data-lg-tip-force="true"><i class="ph-fill ph-clock lgmsg-ico"></i><span class="lgmsg-info-text"><?php echo $timeAgo; ?></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="lgmsg-empty" style="text-align:center; padding:4rem 1rem; color:var(--lgmsg-text-muted, #98989d);">
                        <i class="ph-fill ph-chat-teardrop-dots" style="font-size:3rem; display:block; margin-bottom:1rem;"></i>
                        <p>还没有留言，成为第一个留下祝福的人吧</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 留言触发按钮 -->
        <div class="message_btn" id="mes">
            <span class="mesly shadow-blur">
                <i class="ph-fill ph-chat-circle-text" style="width:2rem;height:2rem;fill:currentColor;stroke:none;"></i>
            </span>
        </div>

    </div>
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

<!-- 留言弹窗 -->
<div class="lgnewui-message-modal-overlay" id="lgmsgCommentModal">
    <div class="lgnewui-message-modal-content" id="lgmsgModalContent">
        <div class="lgnewui-message-close-wrapper">
            <button class="lgnewui-message-close-btn" id="lgmsgModalCloseBtn">
                <i class="ph-fill ph-x" style="width:20px;height:20px;"></i>
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
                        <i class="ph-fill ph-monitor"></i>
                    </div>
                    <span id="lgmsgTagOS">--</span>
                </div>
                <div class="lgnewui-message-v-tag">
                    <div class="lgnewui-message-v-tag-icon lgnewui-message-icon-browser">
                        <i class="ph-fill ph-globe"></i>
                    </div>
                    <span id="lgmsgTagBrowser">--</span>
                </div>
                <div class="lgnewui-message-v-tag">
                    <div class="lgnewui-message-v-tag-icon lgnewui-message-icon-location">
                        <i class="ph-fill ph-map-pin"></i>
                    </div>
                    <span id="lgmsgTagLocation">--</span>
                </div>
            </div>
            <div class="lgnewui-message-input-row" id="lgmsgInputRow">
                <div class="lgnewui-message-input-group" id="lgmsgQQGroup">
                    <label class="lgnewui-message-input-label">
                        <i class="ph-fill ph-at" style="width:14px;height:14px;"></i>
                        QQ号码
                    </label>
                    <input type="text" name="qq" id="lgmsgQQInput" class="lgnewui-message-input" placeholder="输入QQ号码自动获取昵称" maxlength="10" autocomplete="off">
                </div>
                <div class="lgnewui-message-input-group" id="lgmsgNameGroup">
                    <label class="lgnewui-message-input-label">
                        <i class="ph-fill ph-user" style="width:14px;height:14px;"></i>
                        昵称
                    </label>
                    <input type="text" name="name" id="lgmsgNameInput" class="lgnewui-message-input" placeholder="输入您的昵称" maxlength="20" autocomplete="off">
                </div>
            </div>
            <div class="lgnewui-message-privacy-hint" id="lgmsgPrivacyHint"><i class="ph-fill ph-lock"></i>QQ 信息经过加密脱敏处理，不会公开展示，请放心留言</div>
            <div class="lgnewui-message-editor-wrap">
                <div class="lgnewui-message-editor-content" id="lgmsgEditor" contenteditable="true" data-placeholder="想说点什么..."></div>
                <div class="lgnewui-message-emoji-bubbles" id="lgmsgEmojiBubbles"></div>
                <div class="lgnewui-message-editor-toolbar">
                    <div class="lgnewui-message-tb-left">
                        <button class="lgnewui-message-tb-btn" id="lgmsgBtnEmoji" title="表情">
                            <i class="ph-fill ph-smile"></i>
                        </button>
                        <button class="lgnewui-message-tb-btn" id="lgmsgBtnQuote" title="随机一言">
                            <i class="ph-fill ph-sparkle"></i>
                        </button>
                        <div class="lgnewui-message-switch-wrap" id="lgmsgEnterToSendWrap">
                            <div class="lgnewui-message-switch"></div>
                            <span class="lgnewui-message-switch-text">Enter 发送</span>
                        </div>
                    </div>
                    <span class="lgnewui-message-char-counter" id="lgmsgCharCounter">0/500</span>
                    <button class="lgnewui-message-submit-btn" id="lgmsgSubmitBtn">
                        <span class="lgnewui-message-submit-label">发送留言</span>
                        <i class="ph-fill ph-paper-plane-tilt lgnewui-message-submit-icon" style="width:18px;height:18px;"></i>
                        <i class="ph-fill ph-spinner lgnewui-message-submit-loader lgnewui-message-lucide-loader" style="width:18px;height:18px;"></i>
                        <i class="ph-fill ph-check lgnewui-message-submit-check" style="width:18px;height:18px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 表情面板 -->
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

<!-- 留言页配置注入 -->
<script>
    window.LG_CONFIG = window.LG_CONFIG || {};
    window.LG_CONFIG.msgPageInit = true;
    window.LG_CONFIG.msgTotal = <?php echo $shu; ?>;
    window.LG_CONFIG.endpoints = Object.assign(window.LG_CONFIG.endpoints || {}, {
        messageList: '/services/message-list.php',
        messageSubmit: '/services/message.php',
        infoService: '/services/info-service.php'
    });
</script>

<script>
$(function() {
    // 留言按钮点击：弹出留言弹窗
    $('#mes').on('click', function() {
        $('#lgmsgCommentModal').addClass('show');
    });

    // 关闭留言弹窗
    $('#lgmsgModalCloseBtn').on('click', function() {
        $('#lgmsgCommentModal').removeClass('show');
    });

    // 点击遮罩关闭
    $('#lgmsgCommentModal').on('click', function(e) {
        if (e.target === this) {
            $(this).removeClass('show');
        }
    });

    // Tab 切换
    $('#lgmsgTabContainer .lgnewui-message-ios-tab').on('click', function() {
        var mode = $(this).data('mode');
        $('#lgmsgTabContainer .lgnewui-message-ios-tab').removeClass('active');
        $(this).addClass('active');

        // 滑块位置
        var idx = $(this).index();
        $('#lgmsgTabSlider').css('transform', 'translateX(' + (idx * 100) + '%)');

        if (mode === 'anonymous') {
            $('#lgmsgQQGroup').hide();
            $('#lgmsgPrivacyHint').hide();
            $('#lgmsgNameInput').val('匿名').prop('readonly', true);
        } else {
            $('#lgmsgQQGroup').show();
            $('#lgmsgPrivacyHint').show();
            $('#lgmsgNameInput').val('').prop('readonly', false);
        }
    });

    // QQ号输入自动获取昵称
    var qqTimer = null;
    $('#lgmsgQQInput').on('input', function() {
        var qq = $(this).val().trim();
        clearTimeout(qqTimer);
        if (qq.length >= 5) {
            qqTimer = setTimeout(function() {
                $.get('/services/info-service.php', { qq: qq }, function(res) {
                    if (res && res.name) {
                        $('#lgmsgNameInput').val(res.name);
                    }
                }, 'json');
            }, 500);
        }
    });

    // 字符计数
    var editor = document.getElementById('lgmsgEditor');
    if (editor) {
        editor.addEventListener('input', function() {
            var len = this.textContent.length;
            $('#lgmsgCharCounter').text(len + '/500');
        });
    }

    // 提交留言
    $('#lgmsgSubmitBtn').on('click', function() {
        var qq = $('#lgmsgQQInput').val().trim();
        var name = $('#lgmsgNameInput').val().trim();
        var text = editor ? editor.textContent.trim() : '';

        var isAnonymous = $('#lgmsgTabContainer .lgnewui-message-ios-tab.active').data('mode') === 'anonymous';

        if (!isAnonymous && !qq) {
            toastr.warning('请填写QQ号码！', 'Like_Girl');
            return;
        }
        if (!name) {
            toastr.warning('请填写昵称！', 'Like_Girl');
            return;
        }
        if (!text) {
            toastr.warning('请填写留言内容！', 'Like_Girl');
            return;
        }
        if (text.length <= 2) {
            toastr.warning('请填写两个字符以上的内容！', 'Like_Girl');
            return;
        }

        if (!isAnonymous) {
            var qqRegex = /^[0-9]{5,10}$/;
            if (!qqRegex.test(qq)) {
                toastr.warning('QQ号码格式错误！', 'Like_Girl');
                return;
            }
        }

        var $btn = $(this);
        $btn.prop('disabled', true);

        $.ajax({
            url: 'admin/leavingPost.php',
            data: { qq: isAnonymous ? 'anon' : qq, name: name, text: text },
            type: 'POST',
            dataType: 'json',
            success: function(res) {
                if (res.status == 1) {
                    toastr.success('留言提交成功！', 'Like_Girl');
                    $('#lgmsgCommentModal').removeClass('show');
                    if (editor) editor.textContent = '';
                    $('#lgmsgQQInput').val('');
                    $('#lgmsgNameInput').val('');
                    $('#lgmsgCharCounter').text('0/500');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    toastr.error(res.msg || '留言提交失败！', 'Like_Girl');
                }
            },
            error: function(err) {
                var msg = '网络错误，请稍后重试！';
                if (err.responseText) {
                    var code = err.responseText.trim();
                    if (code == '3' || code == '30') msg = 'QQ号码格式错误';
                    else if (code == '8') msg = '你今天已经留言过了~';
                }
                toastr.error(msg, 'Like_Girl');
            },
            complete: function() {
                setTimeout(function() { $btn.prop('disabled', false); }, 3000);
            }
        });
    });

    // 初始化 lucide 图标
    if (typeof lucide !== 'undefined') {
        // lucide.createIcons(); // 使用Phosphor图标
    }
});
</script>

<?php
include_once 'footer.php';
?>
