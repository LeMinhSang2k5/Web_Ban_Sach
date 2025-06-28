<?php
// Lấy dữ liệu sách từ database

// Truy vấn cho sách bán chạy
$sql_bestseller = "SELECT * FROM books WHERE label = 'Bán chạy' LIMIT 4";
$result_bestseller = mysqli_query($conn, $sql_bestseller);

if (!$result_bestseller) {
    die("Lỗi truy vấn sách bán chạy: " . mysqli_error($conn));
}

// Truy vấn cho sách tham khảo
$sql_reference = "SELECT * FROM books WHERE category = 'Sach tham khao'";
$result_reference = mysqli_query($conn, $sql_reference);

if (!$result_reference) {
    die("Lỗi truy vấn sách tham khảo: " . mysqli_error($conn));
}

$sql_luyenthi = "SELECT * FROM books WHERE category = 'Sach luyen thi'";
$result_luyenthi = mysqli_query($conn, $sql_luyenthi);

if (!$result_luyenthi) {
    die("Lỗi truy vấn sách luyện thi: " . mysqli_error($conn));
}

?>

<body>
    <div class="main-contain">
        <div class="banner-all">
            <div class="carousel-wrapper">
                <button class="banner-btn prev">&lt;</button>

                <div class="book-banner">
                    <div class="banner-track">
                        <div class="banner-item1">
                            <img src="assets/img/banner/banner2.webp" alt="">
                        </div>

                        <div class="banner-item1">
                            <img src="assets/img/banner/banner2.webp" alt="">
                        </div>
                        <div class="banner-item1">
                            <img src="assets/img/banner/banner2.webp" alt="">
                        </div>
                        <div class="banner-item1">
                            <img src="assets/img/banner/banner2.webp" alt="">
                        </div>
                        <div class="banner-item1">
                            <img src="assets/img/banner/banner2.webp" alt="">
                        </div>

                    </div>
                </div>

                <button class="banner-btn next">&gt;</button>
            </div>

            <div class="left-banner">
                <div class="banner-item2">
                    <img src="assets/img/banner/banner1.webp" alt="">
                </div>
                <div class="banner-item3">
                    <img src="assets/img/banner/banner3.webp" alt="">
                </div>
            </div>
        </div>
        <div class="content-product" id="sach-moi">
            <h1>Những cuốn sách nổi bật</h1>
            <div class="product-grid">
                <?php
                if (mysqli_num_rows($result_bestseller) > 0) {
                    while ($book = mysqli_fetch_assoc($result_bestseller)) {
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
                    echo '<p>Không có sách bán chạy nào trong database.</p>';
                }
                ?>
            </div>
        </div>

        <div class="content-product" id="sach-tham-khao">
            <div class="reference-product">
                <div class="reference-tabs">
                    <span class="tab active" data-tab="thamkhao">Sách tham khảo</span>
                    <span class="tab" data-tab="luyenthi">Sách luyện thi</span>
                </div>
                <div class="reference-content">
                    <div class="reference-list reference-list-thamkhao product-grid active">
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
                    <div class="reference-list reference-list-luyenthi product-grid">
                        <?php
                        if (mysqli_num_rows($result_luyenthi) > 0) {
                            while ($book = mysqli_fetch_assoc($result_luyenthi)) {
                                echo '<a href="index.php?page=bookDetail&id=' . $book['id'] . '" class="product-link">';
                                echo '<div class="product-card">';
                                echo '  <div class="product-image-wrap">';
                                if (isset($book['label']) && $book['label']) {
                                    echo '<span class="product-label">' . $book['label'] . '<i class="fa fa-fire"></i></span>';
                                }
                                echo '    <img src="' . $book['image'] . '" alt="' . $book['title'] . '" class="product-image">';
                                echo '  </div>';
                                echo '  <h3 class="product-title">' . $book['title'] . '</h3>';
                                echo '  <div class="product-price-row">';
                                echo '    <span class="product-price">' . number_format($book['price'], 0, ",", ".") . ' đ</span>';
                                if ($book['discount']) {
                                    echo '    <span class="product-discount">' . $book['discount'] . '</span>';
                                }
                                echo '  </div>';
                                if ($book['old_price']) {
                                    echo '<div class="product-old-price">' . number_format($book['old_price'], 0, ",", ".") . ' đ</div>';
                                }
                                echo '<div class="product-sold">Đã bán ' . $book['sold'] . '</div>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p>Không có sách luyện thi nào trong database.</p>';
                        }
                        ?>
                    </div>
                </div>
                <div class="see-more-wrap">
                    <button class="see-more-btn">Xem Thêm</button>
                </div>
            </div>
        </div>

        <div class="content-product">
            <div class="category-section">
                <div class="category-header">
                    <span class="category-icon">
                        <img src="./assets/img/icon/ico_sach.png" alt="icon" />
                    </span>
                    <span class="category-title">GỢI Ý SÁCH HAY CHO BẠN</span>
                </div>
                <div class="category-list">
                    <div class="category-item">
                        <a href="index.php?page=bookDetail&id=2">
                            <img src="./assets/img/sach-van-hoc/bupsenxanh.png" alt="Ôn Luyện THPT">
                            <div class="category-name">Búp sen xanh</div>
                        </a>
                    </div>

                    <div class="category-item">
                        <a href="index.php?page=bookDetail&id=9">
                            <img src="./assets/img/sach-thieu-nhi/dat-rung-phuong-nam.png" alt="Ôn Luyện THPT">
                            <div class="category-name">Đất rừng phương nam</div>
                        </a>
                    </div>

                    <div class="category-item">
                        <img src="./assets/img/sach-hoc-ngoai-ngu/HSK-1.png" alt="Ôn Luyện THPT">
                        <div class="category-name">Giáo trình HSK 1</div>
                    </div>

                    <div class="category-item">
                        <img src="./assets/img/sach-hoc-ngoai-ngu/destination-B1.png" alt="Ôn Luyện THPT">
                        <div class="category-name">Destination B1</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.reference-tabs .tab').click(function() {
                // Xóa class active của tất cả tab
                $('.reference-tabs .tab').removeClass('active');
                // Thêm class active cho tab được click
                $(this).addClass('active');

                // Xóa class active của tất cả danh sách
                $('.reference-list').removeClass('active');
                // Thêm class active cho danh sách tương ứng với tab được click
                const tabName = $(this).data('tab');
                $('.reference-list-' + tabName).addClass('active');
            });
            $('.category-item').click(function() {
                var id = $(this).data('id');
                window.location.href = 'index.php?page=bookDetail&id=' + id;
            });

        });
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(function() {
            const $track = $('.banner-track');
            const $items = $('.banner-item1');
            const total = $items.length;
            const realCount = total - 2; // Trừ clone đầu/cuối
            const itemWidth = $('.book-banner').width();
            let current = 1; // bắt đầu từ ảnh thật đầu tiên (vị trí 1)

            function updateSlider(animate = true) {
                const offset = -current * itemWidth;
                if (animate) {
                    $track.css({
                        transform: `translateX(${offset}px)`,
                        transition: 'transform 0.5s ease'
                    });
                } else {
                    $track.css({
                        transform: `translateX(${offset}px)`,
                        transition: 'none'
                    });
                }
            }

            function nextSlide() {
                current++;
                updateSlider();

                // Nếu đến clone đầu → nhảy về ảnh thật đầu sau hiệu ứng
                if (current === total - 1) {
                    setTimeout(() => {
                        current = 1;
                        updateSlider(false);
                    }, 500); // delay phải khớp transition
                }
            }

            function prevSlide() {
                current--;
                updateSlider();

                // Nếu đến clone cuối → nhảy về ảnh thật cuối
                if (current === 0) {
                    setTimeout(() => {
                        current = total - 2;
                        updateSlider(false);
                    }, 500);
                }
            }

            $('.banner-btn.next').click(nextSlide);
            $('.banner-btn.prev').click(prevSlide);

            setInterval(nextSlide, 4000);

            updateSlider(false); // setup vị trí bắt đầu
        });
    </script>


</body>