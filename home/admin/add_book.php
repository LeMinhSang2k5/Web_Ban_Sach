<?php
// Set page title
$page_title = "Thêm sách mới";

require_once('../db.php');
require_once('auth_check.php');

// Kiểm tra admin
checkAdminAuth();

// Xử lý thêm sách
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $category = $_POST['category'];
    $subcategory = $_POST['subcategory'];
    $label = $_POST['label'];
    $price = $_POST['price'];
    $image = $_POST['image'];
    $sold = $_POST['sold'] ?? 0;
    $old_price = $_POST['old_price'];
    $discount = $_POST['discount'];
    $supplier = $_POST['supplier'];
    $publisher = $_POST['publisher'];
    $publish_year = $_POST['publish_year'];
    $pages = $_POST['pages'];
    $size = $_POST['size'];
    $cover_type = $_POST['cover_type'];
    $description = $_POST['description'];
    $reviews = $_POST['reviews'];
    $code = $_POST['code'];
    $stock = $_POST['stock'];

    // Validate required fields
    if (empty($title) || empty($author) || empty($category) || empty($price) || empty($image) || empty($pages) || empty($size) || empty($cover_type) || empty($description) || empty($code) || empty($stock)) {
        $error_message = "Vui lòng điền đầy đủ các trường bắt buộc!";
    } else {
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO books (title, author, category, subcategory, label, price, image, sold, old_price, discount, supplier, publisher, publish_year, pages, size, cover_type, description, reviews, code, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("sssssdsiidssiisssdsi", 
            $title, $author, $category, $subcategory, $label, 
            $price, $image, $sold, $old_price, $discount, 
            $supplier, $publisher, $publish_year, $pages, $size, 
            $cover_type, $description, $reviews, $code, $stock
        );

        if ($stmt->execute()) {
            $success_message = "Thêm sách thành công!";
            // Clear form data after successful insert
            $_POST = array();
        } else {
            $error_message = "Có lỗi xảy ra khi thêm sách: " . $conn->error;
        }
    }
}

// Chuẩn bị content cho layout
ob_start();
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-plus"></i> Thêm sách mới
    </h1>
    <div class="page-actions">
        <a href="manage_books.php" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-error"><?php echo $error_message; ?></div>
<?php endif; ?>

<div class="content-section">
    <form method="POST" class="book-form">
        <div class="form-grid">
            <!-- Thông tin cơ bản -->
            <div class="form-section">
                <h3><i class="fas fa-info-circle"></i> Thông tin cơ bản</h3>
                
                <div class="form-group">
                    <label for="title">Tiêu đề sách <span class="required">*</span></label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="author">Tác giả <span class="required">*</span></label>
                    <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($_POST['author'] ?? ''); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="category">Danh mục <span class="required">*</span></label>
                        <select id="category" name="category" required>
                            <option value="">Chọn danh mục</option>
                            <option value="Văn học">Văn học</option>
                            <option value="Thiếu nhi">Thiếu nhi</option>
                            <option value="Sách tham khảo">Sách tham khảo</option>
                            <option value="Sách luyện thi">Sách luyện thi</option>
                            <option value="Kinh tế">Kinh tế</option>
                            <option value="Tâm Lý-Kỹ Năng Sống">Tâm Lý-Kỹ Năng Sống</option>
                            <option value="Ngoại Ngữ">Ngoại Ngữ</option>
                            <option value="Tiểu thuyết">Tiểu thuyết</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="subcategory">Danh mục con</label>
                        <input type="text" id="subcategory" name="subcategory" value="<?php echo htmlspecialchars($_POST['subcategory'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="label">Nhãn</label>
                    <select id="label" name="label">
                        <option value="">Không có nhãn</option>
                        <option value="Bán chạy">Bán chạy</option>
                        <option value="Xu hướng">Xu hướng</option>
                        <option value="Mới">Mới</option>
                        <option value="Giảm giá">Giảm giá</option>
                        <option value="Classic">Classic</option>
                    </select>
                </div>
            </div>

            <!-- Thông tin giá và kho -->
            <div class="form-section">
                <h3><i class="fas fa-dollar-sign"></i> Thông tin giá và kho</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Giá bán <span class="required">*</span></label>
                        <input type="number" id="price" name="price" step="0.01" value="<?php echo $_POST['price'] ?? ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="old_price">Giá cũ</label>
                        <input type="number" id="old_price" name="old_price" step="0.01" value="<?php echo $_POST['old_price'] ?? ''; ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="discount">Giảm giá (%)</label>
                        <input type="text" id="discount" name="discount" placeholder="Ví dụ: -15%" value="<?php echo htmlspecialchars($_POST['discount'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="stock">Số lượng tồn kho <span class="required">*</span></label>
                        <input type="number" id="stock" name="stock" value="<?php echo $_POST['stock'] ?? ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="sold">Đã bán</label>
                    <input type="number" id="sold" name="sold" value="<?php echo $_POST['sold'] ?? '0'; ?>">
                </div>
            </div>

            <!-- Thông tin xuất bản -->
            <div class="form-section">
                <h3><i class="fas fa-building"></i> Thông tin xuất bản</h3>
                
                <div class="form-group">
                    <label for="publisher">Nhà xuất bản</label>
                    <input type="text" id="publisher" name="publisher" value="<?php echo htmlspecialchars($_POST['publisher'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="supplier">Nhà cung cấp</label>
                    <input type="text" id="supplier" name="supplier" value="<?php echo htmlspecialchars($_POST['supplier'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="publish_year">Năm xuất bản</label>
                    <input type="number" id="publish_year" name="publish_year" min="1900" max="2030" value="<?php echo $_POST['publish_year'] ?? ''; ?>">
                </div>
            </div>

            <!-- Thông tin vật lý -->
            <div class="form-section">
                <h3><i class="fas fa-book-open"></i> Thông tin vật lý</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="pages">Số trang <span class="required">*</span></label>
                        <input type="number" id="pages" name="pages" value="<?php echo $_POST['pages'] ?? ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="size">Kích thước <span class="required">*</span></label>
                        <input type="text" id="size" name="size" placeholder="Ví dụ: 20.5 x 14.5 x 1.5" value="<?php echo htmlspecialchars($_POST['size'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cover_type">Loại bìa <span class="required">*</span></label>
                    <select id="cover_type" name="cover_type" required>
                        <option value="">Chọn loại bìa</option>
                        <option value="Bìa mềm">Bìa mềm</option>
                        <option value="Bìa cứng">Bìa cứng</option>
                    </select>
                </div>
            </div>

            <!-- Hình ảnh và mã -->
            <div class="form-section">
                <h3><i class="fas fa-image"></i> Hình ảnh và mã</h3>
                
                <div class="form-group">
                    <label for="image">Đường dẫn hình ảnh <span class="required">*</span></label>
                    <input type="text" id="image" name="image" placeholder="Ví dụ: ./assets/img/sach-van-hoc/tieude.png" value="<?php echo htmlspecialchars($_POST['image'] ?? ''); ?>" required>
                    <small>Lưu hình ảnh vào thư mục assets/img và nhập đường dẫn tương đối</small>
                </div>

                <div class="form-group">
                    <label for="code">Mã sách <span class="required">*</span></label>
                    <input type="text" id="code" name="code" value="<?php echo htmlspecialchars($_POST['code'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="reviews">Đánh giá</label>
                    <input type="number" id="reviews" name="reviews" min="0" max="5" step="0.1" value="<?php echo $_POST['reviews'] ?? ''; ?>">
                </div>
            </div>
        </div>

        <!-- Mô tả -->
        <div class="form-section full-width">
            <h3><i class="fas fa-align-left"></i> Mô tả sách</h3>
            <div class="form-group">
                <label for="description">Mô tả chi tiết <span class="required">*</span></label>
                <textarea id="description" name="description" rows="8" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                <small>Có thể sử dụng HTML tags để định dạng</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="add_book" class="btn btn-primary">
                <i class="fas fa-save"></i> Thêm sách
            </button>
            <a href="manage-books.php" class="btn btn-secondary">
                <i class="fas fa-times"></i> Hủy
            </a>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?> 