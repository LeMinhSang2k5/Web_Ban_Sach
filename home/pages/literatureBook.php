<?php
$sql_reference = "SELECT * FROM books WHERE category = 'Văn học' AND subcategory = 'Tiểu thuyết' LIMIT 4";
$sql_combo = "SELECT * FROM books WHERE category = 'Văn học' AND subcategory = 'Combo văn học' LIMIT 4";
$sql_new = "SELECT * FROM books WHERE category = 'Văn học' AND subcategory = 'Sách mới sắp phát hành' LIMIT 4";
$result_reference = mysqli_query($conn, $sql_reference);
$result_combo = mysqli_query($conn, $sql_combo);
$result_new = mysqli_query($conn, $sql_new);
?>

<body>
    <div class="main-container">
        <div class="banner-container">
            <img src="assets/img/banner/banner-sach-van-hoc.png" alt="Banner">
        </div>
        <div class="item-sections">
            <div class="item-list">
                <div class="item-item">
                    <img src="assets/img/icon/vanhoc/newvanhoc.png" alt="">
                </div>
                <div class="item-item">
                    <img src="assets/img/icon/vanhoc/99kvanhoc.png" alt="">
                </div>
                <div class="item-item">
                    <img src="assets/img/icon/vanhoc/uudaivanhoc.png" alt="">
                </div>
                <div class="item-item">
                    <img src="assets/img/icon/vanhoc/couponvanhoc.png" alt="">
                </div>
            </div>
            <div class="item-title">
                <img src="assets/img/icon/vanhoc/voucher.png" alt="">
            </div>
            <div class="item-title">
                <img src="assets/img/icon/vanhoc/sachbanchay.png" alt="">
            </div>
            <div class="book-list-container">
                <div class="item-books">
                    <a href="index.php?page=bookDetail&id=21">
                        <img src="assets/img/icon/vanhoc/999lathu.png" alt="">
                    </a>
                </div>
                <div class="item-books">
                    <a href="index.php?page=bookDetail&id=22">
                        <img src="assets/img/icon/vanhoc/tron.png" alt="">
                    </a>
                </div>
                <div class="item-books">
                    <a href="index.php?page=bookDetail&id=23">
                        <img src="assets/img/icon/vanhoc/thuong.png" alt="">
                    </a>
                </div>
                <div class="item-books">
                    <a href="index.php?page=bookDetail&id=24">
                        <img src="assets/img/icon/vanhoc/caycam.png" alt="">
                    </a>
                </div>
                <div class="item-books">
                    <a href="index.php?page=bookDetail&id=3">
                        <img src="assets/img/icon/vanhoc/giakim.png" alt="">
                    </a>
                </div>
                <div class="item-books">
                    <a href="index.php?page=bookDetail&id=25">
                        <img src="assets/img/icon/vanhoc/tet.png" alt="">
                    </a>
                </div>
            </div>
            <div class="item-title">
                <img src="assets/img/icon/vanhoc/khotanvh.png" alt="">
            </div>
            <div class="item-title">
                <img src="assets/img/icon/vanhoc/tieuthuyet.png" alt="">
            </div>
            <div class="content-product">
                <div class="reference-product">
                    <div class="reference-tabs">
                        <span class="tab active" data-tab="tieu-thuyet">Tiểu Thuyết</span>
                    </div>
                    <div class="reference-content">
                        <div class="reference-list reference-list-tieu-thuyet product-grid active">
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
            <div class="item-title">
                <img src="assets/img/icon/vanhoc/combo.png" alt="">
            </div>
            <div class="content-product">
                <div class="reference-product">
                    <div class="reference-tabs">
                        <span class="tab active" data-tab="combo-van-hoc">ComBo Văn Học</span>
                    </div>
                    <div class="reference-content">
                        <div class="reference-list reference-list-combo-van-hoc product-grid active">
                            <?php
                            if (mysqli_num_rows($result_reference) > 0) {
                                while ($book = mysqli_fetch_assoc($result_combo)) {
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
            <div class="item-title">
                <img src="assets/img/icon/vanhoc/sachmoiphathanh.png" alt="">
            </div>
            <div class="content-product">
                <div class="reference-product">
                    <div class="reference-tabs">
                        <span class="tab active" data-tab="sach-moi-phathanh">Sách Mới Phát Hành</span>
                    </div>
                    <div class="reference-content">
                        <div class="reference-list reference-list-sach-moi-phathanh product-grid active">
                            <?php
                            if (mysqli_num_rows($result_new) > 0) {
                                while ($book = mysqli_fetch_assoc($result_new)) {
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
            <div class="item-title">
                <img src="assets/img/icon/vanhoc/truyenngan.png" alt="">
            </div>
            <div class="content-product">
                <div class="reference-product">
                    <div class="reference-tabs">
                        <span class="tab active" data-tab="truyen-ngan">Truyện Ngắn</span>
                    </div>
                    <div class="reference-content">
                        <div class="reference-list reference-list-truyen-ngan product-grid active">
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
</body>