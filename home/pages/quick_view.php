<?php
require_once '../db.php';

if (!isset($_GET['id'])) {
    echo '<p>Không tìm thấy thông tin sách.</p>';
    exit;
}

$book_id = $_GET['id'];
$sql = "SELECT * FROM books WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $book_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$book = mysqli_fetch_assoc($result);

if (!$book) {
    echo '<p>Không tìm thấy sách.</p>';
    exit;
}
?>

<div class="quick-view-container">
    <div class="quick-view-header">
        <h2><?= htmlspecialchars($book['title']) ?></h2>
        <p class="author">Tác giả: <strong><?= htmlspecialchars($book['author']) ?></strong></p>
    </div>

    <div class="quick-view-content">
        <div class="quick-view-image">
            <img src="<?= $book['image'] ?>" alt="<?= htmlspecialchars($book['title']) ?>">
            <?php if (!empty($book['label'])): ?>
                <span class="quick-label"><?= htmlspecialchars($book['label']) ?></span>
            <?php endif; ?>
            <?php if (!empty($book['discount'])): ?>
                <span class="quick-discount"><?= htmlspecialchars($book['discount']) ?></span>
            <?php endif; ?>
        </div>

        <div class="quick-view-info">
            <!-- Rating -->
            <div class="rating-section">
                <div class="stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <i class="fas fa-star <?= $i <= ($book['reviews'] ?? 0) ? 'filled' : 'empty' ?>"></i>
                    <?php endfor; ?>
                </div>
                <span class="rating-text">(<?= $book['reviews'] ?? 0 ?>/5)</span>
            </div>

            <!-- Price -->
            <div class="price-section">
                <div class="current-price"><?= number_format($book['price'], 0, ",", ".") ?>đ</div>
                <?php if (!empty($book['old_price']) && $book['old_price'] > $book['price']): ?>
                    <div class="old-price"><?= number_format($book['old_price'], 0, ",", ".") ?>đ</div>
                    <div class="save-amount">
                        Tiết kiệm: <?= number_format($book['old_price'] - $book['price'], 0, ",", ".") ?>đ
                    </div>
                <?php endif; ?>
            </div>

            <!-- Book Details -->
            <div class="book-details">
                <div class="detail-row">
                    <span class="label">Danh mục:</span>
                    <span class="value"><?= htmlspecialchars($book['category']) ?></span>
                </div>
                <?php if (!empty($book['subcategory'])): ?>
                <div class="detail-row">
                    <span class="label">Thể loại:</span>
                    <span class="value"><?= htmlspecialchars($book['subcategory']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($book['publisher'])): ?>
                <div class="detail-row">
                    <span class="label">Nhà xuất bản:</span>
                    <span class="value"><?= htmlspecialchars($book['publisher']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($book['publish_year'])): ?>
                <div class="detail-row">
                    <span class="label">Năm xuất bản:</span>
                    <span class="value"><?= $book['publish_year'] ?></span>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <span class="label">Số trang:</span>
                    <span class="value"><?= $book['pages'] ?> trang</span>
                </div>
                <?php if (!empty($book['size'])): ?>
                <div class="detail-row">
                    <span class="label">Kích thước:</span>
                    <span class="value"><?= htmlspecialchars($book['size']) ?></span>
                </div>
                <?php endif; ?>
                <div class="detail-row">
                    <span class="label">Loại bìa:</span>
                    <span class="value"><?= htmlspecialchars($book['cover_type']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Tình trạng:</span>
                    <span class="value <?= $book['stock'] > 0 ? 'in-stock' : 'out-stock' ?>">
                        <?= $book['stock'] > 0 ? 'Còn hàng (' . $book['stock'] . ')' : 'Hết hàng' ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="label">Đã bán:</span>
                    <span class="value"><?= $book['sold'] ?> cuốn</span>
                </div>
            </div>

            <!-- Description -->
            <?php if (!empty($book['description'])): ?>
            <div class="description-section">
                <h4>Mô tả sản phẩm</h4>
                <div class="description-content">
                    <?= substr(strip_tags($book['description']), 0, 300) ?>...
                </div>
            </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <div class="quantity-selector">
                    <label>Số lượng:</label>
                    <div class="quantity-controls">
                        <button type="button" class="qty-btn minus" onclick="changeQuantity(-1)">-</button>
                        <input type="number" id="quantity" value="1" min="1" max="<?= $book['stock'] ?>">
                        <button type="button" class="qty-btn plus" onclick="changeQuantity(1)">+</button>
                    </div>
                </div>

                <div class="action-btns">
                    <button class="btn-add-cart" onclick="addToCartFromQuickView(<?= $book['id'] ?>)" 
                            <?= $book['stock'] <= 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-shopping-cart"></i>
                        <?= $book['stock'] > 0 ? 'Thêm vào giỏ' : 'Hết hàng' ?>
                    </button>
                    
                    <a href="index.php?page=bookDetail&id=<?= $book['id'] ?>" class="btn-view-detail">
                        <i class="fas fa-eye"></i>
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.quick-view-container {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.quick-view-header {
    margin-bottom: 20px;
    text-align: center;
    border-bottom: 1px solid #eee;
    padding-bottom: 15px;
}

.quick-view-header h2 {
    margin: 0 0 8px 0;
    font-size: 20px;
    color: #333;
    line-height: 1.4;
}

.quick-view-header .author {
    margin: 0;
    color: #666;
    font-size: 14px;
}

.quick-view-content {
    display: flex;
    gap: 20px;
}

.quick-view-image {
    flex-shrink: 0;
    width: 200px;
    position: relative;
}

.quick-view-image img {
    width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.quick-label {
    position: absolute;
    top: 8px;
    left: 8px;
    background: #ff6b6b;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
}

.quick-discount {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #28a745;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
}

.quick-view-info {
    flex: 1;
    min-width: 0;
}

.rating-section {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 15px;
}

.stars i {
    color: #ddd;
    font-size: 14px;
}

.stars i.filled {
    color: #ffc107;
}

.rating-text {
    font-size: 14px;
    color: #666;
}

.price-section {
    margin-bottom: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.current-price {
    font-size: 24px;
    font-weight: 700;
    color: #e74c3c;
    margin-bottom: 5px;
}

.old-price {
    font-size: 16px;
    color: #999;
    text-decoration: line-through;
    margin-bottom: 5px;
}

.save-amount {
    font-size: 14px;
    color: #28a745;
    font-weight: 500;
}

.book-details {
    margin-bottom: 20px;
}

.detail-row {
    display: flex;
    margin-bottom: 8px;
    font-size: 14px;
}

.detail-row .label {
    width: 120px;
    color: #666;
    flex-shrink: 0;
}

.detail-row .value {
    color: #333;
    font-weight: 500;
}

.detail-row .value.in-stock {
    color: #28a745;
}

.detail-row .value.out-stock {
    color: #dc3545;
}

.description-section {
    margin-bottom: 20px;
}

.description-section h4 {
    margin: 0 0 10px 0;
    font-size: 16px;
    color: #333;
}

.description-content {
    font-size: 14px;
    line-height: 1.6;
    color: #666;
}

.action-buttons {
    border-top: 1px solid #eee;
    padding-top: 20px;
}

.quantity-selector {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
}

.quantity-selector label {
    font-weight: 500;
    color: #333;
}

.quantity-controls {
    display: flex;
    align-items: center;
    border: 1px solid #ddd;
    border-radius: 4px;
    overflow: hidden;
}

.qty-btn {
    width: 32px;
    height: 32px;
    border: none;
    background: #f8f9fa;
    cursor: pointer;
    font-weight: bold;
    color: #666;
    transition: background-color 0.3s ease;
}

.qty-btn:hover {
    background: #e9ecef;
}

#quantity {
    width: 60px;
    height: 32px;
    text-align: center;
    border: none;
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
    font-size: 14px;
}

.action-btns {
    display: flex;
    gap: 10px;
}

.btn-add-cart, .btn-view-detail {
    flex: 1;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-add-cart {
    background: #007bff;
    color: white;
}

.btn-add-cart:hover:not(:disabled) {
    background: #0056b3;
}

.btn-add-cart:disabled {
    background: #6c757d;
    cursor: not-allowed;
}

.btn-view-detail {
    background: white;
    color: #007bff;
    border: 1px solid #007bff;
}

.btn-view-detail:hover {
    background: #007bff;
    color: white;
}

@media (max-width: 480px) {
    .quick-view-content {
        flex-direction: column;
    }
    
    .quick-view-image {
        width: 100%;
        max-width: 200px;
        margin: 0 auto;
    }
    
    .action-btns {
        flex-direction: column;
    }
    
    .detail-row {
        flex-direction: column;
        margin-bottom: 12px;
    }
    
    .detail-row .label {
        width: auto;
        margin-bottom: 4px;
        font-weight: 600;
    }
}
</style>

<script>
function changeQuantity(delta) {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value) || 1;
    const newValue = current + delta;
    const max = parseInt(input.max);
    
    if (newValue >= 1 && newValue <= max) {
        input.value = newValue;
    }
}

function addToCartFromQuickView(bookId) {
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    
    $.ajax({
        url: 'pages/add_to_cart.php',
        method: 'POST',
        data: { 
            book_id: bookId, 
            quantity: quantity 
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Update cart count
                $('#cart-count').text(response.cart_count);
                
                // Close modal
                $('#quickViewModal').fadeOut();
                
                // Show success message
                parent.showNotification('Đã thêm ' + quantity + ' cuốn vào giỏ hàng!', 'success');
            } else {
                parent.showNotification(response.message || 'Có lỗi xảy ra!', 'error');
            }
        },
        error: function() {
            parent.showNotification('Có lỗi xảy ra khi thêm vào giỏ hàng!', 'error');
        }
    });
}
</script> 