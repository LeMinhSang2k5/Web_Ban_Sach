<?php
require_once __DIR__ . '/../config.php';

$order_code = isset($_GET['order_code']) ? trim($_GET['order_code']) : '';
$orders = [];
$order = null;
$order_items = [];
$error = '';

// Nếu có order_code, tìm đơn hàng theo mã
if ($order_code !== '') {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code = ?");
    $stmt->bind_param("s", $order_code);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
        // Lấy chi tiết sản phẩm
        $stmt_items = $conn->prepare("SELECT oi.*, b.title, b.image, b.author, b.publisher FROM order_items oi JOIN books b ON oi.book_id = b.id WHERE oi.order_id = ?");
        $stmt_items->bind_param("i", $order['id']);
        $stmt_items->execute();
        $order_items = $stmt_items->get_result();
    } else {
        $error = 'Không tìm thấy đơn hàng với mã: ' . htmlspecialchars($order_code);
    }
} elseif (isLoggedIn()) {
    // Nếu đã đăng nhập, lấy tất cả đơn hàng của user
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $orders = $stmt->get_result();
}
?>
<link rel="stylesheet" href="assets/css/checkout.css">
<div class="checkout-container">
    <div class="checkout-header">
        <h1><i class="fas fa-truck"></i> Tra cứu đơn hàng</h1>
        <a href="index.php" class="back-to-cart">← Về trang chủ</a>
    </div>
    <form method="GET" class="checkout-form" style="margin-bottom: 32px;" action="index.php">
        <input type="hidden" name="page" value="order_tracking">
        <div class="form-group">
            <label for="order_code">Nhập mã đơn hàng để tra cứu:</label>
            <input type="text" id="order_code" name="order_code" value="<?php echo htmlspecialchars($order_code); ?>" placeholder="Ví dụ: ORD20240601123456" style="max-width:350px;">
            <button type="submit" class="btn" style="margin-left:12px;">Tra cứu</button>
        </div>
    </form>
    <?php if ($error): ?>
        <div class="error-messages">
            <p class="error"><?php echo $error; ?></p>
        </div>
    <?php endif; ?>
    <?php if ($order): ?>
        <div class="order-summary">
            <h2>Thông tin đơn hàng</h2>
            <div class="order-info">
                <p><strong>Mã đơn hàng:</strong> <?php echo htmlspecialchars($order['order_code']); ?></p>
                <p><strong>Khách hàng:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                <p><strong>Địa chỉ giao hàng:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                <p><strong>Phương thức thanh toán:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                <p><strong>Ghi chú:</strong> <?php echo htmlspecialchars($order['notes']); ?></p>
                <p><strong>Trạng thái:</strong> <span style="font-weight:bold;color:#007bff;"> <?php echo htmlspecialchars($order['order_status']); ?> </span></p>
                <p><strong>Ngày đặt:</strong> <?php echo htmlspecialchars($order['created_at']); ?></p>
            </div>
            <h3>Chi tiết sản phẩm</h3>
            <div class="order-items">
                <?php while ($item = $order_items->fetch_assoc()): ?>
                    <div class="order-item">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" class="item-image">
                        <div class="item-details">
                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p class="item-meta">
                                Tác giả: <?php echo htmlspecialchars($item['author'] ?? 'Không có'); ?><br>
                                NXB: <?php echo htmlspecialchars($item['publisher'] ?? 'Không có'); ?>
                            </p>
                        </div>
                        <div class="item-subtotal">
                            SL: <?php echo $item['quantity']; ?> x <?php echo number_format($item['price'], 0, ',', '.'); ?>đ = <b><?php echo number_format($item['subtotal'], 0, ',', '.'); ?>đ</b>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="order-total">
                <div class="total-row final-total">
                    <span>Tổng cộng:</span>
                    <span id="total"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?>đ</span>
                </div>
            </div>
        </div>
    <?php elseif (isLoggedIn() && $orders && $orders->num_rows > 0): ?>
        <div class="order-summary">
            <h2>Đơn hàng của bạn</h2>
            <table style="width:100%;margin-top:16px;border-collapse:collapse;">
                <thead>
                    <tr style="background:#f8f9fa;">
                        <th style="padding:8px;border:1px solid #eee;">Mã đơn</th>
                        <th style="padding:8px;border:1px solid #eee;">Ngày đặt</th>
                        <th style="padding:8px;border:1px solid #eee;">Tổng tiền</th>
                        <th style="padding:8px;border:1px solid #eee;">Trạng thái</th>
                        <th style="padding:8px;border:1px solid #eee;">Xem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $orders->fetch_assoc()): ?>
                        <tr>
                            <td style="padding:8px;border:1px solid #eee;"><?php echo htmlspecialchars($row['order_code']); ?></td>
                            <td style="padding:8px;border:1px solid #eee;"><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td style="padding:8px;border:1px solid #eee;"><?php echo number_format($row['total_amount'], 0, ',', '.'); ?>đ</td>
                            <td style="padding:8px;border:1px solid #eee;"><span style="font-weight:bold;color:#007bff;"> <?php echo htmlspecialchars($row['order_status']); ?> </span></td>
                            <td style="padding:8px;border:1px solid #eee;"><a href="index.php?page=order_tracking&order_code=<?php echo urlencode($row['order_code']); ?>" class="btn">Xem</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (isLoggedIn() && $orders && $orders->num_rows == 0): ?>
        <div class="empty-cart-checkout">
            <i class="fas fa-box"></i>
            <p>Bạn chưa có đơn hàng nào.</p>
            <a href="index.php" class="continue-shopping">Tiếp tục mua sắm</a>
        </div>
    <?php endif; ?>
</div>