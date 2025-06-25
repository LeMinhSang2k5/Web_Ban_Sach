<?php

// Truy vấn cho sách bán chạy
$sql_bestseller = "SELECT * FROM books WHERE label = 'Bán chạy' LIMIT 4";
$result_bestseller = mysqli_query($conn, $sql_bestseller);

// Truy vấn cho sách tham khảo
$sql_vanhoc = "SELECT * FROM books WHERE category = 'Văn học' LIMIT 4 OFFSET 10";
$result_vanhoc = mysqli_query($conn, $sql_vanhoc);

$sql_thieunhi = "SELECT * FROM books WHERE category = 'Thiếu nhi' LIMIT 4";
$result_thieunhi = mysqli_query($conn, $sql_thieunhi);

$sql_kim_dong = "SELECT * FROM books WHERE supplier = 'Nhà Xuất Bản Kim Đồng' LIMIT 4";
$result_kim_dong = mysqli_query($conn, $sql_kim_dong);

$sql_dinhti = "SELECT * FROM books WHERE supplier = 'Đình Tị' LIMIT 4";
$result_dinhti = mysqli_query($conn, $sql_dinhti);

$sql_ngoai_ngu = "SELECT * FROM books WHERE category = 'Sach ngoai ngu' LIMIT 4";
$result_ngoai_ngu = mysqli_query($conn, $sql_ngoai_ngu);

$sql_az_vn = "SELECT * FROM books WHERE supplier = 'AZ Việt Nam' LIMIT 4";
$result_az_vn = mysqli_query($conn, $sql_az_vn);

$sql_nxb_vanhoc = "SELECT * FROM books WHERE publisher = 'NXB Văn Học' LIMIT 4";
$result_nxb_vanhoc = mysqli_query($conn, $sql_nxb_vanhoc);

$sql_khoa_hoc = "SELECT * FROM books WHERE category = 'Sach khoa hoc' LIMIT 4";
$result_khoa_hoc = mysqli_query($conn, $sql_khoa_hoc);

$sql_kinh_te = "SELECT * FROM books WHERE category = 'Kinh te' LIMIT 4";
$result_kinh_te = mysqli_query($conn, $sql_kinh_te);

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


<div class="main-contain">
    <!-- <div class="slider-banner">
        <button class="slider-btn prev">&lt;</button>
        <div class="slider-track">
            <div class="slider-item">
                <img src="./assets/img/banner/home/don-luong-ve.png" alt="Banner">
            </div>
            <div class="slider-item">
                <img src="./assets/img/banner/home/banner2.png" alt="Banner">
            </div>
        </div>
        <button class="slider-btn next">&gt;</button>
    </div> -->

    <div class="item-banner">
        <img src="./assets/img/banner/home/banner-he-1.png" alt="Banner">
    </div>


    <div class="item-banner">
        <img src="./assets/img/banner/home/ban-chay.png" alt="Banner">
    </div>

    <div class="content-product" id="sach-moi">
        <div class="category-header">
            <span class="category-icon">
                <img src="./assets/img/icon/ico_sach.png" alt="icon" />
            </span>
            <span class="category-title">Sách Bán Chạy</span>
        </div>
        <div class="product-grid">
            <?php
            if (mysqli_num_rows($result_bestseller) > 0) {
                while ($book = mysqli_fetch_assoc($result_bestseller)) {
                    echo renderBookCard($book);
                }
            } else {
                echo '<p>Không có sách bán chạy nào trong database.</p>';
            }
            ?>
        </div>
    </div>

    
    <div class="two-book-slide">
        <button class="slider-btn prev">&lt;</button>
        <div class="slider-tracks">
            <div class="slider-items">
                <a href="index.php?page=bookDetail&id=19">
                    <img src="./assets/img/home/ho-diep.png">
                </a>
            </div>
            <div class="slider-items">
                <img src="./assets/img/home/thuy-tram.png">
            </div>
            <div class="slider-items">
                <img src="./assets/img/home/stop-overthinking.png">
            </div>
            <div class="slider-items"> 
                <img src="./assets/img/home/chia-se.png">
            </div>
        </div>
        <button class="slider-btn next">&gt;</button>
    </div>
    
    <div class="item-banner">
        <img src="./assets/img/banner/home/xa-kho.png" alt="Banner">
    </div>

    <div class="content-product" id="sach-tham-khao">
        <div class="reference-product">
            <div class="reference-tabs">
                <span class="tab active" data-tab="thamkhao">Văn Học</span>
                <span class="tab" data-tab="luyenthi">Thiếu Nhi</span>
            </div>
            <div class="reference-content">
                <div class="reference-list reference-list-thamkhao product-grid active">
                    <?php
                    if (mysqli_num_rows($result_vanhoc) > 0) {
                        while ($book = mysqli_fetch_assoc($result_vanhoc)) {
                            echo renderBookCard($book);
                        }
                    } else {
                        echo '<p>Không có sách văn học nào trong database.</p>';
                    }
                    ?>
                </div>
                <div class="reference-list reference-list-luyenthi product-grid">
                    <?php
                    if (mysqli_num_rows($result_thieunhi) > 0) {
                        while ($book = mysqli_fetch_assoc($result_thieunhi)) {
                            echo renderBookCard($book);
                        }
                    } else {
                        echo '<p>Không có sách luyện thi nào trong database.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="item-banner">
        <img src="./assets/img/banner/home/khoa-hoc.png" alt="Banner">
    </div>

    <div class="content-product" id="sach-khoa-hoc">
        <div class="reference-product">
            <div class="reference-tabs">
                <span class="tab active" data-tab="khoa-hoc">Sách Khoa Học</span>
                <span class="tab" data-tab="kinh-te">Kinh Tế</span>
            </div>
        </div>
        <div class="reference-content">
            <div class="reference-list reference-list-khoa-hoc product-grid active">
                <?php
                if (mysqli_num_rows($result_khoa_hoc) > 0) {
                    while ($book = mysqli_fetch_assoc($result_khoa_hoc)) {
                        echo renderBookCard($book);
                    }
                } else {
                    echo '<p>Không có sách bán chạy nào trong database.</p>';
                }
                ?>
            </div>
            <div class="reference-list reference-list-kinh-te product-grid">
                <?php
                if (mysqli_num_rows($result_kinh_te) > 0) {
                    while ($book = mysqli_fetch_assoc($result_kinh_te)) {
                        echo renderBookCard($book);
                    }
                } else {
                    echo '<p>Không có sách bán chạy nào trong database.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="item-banner">
        <img src="./assets/img/banner/home/thuong-hieu.png" alt="Banner">
    </div>


    <div class="content-product" id="sach-thuong-hieu">
        <div class="reference-product">
            <div class="reference-tabs">
                <span class="tab active" data-tab="kimdong">Kim Đồng</span>
                <span class="tab" data-tab="dinhti">Đình Tị</span>
            </div>
            <div class="reference-content">
                <div class="reference-list reference-list-kimdong product-grid active">
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
                <div class="reference-list reference-list-dinhti product-grid">
                    <?php
                    if (mysqli_num_rows($result_dinhti) > 0) {
                        while ($book = mysqli_fetch_assoc($result_dinhti)) {
                            echo renderBookCard($book);
                        }
                    } else {
                        echo '<p>Không có sách luyện thi nào trong database.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="content-product" id="sach-nha-xuat-ban">
        <div class="reference-product">
            <div class="reference-tabs">
                <span class="tab active" data-tab="az-vn">AZ Việt Nam</span>
                <span class="tab" data-tab="thanh-nien">Thanh Niên</span>
            </div>
            <div class="reference-content">
                <div class="reference-list reference-list-az-vn product-grid active">
                    <?php
                    if (mysqli_num_rows($result_az_vn) > 0) {
                        while ($book = mysqli_fetch_assoc($result_az_vn)) {
                            echo renderBookCard($book);
                        }
                    } else {
                        echo '<p>Không có sách tham khảo nào trong database.</p>';
                    }
                    ?>
                </div>
                <div class="reference-list reference-list-thanh-nien product-grid">
                    <?php
                    if (mysqli_num_rows($result_nxb_vanhoc) > 0) {
                        while ($book = mysqli_fetch_assoc($result_nxb_vanhoc)) {
                            echo renderBookCard($book);
                        }
                    } else {
                        echo '<p>Không có sách luyện thi nào trong database.</p>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function setupTabSwitching(containerSelector) {
        const $container = $(containerSelector);

        $container.find('.tab').click(function () {
            $container.find('.tab').removeClass('active');
            $(this).addClass('active');
            $container.find('.reference-list').removeClass('active');
            const tabName = $(this).data('tab');
            $container.find('.reference-list-' + tabName).addClass('active');
        });

    }

    $(function () {
        let bannerCurrent = 0;
        const $bannerTrack = $('.slider-banner .slider-track');
        const $bannerItems = $('.slider-banner .slider-item');
        const bannerTotal = $bannerItems.length;

        function updateBannerSlider() {
            const itemWidth = $('.slider-banner').width();
            const offset = -bannerCurrent * itemWidth;
            $bannerTrack.css('transform', 'translateX(' + offset + 'px)');
        }

        function nextBannerSlide() {
            bannerCurrent = (bannerCurrent + 1) % bannerTotal;
            updateBannerSlider();
        }

        function prevBannerSlide() {
            bannerCurrent = (bannerCurrent - 1 + bannerTotal) % bannerTotal;
            updateBannerSlider();
        }

        $('.slider-banner .slider-btn.next').click(nextBannerSlide);
        $('.slider-banner .slider-btn.prev').click(prevBannerSlide);
    });

    $(function () {
        let current = 0;
        const $track = $('.two-book-slide .slider-tracks');
        const $items = $('.two-book-slide .slider-items');
        const total = $items.length;
        const show = 2; // Số lượng item hiển thị cùng lúc

        function updateBookSlider() {
                const itemWidth = $('.two-book-slide').width() / show;
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

        $('.two-book-slide .slider-btn.next').click(nextBookSlide);
        $('.two-book-slide .slider-btn.prev').click(prevBookSlide);
    });

    $(document).ready(function () {
        setupTabSwitching('#sach-tham-khao');
        setupTabSwitching('#sach-thuong-hieu');
        setupTabSwitching('#sach-ngoai-ngu');
        setupTabSwitching('#sach-nha-xuat-ban');
        setupTabSwitching('#sach-khoa-hoc');
        // Xử lý sách gợi ý
        $('.category-item').click(function () {
            var id = $(this).data('id');
            window.location.href = 'index.php?page=bookDetail&id=' + id;
        });
    });
</script>