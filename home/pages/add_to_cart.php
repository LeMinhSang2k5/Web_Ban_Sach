<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    
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

    // Chia trường hợp đăng nhập và không đăng nhập
    if (isset($_SESSION['user_id'])) {
        // ===== TRƯỜNG HỢP ĐÃ ĐĂNG NHẬP =====
        $user_id = $_SESSION['user_id'];
        
        try {
            $conn->begin_transaction();            
            // Lấy hoặc tạo cart cho user
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
                // Tạo cart mới
                $stmt = $conn->prepare("INSERT INTO cart (user_id, status) VALUES (?, 'active')");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $cart_id = $conn->insert_id;
            }
            
            // Thêm hoặc cập nhật item trong database
            $stmt = $conn->prepare("
                INSERT INTO cart_items (cart_id, book_id, quantity, price) 
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                quantity = quantity + VALUES(quantity)
            ");
            $stmt->bind_param("iiid", $cart_id, $book_id, $quantity, $book['price']);
            $stmt->execute();
            
            // Đồng bộ session cart với database cart
            $stmt = $conn->prepare("
                SELECT ci.book_id, ci.quantity, ci.price,
                       b.title, b.image, b.author, b.publisher, b.discount, b.old_price
                FROM cart_items ci
                JOIN cart c ON ci.cart_id = c.id
                JOIN books b ON ci.book_id = b.id
                WHERE c.user_id = ? AND c.status = 'active'
            ");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $_SESSION['cart'] = [];
            while ($row = $result->fetch_assoc()) {
                $_SESSION['cart'][] = [
                    'book_id' => $row['book_id'],
                    'title' => $row['title'],
                    'price' => $row['price'],
                    'image' => $row['image'],
                    'author' => $row['author'],
                    'publisher' => $row['publisher'],
                    'discount' => $row['discount'],
                    'old_price' => $row['old_price'],
                    'quantity' => $row['quantity']
                ];
            }
            
            $conn->commit();
            
        } catch (Exception $e) {
            $conn->rollback();
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm vào giỏ hàng: ' . $e->getMessage()]);
            exit;
        }
        
    } else {
        // ===== TRƯỜNG HỢP CHƯA ĐĂNG NHẬP =====

        // Initialize cart if not exists
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if book already in cart
        $book_exists = false;
        foreach ($_SESSION['cart'] as &$item) {
            $item_book_id = isset($item['book_id']) ? $item['book_id'] : (isset($item['id']) ? $item['id'] : null);
            
            if ($item_book_id && $item_book_id == $book_id) {
                $item['quantity'] += $quantity;
                $book_exists = true;
                break;
            }
        }
        // ✅ QUAN TRỌNG: Xóa tham chiếu để tránh bug trùng lặp
        unset($item);

        // If book not in cart, add it
        if (!$book_exists) {
            $new_item = [
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
            $_SESSION['cart'][] = $new_item;
        }
    }

    // Calculate total items in cart
    $total_items = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total_items += $item['quantity'];
    }

    echo json_encode([
        'success' => true, 
        'message' => 'Đã thêm sách vào giỏ hàng thành công',
        'total_items' => $total_items,
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}
?> 