<body>
    <div class="main-contain">

        <div>
            <img src="./assets/img/banner1.jpg" alt="Banner" id = "banner">
        </div>
        <div class="content-product" id="sach-moi">
            <h1>Những cuốn sách nổi bật</h1>
            <div class="product-grid">
                <?php
                // Mảng giả lập dữ liệu sách
                $books = [
                    [
                        'title' => 'Đắc Nhân Tâm',
                        'label' => 'Bán chạy',
                        'price' => 85000,
                        'image' => './assets/img/dacnhantam.webp',
                        'sold' => 448,
                        'old_price' => 100000,
                        'discount' => '-15%',
                    ],
                    [
                        'title' => 'Búp sen xanh',
                        'label' => 'Bán chạy',
                        'price' => 99000,
                        'image' => './assets/img/bupsenxanh.png',
                        'sold' => 448,
                        'old_price' => 100000,
                        'discount' => '-15%',
                    ],
                    [
                        'title' => 'Nhà Giả Kim',
                        'label' => 'Bán chạy',
                        'price' => 110000,
                        'image' => './assets/img/nhagiakim.png',
                        'sold' => 448,
                        'old_price' => 100000,
                        'discount' => '-15%',
                    ],
                    [
                        'title' => 'Bông sen vàng',
                        'price' => 110000,
                        'image' => './assets/img/bongsenvang.png',
                        'sold' => 448,
                        'old_price' => 100000,
                        'discount' => '-15%',
                    ]
                    // [
                    //     'title' => 'Hồ điệp và kình ngư',
                    //     'price' => 110000,
                    //     'image' => './assets/img/hodiepvakinhngu.png',
                    //     'sold' => 448,
                    //     'old_price' => 100000,
                    //     'discount' => '-15%',
                    // ],
                ];

                foreach ($books as $book) {
                    echo '<div class="product-card">';
                    echo '  <div class="product-image-wrap">';
                    if ($book['label']) {
                        echo '<span class="product-label">'.$book['label'].'<i class="fa fa-fire"></i></span>';
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

        <div class="content-product" id="sach-tham-khao"> 
            <div class="reference-product">
                <div class="reference-tabs">
                    <span class="tab active" data-tab="thamkhao">Sách tham khảo</span>
                    <span class="tab" data-tab="luyenthi">Sách luyện thi</span>
                </div>
                <div class="reference-content">
                    <div class="reference-list reference-list-thamkhao product-grid active">
                        <?php
                        $books_thamkhao = [
                            [
                                'title' => 'Đột Phá Tư Duy Kì Thi Tốt Nghiệp THPT - Môn Sinh Học',
                                'price' => 120000,
                                'old_price' => 150000,
                                'discount' => '-20%',
                                'sold' => 45,
                                'label' => '',
                                'image' => './assets/img/sinhhoc.png'
                            ],
                            [
                                'title' => 'Đề Minh Họa Tốt Nghiệp THPT 2025',
                                'price' => 80000,
                                'old_price' => 10000,
                                'discount' => '-20%',
                                'sold' => 602,
                                'label' => '',
                                'image' => './assets/img/nguvan.png'
                            ]
                        ];
                        foreach ($books_thamkhao as $book) {
                            echo '<div class="product-card">';
                            echo '  <div class="product-image-wrap">';
                            if ($book['label']) {
                                echo '<span class="product-label">'.$book['label'].'<i class="fa fa-fire"></i></span>';
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
                                'image' => './assets/img/hoahoc.png'
                            ],
                            [
                                'title' => 'Kỹ năng viết văn bản nghị luận',
                                'price' => 47000,
                                'old_price' => 60000,
                                'discount' => '-21%',
                                'sold' => 175,
                                'label' => '',
                                'image' => './assets/img/nghiluanvanhoc.png'
                            ]
                        ];
                        foreach ($books_luyenthi as $book) {
                            echo '<div class="product-card">';
                            echo '  <div class="product-image-wrap">';
                            if ($book['label']) {
                                echo '<span class="product-label">'.$book['label'].'<i class="fa fa-fire"></i></span>';
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
    </div>

<script>
document.querySelectorAll('.reference-tabs .tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.reference-tabs .tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        document.querySelectorAll('.reference-list').forEach(list => list.classList.remove('active'));
        const tabName = this.getAttribute('data-tab');
        document.querySelector('.reference-list-' + tabName).classList.add('active');
    });
});
</script>

</body>