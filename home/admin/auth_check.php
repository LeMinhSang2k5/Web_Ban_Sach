<?php
session_start();

// Kiểm tra xem admin đã đăng nhập chưa
function checkAdminAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: index.php");
        exit();
    }
}

// Hàm đăng xuất admin
function adminLogout() {
    // Xóa tất cả session admin
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_email']);
    
    // Chuyển hướng về trang login
    header("Location: index.php");
    exit();
}

// Kiểm tra yêu cầu đăng xuất
if (isset($_POST['logout'])) {
    adminLogout();
}

// Hàm lấy thông tin admin hiện tại
function getCurrentAdmin() {
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'],
            'email' => $_SESSION['admin_email']
        ];
    }
    return null;
}
?> 