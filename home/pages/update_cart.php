<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
    // Validate input
    if ($book_id <= 0 || $quantity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        exit;
    }

    // Cập nhật trong session
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as &$item) {
            if (isset($item['book_id']) && $item['book_id'] == $book_id) {
                $item['quantity'] = $quantity;
                break;
            }
            // Kiểm tra cả trường hợp sử dụng 'id' (cho các item cũ)
            if (isset($item['id']) && $item['id'] == $book_id) {
                $item['book_id'] = $item['id'];
                unset($item['id']);
                $item['quantity'] = $quantity;
                break;
            }
        }
    }

    // Cập nhật trong database nếu đã đăng nhập
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        
        try {
            $conn->begin_transaction();
            
            // Lấy cart_id hiện tại
            $stmt = $conn->prepare("
                SELECT c.id 
                FROM cart c 
                WHERE c.user_id = ? AND c.status = 'active'
                ORDER BY c.created_at DESC 
                LIMIT 1
            ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $cart = $result->fetch_assoc();
                $cart_id = $cart['id'];
                
                // Cập nhật số lượng
                $stmt = $conn->prepare("
                    UPDATE cart_items 
                    SET quantity = ? 
                    WHERE cart_id = ? AND book_id = ?
                ");
                $stmt->bind_param("iii", $quantity, $cart_id, $book_id);
                $stmt->execute();
                
                if ($stmt->affected_rows > 0) {
                    $conn->commit();
                    echo json_encode(['success' => true]);
                    exit;
                } else {
                    $conn->rollback();
                    echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng']);
                    exit;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy giỏ hàng']);
                exit;
            }
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Error updating cart: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi cập nhật giỏ hàng']);
            exit;
        }
    } else {
        echo json_encode(['success' => true]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
?> 