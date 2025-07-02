<?php
session_start();
require_once('../db.php');

// Kiểm tra đăng nhập user
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
$user_id = $_SESSION['user_id'];

// Lấy danh sách đơn hàng của user
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Đơn hàng của tôi</title>
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>
    <h1>Đơn hàng của tôi</h1>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['order_date'] ?></td>
                <td><?= number_format($row['total'], 0, ',', '.') ?> đ</td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><a href="order_detail.php?id=<?= $row['id'] ?>">Xem chi tiết</a></td>
            </tr>
        <?php endwhile; ?>
    </table>
    <a href="home.php">← Quay lại trang chủ</a>
</body>

</html>