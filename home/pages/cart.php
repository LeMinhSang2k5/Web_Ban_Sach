<?php
ob_start();
include 'config.php';

// Khởi tạo biến giỏ hàng
$cart_items = [];

// Lấy giỏ hàng từ database nếu đã đăng nhập
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Lấy giỏ hàng active của user
    $stmt = $conn->prepare("
        SELECT c.id as cart_id, ci.book_id, ci.quantity, ci.price,
               b.title, b.image, b.author, b.publisher, b.discount, b.old_price
        FROM cart c
        JOIN cart_items ci ON c.id = ci.cart_id
        JOIN books b ON ci.book_id = b.id
        WHERE c.user_id = ? AND c.status = 'active'
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $cart_items[] = [
            'book_id' => $row['book_id'],
            'title' => $row['title'],
            'price' => $row['price'],
            'image' => $row['image'],
            'author' => $row['author'],
            'publisher' => $row['publisher'],
            'discount' => $row['discount'],
            'old_price' => $row['old_price'],
            'quantity' => $row['quantity']
        ];
    }
} else {
    // Nếu chưa đăng nhập, lấy từ session
    $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    // Đảm bảo consistency trong field name
    if (!empty($cart_items)) {
        foreach ($cart_items as &$item) {
            if (isset($item['id']) && !isset($item['book_id'])) {
                $item['book_id'] = $item['id'];
            }
        }
        // ✅ QUAN TRỌNG: Xóa tham chiếu để tránh bug trùng lặp
        unset($item);
    }
}
?>

<link rel="stylesheet" href="assets/css/cart.css">

<div class="cart-container ">
    <div class="cart-title">
        GIỎ HÀNG CỦA BẠN (<?php echo count($cart_items); ?> sản phẩm)
    </div>

    <?php if (!isLoggedIn()): ?>
    <div class="login-notice">
        <p>Bạn có thể <a href="#" class="btnLogin-popup">đăng nhập</a> để lưu giỏ hàng hoặc tiếp tục mua hàng với thông tin cá nhân!</p>
    </div>
    <?php endif; ?>

    <?php if (!empty($cart_items)): ?>
        <div class="cart-header">
            <div class="select-all">
                <input type="checkbox" id="select-all-items">
                <label for="select-all-items">Chọn tất cả</label>
            </div>
            <div class="cart-actions">
                <button class="clear-cart-btn" onclick="clearCart()">
                    <i class="fas fa-trash-alt"></i> Xóa toàn bộ giỏ hàng
                </button>
            </div>
            <div class="cart-columns">
                <div>Sản phẩm</div>
                <div>Số lượng</div>
                <div>Thành tiền</div>
                <div></div>
            </div>
        </div>

        <div class="cart-items">
            <?php foreach ($cart_items as $item): ?>
                <div class="cart-item" data-id="<?php echo htmlspecialchars($item['book_id']); ?>" data-price="<?php echo $item['price']; ?>">
                    <div class="item-checkbox-container">
                        <input type="checkbox" class="item-checkbox">
                    </div>
                    <div class="cart-item-info">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['title']); ?>" 
                             class="cart-item-image">
                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                            <p class="cart-item-author">Tác giả: <?php echo htmlspecialchars($item['author'] ?? 'Không có'); ?></p>
                            <p class="cart-item-publisher">NXB: <?php echo htmlspecialchars($item['publisher'] ?? 'Không có'); ?></p>
                            <div class="cart-item-price">
                                <?php echo number_format($item['price'], 0, ',', '.'); ?>đ
                                <?php if (!empty($item['old_price'])): ?>
                                    <span class="cart-item-original-price">
                                        <?php echo number_format($item['old_price'], 0, ',', '.'); ?>đ
                                    </span>
                                <?php endif; ?>
                                <?php if (!empty($item['discount'])): ?>
                                    <span class="cart-item-discount"><?php echo $item['discount']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="quantity-controls">
                        <button class="quantity-btn minus" onclick="updateQuantity(this, -1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" class="quantity-input" value="<?php echo $item['quantity']; ?>" min="1" onchange="updateQuantity(this, 0)">
                        <button class="quantity-btn plus" onclick="updateQuantity(this, 1)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                    <div class="cart-item-subtotal">
                        <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>đ
                    </div>
                    <button class="delete-btn" onclick="removeFromCart(this)">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <div class="rewards-section">
                <div class="rewards-header">
                    <div class="rewards-title">
                        <i class="fas fa-gift"></i>
                        KHUYẾN MÃI
                    </div>
                    <a href="#" class="view-more">Xem thêm <i class="fas fa-chevron-right"></i></a>
                </div>

                <div class="promotion-card">
                    <div class="promotion-title">Đang cập nhật</div>
                </div>
            </div>

            <div class="cart-total">
                <div class="total-label">Tổng tiền</div>
                <div class="total-amount">
                    <?php
                    $total = 0;
                    foreach ($cart_items as $item) {
                        $total += $item['price'] * $item['quantity'];
                    }
                    echo number_format($total, 0, ',', '.');
                    ?>đ
                </div>
            </div>

            <button class="checkout-btn" onclick="proceedToCheckout()">
                <i class="fas fa-shopping-cart"></i> TIẾN HÀNH ĐẶT HÀNG
            </button>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <i class="fas fa-shopping-cart fa-3x"></i>
            <p>Giỏ hàng của bạn đang trống</p>
            <a href="index.php" class="continue-shopping">Tiếp tục mua sắm</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý chọn tất cả
    const selectAllCheckbox = document.getElementById('select-all-items');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateTotal();
        });
    }

    // Auto-check all items and add event listeners
    itemCheckboxes.forEach(checkbox => {
        checkbox.checked = true;
        checkbox.addEventListener('change', updateTotal);
    });
    
    if (selectAllCheckbox && itemCheckboxes.length > 0) {
        selectAllCheckbox.checked = true;
    }
    
    updateTotal();
});

function updateQuantity(element, change) {
    const input = change === 0 ? element : element.parentElement.querySelector('.quantity-input');
    const currentValue = parseInt(input.value) || 1;
    const newValue = change === 0 ? currentValue : currentValue + change;
    
    if (newValue < 1) return;
    
    const cartItem = element.closest('.cart-item');
    const bookId = cartItem.dataset.id;
    
    fetch('pages/update_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `book_id=${bookId}&quantity=${newValue}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = newValue;
            
            const price = parseFloat(cartItem.dataset.price);
            const subtotal = price * newValue;
            
            cartItem.querySelector('.cart-item-subtotal').textContent = new Intl.NumberFormat('vi-VN').format(subtotal) + 'đ';
            
            // Update cart counter if available
            if (data.total_items !== undefined) {
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.total_items;
                }
            }
            
            updateTotal();
        } else {
            alert(data.message || 'Có lỗi xảy ra khi cập nhật số lượng');
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        alert('Có lỗi xảy ra khi cập nhật số lượng');
    });
}

function removeFromCart(button) {
    if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) return;
    
    const cartItem = button.closest('.cart-item');
    const bookId = cartItem.dataset.id;
    
    fetch('pages/remove_from_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `book_id=${bookId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            cartItem.remove();
            updateTotal();
            
            // Update cart counter if available
            if (data.total_items !== undefined) {
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = data.total_items;
                }
            }
            
            const cartTitle = document.querySelector('.cart-title');
            const itemCount = document.querySelectorAll('.cart-item').length;
            cartTitle.innerHTML = `<i class="fas fa-shopping-cart"></i> GIỎ HÀNG CỦA BẠN (${itemCount} sản phẩm)`;
            
            if (itemCount === 0) {
                location.reload(); // Reload to show empty cart message
            }
        } else {
            alert(data.message || 'Có lỗi xảy ra khi xóa sản phẩm');
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        alert('Có lỗi xảy ra khi xóa sản phẩm');
    });
}

function updateTotal() {
    let total = 0;
    const cartItems = document.querySelectorAll('.cart-item');
    
    cartItems.forEach(item => {
        const checkbox = item.querySelector('.item-checkbox');
        if (checkbox && checkbox.checked) {
            const price = parseFloat(item.dataset.price);
            const quantity = parseInt(item.querySelector('.quantity-input').value);
            total += price * quantity;
        }
    });
    
    document.querySelector('.total-amount').textContent = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
}

function clearCart() {
    if (!confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng? Hành động này không thể hoàn tác!')) {
        return;
    }
    
    fetch('pages/clear_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart counter
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = '0';
            }
            
            // Reload page to show empty cart
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra khi xóa giỏ hàng');
        }
    })
    .catch(error => {
        console.error('Lỗi:', error);
        alert('Có lỗi xảy ra khi xóa giỏ hàng');
    });
}

function proceedToCheckout() {
    const selectedItems = document.querySelectorAll('.item-checkbox:checked');
    if (selectedItems.length === 0) {
        alert('Vui lòng chọn ít nhất một sản phẩm để đặt hàng');
        return;
    }
    // Lấy danh sách book_id
    const bookIds = Array.from(selectedItems).map(cb => cb.closest('.cart-item').dataset.id);
    // Gửi qua fetch POST để lưu vào session
    fetch('pages/prepare_checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'selected_items=' + JSON.stringify(bookIds)
    }).then(() => {
        window.location.href = 'index.php?page=checkout';
    });
}
</script>