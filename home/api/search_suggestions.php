<?php
require_once '../config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Lấy từ khóa tìm kiếm
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

if (empty($query) || strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    // Tìm kiếm gợi ý từ database
    $search_param = "%$query%";
    
    $stmt = $conn->prepare("
        SELECT DISTINCT title, author, id, image, price, old_price, discount
        FROM books 
        WHERE title LIKE ? OR author LIKE ? 
        ORDER BY sold DESC, title ASC 
        LIMIT 8
    ");
    
    $stmt->bind_param("ss", $search_param, $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $suggestions = [];
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'author' => $row['author'],
            'image' => $row['image'],
            'price' => number_format($row['price']),
            'old_price' => $row['old_price'] ? number_format($row['old_price']) : null,
            'discount' => $row['discount']
        ];
    }
    
    echo json_encode($suggestions, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([]);
} 