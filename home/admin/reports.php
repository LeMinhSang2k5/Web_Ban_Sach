<?php
// reports.php - Báo cáo đơn hàng (được include từ index.php)
// Set page title for layout
$page_title = "Báo cáo đơn hàng";

require_once('../db.php');

// Thống kê tổng quan
$stats = [
    'total_orders' => 0,
    'total_revenue' => 0,
    'pending' => 0,
    'completed' => 0,
    'cancelled' => 0
];

// Đếm tổng số đơn hàng
$result = $conn->query("SELECT COUNT(*) as total FROM orders");
$stats['total_orders'] = $result ? $result->fetch_assoc()['total'] : 0;

// Tổng doanh thu (chỉ đơn đã hoàn thành)
$result = $conn->query("SELECT SUM(total_amount) as revenue FROM orders WHERE order_status = 'completed'");
$stats['total_revenue'] = $result ? ($result->fetch_assoc()['revenue'] ?? 0) : 0;

// Đếm theo trạng thái
foreach (['pending', 'completed', 'cancelled'] as $status) {
    $result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE order_status = '".$conn->real_escape_string($status)."'");
    $stats[$status] = $result ? $result->fetch_assoc()['total'] : 0;
}

// Lọc theo trạng thái nếu có
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$where = '';
if (in_array($status_filter, ['pending', 'completed', 'cancelled'])) {
    $where = "WHERE order_status = '".$conn->real_escape_string($status_filter)."'";
}

// Lấy danh sách đơn hàng
$orders = $conn->query("SELECT * FROM orders $where ORDER BY created_at DESC");

// Chuẩn bị content cho layout
ob_start();
?>
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-chart-bar"></i> Báo cáo đơn hàng
    </h1>
    <p style="color: #666; margin-top: 0.5rem;">Thống kê và danh sách tất cả đơn hàng trên hệ thống</p>
</div>

<!-- Thống kê tổng quan -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="stat-info">
            <h3>Tổng đơn hàng</h3>
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
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-info">
            <h3>Chờ xử lý</h3>
            <div class="stat-number"><?php echo number_format($stats['pending']); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
            <i class="fas fa-check"></i>
        </div>
        <div class="stat-info">
            <h3>Hoàn thành</h3>
            <div class="stat-number"><?php echo number_format($stats['completed']); ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c 0%, #e67e22 100%);">
            <i class="fas fa-times"></i>
        </div>
        <div class="stat-info">
            <h3>Đã huỷ</h3>
            <div class="stat-number"><?php echo number_format($stats['cancelled']); ?></div>
        </div>
    </div>
</div>

<!-- Bộ lọc trạng thái -->
<div style="margin: 2rem 0 1rem 0;">
    <form method="GET" style="display: flex; gap: 1rem; align-items: center;">
        <input type="hidden" name="page" value="reports">
        <label for="status"><b>Lọc theo trạng thái:</b></label>
        <select name="status" id="status" onchange="this.form.submit()" style="padding: 0.5rem 1rem; border-radius: 5px; border: 1px solid #ccc;">
            <option value="">Tất cả</option>
            <option value="pending" <?php if($status_filter=='pending') echo 'selected'; ?>>Chờ xử lý</option>
            <option value="completed" <?php if($status_filter=='completed') echo 'selected'; ?>>Hoàn thành</option>
            <option value="cancelled" <?php if($status_filter=='cancelled') echo 'selected'; ?>>Đã huỷ</option>
        </select>
    </form>
</div>

<!-- Danh sách đơn hàng -->
<div class="content-section">
    <h2 style="margin-bottom: 1rem;"><i class="fas fa-list"></i> Danh sách đơn hàng</h2>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày đặt</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($orders && $orders->num_rows > 0): ?>
                    <?php while ($row = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['order_code']); ?></td>
                            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['customer_email']); ?></td>
                            <td><?php echo htmlspecialchars($row['customer_phone']); ?></td>
                            <td><?php echo number_format($row['total_amount'], 0, ',', '.'); ?>đ</td>
                            <td>
                                <?php
                                    $status = $row['order_status'];
                                    $color = '#666';
                                    if ($status == 'pending') $color = '#f39c12';
                                    elseif ($status == 'completed') $color = '#28a745';
                                    elseif ($status == 'cancelled') $color = '#e74c3c';
                                ?>
                                <span class="status-badge" style="background: <?php echo $color; ?>; color: #fff; padding: 4px 12px; border-radius: 8px; font-weight: bold;">
                                    <?php
                                        if ($status == 'pending') echo 'Chờ xử lý';
                                        elseif ($status == 'completed') echo 'Hoàn thành';
                                        elseif ($status == 'cancelled') echo 'Đã huỷ';
                                        else echo htmlspecialchars($status);
                                    ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align:center; color:#888;">Không có đơn hàng nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Bảng doanh thu theo ngày -->
<div class="content-section" style="margin-top:2rem;">
    <h2 style="margin-bottom: 1rem;"><i class="fas fa-calendar-day"></i> Doanh thu theo ngày</h2>
    <?php
    // Lấy doanh thu theo ngày cho các đơn hoàn thành
    $revenue_by_day = $conn->query("SELECT DATE(created_at) as order_date, COUNT(*) as total_orders, SUM(total_amount) as total_revenue FROM orders WHERE order_status = 'completed' GROUP BY DATE(created_at) ORDER BY order_date DESC");
    $grand_total = 0;
    ?>
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Ngày</th>
                    <th>Số đơn hoàn thành</th>
                    <th>Doanh thu</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($revenue_by_day && $revenue_by_day->num_rows > 0): ?>
                    <?php while ($row = $revenue_by_day->fetch_assoc()): ?>
                        <?php $grand_total += $row['total_revenue']; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                            <td><?php echo number_format($row['total_orders']); ?></td>
                            <td><?php echo number_format($row['total_revenue'], 0, ',', '.'); ?>đ</td>
                        </tr>
                    <?php endwhile; ?>
                    <tr style="background:#f8f9fa;font-weight:bold;">
                        <td colspan="2" style="text-align:right;">Tổng doanh thu:</td>
                        <td><?php echo number_format($grand_total, 0, ',', '.'); ?>đ</td>
                    </tr>
                <?php else: ?>
                    <tr><td colspan="3" style="text-align:center; color:#888;">Không có dữ liệu doanh thu.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'layout.php';    