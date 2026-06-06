<?php
session_start();

// 检查登录状态
if (!isset($_SESSION['loginadmin']) || empty($_SESSION['loginadmin'])) {
    echo json_encode(['code' => 0, 'msg' => '未登录']);
    exit;
}

// 检查是否有文件上传
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['code' => 0, 'msg' => '请选择图片']);
    exit;
}

$file = $_FILES['image'];

// 检查文件大小 (最大5MB)
$maxSize = 5 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    echo json_encode(['code' => 0, 'msg' => '图片大小不能超过5MB']);
    exit;
}

// 检查文件类型
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, $allowedTypes)) {
    echo json_encode(['code' => 0, 'msg' => '只支持 JPG、PNG、GIF、WebP 格式']);
    exit;
}

// 检查文件扩展名白名单
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
if (!in_array($fileExt, $allowedExtensions)) {
    echo json_encode(['code' => 0, 'msg' => '不允许的文件类型']);
    exit;
}

// 生成唯一文件名
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = date('YmdHis') . '_' . uniqid() . '.' . $ext;

// 上传目录
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$filepath = $uploadDir . $filename;

// 移动文件
if (move_uploaded_file($file['tmp_name'], $filepath)) {
    // 返回相对路径
    $url = 'admin/uploads/' . $filename;
    echo json_encode(['code' => 1, 'msg' => '上传成功', 'url' => $url]);
} else {
    echo json_encode(['code' => 0, 'msg' => '上传失败']);
}
?>