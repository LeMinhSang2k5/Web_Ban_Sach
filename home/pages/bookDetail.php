<?php
// Bật báo lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kết nối database
require_once __DIR__ . '/../config.php';

// Lấy ID sách từ URL
$book_id = isset($_GET['id']) ? $_GET['id'] : null;

// Debug information
echo "Book ID: " . $book_id . "<br>";

// Truy vấn thông tin sách
$sql = "SELECT * FROM books WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    die("Lỗi prepare statement: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $book_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


$book = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $book['title']; ?> - Chi tiết sách</title>
    <link rel="stylesheet" href="/Web_Ban_Sach/home/assets/css/main.css">
    <link rel="stylesheet" href="/Web_Ban_Sach/home/assets/css/bookDetail.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

    <div class="book-detail-container">
        <!-- Cột trái: Ảnh sách và các ảnh nhỏ -->
        <div class="book-detail-left">
            <div class="main-image">
                <img id="main-img" src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>" onclick="showImageOverlay(this.src)">
            </div>
            <div class="thumbnail-list">
                <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>" onclick="showImageOverlay(this.src)">
                <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>" onclick="showImageOverlay(this.src)">
                <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>" onclick="showImageOverlay(this.src)">
                <div class="more-thumbnails">+5</div>
            </div>
            <button class="add-to-cart">Thêm vào giỏ hàng</button>
            <button class="buy-now">Mua ngay</button>
            <div class="policy-list">
                <p>Thời gian giao hàng: Giao nhanh và uy tín</p>
                <p>Chính sách đổi trả: Đổi trả miễn phí toàn quốc</p>
                <p>Chính sách khách sỉ: Ưu đãi khi mua số lượng lớn</p>
            </div>
        </div>
        <!-- Cột phải: Thông tin sách -->
        <div class="book-detail-right">
            <?php if (!empty($book['label'])): ?>
            <div class="book-badge"><?php echo $book['label']; ?></div>
            <?php endif; ?>
            <h1 class="book-title"><?php echo $book['title']; ?></h1>
            <div class="book-supplier">Nhà cung cấp: <a href="#"><?php echo $book['supplier']; ?></a></div>
            <div class="book-publisher">Nhà xuất bản: <?php echo $book['publisher']; ?></div>
            <div class="book-rating">
                <span>★</span> (<?php echo $book['reviews']; ?> đánh giá) | Đã bán <?php echo $book['sold']; ?>
            </div>
            <div class="book-price">
                <span class="new-price"><?php echo number_format($book['price'], 0, ",", "."); ?> đ</span>
                <?php if (!empty($book['old_price'])): ?>
                <span class="old-price"><?php echo number_format($book['old_price'], 0, ",", "."); ?> đ</span>
                <?php endif; ?>
                <?php if (!empty($book['discount'])): ?>
                <span class="discount"><?php echo $book['discount']; ?></span>
                <?php endif; ?>
            </div>
            <div class="book-stock"><?php echo $book['stock']; ?> nhà sách còn hàng</div>
            <div class="shipping-info">
                <div>Giao hàng đến <b>Phường Bến Nghé, Quận 1, Hồ Chí Minh</b> <a href="#">Thay đổi</a></div>
                <div>Giao hàng tiêu chuẩn - Dự kiến giao Thứ ba - 27/05</div>
            </div>
            <div class="book-promotions">
                <span>Mã giảm 10k</span>
                <span>Mã giảm 20k</span>
                <span>Zalopay: giảm 15%</span>
            </div>

            <!-- phần số lượng sản phẩm-->
            <div class="book-quantity">
                <button type="button" onclick="changeQuantity(-1)">-</button>
                <input type="text" id="quantity-input" value="1" min="1">
                <button type="button" onclick="changeQuantity(1)">+</button>
            </div>

            <!-- phần thông tin chi tiết và mô tả sản phẩm-->
            <div class="book-tabs">
                <div class="tab active" onclick="showTab('info')">Thông tin chi tiết</div>
                <div class="tab" onclick="showTab('desc')">Mô tả sản phẩm</div>
            </div>

            <!-- phần thông tin chi tiết-->
            <div class="book-info-content" id="tab-info">
                <div class="book-info-table">
                    <ul>
                        <li>Mã hàng: <?php echo $book['code']; ?></li>
                        <li>Tên Nhà Cung Cấp: <?php echo $book['supplier']; ?></li>
                        <li>Tác giả: <?php echo $book['author']; ?></li>
                        <li>Nhà xuất bản: <?php echo $book['publisher']; ?></li>
                        <li>Năm xuất bản: <?php echo $book['publish_year']; ?></li>
                        <li>Thể loại: <?php echo $book['category']; ?></li>
                        <li>Số trang: <?php echo $book['pages']; ?></li>
                        <li>Kích thước: <?php echo $book['size']; ?></li>
                        <li>Hình thức bìa: <?php echo $book['cover_type']; ?></li>
                    </ul>
                </div>
            </div>

            <!-- mô tả sản phẩm-->
            <div class="book-info-content" id="tab-desc" style="display:none">
                <div class="book-description">
                    <?php echo $book['description']; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- phần javascript-->
    <script>
    function showTab(tab) {
        document.getElementById('tab-info').style.display = tab === 'info' ? '' : 'none';
        document.getElementById('tab-desc').style.display = tab === 'desc' ? '' : 'none';
        var tabs = document.querySelectorAll('.book-tabs .tab');
        tabs[0].classList.toggle('active', tab === 'info');
        tabs[1].classList.toggle('active', tab === 'desc');
    }

    function changeQuantity(delta) {
        var input = document.getElementById('quantity-input');
        var value = parseInt(input.value) || 1;
        value += delta;
        if (value < 1) value = 1;
        input.value = value;
    }

    function changeMainImage(src) {
        document.getElementById('main-img').src = src;
    }

    function showImageOverlay(src) {
        // Tạo overlay
        var overlay = document.createElement('div');
        overlay.className = 'img-overlay';
        overlay.onclick = function() { document.body.removeChild(overlay); };
        // Tạo ảnh lớn
        var img = document.createElement('img');
        img.src = src;
        overlay.appendChild(img);
        document.body.appendChild(overlay);
    }
    </script>

</body>
</html>