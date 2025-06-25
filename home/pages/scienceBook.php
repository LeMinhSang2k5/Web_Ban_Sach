<?php

$sql_best_seller = "SELECT * FROM books WHERE category = 'Khoa học' AND label = 'Bán chạy' LIMIT 4";
$result_best_seller = mysqli_query($conn, $sql_best_seller);

$sql_tri_tue_nhan_tao = "SELECT * FROM books WHERE subcategory = 'AI' LIMIT 4";
$result_tri_tue_nhan_tao = mysqli_query($conn, $sql_tri_tue_nhan_tao);

$sql_kinh_doanh = "SELECT * FROM books WHERE category = 'Kinh tế' LIMIT 4";
$result_kinh_doanh = mysqli_query($conn, $sql_kinh_doanh);

$sql_khoa_hoc_nghien_cuu = "SELECT * FROM books WHERE category = 'Khoa học' AND subcategory = 'Nghiên cứu' LIMIT 4";
$result_khoa_hoc_nghien_cuu = mysqli_query($conn, $sql_khoa_hoc_nghien_cuu);

$sql_giao_duc = "SELECT * FROM books WHERE category = 'Khoa học' AND subcategory = 'Giáo dục' LIMIT 4";
$result_giao_duc = mysqli_query($conn, $sql_giao_duc);

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

<div class="main-content">
    <div class="banner-container">
        <img src="assets/img/banner/khoa-hoc/banner-khoa-hoc.png" alt="Banner">
    </div>
    <div class="item-section">
        <div class="item-title">
            <img src="assets/img/banner/khoa-hoc/giam-gia.png" alt="">
        </div>
        <div class="item-title-info">
            <img src="assets/img/banner/khoa-hoc/info.png" alt="">
        </div>
        <div class="item-list">
            <div class="item-item" onclick="scrollToSection('ban-chay')">
                <img src="assets/img/icon/khoa-hoc/ban-chay.png" alt="">
            </div>
            <div class="item-item" onclick="scrollToSection('tri-tue-nhan-tao')">
                <img src="assets/img/icon/khoa-hoc/tri-tue-nhan-tao.png" alt="">
            </div>
            <div class="item-item" onclick="scrollToSection('kinh-doanh')">
                <img src="assets/img/icon/khoa-hoc/kinh-doanh.png" alt="">
            </div>
            <div class="item-item" onclick="scrollToSection('khoa-hoc-nghien-cuu')">
                <img src="assets/img/icon/khoa-hoc/khoa-hoc-nghien-cuu.png" alt="">
            </div>
            <div class="item-item" onclick="scrollToSection('giao-duc')">
                <img src="assets/img/icon/khoa-hoc/phat-trien-ca-nhan.png" alt="">
            </div>
        </div>


        <div class="item-title" id="ban-chay">
            <img src="assets/img/banner/khoa-hoc/ban-chay.png" alt="">
        </div>

        <div class="banner-slider">
            <button class="slider-btn prev">&lt;</button>
            <div class="slider-track">
                <div class="slider-item">
                    <a href="index.php?page=bookDetail&id=59">
                        <img src="assets/img/img_book_slider/khoa-hoc/chatgpt.png" alt="">
                    </a>
                </div>
                <div class="slider-item">
                    <a href="index.php?page=bookDetail&id=67">
                        <img src="assets/img/img_book_slider/khoa-hoc/AI-cong-cu.png" alt="">
                    </a>
                </div>
                <div class="slider-item">
                    <a href="index.php?page=bookDetail&id=66">
                        <img src="assets/img/img_book_slider/khoa-hoc/chatgpt-thuc-chien.png" alt="">
                    </a>
                </div>
                <div class="slider-item">
                    <a href="index.php?page=bookDetail&id=68">
                        <img src="assets/img/img_book_slider/khoa-hoc/AI-5.0.png" alt="">
                    </a>
                </div>
            </div>
            <button class="slider-btn next">&gt;</button>
        </div>
    </div>

    <div class="content-product">
        <div class="reference-product">
            <div class="reference-tabs">
                <span class="tab active">Bán chạy</span>
            </div>
            <div class="reference-content">
                <div class="reference-list product-grid active">
                    <?php
                    if (mysqli_num_rows($result_best_seller) > 0) {
                        while ($book = mysqli_fetch_assoc($result_best_seller)) {
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

    <div class="item-title" id="tri-tue-nhan-tao">
        <img src="assets/img/banner/khoa-hoc/tri-tue-nhan-tao.png" alt="">
    </div>


    <div class="content-product">
        <div class="reference-product">
            <div class="reference-tabs">
                <span class="tab active">Trí tuệ nhân tạo</span>
            </div>
            <div class="reference-content">
                <div class="reference-list product-grid active">
                    <?php
                    if (mysqli_num_rows($result_tri_tue_nhan_tao) > 0) {
                        while ($book = mysqli_fetch_assoc($result_tri_tue_nhan_tao)) {
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

    <div class="item-title" id="kinh-doanh">
        <img src="assets/img/banner/khoa-hoc/kinh-doanh.png" alt="">
    </div>

    <div class="content-product">
        <div class="reference-product">
            <div class="reference-tabs">
                <span class="tab active">Kinh doanh & Quản lý</span>
            </div>
            <div class="reference-content">
                <div class="reference-list product-grid active">
                    <?php
                    if (mysqli_num_rows($result_kinh_doanh) > 0) {
                        while ($book = mysqli_fetch_assoc($result_kinh_doanh)) {
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

    <div class="item-title" id="khoa-hoc-nghien-cuu">
        <img src="assets/img/banner/khoa-hoc/nghien-cuu.png" alt="">
    </div>

    <div class="content-product">
        <div class="reference-product">
            <div class="reference-tabs">
                <span class="tab active">Khoa học nghiên cứu</span>
            </div>
            <div class="reference-content">
                <div class="reference-list product-grid active">
                    <?php
                    if (mysqli_num_rows($result_khoa_hoc_nghien_cuu) > 0) {
                        while ($book = mysqli_fetch_assoc($result_khoa_hoc_nghien_cuu)) {
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

    <div class="item-title" id="giao-duc">
        <img src="assets/img/banner/khoa-hoc/giao-duc.png" alt="">
    </div>

    <div class="content-product">
        <div class="reference-product">
            <div class="reference-tabs">
                <span class="tab active">Giáo dục</span>
            </div>
            <div class="reference-content">
                <div class="reference-list product-grid active">
                    <?php
                    if (mysqli_num_rows($result_giao_duc) > 0) {
                        while ($book = mysqli_fetch_assoc($result_giao_duc)) {
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
        const $bannerTrack = $('.banner-slider .slider-track');
        const $bannerItems = $('.banner-slider .slider-item');
        const bannerTotal = $bannerItems.length;

        function updateBannerSlider() {
            const itemWidth = $('.banner-slider').width();
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

        $('.banner-slider .slider-btn.next').click(nextBannerSlide);
        $('.banner-slider .slider-btn.prev').click(prevBannerSlide);
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

    $(document).ready(function () {
        // Xử lý sách gợi ý
        $('.category-item').click(function () {
            var id = $(this).data('id');
            window.location.href = 'index.php?page=bookDetail&id=' + id;
        });
    });
</script>