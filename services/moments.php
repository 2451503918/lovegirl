<?php
/**
 * 动态内容接口 - LG-NewUi
 * 提供首页时光碎片等动态内容
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// 连接数据库
require_once '../admin/connect.php';

$moments = [];

// 获取最新的点滴文章
if ($connect) {
    $result = mysqli_query($connect, "SELECT * FROM little ORDER BY id DESC LIMIT 6");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $moments[] = [
                'id' => $row['id'],
                'title' => $row['title'],
                'content' => mb_substr(strip_tags($row['text']), 0, 100, 'UTF-8'),
                'date' => $row['date'],
                'type' => 'article'
            ];
        }
    }
}

// 如果数据库里没有内容，返回一些示例数据
if (empty($moments)) {
    $moments = [
        [
            'id' => 1,
            'title' => '今天一起去看海',
            'content' => '今天天气真好，我们一起去海边，吹着海风，看着日落...',
            'date' => '2026-04-15',
            'type' => 'article'
        ],
        [
            'id' => 2,
            'title' => '美食探店',
            'content' => '发现了一家超棒的餐厅，菜色很好吃，环境也很浪漫...',
            'date' => '2026-04-10',
            'type' => 'article'
        ]
    ];
}

echo json_encode([
    'code' => 200,
    'data' => $moments,
    'total' => count($moments),
    'timestamp' => time()
], JSON_UNESCAPED_UNICODE);
