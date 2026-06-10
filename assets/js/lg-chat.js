/**
 * lg-chat.js — WeChat-style chat replay for the about page
 * Reads data from the chat-data.php endpoint and plays back the conversation.
 */
(function () {
    'use strict';

    var config = window.__CHAT_CONFIG || {};
    var endpoint = config.loadEndpoint || '/services/chat-data.php';

    // State
    var chatData = null;
    var messageIndex = 0;
    var isPlaying = true;
    var isPaused = false;
    var speedMultiplier = 1.0;
    var pendingTimer = null;
    var currentPerspective = 'boy'; // 'boy' or 'girl'
    var chatBox = null;

    // Speed options
    var speedOptions = [0.5, 1.0, 1.5, 2.0];
    var speedIndex = 1;

    // ------------------------------------------------------------------ init
    function init() {
        chatBox = document.getElementById('chatBox');
        if (!chatBox) return;

        bindControls();
        loadData();
    }

    // ------------------------------------------------------------------ data
    function loadData() {
        showLoading(true);

        var xhr = new XMLHttpRequest();
        xhr.open('GET', endpoint, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            showLoading(false);
            if (xhr.status === 200) {
                try {
                    var res = JSON.parse(xhr.responseText);
                    if (res.code === 0 && res.data) {
                        chatData = res.data;
                        applyHeaderInfo();
                        showWrapper();
                        startPlayback();
                    } else {
                        showError('数据加载失败');
                    }
                } catch (e) {
                    showError('数据解析失败');
                }
            } else {
                showError('网络错误');
            }
        };
        xhr.send();
    }

    function showLoading(show) {
        var el = document.getElementById('page-loading');
        if (el) el.style.display = show ? 'flex' : 'none';
    }

    function showError(msg) {
        chatBox.innerHTML = '<div style="text-align:center;padding:40px;color:#999;">' + msg + '</div>';
        showWrapper();
    }

    function showWrapper() {
        var w = document.querySelector('.lgnewui-chat-wrapper');
        if (w) w.style.opacity = '1';
    }

    // ------------------------------------------------------------------ header
    function applyHeaderInfo() {
        var avatarEl = document.getElementById('headerAvatar');
        var nameEl = document.getElementById('headerName');
        var statusEl = document.querySelector('.lgnewui-chat-user-status');

        var primaryAvatar = currentPerspective === 'boy' ? chatData.boyAvatar : chatData.girlAvatar;
        var primaryName = currentPerspective === 'boy' ? chatData.boyName : chatData.girlName;

        if (avatarEl) avatarEl.src = primaryAvatar;
        if (nameEl) nameEl.textContent = primaryName + ' & ' + (currentPerspective === 'boy' ? chatData.girlName : chatData.boyName);
        if (statusEl) statusEl.innerHTML = '<span class="lgnewui-chat-status-dot"></span> 回忆连接中...';
    }

    // ------------------------------------------------------------------ playback
    function startPlayback() {
        messageIndex = 0;
        chatBox.innerHTML = '';
        isPlaying = true;
        isPaused = false;
        updatePlayBtn();
        playNext();
    }

    function playNext() {
        if (!chatData || !chatData.messages) return;
        if (messageIndex >= chatData.messages.length) {
            // Finished
            isPlaying = false;
            updatePlayBtn();
            var statusEl = document.querySelector('.lgnewui-chat-user-status');
            if (statusEl) statusEl.innerHTML = '<span class="lgnewui-chat-status-dot"></span> 回忆播放完毕';
            return;
        }
        if (isPaused) return;

        var msg = chatData.messages[messageIndex];
        var delay = (msg.delay || 600) / speedMultiplier;

        pendingTimer = setTimeout(function () {
            if (isPaused) return;
            renderMessage(msg);
            messageIndex++;
            playNext();
        }, delay);
    }

    // ------------------------------------------------------------------ render
    function renderMessage(msg) {
        switch (msg.type) {
            case 'bot':
                appendBubble(msg);
                break;
            case 'system':
                appendSystem(msg);
                break;
            case 'button':
                appendButtons(msg);
                break;
        }
        scrollToBottom();
    }

    function appendBubble(msg) {
        var isSelf = isSelfMessage(msg);
        var row = document.createElement('div');
        row.className = 'lgnewui-chat-row' + (isSelf ? ' self' : '');

        var avatarHtml = '<img class="lgnewui-chat-msg-avatar" src="' + escapeHtml(msg.avatar || '') + '" alt="">';
        var nameHtml = '<div class="lgnewui-chat-msg-name">' + escapeHtml(msg.name || '') + '</div>';
        var bubbleHtml = '<div class="lgnewui-chat-bubble">' + escapeHtml(msg.content) + '</div>';

        row.innerHTML = avatarHtml +
            '<div class="lgnewui-chat-bubble-wrap">' + nameHtml + bubbleHtml + '</div>';

        chatBox.appendChild(row);
    }

    function appendSystem(msg) {
        var row = document.createElement('div');
        row.className = 'lgnewui-chat-system-row';
        row.innerHTML = '<span class="lgnewui-chat-system-text">' + escapeHtml(msg.content) + '</span>';
        chatBox.appendChild(row);
    }

    function appendButtons(msg) {
        if (!msg.options || !msg.options.length) return;

        var row = document.createElement('div');
        row.className = 'lgnewui-chat-btn-row';

        msg.options.forEach(function (opt) {
            var btn = document.createElement('button');
            btn.textContent = opt.text;
            btn.addEventListener('click', function () {
                // Mark selected
                row.querySelectorAll('button').forEach(function (b) {
                    b.classList.remove('selected');
                });
                btn.classList.add('selected');

                // If rejected, show a farewell
                if (opt.value === 'reject') {
                    appendSystem({ content: '对方已退出聊天...' });
                    scrollToBottom();
                    isPlaying = false;
                    updatePlayBtn();
                    return;
                }

                // Resume playback after button click
                messageIndex++;
                playNext();
            });
            row.appendChild(btn);
        });

        chatBox.appendChild(row);
        scrollToBottom();

        // Pause playback until user clicks
        // (playNext will be called from button click handler)
    }

    function isSelfMessage(msg) {
        // The "self" side depends on perspective
        if (currentPerspective === 'boy') {
            return msg.name === chatData.girlName;
        }
        return msg.name === chatData.boyName;
    }

    // ------------------------------------------------------------------ controls
    function bindControls() {
        var playBtn = document.getElementById('playBtn');
        var speedBtn = document.getElementById('speedBtn');
        var skipBtn = document.getElementById('skipBtn');
        var resetBtn = document.querySelector('[title="重新开始"]');
        var perspectiveBtn = document.getElementById('perspectiveBtn');
        var menuToggle = document.getElementById('menuToggle');
        var dropdown = document.getElementById('dropdownMenu');

        if (playBtn) playBtn.addEventListener('click', togglePause);
        if (speedBtn) speedBtn.addEventListener('click', cycleSpeed);
        if (skipBtn) skipBtn.addEventListener('click', skipAnimation);
        if (resetBtn) resetBtn.addEventListener('click', function () {
            if (pendingTimer) clearTimeout(pendingTimer);
            startPlayback();
        });
        if (perspectiveBtn) perspectiveBtn.addEventListener('click', togglePerspective);

        if (menuToggle && dropdown) {
            menuToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });
            document.addEventListener('click', function () {
                dropdown.classList.remove('show');
            });
        }
    }

    function togglePause() {
        isPaused = !isPaused;
        updatePlayBtn();
        if (!isPaused && isPlaying) {
            playNext();
        }
    }

    function updatePlayBtn() {
        var btn = document.getElementById('playBtn');
        if (!btn) return;
        var icon = btn.querySelector('i');
        if (!icon) return;
        if (isPaused || !isPlaying) {
            icon.className = 'ph-fill ph-play';
        } else {
            icon.className = 'ph-fill ph-pause';
        }
    }

    function cycleSpeed() {
        speedIndex = (speedIndex + 1) % speedOptions.length;
        speedMultiplier = speedOptions[speedIndex];
        var textEl = document.querySelector('.lgnewui-chat-speed-btn-text');
        if (textEl) textEl.textContent = speedMultiplier + 'x';
    }

    function skipAnimation() {
        if (!chatData || !chatData.messages) return;
        if (pendingTimer) clearTimeout(pendingTimer);

        // Render all remaining messages instantly
        while (messageIndex < chatData.messages.length) {
            var msg = chatData.messages[messageIndex];
            // Skip buttons by auto-accepting
            if (msg.type === 'button') {
                // Auto-select first option
                appendButtons(msg);
                var btns = chatBox.querySelectorAll('.lgnewui-chat-btn-row:last-child button');
                if (btns.length) btns[0].classList.add('selected');
            } else {
                renderMessage(msg);
            }
            messageIndex++;
        }
        isPlaying = false;
        updatePlayBtn();
        scrollToBottom();
    }

    function togglePerspective() {
        currentPerspective = currentPerspective === 'boy' ? 'girl' : 'boy';
        if (pendingTimer) clearTimeout(pendingTimer);
        applyHeaderInfo();
        startPlayback();
    }

    // ------------------------------------------------------------------ scroll
    function scrollToBottom() {
        if (chatBox) {
            requestAnimationFrame(function () {
                chatBox.scrollTop = chatBox.scrollHeight;
            });
        }
    }

    // ------------------------------------------------------------------ util
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    // ------------------------------------------------------------------ boot
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose resetDemo for the menu
    window.resetDemo = function () {
        if (pendingTimer) clearTimeout(pendingTimer);
        startPlayback();
    };

    // Expose export stubs
    window.toggleMute = function () { /* stub */ };
    window.switchTheme = function () { /* stub */ };
    window.exportChat = function () { /* stub */ };

})();
