<?php
ob_start();
include 'config.php';

// ===== Helper Functions =====
function validateOrder($post, $cart_items) {
    $errors = [];
    if (empty(trim($post['customer_name'] ?? '')))
        $errors[] = "Vui lòng nhập họ tên";
    if (empty(trim($post['customer_email'] ?? '')) || !filter_var($post['customer_email'], FILTER_VALIDATE_EMAIL))
        $errors[] = "Email không hợp lệ";
    if (empty(trim($post['customer_phone'] ?? '')))
        $errors[] = "Vui lòng nhập số điện thoại";
    if (empty(trim($post['shipping_address'] ?? '')))
        $errors[] = "Vui lòng nhập địa chỉ giao hàng";
    if (empty($cart_items))
        $errors[] = "Giỏ hàng trống";
    return $errors;
}

function placeOrder($conn, $post, $cart_items, $checkout_items) {
    $total = 0;
    foreach ($cart_items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    $order_code = 'ORD' . date('YmdHis') . rand(100, 999);
    $user_id = $_SESSION['user_id'] ?? null;
    $customer_name = trim($post['customer_name']);
    $customer_email = trim($post['customer_email']);
    $customer_phone = trim($post['customer_phone']);
    $shipping_address = trim($post['shipping_address']);
    $payment_method = $post['payment_method'];
    $notes = trim($post['notes'] ?? '');
    try {
        $conn->begin_transaction();
        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (order_code, user_id, customer_name, customer_email, customer_phone, shipping_address, payment_method, total_amount, notes, order_status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("sissssids", $order_code, $user_id, $customer_name, $customer_email, $customer_phone, $shipping_address, $payment_method, $total, $notes);
        $stmt->execute();
        $order_id = $conn->insert_id;
        // Insert order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
        foreach ($cart_items as $item) {
            $book_id = $item['book_id'] ?? $item['id'];
            $subtotal = $item['price'] * $item['quantity'];
            $stmt->bind_param("iiidd", $order_id, $book_id, $item['quantity'], $item['price'], $subtotal);
            $stmt->execute();
        }
        clearCartAfterOrder($conn, $checkout_items);
        unset($_SESSION['checkout_items']);
        $conn->commit();
        return [
            'success' => true,
            'notification' => [
                'type' => 'success',
                'message' => 'Đặt hàng thành công! Mã đơn hàng: ' . $order_code
            ]
        ];
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Lỗi đặt hàng: " . $e->getMessage());
        return [
            'success' => false,
            'error' => "Có lỗi xảy ra khi đặt hàng. Vui lòng thử lại."
        ];
    }
}

function clearCartAfterOrder($conn, $checkout_items) {
    // Xóa các sản phẩm đã đặt khỏi session cart
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $index => $item) {
            if (in_array($item['book_id'], $checkout_items)) {
                unset($_SESSION['cart'][$index]);
            }
        }
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex lại mảng
    }
    // Xóa các sản phẩm đã đặt khỏi cart_items trong database nếu đã đăng nhập
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        if (!empty($checkout_items)) {
            $placeholders = implode(',', array_fill(0, count($checkout_items), '?'));
            $types = str_repeat('i', count($checkout_items));
            $stmt = $conn->prepare("DELETE ci FROM cart_items ci JOIN cart c ON ci.cart_id = c.id WHERE c.user_id = ? AND c.status = 'active' AND ci.book_id IN ($placeholders)");
            $bind_params = array_merge([$user_id], $checkout_items);
            $stmt->bind_param('i' . $types, ...$bind_params);
            $stmt->execute();
        }
    }
}

// ===== Lấy giỏ hàng chỉ gồm các sản phẩm được chọn =====
$checkout_items = $_SESSION['checkout_items'] ?? [];
$cart_items = [];
$total = 0;
if (!empty($checkout_items) && isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        if (in_array($item['book_id'], $checkout_items)) {
            $cart_items[] = $item;
            $total += $item['price'] * $item['quantity'];
        }
    }
}

// ===== Xử lý đặt hàng =====
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $errors = validateOrder($_POST, $cart_items);
    if (empty($errors)) {
        $result = placeOrder($conn, $_POST, $cart_items, $checkout_items);
        if ($result['success']) {
            $_SESSION['notification'] = $result['notification'];
            header('Location: index.php');
            exit();
        } else {
            $errors[] = $result['error'];
        }
    }
}
?>

<link rel="stylesheet" href="assets/css/checkout.css">

<div class="checkout-container">
    <div class="content-checkout">
        <div class="checkout-header">
            <h1>THANH TOÁN</h1>
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
                                    <p><strong>Đang đặt hàng với tài khoản:</strong>
                                        <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label for="customer_name">Họ và tên *</label>
                                <input type="text" id="customer_name" name="customer_name"
                                    value="<?php echo isLoggedIn() ? htmlspecialchars($_SESSION['username']) : ''; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="customer_email">Email *</label>
                                <input type="email" id="customer_email" name="customer_email"
                                    value="<?php echo isLoggedIn() ? htmlspecialchars($_SESSION['email']) : ''; ?>" required>
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
                                <textarea id="notes" name="notes" rows="2"
                                    placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."></textarea>
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
                            <div id="bank-info" style="display:none; margin-top:10px;">
                                <div style="display: flex; align-items: center; gap: 32px;">
                                    <div style="min-width:220px;">
                                        <strong>Thông tin chuyển khoản:</strong><br>
                                        Ngân hàng: ACB<br>
                                        Số tài khoản: 33939597<br>
                                        Chủ tài khoản: Lê Minh Sang<br>
                                        Nội dung: [Mã đơn hàng]<br>
                                    </div>
                                    <div style="margin-top:0;">
                                        <img id="bank-qr" src="https://img.vietqr.io/image/ACB-33939597-compact2.png?amount=<?php echo $total; ?>&addInfo=Thanh%20toan%20don%20hang" alt="QR chuyển khoản" style="max-width:200px;display:block;">
                                        <div style="font-size:12px; color:#888; text-align:center;">Quét mã QR để chuyển khoản nhanh</div>
                                    </div>
                                </div>
                            </div>
                            <div id="momo-info" style="display:none; margin-top:10px;">
                                <div style="display: flex; align-items: center; gap: 32px;">
                                    <div style="min-width:220px;">
                                        <strong>Ví MoMo:</strong><br>
                                        Số điện thoại: 0901234567<br>
                                        Chủ ví: Nguyễn Văn A<br>
                                        Nội dung: [Mã đơn hàng]<br>
                                    </div>
                                    <div style="margin-top:0;">
                                        <img id="momo-qr" src="https://img.vietqr.io/image/MB-0901234567-compact2.png?amount=<?php echo $total; ?>&addInfo=Thanh%20toan%20don%20hang" alt="QR MoMo" style="max-width:200px;display:block;">
                                        <div style="font-size:12px; color:#888; text-align:center;">Quét mã QR để thanh toán MoMo</div>
                                    </div>
                                </div>
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
                                            alt="<?php echo htmlspecialchars($item['title']); ?>" class="item-image">
                                        <div class="item-details">
                                            <h4><?php echo htmlspecialchars($item['title']); ?></h4>
                                            <p class="item-meta">
                                                Tác giả: <?php echo htmlspecialchars($item['author'] ?? 'Không có'); ?><br>
                                                NXB: <?php echo htmlspecialchars($item['publisher'] ?? 'Không có'); ?>
                                            </p>
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
                                <p><small>Bằng việc đặt hàng, bạn đồng ý với <a href="#">Điều khoản sử dụng</a> của chúng
                                        tôi.</small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        <?php endif; ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Validation form
        const $form = $('.checkout-form');
        if ($form.length) {
            $form.on('submit', function(e) {
                const $requiredFields = $(this).find('[required]');
                let isValid = true;
                
                $requiredFields.each(function() {
                    const $field = $(this);
                    if (!$field.val().trim()) {
                        $field.addClass('error');
                        isValid = false;
                    } else {
                        $field.removeClass('error');
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Vui lòng điền đầy đủ thông tin bắt buộc');
                }
            });
        }
        
        // Hiển thị hướng dẫn thanh toán động
        $('input[name="payment_method"]').on('change', function() {
            const selectedValue = $(this).val();
            $('#bank-info').toggle(selectedValue === 'bank_transfer');
            $('#momo-info').toggle(selectedValue === 'momo');
            $('#bank-qr').toggle(selectedValue === 'bank_transfer');
            $('#momo-qr').toggle(selectedValue === 'momo');
        });
    });

    function loadCartSummary() {
        // Load cart items from session/localStorage
        // This is a simplified version - you might want to fetch from server
        $('.order-items').html('<p>Đang tải thông tin đơn hàng...</p>');
    }
</script>