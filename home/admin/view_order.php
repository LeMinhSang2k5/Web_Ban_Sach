<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "Không tìm thấy đơn hàng.";
    exit();
}
$order_code = $_GET['id'];
$stmt = $conn->prepare("SELECT orders.*, users.username, users.email FROM orders JOIN users ON orders.user_id = users.id WHERE orders.order_code = ?");
$stmt->bind_param("s", $order_code);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo "Không tìm thấy đơn hàng.";
    exit();
}
$order = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
    <h1>Chi tiết đơn hàng #<?= htmlspecialchars($order['order_code']) ?></h1>
    <p><strong>Khách hàng:</strong> <?= htmlspecialchars($order['username']) ?> (<?= htmlspecialchars($order['email']) ?>)</p>
    <p><strong>Ngày đặt:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
    <p><strong>Tổng tiền:</strong> <?= number_format($order['total_amount'], 0, ',', '.') ?> đ</p>
    <p><strong>Trạng thái:</strong> <?= htmlspecialchars($order['order_status']) ?></p>
    <!-- Nếu có bảng order_items, có thể hiển thị chi tiết sản phẩm ở đây -->
    <a href="orders.php">← Quay lại danh sách đơn hàng</a>
</body>

</html>