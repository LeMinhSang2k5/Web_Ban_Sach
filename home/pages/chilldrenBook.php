<?php
include 'config.php';
$sql_reference = "SELECT * FROM books WHERE label = 'bán chạy' AND category = 'thiếu nhi'";
$result_reference = mysqli_query($conn, $sql_reference);
?>


<body>
<div class="main">
    <div class="banner-container">
        <img src="assets/img/banner/banner-thieu-nhi.png" alt="Banner">
    </div>
    <div class="item-section">
        <div class="item-title">
            <img src="assets/img/banner/ma-giam-gia.png" alt="">
        </div>
        <div class="item-list">
            <div class="item-item">
                <img src="assets/img/icon/sach-hot.png" alt="">
            </div>
            <div class="item-item">
                <img src="assets/img/icon/tu-truyen-thieu-nhi.png" alt="">
            </div>
            <div class="item-item">
                <img src="assets/img/icon/goi-y.png" alt="">
            </div>
            <div class="item-item">
                <img src="assets/img/icon/sach-theo-nha-cung-cap.png" alt="">
            </div>
        </div>
    </div>
    <div class="item-section">
        <div class="item-title">
            <img src="assets/img/banner/sach-hot.png" alt="">
        </div>
        <div class="book-slider">
            <button class="slider-btn prev">&lt;</button>
            <div class="slider-track">
                <div class="slider-item">
                    <a href="index.php?page=bookDetail&id=12">
                        <img src="assets/img/img_book_slider/tieu-su-cac-qg.png" alt="">
                    </a>
                </div>
                <div class="slider-item">
                    <img src="assets/img/img_book_slider/100-ki-nang.png" alt="">
                </div>
                <div class="slider-item">
                    <a href="index.php?page=bookDetail&id=2">
                        <img src="assets/img/img_book_slider/bup-sen-xanh.png" alt="">
                    </a>
                </div>
                <div class="slider-item">
                    <a href="index.php?page=bookDetail&id=14">
                        <img src="assets/img/img_book_slider/luoc-su-nuoc-viet.png" alt="">
                    </a>
                </div>
                <div class="slider-item">
                    <img src="assets/img/img_book_slider/100-ki-nang.png" alt="">
                </div>
                <div class="slider-item">
                    <img src="assets/img/img_book_slider/tieu-su-cac-qg.png" alt="">
                </div>
                <div class="slider-item">
                    <img src="assets/img/img_book_slider/hoang-tu-be.png" alt="">
                </div>
            </div>
            <button class="slider-btn next">&gt;</button>
        </div>
    </div>
    <div class="content-product" id="sach-hot">
        <div class="reference-product">
            <div class="reference-tabs">
                <span class="tab active" data-tab="sach-hot">Sách bán chạy</span>
                <span class="tab" data-tab="sach-moi">Sách mới</span>
            </div>
            <div class="reference-content">
                <div class="reference-list reference-list-sach-hot product-grid active">
                <?php
                    if (mysqli_num_rows($result_reference) > 0) {
                        while ($book = mysqli_fetch_assoc($result_reference)) {
                            echo '<a href="index.php?page=bookDetail&id=' . $book['id'] . '" class="product-link">';
                            echo '<div class="product-card">';
                            echo '  <div class="product-image-wrap">';
                            if (!empty($book['label'])) {
                                echo '<span class="product-label">' . $book['label'] . '<i class="fa fa-fire"></i></span>';
                            }
                            echo '    <img src="' . $book['image'] . '" alt="' . $book['title'] . '" class="product-image">';
                            echo '  </div>';
                            echo '  <h3 class="product-title">' . $book['title'] . '</h3>';
                            echo '  <div class="product-price-row">';
                            echo '    <span class="product-price">' . number_format($book['price'], 0, ",", ".") . ' đ</span>';
                            if (!empty($book['discount'])) {
                                echo '    <span class="product-discount">' . $book['discount'] . '</span>';
                            }
                            echo '  </div>';
                            if (!empty($book['old_price'])) {
                                echo '<div class="product-old-price">' . number_format($book['old_price'], 0, ",", ".") . ' đ</div>';
                            }
                            echo '<div class="product-sold">Đã bán ' . $book['sold'] . '</div>';
                            echo '</div>';
                            echo '</a>';
                        }
                    } else {
                        echo '<p>Không có sách tham khảo nào trong database.</p>';
                    }
                ?>
            </div>
        </div>
    </div>
</div>

<script src="jquery/jquery-3.7.1.min.js"></script>
<script>
    $(function () {
        let current = 0;
        const $track = $('.slider-track');
        const $items = $('.slider-item');
        const total = $items.length;
        const show = 3; // Số lượng item hiển thị cùng lúc

        function updateSlider() {
            const itemWidth = $('.book-slider').width() / show;
            const offset = -current * itemWidth;
            $track.css('transform', 'translateX(' + offset + 'px)');
        }

        function nextSlide() {
            current = (current + 1) % (total - show + 1);
            updateSlider();
        }

        function prevSlide() {
            current = (current - 1 + (total - show + 1)) % (total - show + 1);
            updateSlider();
        }

        $('.slider-btn.next').click(nextSlide);
        $('.slider-btn.prev').click(prevSlide);

        setInterval(nextSlide, 3000);

        updateSlider();
    });
</script>

</body>