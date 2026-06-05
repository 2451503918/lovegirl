/**
 * LG-NewUi 首页应用
 * 基于深度研究演示站实现
 */

(function() {
    'use strict';
    
    window.initLGHomeApp = function() {
        console.log('%c LG-NewUi Home App Initializing... ', 'color: #fff; background: linear-gradient(135deg, #667eea, #764ba2); padding: 5px 10px; border-radius: 3px; font-weight: bold;');
        
        // 初始化各个模块
        initSmartMediaCard();
        initWeatherCards();
        initLoveDayCounter();
        initLoveListModule();
        initQuickActions();
        initRandomQuote();
        initAccessBeacon();
        initConfettiEffect();
        
        console.log('%c LG-NewUi Home App Initialized! ', 'color: #fff; background: linear-gradient(135deg, #667eea, #764ba2); padding: 5px 10px; border-radius: 3px; font-weight: bold;');
    };
    
    // 1. 智能媒体卡片（时光碎片）
    function initSmartMediaCard() {
        const card = document.getElementById('moment-card');
        if (!card) return;
        
        const switchBtn = card.querySelector('.lgnewui-smart-card__switch-btn');
        if (switchBtn) {
            switchBtn.addEventListener('click', function() {
                // 添加切换动画
                card.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    card.style.transform = '';
                    // 这里可以切换不同的时光碎片内容
                    updateMomentContent();
                }, 150);
            });
        }
    }
    
    function updateMomentContent() {
        // 模拟内容切换
        const titles = ['时光碎片', '美好回忆', '珍贵瞬间', '甜蜜时刻'];
        const descriptions = ['记录每一个闪光的瞬间', '收藏我们的美好回忆', '每一刻都值得被珍藏', '甜蜜的时光总是那么美好'];
        const randomIndex = Math.floor(Math.random() * titles.length);
        
        const titleEl = document.querySelector('.lgnewui-smart-card__title');
        const descEl = document.querySelector('.lgnewui-smart-card__desc');
        
        if (titleEl) titleEl.textContent = titles[randomIndex];
        if (descEl) descEl.textContent = descriptions[randomIndex];
    }
    
    // 2. 天气卡片
    function initWeatherCards() {
        const weatherCards = document.querySelectorAll('[data-weather-slot]');
        weatherCards.forEach(card => {
            const slot = card.getAttribute('data-weather-slot');
            loadWeatherData(slot, card);
        });
        
        // 定时刷新天气（5分钟）
        setInterval(() => {
            weatherCards.forEach(card => {
                const slot = card.getAttribute('data-weather-slot');
                loadWeatherData(slot, card);
            });
        }, 5 * 60 * 1000);
    }
    
    function loadWeatherData(slot, card) {
        fetch(`services/weather.php?mode=couple&slot=${slot}`)
            .then(res => res.json())
            .then(data => {
                if (data.code === 200 && data.data) {
                    updateWeatherCard(card, data.data);
                }
            })
            .catch(err => console.log('Weather load error:', err));
    }
    
    function updateWeatherCard(card, weather) {
        // 更新温度
        const tempEl = card.querySelector('.lgnewui-home-weather-text-temp');
        if (tempEl) tempEl.textContent = weather.temp + '°';
        
        // 更新城市
        const cityEl = card.querySelector('.lgnewui-home-weather-text-city');
        if (cityEl) cityEl.textContent = weather.city;
        
        // 更新天气状态
        const statusEl = card.querySelector('.lgnewui-home-weather-text-status');
        if (statusEl) statusEl.textContent = weather.text;
        
        // 更新湿度
        const humidityEl = card.querySelector('.stat-humidity');
        if (humidityEl) humidityEl.textContent = weather.humidity + '%';
        
        // 更新能见度
        const visEl = card.querySelector('.stat-vis');
        if (visEl) visEl.textContent = weather.vis + 'km';
        
        // 更新体感温度
        const feelsEl = card.querySelector('.stat-feels-like');
        if (feelsEl) feelsEl.textContent = weather.feelsLike + '°';
        
        // 更新时间
        const timeEl = card.querySelector('.lgnewui-home-weather-time-tag');
        if (timeEl) {
            const now = new Date();
            timeEl.textContent = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
        }
    }
    
    // 3. 恋爱天数计数器
    function initLoveDayCounter(options) {
        const startTime = options?.startTime || '2022-06-05T00:07';
        const startDate = new Date(startTime.replace('T', ' '));
        
        function updateCounter() {
            const now = new Date();
            const diff = now - startDate;
            
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);
            
            // 更新天数
            const dayEl = document.getElementById('lgnewui-day-counter-days');
            if (dayEl) {
                animateNumber(dayEl, days);
            }
            
            // 更新时分秒
            const hourEl = document.getElementById('lgnewui-day-counter-hours');
            const minuteEl = document.getElementById('lgnewui-day-counter-minutes');
            const secondEl = document.getElementById('lgnewui-day-counter-seconds');
            
            if (hourEl) hourEl.textContent = hours.toString().padStart(2, '0');
            if (minuteEl) minuteEl.textContent = minutes.toString().padStart(2, '0');
            if (secondEl) secondEl.textContent = seconds.toString().padStart(2, '0');
        }
        
        updateCounter();
        setInterval(updateCounter, 1000);
    }
    
    function animateNumber(element, targetNumber) {
        const currentNumber = parseInt(element.textContent) || 0;
        if (currentNumber === targetNumber) return;
        
        element.textContent = targetNumber;
        element.style.transform = 'scale(1.1)';
        element.style.transition = 'transform 0.2s ease';
        setTimeout(() => {
            element.style.transform = '';
        }, 200);
    }
    
    // 4. 恋爱清单模块
    function initLoveListModule() {
        const listCards = document.querySelectorAll('.lgnewui-love-list-card');
        listCards.forEach(card => {
            card.addEventListener('click', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 200);
            });
        });
    }
    
    // 5. 快捷操作
    function initQuickActions() {
        const quickBtns = document.querySelectorAll('.lgnewui-quick-action');
        quickBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                // 添加点击波纹效果
                createRipple(e, this);
            });
        });
    }
    
    function createRipple(event, element) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            left: ${x}px;
            top: ${y}px;
            background: rgba(255,255,255,0.4);
            border-radius: 50%;
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
        `;
        
        element.style.position = 'relative';
        element.style.overflow = 'hidden';
        element.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    }
    
    // 6. 随机一言
    function initRandomQuote() {
        const quoteBtn = document.querySelector('.lgnewui-quote-refresh-btn');
        if (quoteBtn) {
            quoteBtn.addEventListener('click', loadRandomQuote);
        }
        loadRandomQuote();
    }
    
    function loadRandomQuote() {
        fetch('services/random_quote.php')
            .then(res => res.json())
            .then(data => {
                if (data.code === 200 && data.data) {
                    updateQuote(data.data);
                }
            })
            .catch(err => console.log('Quote load error:', err));
    }
    
    function updateQuote(quote) {
        const quoteEl = document.querySelector('.lgnewui-quote-text');
        const authorEl = document.querySelector('.lgnewui-quote-author');
        
        if (quoteEl) {
            quoteEl.style.opacity = '0';
            quoteEl.style.transform = 'translateY(-10px)';
            quoteEl.style.transition = 'all 0.3s ease';
            
            setTimeout(() => {
                quoteEl.textContent = quote.text;
                quoteEl.style.opacity = '1';
                quoteEl.style.transform = '';
            }, 300);
        }
        
        if (authorEl) authorEl.textContent = quote.author ? '—— ' + quote.author : '';
    }
    
    // 7. 访客追踪
    function initAccessBeacon() {
        window.AccessBeacon = {
            init: function(siteId, userId) {
                console.log('Access Beacon initialized', { siteId, userId });
                // 这里可以添加访问统计逻辑
            }
        };
    }
    
    // 8. 礼花效果
    function initConfettiEffect() {
        window.ConfettiEffect = {
            init: function() {
                console.log('Confetti Effect ready');
            },
            fire: function(options = {}) {
                // 简单的礼花效果
                const colors = ['#ff6b6b', '#ffd93d', '#6bcb77', '#4d96ff', '#ff6bd6'];
                const container = document.body;
                const count = options.count || 50;
                
                for (let i = 0; i < count; i++) {
                    setTimeout(() => {
                        createConfetti(container, colors[Math.floor(Math.random() * colors.length)]);
                    }, i * 30);
                }
            }
        };
        
        function createConfetti(container, color) {
            const confetti = document.createElement('div');
            confetti.style.cssText = `
                position: fixed;
                width: ${Math.random() * 10 + 5}px;
                height: ${Math.random() * 10 + 5}px;
                background: ${color};
                left: ${Math.random() * 100}vw;
                top: -20px;
                border-radius: ${Math.random() > 0.5 ? '50%' : '0'};
                z-index: 99999;
                pointer-events: none;
            `;
            
            container.appendChild(confetti);
            
            const animation = confetti.animate([
                { 
                    transform: 'translateY(0) rotate(0deg)', 
                    opacity: 1 
                },
                { 
                    transform: `translateY(${window.innerHeight + 100}px) rotate(${Math.random() * 720}deg)`, 
                    opacity: 0 
                }
            ], {
                duration: 2000 + Math.random() * 1000,
                easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
            });
            
            animation.onfinish = () => confetti.remove();
        }
    }
    
    // 添加CSS动画
    const style = document.createElement('style');
    style.textContent = `
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
    
})();
