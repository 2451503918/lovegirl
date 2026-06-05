/**
 * LG-NewUi 地图模块
 * 基于深度研究演示站实现
 */

(function() {
    'use strict';
    
    let mapInitialized = false;
    let footprints = [];
    
    window.initLGMap = function() {
        console.log('%c LG-NewUi Map Module Loaded ', 'color: #fff; background: linear-gradient(135deg, #667eea, #764ba2); padding: 5px 10px; border-radius: 3px;');
        
        // 初始化示例足迹数据
        footprints = [
            { id: 1, name: '相遇地点', lat: 31.2304, lng: 121.4737, city: '上海', date: '2022-06-05', color: '#ff6b6b', description: '第一次相遇的地方' },
            { id: 2, name: '约会城市', lat: 39.9042, lng: 116.4074, city: '北京', date: '2022-10-01', color: '#4ecdc4', description: '第一次一起旅行' },
            { id: 3, name: '浪漫之旅', lat: 25.0330, lng: 121.5654, city: '台北', date: '2023-02-14', color: '#ffe66d', description: '情人节特别旅行' },
            { id: 4, name: '海边回忆', lat: 22.5431, lng: 114.0579, city: '深圳', date: '2023-08-20', color: '#95e1d3', description: '夏天的海边约会' },
            { id: 5, name: '山城打卡', lat: 29.4316, lng: 106.9123, city: '重庆', date: '2024-01-01', color: '#a8e6cf', description: '跨年旅行' },
            { id: 6, name: '彩云之南', lat: 25.0395, lng: 102.7103, city: '昆明', date: '2024-05-20', color: '#ffd93d', description: '520表白旅行' }
        ];
        
        createMapContainer();
        loadFootprints();
        bindEvents();
    };
    
    function createMapContainer() {
        const mapContainer = document.getElementById('lg-map-container');
        if (!mapContainer) {
            // 创建地图容器（如果不存在）
            const container = document.createElement('div');
            container.id = 'lg-map-container';
            container.className = 'lg-map-container';
            container.style.cssText = `
                position: relative;
                width: 100%;
                height: 400px;
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
                border-radius: 20px;
                overflow: hidden;
            `;
            
            // 添加中国地图背景（简单模拟）
            const mapBg = document.createElement('div');
            mapBg.className = 'lg-map-bg';
            mapBg.innerHTML = `
                <svg viewBox="0 0 400 300" style="width: 100%; height: 100%;">
                    <defs>
                        <linearGradient id="mapGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#667eea;stop-opacity:0.3"/>
                            <stop offset="100%" style="stop-color:#764ba2;stop-opacity:0.3"/>
                        </linearGradient>
                    </defs>
                    <path d="M100,80 Q150,60 200,80 Q250,70 300,90 L320,150 Q300,200 250,220 L200,240 Q150,230 100,210 L80,150 Z" 
                          fill="url(#mapGradient)" stroke="#667eea" stroke-width="1" opacity="0.5"/>
                </svg>
            `;
            container.appendChild(mapBg);
            
            // 添加足迹点容器
            const markersContainer = document.createElement('div');
            markersContainer.id = 'lg-map-markers';
            markersContainer.className = 'lg-map-markers';
            container.appendChild(markersContainer);
            
            // 添加信息面板
            const infoPanel = document.createElement('div');
            infoPanel.id = 'lg-map-info';
            infoPanel.className = 'lg-map-info';
            infoPanel.style.cssText = `
                position: absolute;
                bottom: 20px;
                left: 20px;
                right: 20px;
                background: rgba(255,255,255,0.95);
                backdrop-filter: blur(10px);
                border-radius: 12px;
                padding: 16px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                display: none;
            `;
            container.appendChild(infoPanel);
            
            // 查找合适位置插入地图
            const articleList = document.querySelector('.article-list');
            if (articleList && articleList.parentNode) {
                articleList.parentNode.insertBefore(container, articleList);
            } else if (document.getElementById('main-container')) {
                document.getElementById('main-container').appendChild(container);
            } else {
                document.body.appendChild(container);
            }
        }
    }
    
    function loadFootprints() {
        const markersContainer = document.getElementById('lg-map-markers');
        if (!markersContainer) return;
        
        markersContainer.innerHTML = '';
        
        footprints.forEach((footprint, index) => {
            const marker = document.createElement('div');
            marker.className = 'lg-map-marker';
            marker.dataset.id = footprint.id;
            marker.style.cssText = `
                position: absolute;
                left: ${20 + (index * 15)}%;
                top: ${20 + (index % 3) * 25}%;
                width: 40px;
                height: 40px;
                background: ${footprint.color};
                border-radius: 50%;
                border: 3px solid white;
                box-shadow: 0 5px 15px rgba(0,0,0,0.3);
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 18px;
                animation: mapPulse 2s ease-in-out infinite;
                transition: transform 0.3s;
            `;
            marker.innerHTML = '<i class="ph-fill ph-heart"></i>';
            marker.title = footprint.name;
            
            marker.addEventListener('click', () => showFootprintInfo(footprint));
            marker.addEventListener('mouseenter', () => {
                marker.style.transform = 'scale(1.2)';
            });
            marker.addEventListener('mouseleave', () => {
                marker.style.transform = '';
            });
            
            markersContainer.appendChild(marker);
        });
        
        // 添加地图动画CSS
        if (!document.getElementById('lg-map-style')) {
            const style = document.createElement('style');
            style.id = 'lg-map-style';
            style.textContent = `
                @keyframes mapPulse {
                    0%, 100% { box-shadow: 0 5px 15px rgba(0,0,0,0.3), 0 0 0 0 rgba(102, 126, 234, 0.4); }
                    50% { box-shadow: 0 5px 15px rgba(0,0,0,0.3), 0 0 0 15px rgba(102, 126, 234, 0); }
                }
            `;
            document.head.appendChild(style);
        }
    }
    
    function showFootprintInfo(footprint) {
        const infoPanel = document.getElementById('lg-map-info');
        if (!infoPanel) return;
        
        infoPanel.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: ${footprint.color}; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                    <i class="ph-fill ph-map-pin"></i>
                </div>
                <div>
                    <div style="font-weight: 700; font-size: 16px; color: #333;">${footprint.name}</div>
                    <div style="font-size: 13px; color: #888;">${footprint.city} · ${footprint.date}</div>
                </div>
            </div>
            <div style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #eee; font-size: 14px; color: #666;">
                ${footprint.description}
            </div>
        `;
        
        infoPanel.style.display = 'block';
        infoPanel.style.animation = 'fadeInUp 0.3s ease';
    }
    
    function bindEvents() {
        // 添加足迹按钮
        const addBtn = document.getElementById('lg-add-footprint');
        if (addBtn) {
            addBtn.addEventListener('click', addFootprint);
        }
    }
    
    window.addFootprint = function() {
        const name = prompt('请输入地点名称：');
        if (!name) return;
        
        const city = prompt('请输入城市：');
        if (!city) return;
        
        const newFootprint = {
            id: Date.now(),
            name: name,
            city: city,
            date: new Date().toISOString().split('T')[0],
            color: `hsl(${Math.random() * 360}, 70%, 60%)`,
            description: '新添加的足迹'
        };
        
        footprints.push(newFootprint);
        loadFootprints();
        
        if (window.ConfettiEffect) {
            window.ConfettiEffect.fire({ count: 50 });
        }
        
        alert('足迹添加成功！');
    };
    
    window.LGMap = {
        getFootprints: function() { return footprints; },
        addFootprint: addFootprint,
        showFootprintInfo: showFootprintInfo,
        refresh: loadFootprints
    };
    
})();
