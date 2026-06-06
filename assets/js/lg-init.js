/**
 * LG Init - 应用初始化脚本
 * 在所有页面加载完成后执行
 */
(function() {
    'use strict';

    window.LGApp = window.LGApp || {};

    /**
     * 应用初始化
     */
    LGApp.init = function(config) {
        LGApp.setConfig(config || {});

        // 初始化 Lucide 图标
        if (typeof lucide !== 'undefined' && lucide.createIcons) {
            try { lucide.createIcons(); } catch(e) {}
        }

        // 初始化 AOS 动画
        if (typeof AOS !== 'undefined') {
            var aosConfig = window.LG_AOS_CONFIG || {};
            AOS.init({
                duration: aosConfig.duration || 800,
                easing: aosConfig.easing || 'ease-out-cubic',
                offset: aosConfig.offset || 50,
                once: aosConfig.once !== false,
                mirror: aosConfig.mirror || false,
                anchorPlacement: aosConfig.anchorPlacement || 'top-bottom',
            });
        }

        // 初始化图片懒加载
        if (typeof FunLazy === 'function') {
            try {
                FunLazy({
                    placeholder: '/Style/img/Loading2.gif',
                    effect: 'show',
                    strictLazyMode: false,
                    useErrorImagePlaceholder: '/Style/img/error.svg'
                });
            } catch(e) {}
        }

        // 初始化 ViewImage 图片查看器
        if (typeof ViewImage !== 'undefined' && ViewImage.init) {
            try { ViewImage.init('[view-image] img:not([no-view])'); } catch(e) {}
        }

        // 页面加载完成标记
        document.body.classList.add('loaded');
    };

    /**
     * 设置配置
     */
    LGApp.setConfig = function(config) {
        LGApp.config = Object.assign(LGApp.config || {}, config);
    };

    // DOM Ready 时自动初始化
    document.addEventListener('DOMContentLoaded', function() {
        if (window.LG_CONFIG) {
            LGApp.init(window.LG_CONFIG);
        }
    });
})();
