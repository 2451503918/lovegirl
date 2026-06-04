/* ============================================
   Tooltip系统 - data-lg-tip 支持
   ============================================ */
(function() {
    'use strict';
    var tipEl = null;
    var innerEl = null;
    var currentTarget = null;
    var showTimer = null;

    function createTooltip() {
        tipEl = document.createElement('div');
        tipEl.className = 'lg-tooltip';
        innerEl = document.createElement('div');
        innerEl.className = 'lg-tooltip__inner';
        tipEl.appendChild(innerEl);
        document.body.appendChild(tipEl);
    }

    function show(target) {
        var text = target.getAttribute('data-lg-tip');
        if (!text) return;
        currentTarget = target;
        innerEl.textContent = text;
        tipEl.classList.add('lg-tooltip--visible');
        position(target);
    }

    function hide() {
        if (tipEl) tipEl.classList.remove('lg-tooltip--visible');
        currentTarget = null;
    }

    function position(target) {
        var rect = target.getBoundingClientRect();
        var tipRect = tipEl.getBoundingClientRect();
        var top = rect.top - tipRect.height - 8;
        var left = rect.left + (rect.width - tipRect.width) / 2;
        if (top < 4) top = rect.bottom + 8;
        if (left < 4) left = 4;
        if (left + tipRect.width > window.innerWidth - 4) left = window.innerWidth - tipRect.width - 4;
        tipEl.style.top = top + 'px';
        tipEl.style.left = left + 'px';
    }

    function init() {
        createTooltip();
        document.addEventListener('mouseenter', function(e) {
            var target = e.target.closest('[data-lg-tip]');
            if (!target) return;
            clearTimeout(showTimer);
            showTimer = setTimeout(function() { show(target); }, 200);
        }, true);
        document.addEventListener('mouseleave', function(e) {
            var target = e.target.closest('[data-lg-tip]');
            if (!target) return;
            clearTimeout(showTimer);
            hide();
        }, true);
        // 强制显示
        document.addEventListener('mouseenter', function(e) {
            var target = e.target.closest('[data-lg-tip-force="true"]');
            if (!target) return;
            clearTimeout(showTimer);
            show(target);
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
