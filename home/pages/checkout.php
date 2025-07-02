<?php
include 'config.php';

// Lấy giỏ hàng
$cart_items = [];
$total = 0;

if (isset($_SESSION['user_id'])) {
    // Lấy giỏ hàng từ database cho user đã đăng nhập
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("
        SELECT c.id as cart_id, ci.book_id, ci.quantity, ci.price, 
               b.title, b.image, b.author, b.publisher
        FROM cart c
        JOIN cart_items ci ON c.id = ci.cart_id
        JOIN books b ON ci.book_id = b.id
        WHERE c.user_id = ? AND c.status = 'active'
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = $row;
        $total += $row['price'] * $row['quantity'];
    }
} else {
    // Lấy giỏ hàng từ session cho khách chưa đăng nhập
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $cart_items[] = $item;
            $total += $item['price'] * $item['quantity'];
        }
    }
}



// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $customer_name = trim($_POST['customer_name']);
    $customer_email = trim($_POST['customer_email']);
    $customer_phone = trim($_POST['customer_phone']);
    $shipping_address = trim($_POST['shipping_address']);
    $payment_method = $_POST['payment_method'];
    $notes = trim($_POST['notes'] ?? '');
    
    // Validate dữ liệu
    $errors = [];
    if (empty($customer_name)) $errors[] = "Vui lòng nhập họ tên";
    if (empty($customer_email) || !filter_var($customer_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ";
    if (empty($customer_phone)) $errors[] = "Vui lòng nhập số điện thoại";
    if (empty($shipping_address)) $errors[] = "Vui lòng nhập địa chỉ giao hàng";
    if (empty($cart_items)) $errors[] = "Giỏ hàng trống";
    
    if (empty($errors)) {
        try {
            $conn->begin_transaction();
            
            // Tạo đơn hàng
            $order_code = 'ORD' . date('YmdHis') . rand(100, 999);
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            
            $stmt = $conn->prepare("
                INSERT INTO orders (order_code, user_id, customer_name, customer_email, customer_phone, 
                                   shipping_address, payment_method, total_amount, notes, order_status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
            ");
            $stmt->bind_param("sissssids", $order_code, $user_id, $customer_name, $customer_email, 
                            $customer_phone, $shipping_address, $payment_method, $total, $notes);
            $stmt->execute();
            $order_id = $conn->insert_id;
            
            // Thêm chi tiết đơn hàng
            $stmt = $conn->prepare("
                INSERT INTO order_items (order_id, book_id, quantity, price, subtotal) 
                VALUES (?, ?, ?, ?, ?)
            ");
            
            foreach ($cart_items as $item) {
                $book_id = isset($item['book_id']) ? $item['book_id'] : $item['id'];
                $subtotal = $item['price'] * $item['quantity'];
                $stmt->bind_param("iiidd", $order_id, $book_id, $item['quantity'], $item['price'], $subtotal);
                $stmt->execute();
            }
            
            // Xóa giỏ hàng sau khi đặt hàng thành công
            if (isset($_SESSION['user_id'])) {
                // Xóa cart trong database
                $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND status = 'active'");
                $stmt->bind_param("i", $_SESSION['user_id']);
                $stmt->execute();
            } else {
                // Xóa session cart
                unset($_SESSION['cart']);
            }
            
            $conn->commit();
            
            $_SESSION['notification'] = [
                'type' => 'success',
                'message' => 'Đặt hàng thành công! Mã đơn hàng: ' . $order_code
            ];
            header("Location: index.php");
            exit();
            
        } catch (Exception $e) {
            $conn->rollback();
            error_log("Lỗi đặt hàng: " . $e->getMessage());
            $errors[] = "Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại.";
        }
    }
}
?>

<link rel="stylesheet" href="assets/css/checkout.css">

<div class="checkout-container">
    <div class="checkout-header">
        <h1><i class="fas fa-shopping-cart"></i> THANH TOÁN</h1>
        <a href="index.php?page=cart" class="back-to-cart">← Quay lại giỏ hàng</a>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="error-messages">
        <?php foreach ($errors as $error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (empty($cart_items)): ?>
    <div class="empty-cart-checkout">
        <i class="fas fa-shopping-cart fa-3x"></i>
        <p>Giỏ hàng của bạn đang trống</p>
        <a href="index.php" class="continue-shopping">Tiếp tục mua sắm</a>
    </div>
    <?php else: ?>
    
    <form method="POST" class="checkout-form">
        <div class="checkout-content">
            <div class="checkout-left">
                <div class="customer-info">
                    <h2>Thông tin khách hàng</h2>
                    
                    <?php if (isLoggedIn()): ?>
                    <div class="logged-in-info">
                        <p><strong>Đang đặt hàng với tài khoản:</strong> <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                    </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="customer_name">Họ và tên *</label>
                        <input type="text" id="customer_name" name="customer_name" 
                               value="<?php echo isLoggedIn() ? htmlspecialchars($_SESSION['username']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_email">Email *</label>
                        <input type="email" id="customer_email" name="customer_email" 
                               value="<?php echo isLoggedIn() ? htmlspecialchars($_SESSION['email']) : ''; ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="customer_phone">Số điện thoại *</label>
                        <input type="tel" id="customer_phone" name="customer_phone" required>
                    </div>
                </div>

                <div class="shipping-info">
                    <h2>Thông tin giao hàng</h2>
                    
                    <div class="form-group">
                        <label for="shipping_address">Địa chỉ giao hàng *</label>
                        <textarea id="shipping_address" name="shipping_address" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="notes">Ghi chú đơn hàng</label>
                        <textarea id="notes" name="notes" rows="2" placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."></textarea>
                    </div>
                </div>

                <div class="payment-info">
                    <h2>Phương thức thanh toán</h2>
                    
                    <div class="payment-methods">
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <span class="method-name">
                                <i class="fas fa-money-bill-wave"></i>
                                Thanh toán khi nhận hàng (COD)
                            </span>
                        </label>
                        
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="bank_transfer">
                            <span class="method-name">
                                <i class="fas fa-university"></i>
                                Chuyển khoản ngân hàng
                            </span>
                        </label>
                        
                        <label class="payment-method">
                            <input type="radio" name="payment_method" value="momo">
                            <span class="method-name">
                                <i class="fas fa-mobile-alt"></i>
                                Ví điện tử MoMo
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="checkout-right">
                <div class="order-summary">
                    <h2>Tóm tắt đơn hàng</h2>
                    
                    <div class="order-items">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="order-item">
                            <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                 class="item-image">
                            <div class="item-details">
                                <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                <p class="item-meta">
                                    Tác giả: <?php echo htmlspecialchars($item['author'] ?? 'Không có'); ?><br>
                                    NXB: <?php echo htmlspecialchars($item['publisher'] ?? 'Không có'); ?>
                                </p>
                                <div class="item-price">
                                    <span class="quantity">x<?php echo $item['quantity']; ?></span>
                                    <span class="price"><?php echo number_format($item['price'], 0, ',', '.'); ?>đ</span>
                                </div>
                            </div>
                            <div class="item-subtotal">
                                <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="order-total">
                        <div class="total-row">
                            <span>Tạm tính:</span>
                            <span id="subtotal"><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                        </div>
                        <div class="total-row">
                            <span>Phí vận chuyển:</span>
                            <span>Miễn phí</span>
                        </div>
                        <div class="total-row final-total">
                            <span>Tổng cộng:</span>
                            <span id="total"><?php echo number_format($total, 0, ',', '.'); ?>đ</span>
                        </div>
                    </div>
                    
                    <button type="submit" name="place_order" class="place-order-btn">
                        <i class="fas fa-check"></i> ĐẶT HÀNG
                    </button>
                    
                    <div class="order-terms">
                        <p><small>Bằng việc đặt hàng, bạn đồng ý với <a href="#">Điều khoản sử dụng</a> của chúng tôi.</small></p>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadCartSummary();
    
    // Validation form
    const form = document.querySelector('.checkout-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Vui lòng điền đầy đủ thông tin bắt buộc');
            }
        });
    }
});

function loadCartSummary() {
    // Load cart items from session/localStorage
    // This is a simplified version - you might want to fetch from server
    const orderItems = document.querySelector('.order-items');
    orderItems.innerHTML = '<p>Đang tải thông tin đơn hàng...</p>';
}
</script> 