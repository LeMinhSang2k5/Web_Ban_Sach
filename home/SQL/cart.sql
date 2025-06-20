-- Bảng cart: lưu thông tin giỏ hàng
CREATE TABLE IF NOT EXISTS cart (
    id INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(10) UNSIGNED NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM(
        'active',
        'ordered',
        'abandoned'
    ) DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- Bảng cart_items: chi tiết sách trong giỏ hàng
CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT(10) UNSIGNED NOT NULL,
    book_id INT(11) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    price DECIMAL(10, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES cart (id) ON DELETE CASCADE,
    FOREIGN KEY (book_id) REFERENCES books (id) ON DELETE CASCADE,
    -- Thêm unique constraint để tránh trùng lặp sách trong cùng một giỏ hàng
    UNIQUE KEY unique_book_in_cart (cart_id, book_id)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;