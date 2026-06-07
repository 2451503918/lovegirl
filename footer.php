<script src="/Style/toastr/toastr.js"></script>

    <!-- ===== 音乐播放器（全局） ===== -->

    <div id="nav-music">
        <div id="nav-music-hoverTips" onclick="lg_love.musicToggle()">
            <svg viewBox="0 0 1024 1024" class="lgnewui-nav-music-play-icon" aria-hidden="true">
                <path d="M324.085 95.787l500.422 300.664c82.373 50.453 79.284 136.946-1.03 186.37v0l-506.6 304.784c-41.187 23.683-87.522 37.068-131.798 9.267-36.037-22.653-46.335-58.691-46.335-97.819v-616.774c0-39.127 13.386-75.166 48.395-97.819 45.305-27.801 94.731-14.416 136.946 11.327v0z" fill="#ffffff" />
            </svg>
        </div>
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
                    <circle cx="12" cy="12" r="10" stroke-dasharray="31.4" stroke-dashoffset="10"></circle>
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

    <!-- 音乐播放列表面板 -->
    <div class="lgnewui-music-panel" id="lgnewuiMusicPanel" style="display:none;">
        <div class="lgnewui-music-panel__header">
            <h4 class="lgnewui-music-panel__title">播放列表</h4>
            <button class="lgnewui-music-panel__close" onclick="toggleMusicPanel()"><i class="ph-bold ph-x"></i></button>
        </div>
        <div class="lgnewui-music-panel__body" id="lgnewuiMusicPlaylist"></div>
    </div>

    <!-- 音乐确认弹窗 -->
    <div class="lgnewui-confirm-dialog" id="musicConfirmDialog" style="display:none;">
        <div class="lgnewui-confirm-dialog__overlay" onclick="closeMusicConfirm()"></div>
        <div class="lgnewui-confirm-dialog__content">
            <div class="lgnewui-confirm-dialog__icon"><i class="ph-fill ph-music-note"></i></div>
            <h4 class="lgnewui-confirm-dialog__title">播放音乐</h4>
            <p class="lgnewui-confirm-dialog__text" id="musicConfirmText">确定要播放这首音乐吗？</p>
            <div class="lgnewui-confirm-dialog__actions">
                <button class="lgnewui-confirm-dialog__btn lgnewui-confirm-dialog__btn--cancel" onclick="closeMusicConfirm()">取消</button>
                <button class="lgnewui-confirm-dialog__btn lgnewui-confirm-dialog__btn--confirm" id="musicConfirmPlayBtn">播放</button>
            </div>
        </div>
    </div>

    <script>
    function toggleMusicPanel() {
        var panel = document.getElementById('lgnewuiMusicPanel');
        if (panel) panel.style.display = panel.style.display === 'none' ? '' : 'none';
    }
    function closeMusicConfirm() {
        var dialog = document.getElementById('musicConfirmDialog');
        if (dialog) dialog.style.display = 'none';
    }
    </script>

    <!-- ===== 地图浮层（全局） ===== -->
    <div class="lg-map-overlay" id="lgMapOverlay" style="display:none;">
        <div class="lg-map-overlay__backdrop" onclick="closeLGMap()"></div>
        <div class="lg-map-overlay__content">
            <div class="lg-map-overlay__header">
                <h3 class="lg-map-overlay__title">我们的足迹</h3>
                <button class="lg-map-overlay__close" onclick="closeLGMap()"><i class="ph-bold ph-x"></i></button>
            </div>
            <div class="lg-map-overlay__body">
                <div class="lg-map-modal" id="lg-map-container"></div>
                <div class="lg-map-lovers-panel" id="lg-map-lovers-panel">
                    <div class="lg-map-lover lg-map-lover--male">
                        <img class="lg-map-lover__avatar lg-male-avatar" src="" alt="">
                        <div class="lg-map-lover__info">
                            <span class="lg-map-lover__name"><?php echo htmlspecialchars($text['boy'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="lg-map-lover__location" id="lg-map-male-location">--</span>
                        </div>
                    </div>
                    <div class="lg-map-lover lg-map-lover--female">
                        <img class="lg-map-lover__avatar lg-female-avatar" src="" alt="">
                        <div class="lg-map-lover__info">
                            <span class="lg-map-lover__name"><?php echo htmlspecialchars($text['girl'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="lg-map-lover__location" id="lg-map-female-location">--</span>
                        </div>
                    </div>
                </div>
                <div class="lg-map-distance-panel" id="lg-map-distance-panel">
                    <i class="ph-fill ph-map-pin-line"></i>
                    <span>相距 <strong id="lg-map-distance-value">--</strong> km</span>
                </div>
            </div>
        </div>
    </div>
    <script src="/assets/js/lg-map.js?LikeGirl=<?php echo $version ?? '5.0' ?>"></script>
    <script>
    function closeLGMap() {
        var overlay = document.getElementById('lgMapOverlay');
        if (overlay) overlay.style.display = 'none';
        document.body.style.overflow = '';
    }
    (function() {
        var maleAvatar = (window.LG_CONFIG && window.LG_CONFIG.maleAvatar) || '';
        var femaleAvatar = (window.LG_CONFIG && window.LG_CONFIG.femaleAvatar) || '';
        document.querySelectorAll('.lg-map-lover__avatar.lg-male-avatar').forEach(function(el) {
            if (maleAvatar) el.src = maleAvatar;
        });
        document.querySelectorAll('.lg-map-lover__avatar.lg-female-avatar').forEach(function(el) {
            if (femaleAvatar) el.src = femaleAvatar;
        });
    })();
    </script>

    <!-- ===== 浮动操作按钮（全局） ===== -->
    <div class="lgnewui-fab-group" id="lgnewuiFabGroup">
        <button class="lgnewui-fab lgnewui-fab--map" onclick="document.getElementById('lgMapOverlay').style.display='';document.body.style.overflow='hidden';if(window.initLGMap)initLGMap();" title="足迹地图">
            <i class="ph-fill ph-map-trifold"></i>
        </button>
        <button class="lgnewui-fab lgnewui-fab--top" onclick="scrollToTop()" title="回到顶部">
            <i class="ph-bold ph-arrow-up"></i>
        </button>
    </div>


    

    
    <script>
        function scrollToTop(duration = 500) {
          $('html, body').animate({ scrollTop: 0 }, duration);
        }
        
        

        $(function () {
            
            initLoveAlbum();
            
            initScrollButton('#MessageBtn', '#MessageArea', 800, 800);

                        
            let $tooltip;
            
            function showTooltip($element) {
                const tipText = $element.data('tip') || '';
                const position = $element.data('tip-position') || 'top';
            
                if (!$tooltip) {
                    $tooltip = $('<div class="custom-tooltip"></div>').appendTo('body');
                }
                $tooltip.text(tipText).removeClass('top bottom left right').addClass(position).show();
            
                // 获取元素相对于页面的绝对位置
                const offset = $element.offset();
                const elWidth = $element.outerWidth();
                const elHeight = $element.outerHeight();
            
                $tooltip.css({ visibility: 'hidden', display: 'block' }); // 临时显示计算大小
                const tipWidth = $tooltip.outerWidth();
                const tipHeight = $tooltip.outerHeight();
            
                let top = 0, left = 0;
                switch (position) {
                    case 'top':
                        top = offset.top - tipHeight - 10;
                        left = offset.left + (elWidth - tipWidth) / 2;
                        break;
                    case 'bottom':
                        top = offset.top + elHeight + 10;
                        left = offset.left + (elWidth - tipWidth) / 2;
                        break;
                    case 'left':
                        top = offset.top + (elHeight - tipHeight) / 2;
                        left = offset.left - tipWidth - 10;
                        break;
                    case 'right':
                        top = offset.top + (elHeight - tipHeight) / 2;
                        left = offset.left + elWidth + 10;
                        break;
                }
            
                // 边界处理
                const viewportWidth = $(window).width();
                const viewportHeight = $(window).height();
                if (left < 10) left = 10;
                if (left + tipWidth > viewportWidth - 10) left = viewportWidth - tipWidth - 10;
                if (top < 10) top = 10;
                if (top + tipHeight > $(document).height() - 10) top = $(document).height() - tipHeight - 10;
            
                $tooltip.css({ top: top + 'px', left: left + 'px', visibility: 'visible', opacity : 1 });
            }
            
            function hideTooltip() {
                if ($tooltip) $tooltip.hide();
            }
            
            // 事件绑定
            $(document).on({
                mouseenter: function() { showTooltip($(this)); },
                mouseleave: function() { hideTooltip(); }
            }, '[data-tip]');
            
            // 滚动或 touch 时隐藏
            $(window).on('scroll touchstart touchmove', hideTooltip);
            
            // 使用事件委托确保动态加载的卡片也能点击
            $(document).on('click', '.card, .card-b', function(e) {
                // 如果点击的不是链接本身，则触发链接点击
                if (!$(e.target).is('a') && !$(e.target).closest('a').length) {
                    var link = $(this).find('a').get(0);
                    if (link) {
                        e.preventDefault();
                        link.click();
                    }
                }
            });
        

            $('video').each(function() {
                var video = $(this);
                setupVideoPlayer(video);
            });

            
            $(".love_img img,.lovelist img,.little_texts img").addClass("spotlight").each(function () {
                this.onclick = function () {
                    return hs.expand(this)
                }
                toastr.options = {
                    "closeButton": true,
                    "debug": false,
                    "newestOnTop": false,
                    "progressBar": true,
                    "rtl": false,
                    "positionClass": "toast-top-right",
                    "preventDuplicates": false,
                    "onclick": null,
                    "showDuration": 300,
                    "hideDuration": 1000,
                    "timeOut": 5000,
                    "extendedTimeOut": 1000,
                    "showEasing": "swing",
                    "hideEasing": "linear",
                    "showMethod": "fadeIn",
                    "hideMethod": "fadeOut"
                };
            });

            window.onscroll = function () {
                let scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
                if (scrollTop > 500) {
                    $('.wenan').css({
                        'color': '#333333'
                    });
                    $('.alogo').css({
                        'color': '#333333'
                    });
                }

                if (scrollTop < 500) {
                    $('.wenan').css({
                        'color': 'rgb(97 97 97)'
                    });
                    $('.alogo').css({
                        'color': 'rgb(97 97 97)'
                    });
                }
            }

            FunLazy({
                placeholder: "Style/img/Loading2.gif",
                effect: "show",
                strictLazyMode: false,
                useErrorImagePlaceholder: "Style/img/error.svg"
            })
            


        })


    </script>
    <style>
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
            width: 3em;
            height: 3em;
            border-radius: 50%;
            box-shadow: 0 2px 20px #c5c5c575;
            border: 2px solid #fff;
            margin-right: 0.8rem;
        }
    </style>
</div>

<!-- ===== 全局底部 Footer ===== -->
<style>
.lgnewui-footer {
    text-align: center;
    padding: 2rem 1rem;
    color: #94a3b8;
    font-size: 0.85rem;
}
.lgnewui-footer__animal {
    width: 80px;
    margin: 0 auto 1rem;
    opacity: 0.6;
}
.lgnewui-footer__badges {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}
.lgnewui-footer__badge img {
    height: 20px;
    opacity: 0.7;
}
.lgnewui-footer__icp {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    margin-top: 0.5rem;
}
.lgnewui-footer__icp img {
    width: 14px;
    height: 14px;
}
.lgnewui-footer__icp a {
    color: #94a3b8;
    text-decoration: none;
}
.lgnewui-footer__icp a:hover {
    color: #64748b;
}
.lgnewui-footer__copyright {
    margin-top: 0.5rem;
}
</style>
<footer class="lgnewui-footer">
    <div class="lgnewui-footer__animal">
        <img src="/Style/img/animals.png" alt="animals" onerror="this.parentElement.style.display='none'">
    </div>
    <div class="lgnewui-footer__badges">
        <a href="https://github.com" target="_blank" rel="noopener" class="lgnewui-footer__badge">
            <img src="https://img.shields.io/badge/Powered%20By-LikeGirl-ff69b4?style=flat-square" alt="LikeGirl">
        </a>
        <a href="https://github.com" target="_blank" rel="noopener" class="lgnewui-footer__badge">
            <img src="https://img.shields.io/badge/Version-<?php echo htmlspecialchars($version ?? '5.0', ENT_QUOTES, 'UTF-8') ?>-blue?style=flat-square" alt="Version">
        </a>
        <a href="https://github.com" target="_blank" rel="noopener" class="lgnewui-footer__badge">
            <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="License">
        </a>
    </div>
    <?php if (!empty($text['icp'])): ?>
    <div class="lgnewui-footer__icp">
        <img src="/Style/img/icp.svg" alt="" aria-hidden="true">
        <a href="https://beian.miit.gov.cn/#/Integrated/index" target="_blank" rel="noopener"><?php echo htmlspecialchars($text['icp'], ENT_QUOTES, 'UTF-8') ?></a>
    </div>
    <?php endif; ?>
    <?php if (!empty($text['Copyright'])): ?>
    <div class="lgnewui-footer__copyright">
        <?php echo htmlspecialchars($text['Copyright'], ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>
</footer>

<?php echo htmlspecialchars_decode($diy['footerCon'] ?? '', ENT_QUOTES) ?>

</body>
</html>