<?php
// Quản lý danh mục sách
$page_title = "Quản lý danh mục";
require_once('../db.php');

// Xử lý thêm danh mục
if (isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    if (!empty($category_name)) {
        $success_message = "Danh mục '$category_name' sẽ có sẵn khi thêm sách mới!";
    } else {
        $error_message = "Vui lòng nhập tên danh mục!";
    }
}

// Xử lý xóa danh mục (chuyển sách sang danh mục khác)
if (isset($_POST['delete_category'])) {
    $category_to_delete = $_POST['category_name'];
    $move_to_category = $_POST['move_to_category'];
    
    if (!empty($move_to_category) && $move_to_category !== $category_to_delete) {
        $stmt = $conn->prepare("UPDATE books SET category = ? WHERE category = ?");
        $stmt->bind_param("ss", $move_to_category, $category_to_delete);
        if ($stmt->execute()) {
            $success_message = "Đã xóa danh mục '$category_to_delete' và chuyển tất cả sách sang '$move_to_category'!";
        } else {
            $error_message = "Có lỗi xảy ra khi xóa danh mục!";
        }
    } else {
        $error_message = "Vui lòng chọn danh mục đích để chuyển sách!";
    }
}

// Lấy thống kê danh mục
$stats_query = "SELECT 
    category,
    COUNT(*) as book_count,
    AVG(price) as avg_price,
    SUM(sold) as total_sold
FROM books 
WHERE category != ''
GROUP BY category 
ORDER BY book_count DESC";
$categories = $conn->query($stats_query);

// Thống kê tổng quan
$total_categories = $conn->query("SELECT COUNT(DISTINCT category) as total FROM books WHERE category != ''")->fetch_assoc()['total'];
$total_subcategories = $conn->query("SELECT COUNT(DISTINCT subcategory) as total FROM books WHERE subcategory != ''")->fetch_assoc()['total'];
$total_books = $conn->query("SELECT COUNT(*) as total FROM books")->fetch_assoc()['total'];

// Lấy tất cả danh mục cho dropdown
$all_categories = $conn->query("SELECT DISTINCT category FROM books WHERE category != '' ORDER BY category ASC");

ob_start();
?>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-error"><?php echo $error_message; ?></div>
<?php endif; ?>

<!-- Thống kê tổng quan -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <h3>Tổng danh mục</h3>
        <div class="stat-number"><?php echo number_format($total_categories); ?></div>
    </div>
    <div class="stat-card">
        <h3>Danh mục con</h3>
        <div class="stat-number"><?php echo number_format($total_subcategories); ?></div>
    </div>
    <div class="stat-card">
        <h3>Tổng sách</h3>
        <div class="stat-number"><?php echo number_format($total_books); ?></div>
    </div>
</div>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tags"></i> Quản lý danh mục (<?php echo $total_categories; ?> danh mục)
    </h1>
    <div class="page-actions">
        <button class="btn btn-success" onclick="openAddModal()">
            <i class="fas fa-plus"></i> Thêm danh mục
        </button>
    </div>
</div>

<div class="content-section">
    <?php if ($categories->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Danh mục</th>
                    <th>Số lượng sách</th>
                    <th>Giá trung bình</th>
                    <th>Đã bán</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($category = $categories->fetch_assoc()): ?>
                <tr>
                    <td>
                        <strong><?php echo htmlspecialchars($category['category']); ?></strong>
                        <?php
                        // Lấy danh mục con
                        $subcats = $conn->query("SELECT DISTINCT subcategory FROM books WHERE category = '{$category['category']}' AND subcategory != ''");
                        if ($subcats && $subcats->num_rows > 0):
                        ?>
                            <br><small style="color: #666;">
                                Danh mục con: 
                                <?php 
                                $subcat_list = [];
                                while ($sub = $subcats->fetch_assoc()) {
                                    $subcat_list[] = $sub['subcategory'];
                                }
                                echo implode(', ', $subcat_list);
                                ?>
                            </small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-primary"><?php echo number_format($category['book_count']); ?> sách</span>
                    </td>
                    <td>
                        <?php echo number_format($category['avg_price']); ?>đ
                    </td>
                    <td>
                        <span style="color: #e74c3c; font-weight: bold;">
                            <?php echo number_format($category['total_sold']); ?>
                        </span>
                    </td>
                    <td>
                        <div class="book-actions">
                            <a href="manage_books.php?search=<?php echo urlencode($category['category']); ?>" 
                               class="btn btn-info" title="Xem sách trong danh mục">
                                <i class="fas fa-eye"></i> Xem sách
                            </a>
                            <button type="button" class="btn btn-danger" 
                                    onclick="openDeleteModal('<?php echo htmlspecialchars($category['category'], ENT_QUOTES); ?>')"
                                    title="Xóa danh mục">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-data">
            <i class="fas fa-tags" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
            <p>Không tìm thấy danh mục nào.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Modal thêm danh mục -->
<div id="addCategoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-plus"></i> Thêm danh mục mới</h2>
            <span class="close">&times;</span>
        </div>
        <form method="POST">
            <div class="form-group">
                <label for="category_name">Tên danh mục <span class="required">*</span></label>
                <input type="text" id="category_name" name="category_name" required placeholder="Nhập tên danh mục...">
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Hủy</button>
                <button type="submit" name="add_category" class="btn btn-success">
                    <i class="fas fa-plus"></i> Thêm danh mục
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal xóa danh mục -->
<div id="deleteCategoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-trash"></i> Xóa danh mục</h2>
            <span class="close-delete">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" id="delete_category_name" name="category_name">
            
            <div class="warning-message">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Bạn đang xóa danh mục <strong id="category_to_delete"></strong></p>
                <p>Tất cả sách trong danh mục này sẽ được chuyển sang danh mục khác.</p>
            </div>
            
            <div class="form-group">
                <label for="move_to_category">Chuyển sách sang danh mục <span class="required">*</span></label>
                <select id="move_to_category" name="move_to_category" required>
                    <option value="">Chọn danh mục đích</option>
                    <?php 
                    mysqli_data_seek($all_categories, 0);
                    while ($cat = mysqli_fetch_assoc($all_categories)): 
                    ?>
                        <option value="<?php echo htmlspecialchars($cat['category']); ?>">
                            <?php echo htmlspecialchars($cat['category']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Hủy</button>
                <button type="submit" name="delete_category" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Xóa và chuyển sách
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.stat-card h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    opacity: 0.9;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    margin: 0;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}

.badge-primary {
    color: #fff;
    background-color: #007bff;
}

.btn-info {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
}

.btn-info:hover {
    background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 5% auto;
    padding: 0;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.2rem;
}

.close, .close-delete {
    color: white;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.3s;
}

.close:hover, .close-delete:hover {
    opacity: 1;
}

.modal form {
    padding: 1.5rem;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #333;
}

.form-group input, .form-group select {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e1e1e1;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-group input:focus, .form-group select:focus {
    border-color: #667eea;
    outline: none;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

.required {
    color: #e74c3c;
}

.warning-message {
    background-color: #fff3cd;
    border: 1px solid #ffeaa7;
    color: #856404;
    padding: 1rem;
    border-radius: 5px;
    margin-bottom: 1rem;
}

.warning-message i {
    color: #f39c12;
    margin-right: 0.5rem;
}
</style>

<script>
function openAddModal() {
    document.getElementById('addCategoryModal').style.display = 'block';
}

function closeAddModal() {
    document.getElementById('addCategoryModal').style.display = 'none';
}

function openDeleteModal(categoryName) {
    document.getElementById('delete_category_name').value = categoryName;
    document.getElementById('category_to_delete').textContent = categoryName;
    
    // Ẩn danh mục đang xóa khỏi dropdown
    const dropdown = document.getElementById('move_to_category');
    const options = dropdown.querySelectorAll('option');
    options.forEach(option => {
        if (option.value === categoryName) {
            option.style.display = 'none';
        } else {
            option.style.display = 'block';
        }
    });
    
    document.getElementById('deleteCategoryModal').style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteCategoryModal').style.display = 'none';
}

// Đóng modal khi click outside
window.onclick = function(event) {
    const addModal = document.getElementById('addCategoryModal');
    const deleteModal = document.getElementById('deleteCategoryModal');
    
    if (event.target == addModal) {
        closeAddModal();
    }
    if (event.target == deleteModal) {
        closeDeleteModal();
    }
}

// Đóng modal khi nhấn ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeAddModal();
        closeDeleteModal();
    }
});

// Event listeners cho các nút đóng
document.querySelector('.close').onclick = closeAddModal;
document.querySelector('.close-delete').onclick = closeDeleteModal;
</script>

<?php
$content = ob_get_clean();
include 'layout.php';
?> 