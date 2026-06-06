<?php
header('Content-Type: application/json');

include_once 'admin/connect.php';
include_once 'admin/Function.php';

$page = isset($_POST['page']) ? intval($_POST['page']) : 1;
$limit = isset($_POST['limit']) ? intval($_POST['limit']) : 6;
$offset = ($page - 1) * $limit;

// 查询总数
$totalRes = $connect->query("SELECT COUNT(*) as total FROM photo WHERE private = 0");
$total = $totalRes->fetch_assoc()['total'];

// 预处理分页查询（v5.2.1: photo 表）
$stmt = $connect->prepare("SELECT img, `desc`, title, date FROM photo WHERE private = 0 ORDER BY id DESC LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'img' => $row['img'],
        'date' => $row['date'],
        'text' => $row['desc']
    ];
}

echo json_encode([
    'code' => 200,
    'data' => $data,
    'total' => $total,
    'page' => $page,
    'limit' => $limit
]);