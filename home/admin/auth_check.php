<?php
// Đảm bảo session đã được start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include config để có function isAdmin()
require_once('../config.php');

// Kiểm tra xem user đã đăng nhập và có quyền admin chưa
function checkAdminAuth() {
    // Kiểm tra đăng nhập
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        $_SESSION['notification'] = [
            'type' => 'error',
            'message' => 'Vui lòng đăng nhập để truy cập trang quản trị!'
        ];
        header("Location: ../index.php");
        exit();
    }
    
    // Kiểm tra quyền admin
    if (!isAdmin($_SESSION['user_id'])) {
        $_SESSION['notification'] = [
            'type' => 'error',  
            'message' => 'Bạn không có quyền truy cập trang quản trị!'
        ];
        header("Location: ../index.php");
        exit();
    }
}

// Hàm đăng xuất admin (chuyển về trang chính)
function adminLogout() {
    $username = $_SESSION['username'];
    
    // Backup cart trước khi logout nếu có
    $temp_cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    
    // Chỉ unset các session của user, giữ lại cart
    unset($_SESSION['username'], $_SESSION['email'], $_SESSION['user_id']);
    
    // Khôi phục cart
    $_SESSION['cart'] = $temp_cart;
    
    $_SESSION['notification'] = [
        'type' => 'success',
        'message' => 'Admin đã đăng xuất thành công! Tạm biệt ' . $username
    ];
    
    header("Location: ../index.php");
    exit();
}

// Kiểm tra yêu cầu đăng xuất
if (isset($_POST['logout'])) {
    adminLogout();
}

// Hàm lấy thông tin admin hiện tại
function getCurrentAdmin() {
    if (isset($_SESSION['user_id']) && isAdmin($_SESSION['user_id'])) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'email' => $_SESSION['email']
        ];
    }
    return null;
}
?> 