<?php
// Admin Entry Point
session_start();
require_once('auth_check.php');

// Kiểm tra xác thực admin
checkAdminAuth();

// Kiểm tra xem có parameter page không
$page = $_GET['page'] ?? 'dashboard';

// Routing trong admin
switch($page) {
    case 'dashboard':
    case '':
        include 'Dashboard.php';
        break;
    case 'manage_books':
        include 'manage_books.php';
        break;
    case 'add_book':
        include 'add_book.php';
        break;
    case 'manage_user':
        include 'manage_user.php';
        break;
    case 'manage_categories':
        include 'manage_categories.php';
        break;
    case 'manage_orders':
        include 'orders.php';
        break;
    case 'reports':
        include 'reports.php';
        break;
    default:
        // Nếu không tìm thấy page, redirect về dashboard
        header("Location: index.php?page=dashboard");
        exit();
}
?> 