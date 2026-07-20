<?php
// 允许所有来源（如果您和前端同域，这行可以保留）
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 如果是预检请求，直接结束
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') exit;

$file = __DIR__ . '/rent_data.json';

// GET 请求 → 返回当前数据
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (file_exists($file)) {
        header('Content-Type: application/json');
        echo file_get_contents($file);
    } else {
        echo '{}';  // 如果文件不存在，返回空对象
    }
    exit;
}

// POST 请求 → 保存新数据
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    if ($data === null) {
        http_response_code(400);
        echo json_encode(['error' => '数据格式错误']);
        exit;
    }
    // 写入文件（先写临时文件，再重命名，防止写入中断导致数据损坏）
    $tmp = $file . '.tmp';
    file_put_contents($tmp, json_encode($data, JSON_PRETTY_PRINT));
    if (rename($tmp, $file)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => '保存失败']);
    }
    exit;
}

// 其他请求返回 405
http_response_code(405);
echo json_encode(['error' => '不支持的请求方法']);
?>
