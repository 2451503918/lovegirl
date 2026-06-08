/* ============================================
   自定义右键菜单
   ============================================ */
(function() {
    'use strict';
    var menu = null;

    function createMenu() {
        menu = document.createElement('div');
        menu.className = 'lg-ctx-menu';
        menu.innerHTML = [
            '<a class="lg-ctx-menu__item" data-action="home"><i class="ph-fill ph-house"></i>首页</a>',
            '<a class="lg-ctx-menu__item" data-action="top"><i class="ph-fill ph-arrow-circle-up"></i>回到顶部</a>',
            '<div class="lg-ctx-menu__divider"></div>',
            '<a class="lg-ctx-menu__item" data-action="articles"><i class="ph-fill ph-notebook"></i>点滴</a>',
            '<a class="lg-ctx-menu__item" data-action="messages"><i class="ph-fill ph-chat-teardrop-dots"></i>留言</a>',
            '<a class="lg-ctx-menu__item" data-action="timeline"><i class="ph-fill ph-clock-countdown"></i>轨迹</a>',
            '<a class="lg-ctx-menu__item" data-action="albums"><i class="ph-fill ph-camera"></i>相册</a>',
            '<a class="lg-ctx-menu__item" data-action="list"><i class="ph-fill ph-list-checks"></i>清单</a>',
            '<div class="lg-ctx-menu__divider"></div>',
            '<a class="lg-ctx-menu__item" data-action="about"><i class="ph-fill ph-book-open-text"></i>关于</a>'
        ].join('');
        document.body.appendChild(menu);

        menu.addEventListener('click', function(e) {
            var item = e.target.closest('[data-action]');
            if (!item) return;
            var action = item.getAttribute('data-action');
            var urls = { home:'index.php', articles:'articles.php', messages:'messages.php',
                timeline:'timeline.php', albums:'albums.php', list:'lovelist.php', about:'about.php' };
            if (action === 'top') {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else if (urls[action]) {
                window.location.href = urls[action];
            }
            hide();
        });
    }

    function show(x, y) {
        if (!menu) createMenu();
        menu.style.left = Math.min(x, window.innerWidth - 200) + 'px';
        menu.style.top = Math.min(y, window.innerHeight - 300) + 'px';
        menu.classList.add('lg-ctx-menu--show');
    }

    function hide() {
        if (menu) menu.classList.remove('lg-ctx-menu--show');
    }

    document.addEventListener('contextmenu', function(e) {
        // 不在输入框/编辑器中拦截
        if (e.target.closest('input, textarea, [contenteditable]')) return;
        e.preventDefault();
        show(e.clientX, e.clientY);
    });

    document.addEventListener('click', function() { hide(); });
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') hide(); });
})();
