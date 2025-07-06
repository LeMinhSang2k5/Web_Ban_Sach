<?php
// Set page title
$page_title = "Quản lý sách";

require_once('../db.php');

// Xử lý xóa sách
if (isset($_POST['delete_book'])) {
    $book_id = $_POST['book_id'];
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $book_id);
    if ($stmt->execute()) {
        $success_message = "Xóa sách thành công!";
    } else {
        $error_message = "Có lỗi xảy ra khi xóa sách!";
    }
}

// Xử lý cập nhật sách
if (isset($_POST['edit_book'])) {
    $id = $_POST['edit_book_id'];
    $title = trim($_POST['edit_title']);
    $author = trim($_POST['edit_author']);
    $publisher = trim($_POST['edit_publisher']);
    $price = (int)$_POST['edit_price'];
    $stock = (int)$_POST['edit_stock'];
    $category = trim($_POST['edit_category']);
    $subcategory = trim($_POST['edit_subcategory']);
    // Thêm các trường khác nếu cần
    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, publisher=?, price=?, stock=?, category=?, subcategory=? WHERE id=?");
    $stmt->bind_param("sssiiisi", $title, $author, $publisher, $price, $stock, $category, $subcategory, $id);
    if ($stmt->execute()) {
        $success_message = "Cập nhật sách thành công!";
    } else {
        $error_message = "Có lỗi xảy ra khi cập nhật sách!";
    }
}

// Lấy danh sách sách
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// Ensure page is always at least 1
$page = max(1, $page);
$limit = 10;
$offset = ($page - 1) * $limit;

$where_clause = "";
if (!empty($search)) {
    $where_clause = "WHERE title LIKE '%$search%' OR author LIKE '%$search%' OR publisher LIKE '%$search%'";
}

$query = "SELECT * FROM books $where_clause ORDER BY id ASC LIMIT $limit OFFSET $offset";
$books = $conn->query($query);

// Đếm tổng số sách để phân trang
$count_query = "SELECT COUNT(*) as total FROM books $where_clause";
$count_result = $conn->query($count_query);
$total_books = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_books / $limit);

// Lấy tất cả danh mục
$parents = $conn->query("SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name ASC");
$parent_categories = [];
while ($row = $parents->fetch_assoc()) {
    $parent_categories[] = $row['name'];
}

// Lấy tất cả danh mục con (nếu muốn dùng chung dropdown)
$subcategory_result = $conn->query("SELECT DISTINCT subcategory FROM books WHERE subcategory != '' ORDER BY subcategory ASC");
$subcategories = [];
while ($row = $subcategory_result->fetch_assoc()) {
    $subcategories[] = $row['subcategory'];
}

// Chuẩn bị content cho layout
ob_start();
?>
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-book"></i> Danh sách sách (<?php echo $total_books; ?> sách)
            </h1>
            <div class="page-actions">
                <a href="add_book.php" class="btn btn-success">
                    <i class="fas fa-plus"></i> Thêm sách mới
                </a>
                <div class="search-form">
                    <form method="GET" style="display: flex; gap: 1rem;">
                        <input type="text" name="search" placeholder="Tìm kiếm sách..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                        <?php if (!empty($search)): ?>
                            <a href="manage_books.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Xóa bộ lọc
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <div class="content-section">
            <?php if ($books->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 5%;">ID</th>
                            <th style="width: 10%;">Hình ảnh</th>
                            <th style="width: 10%;">Tên sách</th>
                            <th style="width: 10%;">Danh mục</th>
                            <th style="width: 10%;">Danh mục con</th>
                            <th style="width: 10%;">Tồn kho</th>
                            <th style="width: 10%;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($book = $books->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $book['id']; ?></td>
                            <td>
                                <?php 
                                    $image_src = $book['image'];
                                    if (strpos($image_src, 'http') === 0) {
                                        $display_src = $image_src;
                                    } else {
                                        $display_src = '.' . $image_src;
                                    }
                                ?>
                                <img src="<?php echo htmlspecialchars($display_src); ?>" 
                                     alt="<?php echo htmlspecialchars($book['title']); ?>" 
                                     class="book-image">
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($book['title']); ?></strong>
                                <?php if (!empty($book['description'])): ?>
                                    <br><small><?php echo substr(htmlspecialchars($book['description']), 0, 50) . '...'; ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($book['category']); ?></td>
                            <td><?php echo htmlspecialchars($book['subcategory']); ?></td>
                            <td><?php echo htmlspecialchars($book['stock']); ?></td>
                            <td>
                                <div class="book-actions">
                                    <button type="button" class="btn btn-success btn-edit-book" 
                                            data-id="<?php echo $book['id']; ?>"
                                            data-title="<?php echo htmlspecialchars($book['title'], ENT_QUOTES); ?>"
                                            data-author="<?php echo htmlspecialchars($book['author'], ENT_QUOTES); ?>"
                                            data-publisher="<?php echo htmlspecialchars($book['publisher'], ENT_QUOTES); ?>"
                                            data-price="<?php echo $book['price']; ?>"
                                            data-stock="<?php echo $book['stock']; ?>"
                                            data-category="<?php echo htmlspecialchars($book['category'], ENT_QUOTES); ?>"
                                            data-subcategory="<?php echo htmlspecialchars($book['subcategory'], ENT_QUOTES); ?>">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <form method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Bạn có chắc muốn xóa sách này?');">
                                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                        <button type="submit" name="delete_book" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>no data.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">← Trước</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Sau →</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Modal sửa sách -->
        <div id="editBookModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2><i class="fas fa-edit"></i> Sửa sách</h2>
                    <span class="close-edit-book" style="cursor:pointer;">&times;</span>
                </div>
                <form method="POST" id="editBookForm">
                    <input type="hidden" name="edit_book_id" id="edit_book_id">
                    <div class="form-group">
                        <label for="edit_title">Tên sách</label>
                        <input type="text" name="edit_title" id="edit_title" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_author">Tác giả</label>
                        <input type="text" name="edit_author" id="edit_author" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_publisher">Nhà xuất bản</label>
                        <input type="text" name="edit_publisher" id="edit_publisher" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_price">Giá</label>
                        <input type="number" name="edit_price" id="edit_price" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_stock">Số lượng</label>
                        <input type="number" name="edit_stock" id="edit_stock" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_category">Danh mục</label>
                        <select name="edit_category" id="edit_category" required>
                            <option value="">-- Chọn danh mục --</option>
                            <?php foreach ($parent_categories as $parent): ?>
                                <option value="<?php echo htmlspecialchars($parent); ?>"><?php echo htmlspecialchars($parent); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_subcategory">Danh mục con</label>
                        <select name="edit_subcategory" id="edit_subcategory">
                            <option value="">-- Chọn danh mục con --</option>
                            <?php foreach ($subcategories as $subcategory): ?>
                                <option value="<?php echo htmlspecialchars($subcategory); ?>"><?php echo htmlspecialchars($subcategory); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="modal-actions">
                        <button type="button" class="btn btn-secondary" id="closeEditBookModal">Hủy</button>
                        <button type="submit" name="edit_book" class="btn btn-success">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- jQuery Modal Script -->
        <script src="../jquery/jquery-3.7.1.min.js"></script>
        <script>
        $(document).ready(function() {
            // Mở modal và đổ dữ liệu vào form
            $('.btn-edit-book').on('click', function() {
                $('#edit_book_id').val($(this).data('id'));
                $('#edit_title').val($(this).data('title'));
                $('#edit_author').val($(this).data('author'));
                $('#edit_publisher').val($(this).data('publisher'));
                $('#edit_price').val($(this).data('price'));
                $('#edit_stock').val($(this).data('stock'));
                $('#edit_category').val($(this).data('category'));
                $('#edit_subcategory').val($(this).data('subcategory'));
                $('#editBookModal').show();
            });
            // Đóng modal
            $('#closeEditBookModal, .close-edit-book').on('click', function() {
                $('#editBookModal').hide();
            });
            // Đóng modal khi click outside
            $(window).on('click', function(event) {
                if ($(event.target).is('#editBookModal')) {
                    $('#editBookModal').hide();
                }
            });
        });
        </script>

<?php
$content = ob_get_clean();

// Include layout
include 'layout.php';
?> 