<?php
ob_start();
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiệm sách của giấc mơ</title>
    <link rel="icon" href="assets/img/icon/ico_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/children.css">
    <link rel="stylesheet" href="assets/css/vanhoc.css">
    <link rel="stylesheet" href="assets/css/scienceBook.css">
    <link rel="stylesheet" href="assets/css/search-autocomplete.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="jquery/jquery-3.7.1.min.js"></script>
    <script src="assets/js/search-autocomplete.js"></script>
</head>

<body>
    <!-- HEADER -->
    <header>
        <div class="container">
            <a href="index.php" class="logo-link">
                <img src="assets/img/logo/logo_uth.png" id="logo">
            </a>

            <!-- Dropdown loại sách -->
            <div class="dropdown">
                <button class="dropdown-toggle">
                    <img src="assets/img/icon/ico_menu.svg">
                </button>
                <ul class="dropdown-menu">
                    <li><a href="index.php?page=literature">Văn học</a></li>
                    <li><a href="index.php?page=science">Khoa học</a></li>
                    <li><a href="index.php?page=children">Thiếu nhi</a></li>
                    <li><a href="#">Lịch sử</a></li>
                    <li><a href="#">Kinh tế</a></li>
                </ul>
            </div>
            <div>
                <form class="search-bar" id="searchForm">
                    <input type="search" placeholder="Tìm kiếm sách..." aria-label="Tìm kiếm sách">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <nav>
                <ul>
                    <li><a href="#" class="nav-link"><i class="fa-solid fa-bell"></i>Thông báo</a></li>
                    <li>
                        <a href="index.php?page=cart" class="nav-link">
                            <i class="fas fa-shopping-cart"></i>Giỏ hàng
                            <span id="cart-count" class="cart-count">
                                <?php
                                if (isset($_SESSION['cart'])) {
                                    $total_items = 0;
                                    foreach ($_SESSION['cart'] as $item) {
                                        $total_items += $item['quantity'];
                                    }
                                    echo $total_items;
                                } else {
                                    echo '0';
                                }
                                ?>
                            </span>
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                    <li class="user-account">
                        <a href="#" class="nav-link">
                            <i class="fas fa-user"></i>
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <div class="account-dropdown">
                            <a href="admin/" class="admin-link" style="display: block; padding: 0.5rem; text-decoration: none; color: #333; border-bottom: 1px solid #eee;">Quản trị Admin</a>
                            <form action="config.php" method="POST">
                                <button type="submit" name="logout" class="logout-btn">Đăng xuất</button>
                            </form>
                            <a href="index.php?page=order_tracking" class="nav-link">Đơn hàng</a>
                        </div>
                    </li>
                    <?php else: ?>
                        <li class="btnLogin-popup">
                            <a href="#" class="nav-link">
                                <i class="fas fa-user btnLogin-popup"></i>Tài Khoản
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <div class="main-contain">
        <div class="wrapper">
            <span class="icon-close"><i class="fa-solid fa-xmark"></i></span>
            <div class="form-box <?= isActiveForm('login', $activeForm); ?> login ">
                <h2>Đăng Nhập</h2>
                <?= showError($error['login']); ?>
                <form action="config.php" method="POST">
                    <div class="input-box">
                        <span class="icon"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" required>
                        <label>Email</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" required>
                        <label>Mật khẩu</label>
                    </div>
                    <div class="remember-forgot">
                        <label><input type="checkbox">
                            Ghi nhớ đăng nhập</label>
                        <a href="#">Quên mật khẩu?</a>
                    </div>
                    <button type="submit" class="btnn" name="login">Đăng Nhập</button>
                    <div class="login-register">
                        <p>Bạn chưa có tài khoản? <a href="#" class="register-link">Đăng ký</a></p>
                    </div>
                </form>
            </div>
            <div class="form-box register <?= isActiveForm('register', $activeForm); ?>">
                <h2>Đăng Ký</h2>
                <?= showError($error['register']); ?>
                <form action="config.php" method="POST">
                    <div class="input-box">
                        <span class="icon"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="username" required>
                        <label>Tên người dùng</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" required>
                        <label>Email</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" required>
                        <label>Mật khẩu</label>
                    </div>
                    <div class="remember-forgot">
                        <label><input type="checkbox" required>
                            Tôi đồng ý với các điều khoản & điều kiện</label>
                    </div>
                    <button type="submit" class="btnn" name="register">Đăng Ký</button>
                    <div class="login-register">
                        <p>Bạn đã có tài khoản? <a href="#" class="login-link">Đăng nhập</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
    <?php 
    $notification = getNotification();
    if ($notification): 
    ?>
    <div class="notification <?php echo $notification['type']; ?>">
        <i class="fas <?php 
            echo $notification['type'] === 'success' ? 'fa-check-circle' : 
                ($notification['type'] === 'error' ? 'fa-times-circle' : 'fa-exclamation-circle'); 
        ?>"></i>
        <div class="message"><?php echo htmlspecialchars($notification['message']); ?></div>
        <div class="progress">
            <div class="progress-bar"></div>
        </div>
    </div>
    <?php endif; ?>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Hiển thị notification
        const notification = document.querySelector('.notification');
        if (notification) {
            setTimeout(() => {
                notification.classList.add('show');
            }, 100);

            // Tự động ẩn sau 3 giây
            setTimeout(() => {
                notification.classList.remove('show');
                // Xóa notification sau khi animation kết thúc
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }
    });
    </script>
</body>

</html>