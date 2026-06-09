<?php
$pageTitle = '留言';
include_once 'head.php';

if (!isset($text) || !is_array($text)) {
    $text = ['boy' => '男方', 'girl' => '女方', 'boyimg' => '', 'girlimg' => '',
        'startTime' => date('Y-m-d H:i:s', time() - 365 * 86400), 'logo' => '我们的故事',
        'writing' => '记录美好时光', 'Copyright' => '', 'icp' => ''];
}

// 获取留言总数
$msgTotal = 0;
if ($connect) {
    $res = mysqli_query($connect, "SELECT COUNT(*) AS cnt FROM leaving");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        $msgTotal = intval($row['cnt'] ?? 0);
    }
}
?>

<div id="pjax-container">
    <div class="Width_limit_10rem leav">

        <!-- 留言卡片容器（由 JS 渲染） -->
        <div class="Message_Wrap">
            <div class="row Message_Main" id="lgmsgCardGrid">
                <!-- 初始骨架屏 -->
                <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="col-lg-6 col-md-6 col-sm-12 col-sm-x-12 lgmsg-skeleton-wrap">
                    <div class="lgmsg-skeleton" style="animation-delay:<?php echo $i * 80 ?>ms">
                        <div class="sk-header">
                            <div class="lgmsg-skeleton-line sk-avatar"></div>
                            <div>
                                <div class="lgmsg-skeleton-line sk-name"></div>
                                <div class="lgmsg-skeleton-line sk-time"></div>
                            </div>
                        </div>
                        <div class="lgmsg-skeleton-line sk-text1"></div>
                        <div class="lgmsg-skeleton-line sk-text2"></div>
                        <div class="lgmsg-skeleton-line sk-text3"></div>
                        <div class="sk-footer">
                            <div class="lgmsg-skeleton-line sk-pill"></div>
                            <div class="lgmsg-skeleton-line sk-pill" style="width:50px"></div>
                            <div class="lgmsg-skeleton-line sk-pill" style="width:45px"></div>
                        </div>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- 加载更多 / 无更多 -->
        <div id="lgmsgLoadMoreWrap" class="lgmsg-load-more-wrap" style="display:none;">
            <button class="lgmsg-load-more-btn" id="lgmsgLoadMoreBtn">
                <span class="btn-text"><i class="ph ph-arrow-down"></i> 加载更多</span>
            </button>
        </div>
        <div id="lgmsgNoMore" class="lgmsg-no-more" style="display:none;">— 已经到底了 —</div>

        <!-- 侧边抽屉（留言详情 + 回复） -->
        <div class="lgnewui-message-drawer-overlay" id="lgmsgDrawer">
            <div class="lgnewui-message-drawer-content" id="lgmsgDrawerContent">
                <div class="lgnewui-message-drawer-bg"></div>
                <div class="lgnewui-message-drawer-header">
                    <div class="lgnewui-message-drawer-title-wrap">
                        <div class="lgnewui-message-drawer-title-icon">
                            <i data-lucide="message-square" style="width:18px;height:18px;"></i>
                        </div>
                        <div>
                            <div class="lgnewui-message-drawer-title">留言详情</div>
                            <div class="lgnewui-message-drawer-subtitle" id="lgmsgDrawerSubtitle">0 条回复</div>
                        </div>
                    </div>
                    <button class="lgnewui-message-close-btn" id="lgmsgDrawerClose" aria-label="关闭留言详情">
                        <i data-lucide="x" style="width:20px;height:20px;" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="lgnewui-message-drawer-scroll" id="lgmsgDrawerScroll">
                    <div class="lgnewui-message-drawer-body" id="lgmsgDrawerBody">
                        <!-- 由 JS 动态渲染：父留言 + 回复列表 -->
                    </div>
                    <div class="lgnewui-message-drawer-footer" id="lgmsgDrawerFooter">
                        <div class="lgnewui-message-drawer-footer-inner" id="lgmsgDrawerFooterInner">
                            <div class="lgnewui-message-drawer-collapsed-wrap">
                                <div class="lgnewui-message-drawer-collapsed" id="lgmsgDrawerCollapsed">
                                    <div class="lgnewui-message-dc-input">
                                        <i data-lucide="message-square"></i>
                                        留下一条友善的评论...
                                    </div>
                                </div>
                            </div>
                            <div class="lgnewui-message-drawer-expanded-wrap">
                                <div class="lgnewui-message-drawer-expanded" id="lgmsgDrawerExpanded">
                                    <div class="lgnewui-message-drawer-identity" id="lgmsgDrawerIdentityBar">
                                        <div id="lgmsgDrawerIdentityClickArea" style="display:flex; align-items:center; gap:10px; flex:1; overflow:hidden;">
                                            <img src="data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22%23c7c7cc%22%3E%3Ccircle cx=%2212%22 cy=%228%22 r=%224%22/%3E%3Cpath d=%22M20 21a8 8 0 1 0-16 0%22/%3E%3C/svg%3E" id="lgmsgDrawerIdentityAvatar" alt="用户头像">
                                            <span id="lgmsgDrawerIdentityName">未认证，点击设置身份</span>
                                            <i data-lucide="square-pen" class="edit-icon"></i>
                                        </div>
                                        <div id="lgmsgDrawerCollapseBtn" style="padding:4px; margin-right:-4px; cursor:pointer; color:var(--lgmsg-text-muted);" title="收起面板">
                                            <i data-lucide="chevron-down" style="width:20px;height:20px;"></i>
                                        </div>
                                    </div>
                                    <div class="lgnewui-message-drawer-input-tags" id="lgmsgDrawerVisitorTags"></div>
                                    <div class="lgnewui-message-drawer-editor-wrap">
                                        <div class="lgnewui-message-drawer-editor" id="lgmsgDrawerEditor" contenteditable="true" data-placeholder="友善的留言是交流的起点..."></div>
                                        <div class="lgnewui-message-drawer-toolbar">
                                            <div class="lgnewui-message-tb-left" style="gap: 2px;">
                                                <button class="lgnewui-message-tb-btn" id="lgmsgBtnDrawerEmoji" title="表情" style="width:32px;height:32px;">
                                                    <i data-lucide="smile"></i>
                                                </button>
                                                <button class="lgnewui-message-tb-btn" id="lgmsgBtnDrawerQuote" title="随机一言" style="width:32px;height:32px;">
                                                    <i data-lucide="sparkles"></i>
                                                </button>
                                                <div class="lgnewui-message-switch-wrap" id="lgmsgDrawerEnterSwitch">
                                                    <div class="lgnewui-message-switch"></div>
                                                    <span class="lgnewui-message-switch-text">Enter 发送</span>
                                                </div>
                                            </div>
                                            <span class="lgnewui-message-char-counter" id="lgmsgDrawerCharCounter" style="margin-right: 12px; margin-left: auto;">0/500</span>
                                            <button class="lgnewui-message-reply-btn" id="lgmsgReplySendBtn" aria-label="发送回复"><i data-lucide="send" style="width:16px;height:16px;" aria-hidden="true"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 消息操作菜单 -->
        <div class="lgnewui-message-context-menu" id="lgmsgContextMenu">
            <div class="lgnewui-message-cm-item" data-action="like" title="点赞">
                <i data-lucide="heart"></i>
            </div>
            <div class="lgnewui-message-cm-item" data-action="copy" title="复制">
                <i data-lucide="copy"></i>
            </div>
            <div class="lgnewui-message-cm-item" data-action="quote" title="引用">
                <i data-lucide="quote"></i>
            </div>
            <div class="lgnewui-message-cm-item" data-action="plusone" title="+1">
                <i data-lucide="plus"></i>
            </div>
        </div>

        <!-- 身份认证弹窗 -->
        <div class="lgnewui-message-modal-overlay" id="lgmsgAuthModal">
            <div class="lgnewui-message-modal-content" style="max-width: 400px; max-height: unset; border-radius: 20px;">
                <div class="lgnewui-message-close-wrapper" style="top: 16px; right: 16px;">
                    <button class="lgnewui-message-close-btn" id="lgmsgAuthCloseBtn" style="width: 28px; height: 28px;">
                        <i data-lucide="x" style="width:16px;height:16px;"></i>
                    </button>
                </div>
                <div class="lgnewui-message-modal-body" style="padding: 24px;">
                    <div class="lgnewui-message-drawer-title" id="lgmsgAuthTitle" style="text-align: center; margin-bottom: 20px;">身份设置</div>
                    <div class="lgnewui-message-ios-tabs-wrap" style="justify-content: center; margin-bottom: 24px;">
                        <div class="lgnewui-message-ios-tabs" id="lgmsgAuthTabContainer">
                            <div class="lgnewui-message-ios-tab-slider" id="lgmsgAuthTabSlider"></div>
                            <div class="lgnewui-message-ios-tab active" data-mode="qq">QQ身份</div>
                            <div class="lgnewui-message-ios-tab" data-mode="anonymous">匿名身份</div>
                        </div>
                    </div>
                    <div class="lgnewui-message-input-row" id="lgmsgAuthInputRow" style="flex-direction: column;"></div>
                    <div class="lgnewui-message-privacy-hint" id="lgmsgAuthPrivacyHint"><i data-lucide="lock"></i>QQ 信息经过加密脱敏处理，不会公开展示，请放心留言</div>
                    <button class="lgnewui-message-submit-btn" id="lgmsgAuthSaveBtn" style="width: 100%; justify-content: center; margin-top: 24px;">
                        <span class="lgnewui-message-submit-label">保存身份</span>
                        <i data-lucide="save" class="lgnewui-message-submit-icon" style="width:18px;height:18px;"></i>
                        <i data-lucide="loader" class="lgnewui-message-submit-loader lgnewui-message-lucide-loader" style="width:18px;height:18px;"></i>
                        <i data-lucide="check" class="lgnewui-message-submit-check" style="width:18px;height:18px;"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- 留言页配置注入 -->
        <script>
            window.LG_CONFIG = window.LG_CONFIG || {};
            window.LG_CONFIG.msgPageInit = true;
            window.LG_CONFIG.msgTotal = <?php echo intval($msgTotal) ?>;
            window.LG_CONFIG.anonAvatars = <?php echo json_encode([]); ?>;
            window.LG_CONFIG.endpoints = Object.assign(window.LG_CONFIG.endpoints || {}, {
                messageList: '/services/message-list.php',
                messageSubmit: '/services/message.php',
                infoService: '/services/info-service.php',
                weatherApi: '/services/weather.php'
            });
        </script>

    </div>
</div>

<!-- 留言弹窗/表情面板/确认弹窗已移至 footer.php 统一管理 -->

<?php include_once 'footer.php'; ?>
