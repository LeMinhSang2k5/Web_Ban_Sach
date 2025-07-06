<?php
// Quản lý danh mục sách
$page_title = "Quản lý danh mục";
require_once('../db.php');

$parents = $conn->query("SELECT id, name FROM categories WHERE parent_id IS NULL ORDER BY name ASC");

// Xây dựng mảng category_map để hiển thị
$category_map = [];
$total_categories = 0;
$total_subcategories = 0;

if ($parents && $parents->num_rows > 0) {
    while ($parent = $parents->fetch_assoc()) {
        $category_name = $parent['name'];
        $category_map[$category_name] = [];
        $total_categories++;
        
        // Lấy danh mục con
        $subcats = $conn->query("SELECT subcategory FROM categories WHERE category = '$category_name' AND subcategory != '' GROUP BY subcategory");
        if ($subcats && $subcats->num_rows > 0) {
            while ($subcat = $subcats->fetch_assoc()) {
                $category_map[$category_name][] = $subcat['subcategory'];
                $total_subcategories++;
            }
        }
    }
}

ob_start();
?>

<!-- Thống kê tổng quan -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <h3>Tổng danh mục</h3>
        <div class="stat-number"><?php echo $total_categories; ?></div>
    </div>
    <div class="stat-card">
        <h3>Danh mục con</h3>
        <div class="stat-number"><?php echo $total_subcategories; ?></div>
    </div>
</div>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tags"></i> Quản lý danh mục
    </h1>
    <div class="page-actions">
        <button class="btn btn-success"">
            <i class="fas fa-plus"></i> Thêm danh mục
        </button>
    </div>
</div>

<div class="content-section">
    <?php if (count($category_map) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Danh mục</th>
                    <th>Danh mục con</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($category_map as $cat => $subs): ?>
                <tr>
                    <td><strong><?php echo ($cat); ?></strong></td>
                    <td>
                        <?php if (count($subs) > 0): ?>
                            <?php echo implode(', ', array_map('htmlspecialchars', $subs)); ?>
                        <?php else: ?>
                            <em>Không có</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="book-actions">
                            <button type="button" class="btn btn-warning" 
                                    onclick=""
                                    title="Sửa danh mục">
                                <i class="fas fa-edit"></i> Sửa
                            </button>
                            <button type="button" class="btn btn-danger" 
                                    onclick=""
                                    title="Xóa danh mục">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="no-data">
            <i class="fas fa-tags" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
            <p>Không tìm thấy danh mục nào.</p>
        </div>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?> 