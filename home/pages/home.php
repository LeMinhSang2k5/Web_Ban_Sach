<?php
// Lấy dữ liệu sách từ database

// Truy vấn cho sách bán chạy
$sql_bestseller = "SELECT * FROM books WHERE label = 'Bán chạy'";
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
?>

<body>
    <div class="main-contain">

        <div>
            <img src="./assets/img/banner/banner1.jpg" alt="Banner" id="banner">
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
                        $books_luyenthi = [
                            [
                                'title' => 'Tổng ôn hoá học',
                                'price' => 199000,
                                'old_price' => 250000,
                                'discount' => '-20%',
                                'sold' => 583,
                                'label' => 'Bán chạy',
                                'image' => './assets/img/sach-luyen-thi/hoahoc.png'
                            ],
                            [
                                'title' => 'Kỹ năng viết văn bản nghị luận',
                                'price' => 47000,
                                'old_price' => 60000,
                                'discount' => '-21%',
                                'sold' => 175,
                                'label' => '',
                                'image' => './assets/img/sach-luyen-thi/nghiluanvanhoc.png'
                            ]
                        ];
                        
                        foreach ($books_luyenthi as $book) {
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
                        ?>
                    </div>
                </div>
                <div class="see-more-wrap">
                    <button class="see-more-btn">Xem Thêm</button>
                </div>
            </div>
        </div>

        <div class="content-product" id="sach-moi">
            <div class="category-section">
                <div class="category-header">
                    <span class="category-icon">
                        <img src="./assets/img/icon/ico_sach.png" alt="icon" />
                    </span>
                    <span class="category-title">GỢI Ý SÁCH HAY CHO BẠN</span>
                </div>
                <div class="category-list">
                    <div class="category-item">
                        <img src="./assets/img/sach-thieu-nhi/harry-potter.png" alt="Harry Potter">
                        <div class="category-name">Harry Potter</div>
                    </div>
                    <div class="category-item">
                        <img src="./assets/img/sach-luyen-thi/tong-on-toan-hoc.png" alt="Ôn Luyện THPT">
                        <div class="category-name">Ôn Luyện THPT</div>
                    </div>
                    <div class="category-item">
                        <img src="./assets/img/sach-van-hoc/bupsenxanh.png" alt="Ôn Luyện THPT">
                        <div class="category-name">Búp sen xanh</div>
                    </div>

                    <div class="category-item">
                        <img src="./assets/img/sach-thieu-nhi/dat-rung-phuong-nam.png" alt="Ôn Luyện THPT">
                        <div class="category-name">Đất rừng phương nam</div>
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
        });
    </script>

</body>