<?php
header('Content-Type: application/json; charset=utf-8');

$input = file_get_contents('php://input');
$order = json_decode($input, true);

if ($order) {
    $file = 'orders.json';
    $orders = file_exists($file) ? json_decode(file_get_contents($file), true) : [];
    if (!is_array($orders)) $orders = [];
    
    // إضافة الطلب الجديد في البداية
    array_unshift($orders, $order);
    
    file_put_contents($file, json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
}
?>
