<?php
header('Content-Type: application/json; charset=utf-8');
$file = 'reviews.json';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data && !empty($data['name']) && !empty($data['comment'])) {
    $reviews = [];
    if (file_exists($file)) {
        $json_data = file_get_contents($file);
        $reviews = json_decode($json_data, true);
        if (!is_array($reviews)) {
            $reviews = [];
        }
    }

    $newReview = [
        'name' => htmlspecialchars($data['name']),
        'rating' => intval($data['rating'] ?? 5),
        'comment' => htmlspecialchars($data['comment']),
        'date' => date('Y-m-d H:i')
    ];

    array_unshift($reviews, $newReview);
    file_put_contents($file, json_encode($reviews, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    
    echo json_encode(['status' => 'success', 'message' => 'تم حفظ التقييم بنجاح']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'بيانات غير مكتملة']);
}
