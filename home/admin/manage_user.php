<?php
// Set page title
$page_title = "Quản lý người dùng";

require_once('../db.php');

// Xử lý cập nhật thông tin người dùng
if (isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $new_password = trim($_POST['new_password']);
    
    // Kiểm tra không cho phép sửa admin
    $check_admin = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $check_admin->bind_param("i", $user_id);
    $check_admin->execute();
    $result = $check_admin->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $user['role'] !== 'admin') {
        // Kiểm tra email đã tồn tại (trừ user hiện tại)
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check_email->bind_param("si", $email, $user_id);
        $check_email->execute();
        $email_exists = $check_email->get_result();
        
        if ($email_exists->num_rows > 0) {
            $error_message = "Email đã được sử dụng bởi người dùng khác!";
        } else {
            // Cập nhật thông tin
            if (!empty($new_password)) {
                // Cập nhật cả mật khẩu
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ? AND role != 'admin'");
                $stmt->bind_param("sssi", $username, $email, $hashed_password, $user_id);
            } else {
                // Chỉ cập nhật username và email
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ? AND role != 'admin'");
                $stmt->bind_param("ssi", $username, $email, $user_id);
            }
            
            if ($stmt->execute()) {
                $success_message = "Cập nhật thông tin người dùng thành công!";
            } else {
                $error_message = "Có lỗi xảy ra khi cập nhật thông tin!";
            }
        }
    } else {
        $error_message = "Không thể sửa thông tin tài khoản admin!";
    }
}

// Xử lý xóa người dùng
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    
    // Không cho phép xóa admin
    $check_admin = $conn->prepare("SELECT role FROM users WHERE id = ?");
    $check_admin->bind_param("i", $user_id);
    $check_admin->execute();
    $result = $check_admin->get_result();
    $user = $result->fetch_assoc();
    
    if ($user && $user['role'] !== 'admin') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $success_message = "Xóa người dùng thành công!";
        } else {
            $error_message = "Có lỗi xảy ra khi xóa người dùng!";
        }
    } else {
        $error_message = "Không thể xóa tài khoản admin!";
    }
}

// Lấy danh sách người dùng
$search = isset($_GET['search']) ? $_GET['search'] : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// Ensure page is always at least 1
$page = max(1, $page);
$limit = 15;
$offset = ($page - 1) * $limit;

$where_clause = "WHERE 1=1";
if (!empty($search)) {
    $where_clause .= " AND (username LIKE '%$search%' OR email LIKE '%$search%')";
}
if (!empty($role_filter)) {
    $where_clause .= " AND role = '$role_filter'";
}

$query = "SELECT * FROM users $where_clause ORDER BY id ASC LIMIT $limit OFFSET $offset";
$users = $conn->query($query);

// Đếm tổng số người dùng để phân trang
$count_query = "SELECT COUNT(*) as total FROM users $where_clause";
$count_result = $conn->query($count_query);
$total_users = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_users / $limit);

// Thống kê
$stats = [];
$stats['total_users'] = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'")->fetch_assoc()['total'];
$stats['total_admins'] = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")->fetch_assoc()['total'];
$stats['all_users'] = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];

// Chuẩn bị content cho layout
ob_start();
?>

<!-- Modal sửa người dùng -->
<div id="editUserModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Sửa thông tin người dùng</h2>
            <span class="close">&times;</span>
        </div>
        <form method="POST" id="editUserForm">
            <input type="hidden" id="edit_user_id" name="user_id">
            
            <div class="form-group">
                <label for="edit_username">Tên người dùng:</label>
                <input type="text" id="edit_username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="edit_email">Email:</label>
                <input type="email" id="edit_email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="edit_password">Mật khẩu mới (để trống nếu không đổi):</label>
                <input type="password" id="edit_password" name="new_password" placeholder="Nhập mật khẩu mới...">
                <small>Để trống nếu không muốn thay đổi mật khẩu</small>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="closeModal()">Hủy</button>
                <button type="submit" name="update_user" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($success_message)): ?>
    <div class="alert alert-success"><?php echo $success_message; ?></div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div class="alert alert-error"><?php echo $error_message; ?></div>
<?php endif; ?>

<!-- Thống kê nhanh -->
<div class="stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-card">
        <h3>Tổng người dùng</h3>
        <div class="stat-number"><?php echo number_format($stats['total_users']); ?></div>
    </div>
    <div class="stat-card">
        <h3>Admin</h3>
        <div class="stat-number"><?php echo number_format($stats['total_admins']); ?></div>
    </div>
    <div class="stat-card">
        <h3>Tổng cộng</h3>
        <div class="stat-number"><?php echo number_format($stats['all_users']); ?></div>
    </div>
</div>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-users"></i> Quản lý người dùng (<?php echo $total_users; ?> người)
    </h1>
    <div class="search-form">
        <form method="GET" style="display: flex; gap: 1rem; align-items: center;">
            <input type="text" name="search" placeholder="Tìm kiếm theo tên hoặc email..." 
                   value="<?php echo htmlspecialchars($search); ?>" style="min-width: 250px;">
            
            <select name="role" style="padding: 0.5rem; border: 2px solid #e1e1e1; border-radius: 5px;">
                <option value="">Tất cả vai trò</option>
                <option value="user" <?php echo $role_filter == 'user' ? 'selected' : ''; ?>>Người dùng</option>
                <option value="admin" <?php echo $role_filter == 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Tìm kiếm
            </button>
            
            <?php if (!empty($search) || !empty($role_filter)): ?>
                <a href="ManageUser.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Xóa bộ lọc
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="content-section">
    <?php if ($users->num_rows > 0): ?>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên người dùng</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php if ($user['role'] == 'admin'): ?>
                                <span class="status-badge" style="background: #667eea; color: white;">
                                    <i class="fas fa-crown"></i> Admin
                                </span>
                            <?php else: ?>
                                <span class="status-badge status-active">
                                    <i class="fas fa-user"></i> Người dùng
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="book-actions">
                                <?php if ($user['role'] !== 'admin'): ?>
                                    <!-- Sửa người dùng -->
                                    <button type="button" class="btn btn-warning" 
                                            onclick="openEditModal(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>')">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    
                                    <!-- Xóa người dùng -->
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn btn-danger"
                                                onclick="return confirm('Bạn có chắc muốn xóa người dùng này? Hành động này không thể hoàn tác!');">
                                            <i class="fas fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #666; font-style: italic;">Tài khoản Admin</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="no-data">
            <i class="fas fa-users" style="font-size: 3rem; color: #ccc; margin-bottom: 1rem;"></i>
            <p>Không tìm thấy người dùng nào.</p>
        </div>
    <?php endif; ?>
</div>

<!-- Phân trang -->
<?php if ($total_pages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>">← Trước</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <?php if ($i == $page): ?>
                <span class="current"><?php echo $i; ?></span>
            <?php else: ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>">Sau →</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<style>
/* Modal styles */
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

.close {
    color: white;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    opacity: 0.7;
    transition: opacity 0.3s;
}

.close:hover {
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

.form-group input {
    width: 100%;
    padding: 0.75rem;
    border: 2px solid #e1e1e1;
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-group input:focus {
    border-color: #667eea;
    outline: none;
}

.form-group small {
    color: #666;
    font-size: 0.9rem;
    display: block;
    margin-top: 0.25rem;
}

.modal-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
}

.btn-warning {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
    color: white;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #e67e22 0%, #d35400 100%);
}
</style>

<script>
function openEditModal(userId, username, email) {
    document.getElementById('edit_user_id').value = userId;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_password').value = '';
    document.getElementById('editUserModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('editUserModal').style.display = 'none';
}

// Đóng modal khi click outside
window.onclick = function(event) {
    var modal = document.getElementById('editUserModal');
    if (event.target == modal) {
        closeModal();
    }
}

// Đóng modal khi nhấn ESC
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// Đóng modal khi click vào nút X
document.querySelector('.close').onclick = closeModal;
</script>

<?php
$content = ob_get_clean();

// Include layout
include 'layout.php';
?> 