<?php
/**
 * 音乐代理API - LG-NewUi
 * 处理音乐资源请求
 */

$type = isset($_GET['type']) ? $_GET['type'] : '';

if ($type === 'proxy') {
    $targetUrl = isset($_GET['target']) ? $_GET['target'] : '';
    $expires = isset($_GET['expires']) ? $_GET['expires'] : '';
    $sig = isset($_GET['sig']) ? $_GET['sig'] : '';
    
    if ($targetUrl) {
        // 代理模式 - 实际项目中可以在这里添加安全检查
        header('Content-Type: audio/mpeg');
        header('Content-Disposition: inline');
        
        // 这里简单返回一个示例，实际项目应处理真实音乐文件
        // 为了演示，我们返回404或重定向
        header('HTTP/1.1 302 Found');
        header('Location: ' . $targetUrl);
        exit;
    }
}

// 默认返回
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'code' => 400,
    'message' => 'Invalid music request',
    'timestamp' => time()
], JSON_UNESCAPED_UNICODE);
