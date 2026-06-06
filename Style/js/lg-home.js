/* ============================================
   LG-Home 首页模块 JS
   天气 + 计数器 + 统计动画
   ============================================ */

(function() {
    'use strict';

    /* ---- 天数计数器 ---- */
    function initDayCounter(startDateStr) {
        var startDate = new Date(startDateStr.replace('T', ' '));
        var daysEl = document.getElementById('lgnewui-day-counter-days');
        var hoursEl = document.getElementById('lgnewui-day-counter-hours');
        var minsEl = document.getElementById('lgnewui-day-counter-minutes');
        var secsEl = document.getElementById('lgnewui-day-counter-seconds');
        var startDisplay = document.getElementById('lgnewui-day-start-date-display');

        if (!daysEl) return;
        if (startDisplay) startDisplay.textContent = startDateStr.replace('T', ' ');

        function update() {
            var now = new Date();
            var diff = now.getTime() - startDate.getTime();
            var days = Math.floor(diff / 86400000);
            var hours = Math.floor((diff % 86400000) / 3600000);
            var mins = Math.floor((diff % 3600000) / 60000);
            var secs = Math.floor((diff % 60000) / 1000);

            daysEl.textContent = days;
            if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
            if (minsEl) minsEl.textContent = String(mins).padStart(2, '0');
            if (secsEl) secsEl.textContent = String(secs).padStart(2, '0');
        }

        update();
        setInterval(update, 1000);
    }

    /* ---- 天气卡片 ---- */
    function initWeather(token) {
        if (!token) return;

        var cards = document.querySelectorAll('[data-weather-slot]');
        cards.forEach(function(card) {
            var slot = card.getAttribute('data-weather-slot');
            // slot 1 = boy location, slot 2 = girl location
            // 使用浏览器定位
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(pos) {
                    fetchWeather(card, pos.coords.latitude, pos.coords.longitude, token);
                }, function() {
                    // fallback: 使用默认位置
                    fetchWeatherByCity(card, '上海', token);
                });
            } else {
                fetchWeatherByCity(card, '上海', token);
            }
        });
    }

    function fetchWeather(card, lat, lon, token) {
        var url = 'https://geoapi.qweather.com/v2/city/lookup?location=' + lon + ',' + lat + '&key=' + token + '&lang=zh';
        fetch(url).then(function(r) { return r.json(); }).then(function(data) {
            if (data.code === '200' && data.location && data.location.length > 0) {
                var city = data.location[0].name;
                fetchWeatherData(card, city, token);
            }
        }).catch(function() {});
    }

    function fetchWeatherByCity(card, city, token) {
        fetchWeatherData(card, city, token);
    }

    function fetchWeatherData(card, city, token) {
        // 先查找城市ID
        var geoUrl = 'https://geoapi.qweather.com/v2/city/lookup?location=' + encodeURIComponent(city) + '&key=' + token;
        fetch(geoUrl).then(function(r) { return r.json(); }).then(function(geoData) {
            if (geoData.code !== '200' || !geoData.location || !geoData.location.length) return;
            var locId = geoData.location[0].id;
            var locName = geoData.location[0].name;

            // 获取实时天气
            var weatherUrl = 'https://devapi.qweather.com/v7/weather/now?location=' + locId + '&key=' + token;
            fetch(weatherUrl).then(function(r) { return r.json(); }).then(function(wData) {
                if (wData.code !== '200') return;
                var now = wData.now;
                card.setAttribute('data-location-name', locName);

                var tempEl = card.querySelector('.lgnewui-home-weather-text-temp');
                var cityEl = card.querySelector('.lgnewui-home-weather-text-city');
                var statusEl = card.querySelector('.lgnewui-home-weather-text-status');
                var timeEl = card.querySelector('.lgnewui-home-weather-time-tag');
                var iconEl = card.querySelector('.lgnewui-home-weather-icon-main');
                var humEl = card.querySelector('.stat-humidity');
                var visEl = card.querySelector('.stat-vis');
                var feelsEl = card.querySelector('.stat-feels');

                if (tempEl) tempEl.textContent = now.temp + '°';
                if (cityEl) cityEl.textContent = locName;
                if (statusEl) statusEl.textContent = now.text;
                if (timeEl) timeEl.textContent = new Date().toLocaleTimeString('zh-CN', { hour: '2-digit', minute: '2-digit' });
                if (iconEl) iconEl.className = 'qi-' + now.icon + '-fill lgnewui-home-weather-icon-main';
                if (humEl) humEl.textContent = now.humidity + '%';
                if (visEl) visEl.textContent = now.vis + 'km';
                if (feelsEl) feelsEl.textContent = now.feelsLike + '°';
            });
        }).catch(function() {});
    }

    /* ---- 统计数字动画 ---- */
    function initStatsAnimation() {
        var nums = document.querySelectorAll('.lgnewui-stats-num, .lgnewui-runtime-num, .lgnewui-lovelist-completed, .lgnewui-lovelist-total, .lgnewui-traffic-value');
        if (!nums.length) return;

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateNumber(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.3 });

        nums.forEach(function(el) { observer.observe(el); });
    }

    function animateNumber(el) {
        var target = parseInt(el.textContent.replace(/[^0-9]/g, ''));
        if (isNaN(target) || target === 0) return;
        var duration = 1200;
        var start = 0;
        var startTime = null;

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var eased = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            var current = Math.floor(eased * target);
            el.textContent = current.toLocaleString();
            if (progress < 1) requestAnimationFrame(step);
        }

        el.textContent = '0';
        requestAnimationFrame(step);
    }

    /* ---- 清单进度条动画 ---- */
    function initProgressAnimation() {
        var bars = document.querySelectorAll('.lgnewui-progress__bar');
        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var bar = entry.target;
                    var width = bar.style.width;
                    bar.style.width = '0%';
                    setTimeout(function() { bar.style.width = width; }, 100);
                    observer.unobserve(bar);
                }
            });
        }, { threshold: 0.3 });
        bars.forEach(function(el) { observer.observe(el); });
    }

    /* ---- 初始化 ---- */
    window.initLGHome = function(config) {
        config = config || {};
        initDayCounter(config.startTime || '2022-06-05T00:07');
        initWeather(config.weatherToken);
        initStatsAnimation();
        initProgressAnimation();
    };
})();
