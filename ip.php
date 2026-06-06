<?php

include_once 'admin/Function.php';

$ip = filter_var($_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
$time = gmdate("Y-m-d/H:i:s", time() + 8 * 3600);
$gsd = get_ip_city_New($ip);
$file = ".ip.txt";

// 限制日志文件大小（最大 1MB），超过则截断保留最后 50KB
if (file_exists($file) && filesize($file) > 1048576) {
    $content = file_get_contents($file);
    file_put_contents($file, substr($content, -51200), LOCK_EX);
}

$fp = fopen($file, "a");
if ($fp) {
    if (flock($fp, LOCK_EX)) {
        if (filesize($file) === 0) {
            fwrite($fp, "\xEF\xBB\xBF");
        }

        $safe_ip = htmlspecialchars($ip, ENT_QUOTES, 'UTF-8');
        $safe_gsd = htmlspecialchars($gsd, ENT_QUOTES, 'UTF-8');
        $txt = "\n\n" . $safe_ip . "\n" . $safe_gsd . "----" . $time . "\n";
        fputs($fp, $txt);
        flock($fp, LOCK_UN);
    }
    fclose($fp);
}