<?php
// Lấy ID sách từ URL
$book_id = isset($_GET['id']) ? $_GET['id'] : null;

// Truy vấn thông tin sách
$sql = "SELECT * FROM books WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
if (!$stmt) {
    error_log("Lỗi prepare statement: " . mysqli_error($conn));
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
                <img id="main-img" src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>"
                    onclick="showImageOverlay(this.src)">
            </div>
            <div class="thumbnail-list">
                <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>"
                    onclick="showImageOverlay(this.src)">
                <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>"
                    onclick="showImageOverlay(this.src)">
                <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>"
                    onclick="showImageOverlay(this.src)">
                <div class="more-thumbnails">+5</div>
            </div>
            <button class="add-to-cart" onclick="addToCart(<?php echo $book['id']; ?>)">Thêm vào giỏ hàng</button>
            <button class="buy-now" onclick="buyNow(<?php echo $book['id']; ?>)">Mua ngay</button>
            <div class="policy-list">
                <p>Thời gian giao hàng: Giao nhanh và uy tín</p>
                <p>Chính sách đổi trả: Đổi trả miễn phí toàn quốc</p>
                <p>Chính sách khách sỉ: Ưu đãi khi mua số lượng lớn</p>
            </div>
        </div>
        <!-- Cột phải: Thông tin sách -->
        <div class="book-detail-right">
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
                <span>Đang cập nhật</span>
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
                    <div class="book-info-row">
                        <div class="book-info-label">Mã hàng:</div>
                        <div class="book-info-value"><?php echo $book['code']; ?></div>
                    </div>
                    <div class="book-info-row">
                        <div class="book-info-label">Tên Nhà Cung Cấp:</div>
                        <div class="book-info-value"><?php echo $book['supplier']; ?></div>
                    </div>
                    <div class="book-info-row">
                        <div class="book-info-label">Tác giả:</div>
                        <div class="book-info-value"><?php echo $book['author']; ?></div>
                    </div>
                    <div class="book-info-row">
                        <div class="book-info-label">Nhà xuất bản:</div>
                        <div class="book-info-value"><?php echo $book['publisher']; ?></div>
                    </div>
                    <div class="book-info-row">
                        <div class="book-info-label">Năm xuất bản:</div>
                        <div class="book-info-value"><?php echo $book['publish_year']; ?></div>
                    </div>
                    <div class="book-info-row">
                        <div class="book-info-label">Thể loại:</div>
                        <div class="book-info-value"><?php echo $book['category']; ?></div>
                    </div>
                    <div class="book-info-row">
                        <div class="book-info-label">Số trang:</div>
                        <div class="book-info-value"><?php echo $book['pages']; ?></div>
                    </div>
                    <div class="book-info-row">
                        <div class="book-info-label">Kích thước:</div>
                        <div class="book-info-value"><?php echo $book['size']; ?>cm</div>
                    </div>
                    <div class="book-info-row">
                        <div class="book-info-label">Hình thức bìa:</div>
                        <div class="book-info-value"><?php echo $book['cover_type']; ?></div>
                    </div>
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

    <!-- Box đánh giá sản phẩm -->
    <div class="book-rating-box">
        <div class="book-rating-score">
            <span class="score">0</span><span class="score-max">/5</span>
            <div class="book-rating-star-list">
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
                <span class="star">★</span>
            </div>
            <div class="book-rating-count">
                (0 đánh giá)
            </div>
        </div>
        <div class="book-rating-bar-list">
            <div class="bar-row"><span>5 sao</span>
                <div class="bar">
                    <div class="bar-inner" style="width:0%"></div>
                </div><span>0%</span>
            </div>
            <div class="bar-row"><span>4 sao</span>
                <div class="bar">
                    <div class="bar-inner" style="width:0%"></div>
                </div><span>0%</span>
            </div>
            <div class="bar-row"><span>3 sao</span>
                <div class="bar">
                    <div class="bar-inner" style="width:0%"></div>
                </div><span>0%</span>
            </div>
            <div class="bar-row"><span>2 sao</span>
                <div class="bar">
                    <div class="bar-inner" style="width:0%"></div>
                </div><span>0%</span>
            </div>
            <div class="bar-row"><span>1 sao</span>
                <div class="bar">
                    <div class="bar-inner" style="width:0%"></div>
                </div><span>0%</span>
            </div>
        </div>
        <div class="book-rating-note">
            Chỉ có thành viên mới có thể viết nhận xét. Vui lòng <a href="#">đăng nhập</a> hoặc <a href="#">đăng ký</a>.
        </div>
    </div>
    <script>
        function showTab(tab) {
            $('#tab-info').css('display', tab === 'info' ? '' : 'none');
            $('#tab-desc').css('display', tab === 'desc' ? '' : 'none');
            var $tabs = $('.book-tabs .tab');
            $tabs.eq(0).toggleClass('active', tab === 'info');
            $tabs.eq(1).toggleClass('active', tab === 'desc');
        }

        function changeQuantity(delta) {
            var $input = $('#quantity-input');
            var value = parseInt($input.val()) || 1;
            value += delta;
            if (value < 1) value = 1;
            $input.val(value);
        }

        function changeMainImage(src) {
            $('#main-img').attr('src', src);
        }

        function showImageOverlay(src) {
            // Tạo overlay
            var $overlay = $('<div class="img-overlay"></div>');

            // Tạo ảnh lớn
            var $img = $('<img>').attr('src', src);
            $img.on('click', function(e) { e.stopPropagation(); }); // Ngăn đóng khi click vào ảnh

            // Hiệu ứng đóng smooth
            function closeOverlay() {
                $overlay.addClass('closing');
                setTimeout(function () {
                    $overlay.remove();
                }, 300);
            }

            $overlay.on('click', closeOverlay);

            // Đóng bằng phím ESC
            function handleKeyPress(e) {
                if (e.key === 'Escape') {
                    closeOverlay();
                    $(document).off('keydown', handleKeyPress);
                }
            }
            $(document).on('keydown', handleKeyPress);

            $overlay.append($img);
            $('body').append($overlay);

            // Ngăn cuộn trang khi overlay mở
            $('body').css('overflow', 'hidden');

            // Khôi phục cuộn trang khi đóng
            $overlay.on('animationend', function (e) {
                if (e.originalEvent.animationName === 'fadeOut') {
                    $('body').css('overflow', '');
                }
            });
        }

        function buyNow(bookId) {
            const quantity = parseInt($('#quantity-input').val()) || 1;

            $.ajax({
                url: 'pages/add_to_cart.php',
                method: 'POST',
                data: { book_id: bookId, quantity: quantity },
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        $.ajax({
                            url: 'pages/prepare_checkout.php',
                            method: 'POST',
                            data: { selected_items: JSON.stringify([bookId]) },
                            success: function() {
                                location.href = 'index.php?page=cart';
                            }
                        });
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
                    }
                }
            });
        }

        function addToCart(bookId) {
            // Lấy số lượng từ input
            const quantity = parseInt($('#quantity-input').val()) || 1;

            // Gửi request đến server
            $.ajax({
                url: 'pages/add_to_cart.php',
                method: 'POST',
                data: { book_id: bookId, quantity: quantity },
                dataType: 'json',
                success: function(data) {
                    console.log('Server response:', data);
                    if (data.success) {
                        alert('Đã thêm sách vào giỏ hàng thành công!');
                    } else {
                        alert(data.message || 'Có lỗi xảy ra khi thêm vào giỏ hàng');
                    }
                }
            });
        }
    </script>

</body>

</html>