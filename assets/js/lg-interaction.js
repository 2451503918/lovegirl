/**
 * LG-NewUi 交互动效
 * 基于深度研究演示站实现
 */

(function() {
    'use strict';
    
    console.log('%c LG-NewUi Interactions Loaded ', 'color: #fff; background: linear-gradient(135deg, #667eea, #764ba2); padding: 5px 10px; border-radius: 3px;');
    
    window.initLGInteractions = function() {
        initHoverTilt();
        initButtonMorph();
        initCardFloat();
        initScrollAnimations();
        initLoadingAnimations();
        initSmoothScroll();
    };
    
    // 1. 卡片悬停倾斜效果
    function initHoverTilt() {
        const tiltCards = document.querySelectorAll('.lg-tilt-card, .lg-bento-card, .lg-stat-card');
        
        tiltCards.forEach(card => {
            card.addEventListener('mousemove', handleTilt);
            card.addEventListener('mouseleave', resetTilt);
        });
        
        function handleTilt(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            
            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;
            
            this.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale(1.02)`;
            this.style.transition = 'transform 0.1s ease';
        }
        
        function resetTilt() {
            this.style.transform = '';
            this.style.transition = 'transform 0.3s ease';
        }
    }
    
    // 2. 按钮变形效果
    function initButtonMorph() {
        const morphButtons = document.querySelectorAll('.lg-btn--morph, .lg-quick-action');
        
        morphButtons.forEach(btn => {
            btn.addEventListener('mousedown', function() {
                this.style.transform = 'scale(0.95)';
            });
            btn.addEventListener('mouseup', function() {
                this.style.transform = '';
            });
            btn.addEventListener('mouseleave', function() {
                this.style.transform = '';
            });
        });
    }
    
    // 3. 卡片浮动效果
    function initCardFloat() {
        const floatCards = document.querySelectorAll('.lg-float-card');
        
        floatCards.forEach((card, index) => {
            card.style.animation = `lgFloat 3s ease-in-out ${index * 0.2}s infinite`;
        });
        
        if (!document.getElementById('lg-float-style')) {
            const style = document.createElement('style');
            style.id = 'lg-float-style';
            style.textContent = `
                @keyframes lgFloat {
                    0%, 100% { transform: translateY(0px); }
                    50% { transform: translateY(-10px); }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    // 4. 滚动动画
    function initScrollAnimations() {
        const animatedElements = document.querySelectorAll('.lg-animate-on-scroll');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('lg-animate-active');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
        
        animatedElements.forEach(el => observer.observe(el));
    }
    
    // 5. 加载动画
    function initLoadingAnimations() {
        const skeletonLoaders = document.querySelectorAll('.lg-skeleton');
        
        skeletonLoaders.forEach(el => {
            el.classList.add('lg-loading');
        });
        
        window.addEventListener('load', () => {
            setTimeout(() => {
                skeletonLoaders.forEach(el => el.classList.remove('lg-loading'));
            }, 800);
        });
    }
    
    // 6. 平滑滚动
    function initSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
    
    // 工具函数：添加交互动画类
    window.LGAnim = {
        fadeIn: function(el, duration = 300) {
            el.style.opacity = '0';
            el.style.display = '';
            el.style.transition = `opacity ${duration}ms ease`;
            requestAnimationFrame(() => {
                el.style.opacity = '1';
            });
        },
        fadeOut: function(el, duration = 300) {
            el.style.opacity = '1';
            el.style.transition = `opacity ${duration}ms ease`;
            el.style.opacity = '0';
            setTimeout(() => el.style.display = 'none', duration);
        },
        slideUp: function(el, duration = 300) {
            el.style.overflow = 'hidden';
            el.style.transition = `height ${duration}ms ease, opacity ${duration}ms ease`;
            el.style.height = el.offsetHeight + 'px';
            requestAnimationFrame(() => {
                el.style.height = '0';
                el.style.opacity = '0';
            });
            setTimeout(() => {
                el.style.display = 'none';
                el.style.removeProperty('overflow');
                el.style.removeProperty('height');
            }, duration);
        },
        slideDown: function(el, duration = 300) {
            el.style.removeProperty('display');
            el.style.overflow = 'hidden';
            const height = el.scrollHeight;
            el.style.height = '0';
            el.style.opacity = '0';
            el.style.transition = `height ${duration}ms ease, opacity ${duration}ms ease`;
            requestAnimationFrame(() => {
                el.style.height = height + 'px';
                el.style.opacity = '1';
            });
            setTimeout(() => {
                el.style.removeProperty('overflow');
                el.style.removeProperty('height');
            }, duration);
        },
        pulse: function(el, times = 3) {
            let count = 0;
            const interval = setInterval(() => {
                el.style.transform = 'scale(1.05)';
                el.style.boxShadow = '0 0 20px rgba(102, 126, 234, 0.4)';
                setTimeout(() => {
                    el.style.transform = '';
                    el.style.boxShadow = '';
                }, 100);
                count++;
                if (count >= times) clearInterval(interval);
            }, 200);
        }
    };
    
})();
