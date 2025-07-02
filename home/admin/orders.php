<?php
session_start();
require_once('../db.php');

// Kiểm tra đăng nhập admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit();
}

// Xử lý cập nhật trạng thái đơn hàng
if (isset($_POST['update_status']) && isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_code = $_POST['order_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_code = ?");
    $stmt->bind_param("ss", $status, $order_code);
    if ($stmt->execute()) {
        $success_message = "Cập nhật trạng thái đơn hàng thành công!";
    } else {
        $error_message = "Có lỗi xảy ra khi cập nhật trạng thái!";
    }
}

// Xử lý xóa đơn hàng
if (isset($_GET['delete_id'])) {
    $delete_code = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_code = ?");
    $stmt->bind_param("s", $delete_code);
    if ($stmt->execute()) {
        $success_message = "Xóa đơn hàng thành công!";
    } else {
        $error_message = "Có lỗi xảy ra khi xóa đơn hàng!";
    }
    header("Location: orders.php");
    exit();
}

// Lấy danh sách đơn hàng
$sql = "SELECT orders.*, users.username, users.email FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.created_at DESC";
$result = $conn->query($sql);

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
        <i class="fas fa-shopping-cart"></i> Quản lý đơn hàng
    </h1>
</div>

<div class="content-section">
    <?php if ($result->num_rows > 0): ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Email</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['order_code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td><?php echo number_format($row['total_amount'], 0, ',', '.'); ?> đ</td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($row['order_code']); ?>">
                                    <select name="status" class="status-select">
                                        <option value="pending" <?php echo $row['order_status'] == 'pending' ? 'selected' : ''; ?>>
                                            Chờ xử lý
                                        </option>
                                        <option value="processing" <?php echo $row['order_status'] == 'processing' ? 'selected' : ''; ?>>
                                            Đang xử lý
                                        </option>
                                        <option value="completed" <?php echo $row['order_status'] == 'completed' ? 'selected' : ''; ?>>
                                            Hoàn thành
                                        </option>
                                        <option value="cancelled" <?php echo $row['order_status'] == 'cancelled' ? 'selected' : ''; ?>>
                                            Đã hủy
                                        </option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-primary btn-sm">Cập nhật</button>
                                </form>
                                <span class="status-badge status-<?php echo $row['order_status']; ?>">
                                    <?php
                                    switch ($row['order_status']) {
                                        case 'pending':
                                            echo 'Chờ xử lý';
                                            break;
                                        case 'processing':
                                            echo 'Đang xử lý';
                                            break;
                                        case 'completed':
                                            echo 'Hoàn thành';
                                            break;
                                        case 'cancelled':
                                            echo 'Đã hủy';
                                            break;
                                        default:
                                            echo htmlspecialchars($row['order_status']);
                                    }
                                    ?>
                                </span>
                            </td>
                            <td>
                                <div class="order-actions">
                                    <a href="view_order.php?id=<?php echo urlencode($row['order_code']); ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
                                    <a href="orders.php?delete_id=<?php echo urlencode($row['order_code']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này?');">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-data">
            <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
            <p>Không tìm thấy đơn hàng nào.</p>
        </div>
    <?php endif; ?>
</div>

<style>
    .table-wrapper {
        overflow-x: auto;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        margin-bottom: 2rem;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
    }

    thead {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
    }

    th,
    td {
        padding: 12px 16px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }

    th {
        font-weight: bold;
    }

    tr:last-child td {
        border-bottom: none;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.95rem;
        font-weight: 500;
        margin-left: 8px;
    }

    .status-pending {
        background: #f8d777;
        color: #a67c00;
    }

    .status-processing {
        background: #aee1fb;
        color: #0077b6;
    }

    .status-completed {
        background: #b7f7c7;
        color: #218838;
    }

    .status-cancelled {
        background: #f8b7b7;
        color: #c82333;
    }

    .btn {
        display: inline-block;
        padding: 6px 14px;
        border: none;
        border-radius: 5px;
        font-size: 0.95rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: #fff;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    .btn-warning {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        color: #fff;
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
    }

    .btn-danger {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        color: #fff;
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #c0392b 0%, #e74c3c 100%);
    }

    .btn-sm {
        padding: 4px 10px;
        font-size: 0.9rem;
    }

    .order-actions {
        display: flex;
        gap: 0.5rem;
    }

    .status-select {
        padding: 4px 8px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 0.95rem;
        margin-bottom: 4px;
    }

    .alert {
        padding: 12px 20px;
        border-radius: 6px;
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .no-data {
        text-align: center;
        color: #888;
        margin: 2rem 0;
    }
</style>

<?php
$content = ob_get_clean();
include 'layout.php';
?>