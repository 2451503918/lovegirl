/**
 * LG-NewUi 音乐播放器
 * 基于深度研究演示站实现
 */

(function() {
    'use strict';

    function escapeHtml(str) {
        if (str === null || str === undefined) return '';
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    let audioPlayer = null;
    let playlist = [];
    let currentIndex = 0;
    let isPlaying = false;
    
    window.initLGMusicPlayer = function() {
        console.log('%c LG-NewUi Music Player Initializing... ', 'color: #fff; background: linear-gradient(135deg, #667eea, #764ba2); padding: 5px 10px; border-radius: 3px; font-weight: bold;');
        
        loadPlaylist();
        createPlayerUI();
        bindPlayerEvents();
    };
    
    function loadPlaylist() {
        fetch('services/music-player-data.php')
            .then(res => res.json())
            .then(data => {
                if (data.code === 200 && data.data) {
                    playlist = data.data.playlist || [];
                    if (playlist.length > 0) {
                    updatePlaylistUI();
                }
            }
        })
        .catch(err => {
            console.log('Load playlist error:', err);
            // 使用默认播放列表
            playlist = [
                { title: '小幸运', artist: '田馥甄', cover: '', url: '' },
                { title: '简单爱', artist: '周杰伦', cover: '', url: '' }
            ];
        });
    }
    
    function createPlayerUI() {
        // 检查是否已存在播放器
        if (document.getElementById('lg-music-player')) return;
        
        const playerHTML = `
            <div id="lg-music-player" class="lg-music-player lg-music-player--minimized">
                <div class="lg-music-player__toggle" onclick="toggleMusicPlayer()">
                    <i class="ph-fill ph-music-notes-simple"></i>
                </div>
                <div class="lg-music-player__main">
                    <div class="lg-music-player__cover">
                        <div class="lg-music-player__cover-image" id="music-cover"></div>
                        <div class="lg-music-player__cover-spin"></div>
                    </div>
                    <div class="lg-music-player__info">
                        <div class="lg-music-player__title" id="music-title">暂无播放音乐</div>
                        <div class="lg-music-player__artist" id="music-artist">--</div>
                    </div>
                    <div class="lg-music-player__controls">
                        <button class="lg-music-player__btn" onclick="prevMusic()">
                            <i class="ph-bold ph-skip-back"></i>
                        </button>
                        <button class="lg-music-player__btn lg-music-player__btn--play" id="play-btn" onclick="togglePlay()">
                            <i class="ph-fill ph-play" id="play-icon"></i>
                        </button>
                        <button class="lg-music-player__btn" onclick="nextMusic()">
                            <i class="ph-bold ph-skip-forward"></i>
                        </button>
                    </div>
                    <div class="lg-music-player__progress">
                        <div class="lg-music-player__progress-bar" id="progress-bar">
                            <div class="lg-music-player__progress-fill" id="progress-fill"></div>
                        </div>
                        <div class="lg-music-player__time">
                            <span id="current-time">0:00</span>
                            <span id="total-time">0:00</span>
                        </div>
                    </div>
                    <div class="lg-music-player__list" id="playlist-container">
                        <div class="lg-music-player__list-title">播放列表</div>
                        <div class="lg-music-player__list-items" id="playlist-items"></div>
                    </div>
                </div>
            </div>
        `;
        
        const style = document.createElement('style');
        style.textContent = `
            .lg-music-player {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 9998;
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                border-radius: 20px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                overflow: hidden;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            }
            
            .lg-music-player--minimized {
                width: 60px;
                height: 60px;
            }
            
            .lg-music-player--minimized .lg-music-player__main {
                display: none;
            }
            
            .lg-music-player__toggle {
                width: 60px;
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                font-size: 24px;
            }
            
            .lg-music-player__main {
                padding: 20px;
                width: 320px;
            }
            
            .lg-music-player__cover {
                width: 120px;
                height: 120px;
                margin: 0 auto 15px;
                position: relative;
            }
            
            .lg-music-player__cover-image {
                width: 100%;
                height: 100%;
                border-radius: 50%;
                background: linear-gradient(135deg, #667eea, #764ba2);
                position: absolute;
                top: 10px;
                left: 10px;
            }
            
            .lg-music-player__cover-spin {
                position: absolute;
                width: 100%;
                height: 100%;
                top: 10px;
                left: 10px;
                border-radius: 50%;
                border: 3px solid transparent;
                border-top-color: #667eea;
                border-right-color: #764ba2;
                animation: spin 3s linear infinite;
            }
            
            .lg-music-player__info {
                text-align: center;
                margin-bottom: 15px;
            }
            
            .lg-music-player__title {
                font-weight: 700;
                font-size: 16px;
                color: #333;
                margin-bottom: 5px;
            }
            
            .lg-music-player__artist {
                font-size: 13px;
                color: #888;
            }
            
            .lg-music-player__controls {
                display: flex;
                justify-content: center;
                gap: 15px;
                margin-bottom: 15px;
            }
            
            .lg-music-player__btn {
                width: 44px;
                height: 44px;
                border-radius: 50%;
                border: none;
                background: linear-gradient(135deg, #f0f0f0);
                cursor: pointer;
                font-size: 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s;
            }
            
            .lg-music-player__btn:hover {
                transform: scale(1.1);
            }
            
            .lg-music-player__btn--play {
                width: 56px;
                height: 56px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                font-size: 22px;
            }
            
            .lg-music-player__progress {
                margin-bottom: 15px;
            }
            
            .lg-music-player__progress-bar {
                height: 4px;
                background: #e0e0e0;
                border-radius: 2px;
                overflow: hidden;
                margin-bottom: 8px;
                cursor: pointer;
            }
            
            .lg-music-player__progress-fill {
                height: 100%;
                background: linear-gradient(135deg, #667eea, #764ba2);
                width: 0%;
                transition: width 0.3s;
            }
            
            .lg-music-player__time {
                display: flex;
                justify-content: space-between;
                font-size: 12px;
                color: #888;
            }
            
            .lg-music-player__list {
                border-top: 1px solid #eee;
                padding-top: 15px;
            }
            
            .lg-music-player__list-title {
                font-size: 13px;
                color: #666;
                margin-bottom: 10px;
                font-weight: 600;
            }
            
            .lg-music-player__list-items {
                max-height: 150px;
                overflow-y: auto;
            }
            
            @keyframes spin {
                to {
                    transform: rotate(360deg);
                }
            }
            
            .lg-music-player__list-item {
                padding: 8px 10px;
                display: flex;
                align-items: center;
                gap: 10px;
                border-radius: 8px;
                cursor: pointer;
                transition: background 0.2s;
            }
            
            .lg-music-player__list-item:hover {
                background: #f5f5f5;
            }
            
            .lg-music-player__list-item.active {
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
            }
        `;
        
        document.head.appendChild(style);
        
        const container = document.createElement('div');
        container.innerHTML = playerHTML;
        document.body.appendChild(container.firstElementChild);
    }
    
    function bindPlayerEvents() {
        // 播放列表点击事件
        document.addEventListener('click', function(e) {
            if (e.target.closest('.lg-music-player__list-item')) {
                const item = e.target.closest('.lg-music-player__list-item');
                const index = parseInt(item.getAttribute('data-index'));
                playMusic(index);
            }
        });
    }
    
    function updatePlaylistUI() {
        const container = document.getElementById('playlist-items');
        if (!container) return;
        
        container.innerHTML = playlist.map((song, index) =>
            `<div class="lg-music-player__list-item ${index === currentIndex ? 'active' : ''}" data-index="${index}">
                <div style="width: 36px; height: 36px; border-radius: 8px; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white;">
                    <i class="ph-fill ph-music-note-simple"></i>
                </div>
                <div>
                    <div style="font-size: 14px; font-weight: 500;">${escapeHtml(song.title)}</div>
                    <div style="font-size: 12px; color: #888;">${escapeHtml(song.artist)}</div>
                </div>
            </div>`
        ).join('');
    }
    
    window.toggleMusicPlayer = function() {
        const player = document.getElementById('lg-music-player');
        player.classList.toggle('lg-music-player--minimized');
    };
    
    window.togglePlay = function() {
        isPlaying = !isPlaying;
        const playBtn = document.getElementById('play-icon');
        if (playBtn) {
            playBtn.className = isPlaying ? 'ph-fill ph-pause' : 'ph-fill ph-play';
        }
        
        if (playlist.length > 0 && !audioPlayer) {
            if (isPlaying) {
                // audioPlayer.play();
            } else {
                // audioPlayer.pause();
            }
        }
    };
    
    window.playMusic = function(index) {
        currentIndex = index;
        if (playlist.length > 0) {
            const song = playlist[index];
            const titleEl = document.getElementById('music-title');
            const artistEl = document.getElementById('music-artist');
            
            if (titleEl) titleEl.textContent = song.title;
            if (artistEl) artistEl.textContent = song.artist;
            
            updatePlaylistUI();
            isPlaying = true;
        }
    };
    
    window.prevMusic = function() {
        currentIndex = (currentIndex - 1 + playlist.length) % playlist.length;
        playMusic(currentIndex);
    };
    
    window.nextMusic = function() {
        currentIndex = (currentIndex + 1) % playlist.length;
        playMusic(currentIndex);
    };
    
    // lg_love 桥接对象：供 HTML onclick 调用
    window.lg_love = {
        musicToggle: function() {
            var meting = document.querySelector('#nav-music meting-js');
            if (meting && meting.aplayer) {
                meting.aplayer.toggle();
            } else {
                togglePlay();
            }
        },
        musicSkipBack: function() {
            var meting = document.querySelector('#nav-music meting-js');
            if (meting && meting.aplayer) {
                meting.aplayer.skipBack();
            } else {
                prevMusic();
            }
        },
        musicSkipForward: function() {
            var meting = document.querySelector('#nav-music meting-js');
            if (meting && meting.aplayer) {
                meting.aplayer.skipForward();
            } else {
                nextMusic();
            }
        }
    };

})();
