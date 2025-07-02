<?php
require_once('auth_check.php');
require_once('../db.php');

// Kiểm tra xác thực admin - đã được check trong index.php
// checkAdminAuth();

// Lấy thông tin admin
$admin = getCurrentAdmin();

// Lấy page hiện tại từ URL parameter để highlight menu
$current_page = $_GET['page'] ?? 'dashboard';
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Admin Dashboard'; ?> - UTH Bookstore</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>UTH Bookstore - Admin</h1>
        <div class="header-right">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="admin-info">
                <span>Xin chào, <strong><?php echo htmlspecialchars($admin['username']); ?></strong></span>
            </div>
            <form method="POST" style="display: inline;">
                <button type="submit" name="logout" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </button>
            </form>
        </div>
    </div>

    <!-- Layout container -->
    <div class="admin-layout">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">

            <div class="actions-grid">
                <a href="index.php?page=dashboard" class="action-btn <?php echo $current_page == 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="index.php?page=manage_books" class="action-btn <?php echo $current_page == 'manage_books' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i> Quản lý sách
                </a>
                <a href="index.php?page=add_book" class="action-btn <?php echo $current_page == 'add_book' ? 'active' : ''; ?>">
                    <i class="fas fa-plus"></i> Thêm sách mới
                </a>
                <a href="index.php?page=manage_user" class="action-btn <?php echo $current_page == 'manage_user' ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Quản lý người dùng
                </a>
                <a href="index.php?page=orders" class="action-btn <?php echo $current_page == 'orders' ? 'active' : ''; ?>">
                    <i class="fas fa-shopping-cart"></i> Quản lý đơn hàng
                </a>
                <a href="index.php?page=manage_categories" class="action-btn <?php echo $current_page == 'manage_categories' ? 'active' : ''; ?>">
                    <i class="fas fa-tags"></i> Quản lý danh mục
                </a>
                <a href="index.php?page=reports" class="action-btn <?php echo $current_page == 'reports' ? 'active' : ''; ?>">
                    <i class="fas fa-chart-bar"></i> Báo cáo
                </a>
                <a href="../index.php" class="action-btn" style="margin-top: 1rem; background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                    <i class="fas fa-globe"></i> Xem website
                </a>
            </div>
        </div>

        <!-- Main content -->
        <div class="main-content">
            <?php if (isset($content)) echo $content; ?>
        </div>
    </div>

    <script>
    // Toggle sidebar on mobile
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.toggle('active');
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.querySelector('.sidebar-toggle');
        
        if (!sidebar.contains(event.target) && !toggleBtn.contains(event.target)) {
            sidebar.classList.remove('active');
        }
    });

    // Add active class to current page
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = '<?php echo $current_page; ?>';
        const menuItems = document.querySelectorAll('.action-btn');
        
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href && href.includes('page=' + currentPage)) {
                item.classList.add('active');
            }
        });
    });
    </script>
</body>
</html> 