<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    
    if ($book_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        exit;
    }

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Remove from session cart
    $found = false;
    foreach ($_SESSION['cart'] as $index => $item) {
        $item_book_id = isset($item['book_id']) ? $item['book_id'] : (isset($item['id']) ? $item['id'] : null);
        
        if ($item_book_id && $item_book_id == $book_id) {
            unset($_SESSION['cart'][$index]);
            $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
            $found = true;
            break;
        }
    }

    if (!$found) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng']);
        exit;
    }

    // Remove from database if user is logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        
        try {
            $stmt = $conn->prepare("
                DELETE ci FROM cart_items ci
                JOIN cart c ON ci.cart_id = c.id
                WHERE c.user_id = ? AND ci.book_id = ? AND c.status = 'active'
            ");
            $stmt->bind_param("ii", $user_id, $book_id);
            $stmt->execute();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa khỏi giỏ hàng']);
            exit;
        }
    }

    // Calculate total items in cart
    $total_items = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }

    echo json_encode([
        'success' => true, 
        'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
        'total_items' => $total_items
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
?> 