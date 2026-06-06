<?php

session_start();

/*
 * @Page：自定义函数方法
 * @Version：Like Girl 5.2.1-Stable
 * @Author: Ki.
 * @Date: 2025-09-03 00:00:00
 * @LastEditTime: 2025-09-03
 * @Description: 愿得一心人 白头不相离
 * @Document：https://blog.kikiw.cn/index.php/archives/52/
 * @Copyright (c) 2023 - 2025 by Ki All Rights Reserved. 
 * @Warning：禁止以任何方式出售本项目 如有发现一切后果自行负责
 * @Warning：禁止以任何方式出售本项目 如有发现一切后果自行负责
 * @Warning：禁止以任何方式出售本项目 如有发现一切后果自行负责
 * @Message：开发不易 版权信息请保留 （删除/更改版权的无耻之人请勿使用 查到一个挂一个）
 * @Message：开发不易 版权信息请保留 （删除/更改版权的无耻之人请勿使用 查到一个挂一个）
 * @Message：开发不易 版权信息请保留 （删除/更改版权的无耻之人请勿使用 查到一个挂一个）
 */
 
$Filter_IP = $_SERVER['REMOTE_ADDR'];

function checkQQ($qq)
{
    if (preg_match("/^[1-9][0-9]{4,}$/", $qq)) {
        return true;
    } else {
        return false;
    }
}

function replaceSpecialChar($str)
{
    $filter = "/[\\'\"\\\`;]/"; 
    return preg_replace($filter, '', $str);
}


function escapeXSS($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function time_tran($time)
{
    $text = '';
    if (!$time) {
        return $text;
    }
    $current = time();
    $t = $current - $time;
    if ($t < 0) {
        $text = date('Y-m-d', $time);
    } elseif ($t < 60) {
        $text = $t . '秒前';
    } elseif ($t < 3600) {
        $text = floor($t / 60) . '分钟前';
    } elseif ($t < 86400) {
        $text = floor($t / 3600) . '小时前';
    } elseif ($t < 2592000) {
        $text = floor($t / 86400) . '天前';
    } elseif ($t < 31536000) {
        $text = floor($t / 2592000) . '月前';
    } else {
        $text = floor($t / 31536000) . '年前';
    }
    return $text;
}

function get_ip_city_New($ip)
{
    // 验证IP格式
    if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
        return '未知';
    }

    // 保留地址直接返回
    if ($ip === '127.0.0.1' || $ip === '::1') {
        return '本机';
    }

    $ch = curl_init();
    $url = 'https://ip9.com.cn/get?ip=' . urlencode($ip);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // 检查HTTP请求是否成功
    if ($httpCode !== 200 || empty($response)) {
        return '未知';
    }

    $data = json_decode($response, true);

    if (isset($data['ret']) && $data['ret'] == 200 && isset($data['data'])) {
        $location = '';
        if (!empty($data['data']['country']) && $data['data']['country'] !== '保留') {
            $location = $data['data']['country'];
        }
        if (!empty($data['data']['prov'])) {
            $location .= ($location ? ' ' : '') . $data['data']['prov'];
        }
        if (!empty($data['data']['city']) && $data['data']['city'] !== $data['data']['prov']) {
            $location .= ' ' . $data['data']['city'];
        }
        return $location ?: '未知';
    }
    return '未知';
}
