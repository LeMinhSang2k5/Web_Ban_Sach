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

// Lấy danh sách sách
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
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
                <a href="add-book.php" class="btn btn-success">
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
                            <a href="manage-books.php" class="btn btn-secondary">
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
                            <th>ID</th>
                            <th>Hình ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Tác giả</th>
                            <th>Nhà xuất bản</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thao tác</th>
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
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['publisher']); ?></td>
                            <td>
                                <?php if ($book['discount'] > 0): ?>
                                    <span style="text-decoration: line-through; color: #999;">
                                        <?php echo number_format($book['old_price']); ?>đ
                                    </span><br>
                                    <strong style="color: #e74c3c;">
                                        <?php echo number_format($book['price']); ?>đ
                                    </strong>
                                <?php else: ?>
                                    <strong><?php echo number_format($book['price']); ?>đ</strong>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $book['stock']; ?></td>
                            <td>
                                <div class="book-actions">
                                    <a href="edit-book.php?id=<?php echo $book['id']; ?>" 
                                       class="btn btn-success">
                                        <i class="fas fa-edit"></i> Sửa
                                    </a>
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
                    <p>Không tìm thấy sách nào.</p>
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

<?php
$content = ob_get_clean();

// Include layout
include 'layout.php';
?> 