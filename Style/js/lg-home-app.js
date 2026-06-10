/* ============================================
   LG-Home-App 首页完整应用
   智能媒体卡片 + 天气 + 交互
   ============================================ */

(function() {
    'use strict';

    /* ---- 智能媒体卡片（时光碎片）---- */
    function initSmartCard() {
        var card = document.getElementById('moment-card');
        if (!card) return;

        var mediaEl = card.querySelector('.lgnewui-smart-card__media');
        var avatarEl = card.querySelector('.lgnewui-smart-card__avatar');
        var nameEl = card.querySelector('.lgnewui-smart-card__name');
        var timeEl = card.querySelector('.lgnewui-smart-card__time');
        var titleEl = card.querySelector('.lgnewui-smart-card__title');
        var dateEl = card.querySelector('.lgnewui-smart-card__date');
        var descEl = card.querySelector('.lgnewui-smart-card__desc');
        var locationEl = card.querySelector('.lgnewui-smart-card__location-text');
        var switchBtn = card.querySelector('.lgnewui-smart-card__switch-btn');

        var photos = [];
        var currentIndex = 0;

        // 从API获取照片数据
        function loadPhotos() {
            $.post('/getPhotos.php', { page: 1, limit: 10 }, function(res) {
                if (res.code === 200 && res.data && res.data.length > 0) {
                    photos = res.data;
                    showPhoto(0);
                }
            }, 'json').fail(function() {
                // fallback: 使用默认样式
                if (mediaEl) mediaEl.style.background = 'linear-gradient(135deg, #1a1a2e, #16213e)';
            });
        }

        function showPhoto(index) {
            if (!photos.length) return;
            currentIndex = index % photos.length;
            var photo = photos[currentIndex];

            if (mediaEl && photo.img) {
                mediaEl.style.backgroundImage = 'url(' + photo.img + ')';
            }
            if (avatarEl) avatarEl.src = '/services/avatar-proxy.php?type=qq&qq=' + encodeURIComponent(window.LG_CONFIG ? window.LG_CONFIG.boyImg : '') + '&s=640';
            if (nameEl) nameEl.textContent = photo.who || '';
            if (timeEl) timeEl.textContent = photo.date || '';
            if (titleEl) titleEl.textContent = photo.text || '美好瞬间';
            if (dateEl) dateEl.textContent = photo.date || '';
            if (descEl) descEl.textContent = photo.text || '';
        }

        if (switchBtn) {
            switchBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                currentIndex = (currentIndex + 1) % photos.length;
                showPhoto(currentIndex);
            });
        }

        loadPhotos();
    }

    /* ---- 卡片 hover 3D 效果 ---- */
    function initCardHover3D() {
        var widgets = document.querySelectorAll('.lgnewui-widget');
        widgets.forEach(function(card) {
            card.addEventListener('mousemove', function(e) {
                var rect = card.getBoundingClientRect();
                var x = e.clientX - rect.left;
                var y = e.clientY - rect.top;
                var centerX = rect.width / 2;
                var centerY = rect.height / 2;
                var rotateX = (y - centerY) / 20;
                var rotateY = (centerX - x) / 20;
                card.style.transform = 'perspective(1000px) rotateX(' + rotateX + 'deg) rotateY(' + rotateY + 'deg) translateY(-4px)';
            });
            card.addEventListener('mouseleave', function() {
                card.style.transform = '';
            });
        });
    }

    /* ---- 数字滚动动画（增强版）---- */
    function initCounterAnimation() {
        var counters = document.querySelectorAll('.lgnewui-stats-num, .lgnewui-runtime-num');
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var el = entry.target;
                    var target = parseInt(el.textContent.replace(/[^0-9]/g, ''));
                    if (isNaN(target) || target === 0) { observer.unobserve(el); return; }
                    var duration = 1500;
                    var startTime = null;
                    el.textContent = '0';

                    function step(ts) {
                        if (!startTime) startTime = ts;
                        var progress = Math.min((ts - startTime) / duration, 1);
                        var eased = 1 - Math.pow(1 - progress, 4);
                        el.textContent = Math.floor(eased * target).toLocaleString();
                        if (progress < 1) requestAnimationFrame(step);
                    }
                    requestAnimationFrame(step);
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.2 });
        counters.forEach(function(el) { observer.observe(el); });
    }

    /* ---- 进度条动画 ---- */
    function initProgressBars() {
        var bars = document.querySelectorAll('.lgnewui-progress__bar');
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var bar = entry.target;
                    var w = bar.getAttribute('data-width') || bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(function() { bar.style.width = w; }, 200);
                    observer.unobserve(bar);
                }
            });
        }, { threshold: 0.3 });
        bars.forEach(function(el) { observer.observe(el); });
    }

    /* ---- 卡片入场动画 ---- */
    function initCardEntrance() {
        var cards = document.querySelectorAll('.lgnewui-widget, .lgnewui-journal-card, .lgnewui-home-message-card');
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        cards.forEach(function(card, i) {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease ' + (i * 0.05) + 's, transform 0.6s ease ' + (i * 0.05) + 's';
            observer.observe(card);
        });
    }

    /* ---- 初始化 ---- */
    window.initLGHomeApp = function(config) {
        config = config || {};
        initSmartCard();
        initCounterAnimation();
        initProgressBars();
        initCardEntrance();
        // 3D hover 仅桌面端
        if (window.innerWidth > 768) {
            initCardHover3D();
        }
    };
})();
