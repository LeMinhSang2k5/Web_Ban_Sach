<?php
// Chỉ start session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sử dụng include_once để tránh include nhiều lần
require_once('db.php');

// Hàm kiểm tra đăng nhập
if (!function_exists('isLoggedIn')) {
    function isLoggedIn() {
        return isset($_SESSION['username']) && !empty($_SESSION['username']);
    }
}

// Hàm lấy và xóa thông báo
if (!function_exists('getNotification')) {
    function getNotification() {
        if (isset($_SESSION['notification'])) {
            $notification = $_SESSION['notification'];
            unset($_SESSION['notification']);
            return $notification;
        }
        return null;
    }
}

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = 'user';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Sử dụng prepared statement
    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $checkEmail = $stmt->get_result();

    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = 'Email này đã được đăng ký!';
        $_SESSION['active_form'] = 'register';
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Email đã được sử dụng! Vui lòng chọn email khác.'
        ];
            } else {
        $stmt = $conn->prepare("INSERT INTO users(username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $role);
        if($stmt->execute()) {
            // Lấy ID của user vừa tạo
            $user_id = $conn->insert_id;
            
            // Tạo giỏ hàng mới cho user
            $stmt = $conn->prepare("INSERT INTO cart (user_id, status) VALUES (?, 'active')");
            $stmt->bind_param("i", $user_id);
            
            if($stmt->execute()) {
                $_SESSION['notification'] = [
                    'type' => 'success',
                    'message' => 'Đăng ký thành công! Vui lòng đăng nhập.'
                ];
                $_SESSION['active_form'] = 'login';
            } else {
                // Nếu không tạo được giỏ hàng, vẫn cho phép đăng ký thành công
                error_log("Không thể tạo giỏ hàng cho user mới: " . $conn->error);
                $_SESSION['notification'] = [
                    'type' => 'success',
                    'message' => 'Đăng ký thành công! Vui lòng đăng nhập.'
                ];
                $_SESSION['active_form'] = 'login';
            }
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Có lỗi xảy ra! Vui lòng thử lại sau.'
            ];
            $_SESSION['active_form'] = 'register';
        }
    }
    header("location:index.php");
    exit();
}

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Sử dụng prepared statement để tránh SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_id'] = $user['id'];

            // Chuyển giỏ hàng từ session vào database
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                try {
                    $conn->begin_transaction();

                    // Lấy hoặc tạo giỏ hàng
                    $stmt = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND status = 'active' ORDER BY created_at DESC LIMIT 1");
                    $stmt->bind_param("i", $user['id']);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result->num_rows > 0) {
                        $cart = $result->fetch_assoc();
                        $cart_id = $cart['id'];
                    } else {
                        // Tạo giỏ hàng mới nếu chưa có
                        $stmt = $conn->prepare("INSERT INTO cart (user_id, status) VALUES (?, 'active')");
                        $stmt->bind_param("i", $user['id']);
                        $stmt->execute();
                        $cart_id = $conn->insert_id;
                    }

                    // Thêm từng sản phẩm vào giỏ hàng
                    $stmt = $conn->prepare("
                        INSERT INTO cart_items (cart_id, book_id, quantity, price) 
                        VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
                    ");

                    foreach ($_SESSION['cart'] as $item) {
                        // Kiểm tra key để đảm bảo tính nhất quán
                        $book_id_to_use = isset($item['book_id']) ? $item['book_id'] : $item['id'];
                        $stmt->bind_param("iiid", $cart_id, $book_id_to_use, $item['quantity'], $item['price']);
                        $stmt->execute();
                    }

                    $conn->commit();
                    
                    // Đồng bộ lại session cart với database cart
                    $stmt = $conn->prepare("
                        SELECT ci.book_id, ci.quantity, ci.price,
                               b.title, b.image, b.author, b.publisher, b.discount, b.old_price
                        FROM cart_items ci
                        JOIN books b ON ci.book_id = b.id
                        WHERE ci.cart_id = ?
                    ");
                    $stmt->bind_param("i", $cart_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    // Chỉ cập nhật cart từ database, không reset hoàn toàn
                    $db_cart = [];
                    while ($row = $result->fetch_assoc()) {
                        $db_cart[] = [
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
                    $_SESSION['cart'] = $db_cart;
                } catch (Exception $e) {
                    $conn->rollback();
                    error_log("Lỗi khi chuyển giỏ hàng: " . $e->getMessage());
                }
            }

            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Đăng nhập thành công! Chào mừng ' . $user['username']
            ];
            header("location: index.php");
            exit();
        }
    }
    $_SESSION['login_error'] = 'Email hoặc mật khẩu không chính xác';
    $_SESSION['notification'] = [
        'type' => 'error',
        'message' => 'Email hoặc mật khẩu không chính xác!'
    ];
    $_SESSION['active_form'] = 'login';
    header("location:index.php");
    exit();
}

// Xử lý đăng xuất
if (isset($_POST['logout'])) {
    $username = $_SESSION['username'];
    
    // Backup cart trước khi logout nếu có
    $temp_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    
    // Chỉ unset các session của user, giữ lại cart
    unset($_SESSION['username'], $_SESSION['email'], $_SESSION['user_id']);
    
    // Khôi phục cart
    $_SESSION['cart'] = $temp_cart;
    
    $_SESSION['notification'] = [
        'type' => 'warning',
        'message' => 'Đã đăng xuất thành công! Tạm biệt ' . $username
    ];
    header("location: index.php");
    exit();
}
?>
