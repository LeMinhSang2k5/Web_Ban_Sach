<?php
// Set page title
$page_title = "admin-dashboard";

// Lấy thống kê tổng quan
require_once('../db.php');

$stats = [];

// Tổng số sách
$result = $conn->query("SELECT COUNT(*) as total FROM books");
$stats['total_books'] = $result->fetch_assoc()['total'];

// Tổng số người dùng
$result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
$stats['total_users'] = $result->fetch_assoc()['total'];

// Tổng số đơn hàng
$result = $conn->query("SELECT COUNT(*) as total FROM cart WHERE status != 'active'");
$stats['total_orders'] = $result->fetch_assoc()['total'];

// Tổng doanh thu (giả sử có bảng orders)
$stats['total_revenue'] = 0; // Sẽ cập nhật khi có bảng orders

// Chuẩn bị content cho layout
ob_start();
?>
    <!-- Thống kê tổng quan -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Tổng số sách</h3>
            <div class="stat-number"><?php echo number_format($stats['total_books']); ?></div>
        </div>
        <div class="stat-card">
            <h3>Người dùng</h3>
            <div class="stat-number"><?php echo number_format($stats['total_users']); ?></div>
        </div>
        <div class="stat-card">
            <h3>Đơn hàng</h3>
            <div class="stat-number"><?php echo number_format($stats['total_orders']); ?></div>
        </div>
        <div class="stat-card">
            <h3>Doanh thu</h3>
            <div class="stat-number"><?php echo number_format($stats['total_revenue']); ?>đ</div>
        </div>
    </div>

    <!-- Hoạt động gần đây -->
    <div class="content-section">
        <h2><i class="fas fa-users"></i> Người dùng mới đăng ký</h2>
        <div class="table-wrapper">
            <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên người dùng</th>
                    <th>Email</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recent_users = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY id DESC LIMIT 5");
                if ($recent_users->num_rows > 0):
                    while ($user = $recent_users->fetch_assoc()):
                ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><span class="status-badge status-active">Hoạt động</span></td>
                </tr>
                <?php 
                    endwhile; 
                else:
                ?>
                <tr>
                    <td colspan="4" style="text-align: center; color: #666;">Chưa có người dùng nào đăng ký</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>

    <!-- Thống kê bổ sung -->
    <div class="content-section">
        <h2><i class="fas fa-chart-line"></i> Thống kê hệ thống</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Sách bán chạy</h3>
                <div class="stat-number">--</div>
            </div>
            <div class="stat-card">
                <h3>Đơn hàng hôm nay</h3>
                <div class="stat-number">--</div>
            </div>
        </div>
    </div>
<?php
$content = ob_get_clean();

// Include layout
include 'layout.php';
?> 