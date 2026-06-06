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
        // 验证目标URL，仅允许同域或白名单域名
        $allowedHosts = ['music.163.com', 'interface.music.163.com', 'interface3.music.163.com'];
        $parsedUrl = parse_url($targetUrl);
        $targetHost = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $isAllowed = false;
        
        // 允许同域重定向
        if (isset($parsedUrl['host']) && $parsedUrl['host'] === $_SERVER['HTTP_HOST']) {
            $isAllowed = true;
        }
        
        // 允许白名单域名
        foreach ($allowedHosts as $allowedHost) {
            if ($targetHost === $allowedHost || (substr($targetHost, -strlen('.' . $allowedHost)) === '.' . $allowedHost)) {
                $isAllowed = true;
                break;
            }
        }
        
        if (!$isAllowed) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'code' => 403,
                'message' => 'Redirect to this domain is not allowed',
                'timestamp' => time()
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // 代理模式
        header('Content-Type: audio/mpeg');
        header('Content-Disposition: inline');
        
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
