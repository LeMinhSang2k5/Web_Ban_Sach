<?php
// Set page title for layout
$page_title = "Dashboard";

require_once('../db.php');

// Lấy thống kê tổng quan
$stats = [];

// Đếm số sách
$books_result = $conn->query("SELECT COUNT(*) as total FROM books");
$stats['total_books'] = $books_result->fetch_assoc()['total'];

// Đếm số người dùng
$users_result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$stats['total_users'] = $users_result->fetch_assoc()['total'];

// Đếm số admin
$admin_result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'");
$stats['total_admins'] = $admin_result->fetch_assoc()['total'];

// Đếm đơn hàng (nếu có bảng orders)
$order_check = $conn->query("SHOW TABLES LIKE 'orders'");
if ($order_check->num_rows > 0) {
    $orders_result = $conn->query("SELECT COUNT(*) as total FROM orders");
    $stats['total_orders'] = $orders_result->fetch_assoc()['total'];

    // Tính tổng doanh thu
    $revenue_result = $conn->query("SELECT SUM(total_amount) as revenue FROM orders WHERE order_status = 'completed'");
    $stats['total_revenue'] = $revenue_result->fetch_assoc()['revenue'] ?? 0;
} else {
    $stats['total_orders'] = 0;
    $stats['total_revenue'] = 0;
}

// Lấy người dùng mới nhất
$recent_users = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY id DESC LIMIT 5");

// Lấy sách ít tồn kho
$low_stock_books = $conn->query("SELECT * FROM books WHERE stock < 10 ORDER BY stock ASC LIMIT 5");

// Chuẩn bị content cho layout
ob_start();
?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-tachometer-alt"></i> Dashboard
    </h1>
    <p style="color: #666; margin-top: 0.5rem;">Tổng quan hệ thống UTH Bookstore</p>
</div>

<!-- Thống kê tổng quan -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <i class="fas fa-book"></i>
        </div>
        <div class="stat-info">
            <h3>Tổng số sách</h3>
            <div class="stat-number"><?php echo number_format($stats['total_books']); ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3>Người dùng</h3>
            <div class="stat-number"><?php echo number_format($stats['total_users']); ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-info">
            <h3>Đơn hàng</h3>
            <div class="stat-number"><?php echo number_format($stats['total_orders']); ?></div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
            <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-info">
            <h3>Doanh thu</h3>
            <div class="stat-number"><?php echo number_format($stats['total_revenue']); ?>đ</div>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem;">
    <!-- Người dùng mới -->
    <div class="content-section">
        <h2 style="margin-bottom: 1rem;">
            <i class="fas fa-user-plus"></i> Người dùng mới
        </h2>

        <?php if ($recent_users->num_rows > 0): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $recent_users->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="ManageUser.php" class="btn btn-primary">
                    <i class="fas fa-users"></i> Xem tất cả
                </a>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-users" style="font-size: 2rem; color: #ccc;"></i>
                <p>Chưa có người dùng nào.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sách sắp hết -->
    <div class="content-section">
        <h2 style="margin-bottom: 1rem;">
            <i class="fas fa-exclamation-triangle" style="color: #f39c12;"></i> Sách sắp hết
        </h2>

        <?php if ($low_stock_books->num_rows > 0): ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Tên sách</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($book = $low_stock_books->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($book['title']); ?></td>
                                <td>
                                    <strong style="color: <?php echo $book['stock'] == 0 ? '#e74c3c' : '#f39c12'; ?>;">
                                        <?php echo $book['stock']; ?>
                                    </strong>
                                </td>
                                <td>
                                    <?php if ($book['stock'] == 0): ?>
                                        <span class="status-badge" style="background: #e74c3c;">Hết hàng</span>
                                    <?php else: ?>
                                        <span class="status-badge" style="background: #f39c12;">Sắp hết</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="ManageBooks.php" class="btn btn-primary">
                    <i class="fas fa-book"></i> Xem tất cả sách
                </a>
            </div>
        <?php else: ?>
            <div class="no-data">
                <i class="fas fa-check-circle" style="font-size: 2rem; color: #27ae60;"></i>
                <p>Tất cả sách đều có đủ tồn kho!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();

// Include layout template
include 'layout.php';
?>