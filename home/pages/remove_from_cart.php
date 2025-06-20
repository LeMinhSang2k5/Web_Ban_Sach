<?php
session_start();
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    
    // Validate input
    if ($book_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        exit;
    }

    // Xóa khỏi session
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if (isset($item['book_id']) && $item['book_id'] == $book_id) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
                break;
            }
            // Kiểm tra cả trường hợp sử dụng 'id' (cho các item cũ)
            if (isset($item['id']) && $item['id'] == $book_id) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index array
                break;
            }
        }
    }

    // Xóa khỏi database nếu đã đăng nhập
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
                
                // Xóa sản phẩm khỏi giỏ hàng
                $stmt = $conn->prepare("
                    DELETE FROM cart_items 
                    WHERE cart_id = ? AND book_id = ?
                ");
                $stmt->bind_param("ii", $cart_id, $book_id);
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
            error_log("Error removing from cart: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi xóa sản phẩm khỏi giỏ hàng']);
            exit;
        }
    } else {
        echo json_encode(['success' => true]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
?> 