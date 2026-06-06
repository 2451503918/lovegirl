/**
 * LG-NewUi 初始化脚本
 * 统一管理所有模块的初始化
 */
(function() {
    'use strict';

    console.log('%c ❤️ LG-NewUi Initializing... ', 'background: linear-gradient(135deg, #ff6b6b, #ff8e53); color: #fff; padding: 8px 16px; border-radius: 4px; font-weight: 700;');

    function initAll() {
        // 1. 初始化首页应用
        if (typeof window.initLGHomeApp === 'function') {
            window.initLGHomeApp();
        }

        // 2. 初始化交互动效
        if (typeof window.initLGInteractions === 'function') {
            window.initLGInteractions();
        }

        // 3. 初始化音乐播放器
        if (typeof window.initLGMusicPlayer === 'function') {
            window.initLGMusicPlayer();
        }

        // 4. 地图功能已由页面内组件处理（lg-mini-map / page-timeline）

        // 5. 初始化右键菜单
        if (typeof window.initLGContextMenu === 'function') {
            window.initLGContextMenu();
        }

        console.log('%c ✅ LG-NewUi Initialized Successfully! ', 'background: linear-gradient(135deg, #00b894, #00cec9); color: #fff; padding: 8px 16px; border-radius: 4px; font-weight: 700;');
    }

    // 等待DOM加载完成后初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    // 如果使用Pjax，也需要在pjax完成后重新初始化
    if (typeof $ !== 'undefined' && $(document).on) {
        $(document).on('pjax:complete', function() {
            // 清除之前创建的地图容器（防止从 timeline 页面切换后残留）
            var oldMap = document.getElementById('lg-map-container');
            if (oldMap) oldMap.remove();
            var oldStyle = document.getElementById('lg-map-style');
            if (oldStyle) oldStyle.remove();
            // 延迟一点时间，确保Pjax内容完全加载
            setTimeout(initAll, 100);
        });
    }

    // 暴露初始化函数
    window.initLGNewUi = initAll;

})();
