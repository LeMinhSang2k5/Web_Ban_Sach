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

    // Get book information from database
    $stmt = $conn->prepare("SELECT id, title, price, image, author, publisher, discount, old_price FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if (!$book) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy sách']);
        exit;
    }

    // Initialize cart if not exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if book already in cart
    $book_exists = false;
    foreach ($_SESSION['cart'] as &$item) {
        if (isset($item['book_id']) && $item['book_id'] == $book_id) {
            $item['quantity'] += $quantity;
            $book_exists = true;
            break;
        }
    }

    // If book not in cart, add it
    if (!$book_exists) {
        $_SESSION['cart'][] = [
            'book_id' => $book['id'],
            'title' => $book['title'],
            'price' => $book['price'],
            'image' => $book['image'],
            'author' => $book['author'],
            'publisher' => $book['publisher'],
            'discount' => $book['discount'],
            'old_price' => $book['old_price'],
            'quantity' => $quantity
        ];
    }

    // Calculate total items in cart
    $total_items = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }

    // Add to database if user is logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Get active cart for user or create new one
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
            } else {
                // Create new cart
                $stmt = $conn->prepare("INSERT INTO cart (user_id, status) VALUES (?, 'active')");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $cart_id = $conn->insert_id;
            }
            
            // Try to update existing cart item
            $stmt = $conn->prepare("
                INSERT INTO cart_items (cart_id, book_id, quantity, price) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                quantity = quantity + VALUES(quantity)
            ");
            $stmt->bind_param("iiid", $cart_id, $book_id, $quantity, $book['price']);
            $stmt->execute();
            
            // Commit transaction
            $conn->commit();
            
        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            error_log("Error adding to cart: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm vào giỏ hàng']);
            exit;
        }
    }

    echo json_encode([
        'success' => true, 
        'message' => 'Đã thêm sách vào giỏ hàng thành công',
        'total_items' => $total_items
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
?> 