<?php
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
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");

    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = 'Email này đã được đăng ký!';
        $_SESSION['active_form'] = 'register';
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Email đã được sử dụng! Vui lòng chọn email khác.'
        ];
    } else {
        if($conn->query("INSERT INTO users(username,email,password) VALUES ('$username','$email','$password')")) {
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

    $result = $conn->query("SELECT * FROM users WHERE email='$email'");
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

                    // Tạo giỏ hàng mới
                    $stmt = $conn->prepare("INSERT INTO cart (user_id, status) VALUES (?, 'active')");
                    $stmt->bind_param("i", $user['id']);
                    $stmt->execute();
                    $cart_id = $conn->insert_id;

                    // Thêm từng sản phẩm vào giỏ hàng
                    $stmt = $conn->prepare("
                        INSERT INTO cart_items (cart_id, book_id, quantity, price) 
                        VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
                    ");

                    foreach ($_SESSION['cart'] as $item) {
                        $stmt->bind_param("iiid", $cart_id, $item['id'], $item['quantity'], $item['price']);
                        $stmt->execute();
                    }

                    $conn->commit();
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
    session_destroy();
    session_start();
    $_SESSION['notification'] = [
        'type' => 'warning',
        'message' => 'Đã đăng xuất thành công! Tạm biệt ' . $username
    ];
    header("location: index.php");
    exit();
}
?>
