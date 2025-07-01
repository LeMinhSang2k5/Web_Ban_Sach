<?php
$sql_dong_chay_van_hoa = "SELECT * FROM books WHERE category = 'Lịch sử' AND subcategory = 'Dòng chảy văn hóa' LIMIT 4";
$sql_van_hoa_tao_nen_lich_su = "SELECT * FROM books WHERE category = 'Lịch sử' AND subcategory = 'Văn hóa tạo nên lịch sử' LIMIT 4";
$result_dong_chay_van_hoa = mysqli_query($conn, $sql_dong_chay_van_hoa);
$result_van_hoa_tao_nen_lich_su = mysqli_query($conn, $sql_van_hoa_tao_nen_lich_su);

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
        <img src="assets/img/banner/history/banner1.png" alt="Banner">
        <div class="item-book">
            <a href="index.php?page=bookDetail&id=71">
                <img src="assets/img/banner/history/banner2.png" alt="Banner">
            </a>
        </div>
        <div class="item-book">
            <a href="index.php?page=bookDetail&id=72">
                <img src="assets/img/banner/history/banner3.png" alt="Banner">
            </a>
        </div>
        <div class="item-book">
            <a href="index.php?page=bookDetail&id=73">
                <img src="assets/img/banner/history/banner4.png" alt="Banner">
            </a>
        </div>
        <div class="item-title">
            <img src="assets/img/banner/history/banner5.png" alt="Banner">
        </div>
    </div>
    
    
    <div class="content-product">
        <div class="reference-product">
            <div class="reference-tabs">
                <span class="tab active">Dòng chảy văn hóa</span>
            </div>
            <div class="reference-content">
                <div class="reference-list product-grid active">
                    <?php
                    if (mysqli_num_rows($result_dong_chay_van_hoa) > 0) {
                        while ($book = mysqli_fetch_assoc($result_dong_chay_van_hoa)) {
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
        <img src="assets/img/banner/history/banner6.png" alt="Banner">
    </div>
        
    
    <div class="content-product">
        <div class="reference-product">
            <div class="reference-tabs">
                <span class="tab active">Văn hóa tạo nên lịch sử</span>
            </div>
            <div class="reference-content">
                <div class="reference-list product-grid active">
                    <?php
                    if (mysqli_num_rows($result_van_hoa_tao_nen_lich_su) > 0) {
                        while ($book = mysqli_fetch_assoc($result_van_hoa_tao_nen_lich_su)) {
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