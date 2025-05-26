<body>
    <div class="main-contain">

        <div>
            <img src="./assets/img/banner1.jpg" alt="Banner" style="width: 100%; height: auto; border-radius: 10px;">
        </div>
        <div class="content-product" id="sach-moi">
            <h1>Những cuốn sách nổi bật</h1>
            <div class="product-grid">
                <?php
                // Mảng giả lập dữ liệu sách
                $books = [
                    [
                        'title' => 'Đắc Nhân Tâm',
                        'price' => 85000,
                        'image' => './assets/img/haha.png'
                    ],
                    [
                        'title' => 'Tuổi Trẻ Đáng Giá Bao Nhiêu',
                        'price' => 99000,
                        'image' => '../img/tuoi_tre.jpg'
                    ],
                    [
                        'title' => 'Nhà Giả Kim',
                        'price' => 110000,
                        'image' => '../img/nha_gia_kim.jpg'
                    ]
                ];

                foreach ($books as $book) {
                    echo '<div class="product-card">';
                    echo '<img src="' . $book['image'] . '" alt="' . $book['title'] . '">';
                    echo '<h3>' . $book['title'] . '</h3>';
                    echo '<p>' . number_format($book['price'], 0, ",", ".") . ' VNĐ</p>';
                    echo '<button>Mua ngay</button>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

</body>