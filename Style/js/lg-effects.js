/* ============================================
   LG Effects - 礼花 + 访客追踪
   ============================================ */
(function() {
    'use strict';

    /* ---- 礼花效果 ---- */
    window.ConfettiEffect = {
        init: function() {
            if (typeof confetti === 'undefined') return;
            // 页面加载后延迟触发
            setTimeout(function() { ConfettiEffect.loveWingEffect(); }, 1000);
        },
        loveWingEffect: function() {
            if (typeof confetti === 'undefined') return;
            var duration = 2000;
            var end = Date.now() + duration;
            var colors = ['#f87171', '#fb923c', '#f472b6', '#a78bfa', '#60a5fa'];
            (function frame() {
                confetti({ particleCount: 3, angle: 60, spread: 55, origin: { x: 0, y: 0.7 }, colors: colors });
                confetti({ particleCount: 3, angle: 120, spread: 55, origin: { x: 1, y: 0.7 }, colors: colors });
                if (Date.now() < end) requestAnimationFrame(frame);
            })();
        },
        burst: function() {
            if (typeof confetti === 'undefined') return;
            confetti({ particleCount: 100, spread: 70, origin: { y: 0.6 } });
        }
    };

    /* ---- 访客追踪 ---- */
    var beaconEndpoint = '/services/access-beacon.php';
    var requestId = '';
    var token = '';
    var startAt = Date.now();
    var reported = false;

    window.AccessBeacon = {
        init: function(rid, tk) {
            requestId = rid || '';
            token = tk || '';
            startAt = Date.now();
            reported = false;

            window.addEventListener('pagehide', function() { AccessBeacon.report(); });
            document.addEventListener('visibilitychange', function() {
                if (document.visibilityState === 'hidden') AccessBeacon.report();
            }, { passive: true });

            if (window.jQuery) {
                jQuery(document).on('pjax:send', function() { AccessBeacon.report(); });
                jQuery(document).on('pjax:complete', function(e, xhr) {
                    if (xhr && xhr.getResponseHeader) {
                        var newRid = xhr.getResponseHeader('X-LG-Access-Request-Id');
                        var newTk = xhr.getResponseHeader('X-LG-Access-Beacon-Token');
                        if (newRid) requestId = newRid;
                        if (newTk) token = newTk;
                        startAt = Date.now();
                        reported = false;
                    }
                });
            }
        },
        report: function() {
            if (reported || !navigator.sendBeacon) return;
            reported = true;
            var stay = Math.min(Math.round((Date.now() - startAt) / 1000), 86400);
            var fd = new FormData();
            fd.append('request_id', requestId);
            fd.append('stay_seconds', String(stay));
            fd.append('token', token);
            navigator.sendBeacon(beaconEndpoint, fd);
        }
    };
})();
