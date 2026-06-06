/**
 * LG-NewUi 右键菜单
 * 基于深度研究演示站实现
 */

(function() {
    'use strict';
    
    let contextMenu = null;
    let isMenuVisible = false;
    
    window.initLGContextMenu = function() {
        console.log('%c LG-NewUi Context Menu Loaded ', 'color: #fff; background: linear-gradient(135deg, #667eea, #764ba2); padding: 5px 10px; border-radius: 3px;');
        
        createContextMenu();
        bindEvents();
    };
    
    function createContextMenu() {
        if (document.getElementById('lg-context-menu')) return;
        
        const menuHTML = `
            <div id="lg-context-menu" class="lg-context-menu">
                <div class="lg-context-menu__item lg-context-menu__item--header">
                    <i class="ph-fill ph-heart"></i>
                    <span>LG-NewUi</span>
                </div>
                <div class="lg-context-menu__divider"></div>
                <div class="lg-context-menu__item" onclick="location.href='index.php'">
                    <i class="ph-bold ph-house"></i>
                    <span>首页</span>
                </div>
                <div class="lg-context-menu__item" onclick="location.href='little.php'">
                    <i class="ph-bold ph-calendar-heart"></i>
                    <span>点点滴滴</span>
                </div>
                <div class="lg-context-menu__item" onclick="location.href='loveImg.php'">
                    <i class="ph-bold ph-images"></i>
                    <span>相册</span>
                </div>
                <div class="lg-context-menu__item" onclick="location.href='list.php'">
                    <i class="ph-bold ph-check-circle"></i>
                    <span>恋爱清单</span>
                </div>
                <div class="lg-context-menu__item" onclick="location.href='leaving.php'">
                    <i class="ph-bold ph-chat-circle-text"></i>
                    <span>留言板</span>
                </div>
                <div class="lg-context-menu__divider"></div>
                <div class="lg-context-menu__item" onclick="location.href='about.php'">
                    <i class="ph-bold ph-info"></i>
                    <span>关于</span>
                </div>
                <div class="lg-context-menu__item" onclick="toggleTheme()">
                    <i class="ph-bold ph-moon-stars" id="theme-icon"></i>
                    <span>切换主题</span>
                </div>
                <div class="lg-context-menu__divider"></div>
                <div class="lg-context-menu__item" onclick="refreshPage()">
                    <i class="ph-bold ph-arrows-clockwise"></i>
                    <span>刷新页面</span>
                </div>
                <div class="lg-context-menu__item" onclick="playConfetti()">
                    <i class="ph-bold ph-confetti"></i>
                    <span>放礼花</span>
                </div>
            </div>
        `;
        
        const style = document.createElement('style');
        style.textContent = `
            .lg-context-menu {
                position: fixed;
                z-index: 99999;
                background: rgba(255, 255, 255, 0.98);
                backdrop-filter: blur(20px);
                border-radius: 16px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
                min-width: 220px;
                padding: 8px 0;
                display: none;
                opacity: 0;
                transform: scale(0.9);
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .lg-context-menu.visible {
                display: block;
                opacity: 1;
                transform: scale(1);
            }
            
            .lg-context-menu__item {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 16px;
                cursor: pointer;
                transition: all 0.2s;
                font-size: 14px;
                color: #333;
            }
            
            .lg-context-menu__item:hover {
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
            }
            
            .lg-context-menu__item--header {
                font-weight: 700;
                padding-bottom: 12px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            
            .lg-context-menu__item--header:hover {
                background: linear-gradient(135deg, #667eea, #764ba2);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            
            .lg-context-menu__divider {
                height: 1px;
                background: linear-gradient(90deg, transparent, #e0e0e0, transparent);
                margin: 6px 0;
            }
            
            .lg-context-menu__item i {
                font-size: 18px;
                width: 20px;
                text-align: center;
            }
        `;
        
        document.head.appendChild(style);
        
        const container = document.createElement('div');
        container.innerHTML = menuHTML;
        document.body.appendChild(container.firstElementChild);
        
        contextMenu = document.getElementById('lg-context-menu');
    }
    
    function bindEvents() {
        document.addEventListener('contextmenu', handleContextMenu);
        document.addEventListener('click', hideMenu);
        document.addEventListener('scroll', hideMenu, { passive: true });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') hideMenu();
        });
    }
    
    function handleContextMenu(e) {
        e.preventDefault();
        
        if (!contextMenu) return;
        
        // 显示菜单
        contextMenu.classList.add('visible');
        
        // 计算位置
        let x = e.clientX;
        let y = e.clientY;
        
        const menuWidth = contextMenu.offsetWidth;
        const menuHeight = contextMenu.offsetHeight;
        
        if (x + menuWidth > window.innerWidth) {
            x = window.innerWidth - menuWidth - 10;
        }
        
        if (y + menuHeight > window.innerHeight) {
            y = window.innerHeight - menuHeight - 10;
        }
        
        contextMenu.style.left = x + 'px';
        contextMenu.style.top = y + 'px';
        
        isMenuVisible = true;
    }
    
    function hideMenu() {
        if (!contextMenu || !isMenuVisible) return;
        
        contextMenu.classList.remove('visible');
        isMenuVisible = false;
    }
    
    window.toggleTheme = function() {
        const icon = document.getElementById('theme-icon');
        if (document.body.classList.toggle('lg-dark-theme')) {
            icon.className = 'ph-bold ph-sun';
        } else {
            icon.className = 'ph-bold ph-moon-stars';
        }
        hideMenu();
    };
    
    window.refreshPage = function() {
        location.reload();
    };
    
    window.playConfetti = function() {
        if (window.ConfettiEffect) {
            window.ConfettiEffect.fire({ count: 100 });
        }
        hideMenu();
    };
    
})();
