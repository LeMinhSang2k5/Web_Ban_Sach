<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Xóa giỏ hàng từ database nếu user đã đăng nhập
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            
            $conn->begin_transaction();
            
            // Xóa tất cả cart_items của user
            $stmt = $conn->prepare("
                DELETE ci FROM cart_items ci
                JOIN cart c ON ci.cart_id = c.id
                WHERE c.user_id = ? AND c.status = 'active'
            ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            
            $conn->commit();
        }
        
        // Xóa session cart
        $_SESSION['cart'] = [];
        
        echo json_encode([
            'success' => true, 
            'message' => 'Đã xóa toàn bộ giỏ hàng thành công',
            'total_items' => 0
        ]);
        
    } catch (Exception $e) {
        if (isset($conn)) {
            $conn->rollback();
        }
        echo json_encode([
            'success' => false, 
            'message' => 'Có lỗi xảy ra khi xóa giỏ hàng: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
?> 