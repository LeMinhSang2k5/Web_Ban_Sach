<?php
session_start();
require_once('../db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
if (!isset($_GET['id'])) {
    echo 'Không tìm thấy đơn hàng.';
    exit();
}
$order_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param('ii', $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    echo 'Không tìm thấy đơn hàng.';
    exit();
}
$order = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>
    <h1>Chi tiết đơn hàng #<?= $order['id'] ?></h1>
    <p><strong>Ngày đặt:</strong> <?= $order['order_date'] ?></p>
    <p><strong>Tổng tiền:</strong> <?= number_format($order['total'], 0, ',', '.') ?> đ</p>
    <p><strong>Trạng thái:</strong> <?= htmlspecialchars($order['status']) ?></p>
    <!-- Nếu có bảng order_items, có thể hiển thị chi tiết sản phẩm ở đây -->
    <a href="order.php">← Quay lại danh sách đơn hàng</a>
</body>

</html>