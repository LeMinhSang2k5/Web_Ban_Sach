<?php
$sql_reference = "SELECT * FROM books WHERE label = 'bán chạy' AND category = 'thiếu nhi' LIMIT 4";
$result_reference = mysqli_query($conn, $sql_reference);

$sql_combo = "SELECT * FROM books WHERE category = 'thiếu nhi' AND subcategory = 'combo thiếu nhi' LIMIT 4";
$result_combo = mysqli_query($conn, $sql_combo);

$sql_kim_dong = "SELECT * FROM books WHERE category = 'thiếu nhi' AND supplier = 'Nhà Xuất Bản Kim Đồng' LIMIT 4";
$result_kim_dong = mysqli_query($conn, $sql_kim_dong);

$sql_dinh_ti = "SELECT * FROM books WHERE category = 'thiếu nhi' AND supplier = 'Đình Tị' LIMIT 4";
$result_dinh_ti = mysqli_query($conn, $sql_dinh_ti);

function renderBookCard($book)
{
    $html = '<a href="index.php?page=bookDetail&id=' . $book['id'] . '" class="product-link">';
    $html .= '<div class="product-card">';
    $html .= '  <div class="product-image-wrap">';
    if (!empty($book['label'])) {
        $html .= '<span class="product-label">' . $book['label'] . '<i class="fa fa-fire"></i></span>';
    }
    $html .= '    <img src="' . $book['image'] . '" alt="' . $book['title'] . '" class="product-image">';
    $html .= '  </div>';
    $html .= '  <h3 class="product-title">' . $book['title'] . '</h3>';
    $html .= '  <div class="product-price-row">';
    $html .= '    <span class="product-price">' . number_format($book['price'], 0, ",", ".") . ' đ</span>';
    if (!empty($book['discount'])) {
        $html .= '    <span class="product-discount">' . $book['discount'] . '</span>';
    }
    $html .= '  </div>';
    if (!empty($book['old_price'])) {
        $html .= '<div class="product-old-price">' . number_format($book['old_price'], 0, ",", ".") . ' đ</div>';
    }
    $html .= '<div class="product-sold">Đã bán ' . $book['sold'] . '</div>';
    $html .= '</div>';
    $html .= '</a>';
    return $html;
}
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
                <div class="item-item" onclick="scrollToSection('sach-hot')">
                    <img src="assets/img/icon/sach-hot.png" alt="">
                </div>
                <div class="item-item" onclick="scrollToSection('combo')">
                    <img src="assets/img/icon/thieu-nhi/combo.png" alt="">
                </div>
                <div class="item-item">
                    <img src="assets/img/icon/goi-y.png" alt="">
                </div>
                <div class="item-item" onclick="scrollToSection('tu-sach')">
                    <img src="assets/img/icon/sach-theo-nha-cung-cap.png" alt="">
                </div>
            </div>
        </div>
        <div class="item-section" id="sach-hot">
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
                        <a href="index.php?page=bookDetail&id=10">
                            <img src="assets/img/img_book_slider/100-ki-nang.png" alt="">
                        </a>
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
                        <a href="index.php?page=bookDetail&id=13">
                            <img src="assets/img/img_book_slider/hoang-tu-be.png" alt="">
                        </a>
                    </div>
                </div>
                <button class="slider-btn next">&gt;</button>
            </div>
        </div>

        <div class="content-product">
            <div class="reference-product">
                <div class="reference-tabs">
                    <span class="tab active" data-tab="sach-hot">Sách bán chạy</span>
                </div>
                <div class="reference-content">
                    <div class="reference-list reference-list-sach-hot product-grid active">
                        <?php
                        if (mysqli_num_rows($result_reference) > 0) {
                            while ($book = mysqli_fetch_assoc($result_reference)) {
                                echo renderBookCard($book);
                            }
                        } else {
                            echo '<p>Không có sách tham khảo nào trong database.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="item-title">
            <img src="assets/img/banner/combo-banner.png" alt="Banner">
        </div>

        <div class="combo-slider" id="combo">
            <button class="slider-btn prev">&lt;</button>
            <div class="slider-track">
                <div class="slider-item">
                    <a href="index.php?page=bookDetail&id=26">
                        <img src="assets/img/img_book_slider/combo-slider/cuoc-doi-thien-tai.png" alt="">
                    </a>
                </div>
                <div class="slider-item">
                    <a href="index.php?page=bookDetail&id=27">
                        <img src="assets/img/img_book_slider/combo-slider/truyen-tranh-song-ngu.png" alt="">
                    </a>
                </div>
                <div class="slider-item">
                    <a href="index.php?page=bookDetail&id=28">
                        <img src="assets/img/img_book_slider/combo-slider/kns-dau-doi.png" alt="">
                    </a>
                </div>
                <div class="slider-item">
                    <a href="index.php?page=bookDetail&id=29">
                        <img src="assets/img/img_book_slider/combo-slider/xon-xao-dau-doi.png" alt="">
                    </a>
                </div>
            </div>
            <button class="slider-btn next">&gt;</button>
        </div>

        <div class="content-product">
            <div class="reference-product">
                <div class="reference-tabs">
                    <span class="tab active">Combo thiếu nhi</span>
                </div>
                <div class="reference-content">
                    <div class="reference-list product-grid active">
                        <?php
                        if (mysqli_num_rows($result_combo) > 0) {
                            while ($book = mysqli_fetch_assoc($result_combo)) {
                                echo renderBookCard($book);
                            }
                        } else {
                            echo '<p>Không có sách tham khảo nào trong database.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="item-title" id="tu-sach">
            <img src="assets/img/banner/tu-sach.png" alt="Banner">
        </div>

        <div class="content-product">
            <div class="reference-product">
                <div class="reference-tabs">
                    <span class="tab active">Kim Đồng</span>
                </div>
                <div class="reference-content">
                    <div class="reference-list product-grid active">
                        <?php
                        if (mysqli_num_rows($result_kim_dong) > 0) {
                            while ($book = mysqli_fetch_assoc($result_kim_dong)) {
                                echo renderBookCard($book);
                            }
                        } else {
                            echo '<p>Không có sách tham khảo nào trong database.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-product">
            <div class="reference-product">
                <div class="reference-tabs">
                    <span class="tab active">Đình Tị</span>
                </div>
                <div class="reference-content">
                    <div class="reference-list product-grid active">
                        <?php
                        if (mysqli_num_rows($result_dinh_ti) > 0) {
                            while ($book = mysqli_fetch_assoc($result_dinh_ti)) {
                                echo renderBookCard($book);
                            }
                        } else {
                            echo '<p>Không có sách tham khảo nào trong database.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="jquery/jquery-3.7.1.min.js"></script>
    <script>
        $(function () {
            let current = 0;
            const $track = $('.book-slider .slider-track');
            const $items = $('.book-slider .slider-item');
            const total = $items.length;
            const show = 3; // Số lượng item hiển thị cùng lúc

            function updateBookSlider() {
                const itemWidth = $('.book-slider').width() / show;
                const offset = -current * itemWidth;
                $track.css('transform', 'translateX(' + offset + 'px)');
            }

            function nextBookSlide() {
                current = (current + 1) % (total - show + 1);
                updateBookSlider();
            }

            function prevBookSlide() {
                current = (current - 1 + (total - show + 1)) % (total - show + 1);
                updateBookSlider();
            }

            $('.book-slider .slider-btn.next').click(nextBookSlide);
            $('.book-slider .slider-btn.prev').click(prevBookSlide);

            setInterval(nextBookSlide, 3000);

            updateBookSlider();
        });

        function scrollToSection(targetId) {
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }

        // Combo slider - hiển thị 1 hình mỗi lần
        $(function () {
            let comboCurrent = 0;
            const $comboTrack = $('.combo-slider .slider-track');
            const $comboItems = $('.combo-slider .slider-item');
            const comboTotal = $comboItems.length;

            function updateComboSlider() {
                const itemWidth = $('.combo-slider').width();
                const offset = -comboCurrent * itemWidth;
                $comboTrack.css('transform', 'translateX(' + offset + 'px)');
            }

            function nextComboSlide() {
                comboCurrent = (comboCurrent + 1) % comboTotal;
                updateComboSlider();
            }

            function prevComboSlide() {
                comboCurrent = (comboCurrent - 1 + comboTotal) % comboTotal;
                updateComboSlider();
            }

            $('.combo-slider .slider-btn.next').click(nextComboSlide);
            $('.combo-slider .slider-btn.prev').click(prevComboSlide);

            // updateComboSlider();
        });
    </script>

</body>