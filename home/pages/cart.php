<?php
include 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log the session data
error_log("Session data: " . print_r($_SESSION, true));

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Hàm lấy thông tin chi tiết của sách
function getBookDetails($book_ids, $conn) {
    if (empty($book_ids)) {
        return [];
    }
    
    $ids = implode(',', array_map('intval', $book_ids));
    $sql = "SELECT id, title, price, image, author, publisher, discount, old_price 
            FROM books 
            WHERE id IN ($ids)";
    
    error_log("SQL Query: " . $sql);
    $result = $conn->query($sql);
    $books = [];
    
    while ($row = $result->fetch_assoc()) {
        error_log("Book from DB - ID: " . $row['id'] . ", Title: " . $row['title']);
        $books[$row['id']] = $row;
    }
    
    return $books;
}

// Lấy giỏ hàng từ database nếu đã đăng nhập
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Lấy giỏ hàng active của user
    $stmt = $conn->prepare("
        SELECT c.id as cart_id, ci.book_id, ci.quantity, ci.price
        FROM cart c
        JOIN cart_items ci ON c.id = ci.cart_id
        WHERE c.user_id = ? AND c.status = 'active'
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $cart_items = [];
    $book_ids = [];
    
    while ($row = $result->fetch_assoc()) {
        $book_ids[] = $row['book_id'];
        $cart_items[$row['book_id']] = $row; // Sử dụng book_id làm key
    }
    
    // Lấy thông tin chi tiết của các sách
    $books = getBookDetails($book_ids, $conn);
    
    // Kết hợp thông tin sách với số lượng trong giỏ hàng
    foreach ($books as $book_id => $book) {
        if (isset($cart_items[$book_id])) {
            $cart_items[$book_id] = array_merge($cart_items[$book_id], $book);
        }
    }
    
    // Chuyển đổi mảng kết hợp thành mảng tuần tự
    $cart_items = array_values($cart_items);
} else {
    // Nếu chưa đăng nhập, lấy từ session
    $cart_items = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
    if (!empty($cart_items)) {
        // Chuyển đổi 'id' thành 'book_id' cho nhất quán
        foreach ($cart_items as &$item) {
            if (isset($item['id']) && !isset($item['book_id'])) {
                $item['book_id'] = $item['id'];
                unset($item['id']);
            }
        }
        
        $book_ids = array_column($cart_items, 'book_id');
        $books = getBookDetails($book_ids, $conn);
        
        // Tạo mảng tạm để lưu trữ các item đã xử lý
        $processed_items = [];
        foreach ($cart_items as $item) {
            $book_id = $item['book_id'];
            if (isset($books[$book_id]) && !isset($processed_items[$book_id])) {
                $processed_items[$book_id] = array_merge($item, $books[$book_id]);
            }
        }
        
        // Chuyển đổi mảng kết hợp thành mảng tuần tự
        $cart_items = array_values($processed_items);
    }
}

// Kiểm tra xem có bị trùng book_id không
$seen_book_ids = [];
foreach ($cart_items as $item) {
    $book_id = $item['book_id'];
    if (in_array($book_id, $seen_book_ids)) {
        error_log("DUPLICATE FOUND - Book ID: " . $book_id);
    } else {
        $seen_book_ids[] = $book_id;
    }
}

error_log("Unique book IDs in cart: " . implode(", ", $seen_book_ids));
?>

<link rel="stylesheet" href="assets/css/cart.css">

<div class="cart-container">
    <div class="cart-title">
        <i class="fas fa-shopping-cart"></i> GIỎ HÀNG CỦA BẠN (<?php echo count($cart_items); ?> sản phẩm)
    </div>

    <?php if (!isLoggedIn()): ?>
    <div class="login-notice">
        <p>Vui lòng <a href="#" class="btnLogin-popup">đăng nhập</a> để lưu giỏ hàng của bạn!</p>
    </div>
    <?php endif; ?>

    <?php if (!empty($cart_items)): ?>
        <div class="cart-header">
            <div class="select-all">
                <input type="checkbox" id="select-all-items">
                <label for="select-all-items">Chọn tất cả</label>
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
                <?php
                // Debug: In ra thông tin từng item
                error_log("Rendering item: " . print_r($item, true));
                ?>
                <div class="cart-item" data-id="<?php echo htmlspecialchars($item['book_id']); ?>">
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
                    <div class="promotion-title">Mã Giảm 10K - Toàn Sản Phẩm</div>
                    <div class="promotion-desc">Đơn hàng từ 130k - Không áp dụng cho sách ngoại văn</div>
                    <div class="promotion-expiry">HSD: 30/06/2025</div>
                    <div class="progress-bar">
                        <div class="progress-fill"></div>
                    </div>
                    <div class="buy-more-info">Mua thêm 130.000đ để nhận mã</div>
                    <a href="index.php" class="buy-more">Mua thêm</a>
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
            <div class="terms">
                <a href="#">Xem điều khoản và điều kiện mua hàng</a>
            </div>
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

    selectAllCheckbox.addEventListener('change', function() {
        itemCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateTotal();
    });

    // Xử lý số lượng
    itemCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateTotal);
    });
});

function updateQuantity(element, change) {
    const input = change === 0 ? element : element.parentElement.querySelector('.quantity-input');
    const currentValue = parseInt(input.value) || 1;
    const newValue = change === 0 ? currentValue : currentValue + change;
    
    if (newValue < 1) return;
    
    const cartItem = element.closest('.cart-item');
    const bookId = cartItem.dataset.id;
    
    // Gửi request cập nhật số lượng
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
            
            // Cập nhật thành tiền
            const price = parseFloat(cartItem.querySelector('.cart-item-price').textContent.replace(/[^\d]/g, ''));
            const subtotal = price * newValue;
            cartItem.querySelector('.cart-item-subtotal').textContent = new Intl.NumberFormat('vi-VN').format(subtotal) + 'đ';
            
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
            
            // Cập nhật số lượng sản phẩm trong tiêu đề
            const cartTitle = document.querySelector('.cart-title');
            const itemCount = document.querySelectorAll('.cart-item').length;
            cartTitle.textContent = `GIỎ HÀNG (${itemCount} sản phẩm)`;
            
            // Hiển thị giỏ hàng trống nếu không còn sản phẩm
            if (itemCount === 0) {
                const cartItems = document.querySelector('.cart-items');
                cartItems.innerHTML = `
                    <div class="empty-cart">
                        <p>Giỏ hàng của bạn đang trống</p>
                        <a href="index.php" class="continue-shopping">Tiếp tục mua sắm</a>
                    </div>
                `;
                
                // Ẩn phần tổng tiền và nút đặt hàng
                document.querySelector('.cart-summary').style.display = 'none';
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
        if (checkbox.checked) {
            const subtotal = parseFloat(item.querySelector('.cart-item-subtotal').textContent.replace(/[^\d]/g, ''));
            total += subtotal;
        }
    });
    
    document.querySelector('.total-amount').textContent = new Intl.NumberFormat('vi-VN').format(total) + 'đ';
}

function proceedToCheckout() {
    const selectedItems = document.querySelectorAll('.item-checkbox:checked');
    if (selectedItems.length === 0) {
        alert('Vui lòng chọn ít nhất một sản phẩm để đặt hàng');
        return;
    }
    
    // TODO: Chuyển đến trang thanh toán
    window.location.href = 'checkout.php';
}
</script>
