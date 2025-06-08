<?php
// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Thông tin kết nối database
$host = "localhost";
$username = "root";  // Thay đổi thành root vì đây là user mặc định của XAMPP
$password = "";      // Mặc định XAMPP không có password
$database = "mydb";

// Tạo kết nối
$conn = mysqli_connect($host, $username, $password, $database);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");

?>