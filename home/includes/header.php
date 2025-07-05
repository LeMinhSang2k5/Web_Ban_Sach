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
    <link rel="stylesheet" href="assets/css/category.css">
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
                    <button type="submit"><img src="assets/img/icon/ico_search_white.svg" alt="Cart" style="width: 24px; height: 24px;"></button>
                </form>
            </div>
            <nav>
                <ul>
                    <li><a href="#" class="nav-link"><img src="assets/img/icon/ico_noti_gray.svg" alt="Cart" style="width: 24px; height: 24px;"></i>Thông báo</a></li>
                    <li>
                        <a href="index.php?page=cart" class="nav-link">
                            <img src="assets/img/icon/ico_cart_gray.svg" alt="Cart" style="width: 24px; height: 24px;">Giỏ hàng
                            
                                <?php
                                if (isset($_GET['page']) && $_GET['page'] == 'cart') {
                                    $total_quantity = array_sum(array_column($_SESSION['cart'], 'quantity'));
                                    echo '<span id="cart-count" class="cart-count">'
                                            .$total_quantity.'
                                            </span>';
                                }
                                ?>
                        </a>
                    </li>
                    <?php if (isLoggedIn()): ?>
                    <li class="user-account">
                        <a href="#" class="nav-link">
                            <img src="assets/img/icon/ico_account_gray.svg" style="width: 24px; height: 24px;">
                            <?php echo htmlspecialchars($_SESSION['username']); ?>
                        </a>
                        <div class="account-dropdown">
                            <?php if (isAdmin($_SESSION['user_id'])): ?>
                            <a href="admin/" class="admin-link" style="display: block; padding: 0.5rem; text-decoration: none; color: #333; border-bottom: 1px solid #eee;">Quản trị Admin</a>
                            <?php endif; ?>
                            <form action="config.php" method="POST">
                                <button type="submit" name="logout" class="logout-btn">Đăng xuất</button>
                            </form>
                            <a href="index.php?page=order_tracking" class="nav-link">Đơn hàng</a>
                        </div>
                    </li>
                    <?php else: ?>
                        <li class="btnLogin-popup">
                            <a href="#" class="nav-link">
                                <img src="assets/img/icon/ico_account_gray.svg" style="width: 24px; height: 24px;">Tài Khoản
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
                        <input type="text" name="username" required>
                        <label>Tên đăng nhập</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" required>
                        <label>Mật khẩu</label>
                    </div>
                    <div class="remember-forgot">
                        <label><input type="checkbox" name="remember_me" value="1">
                            Ghi nhớ đăng nhập</label>
                        <a href="index.php?page=forgot-password" class="forgot-password-link">Quên mật khẩu?</a>
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
    $(document).ready(function() {
        // Hiển thị notification
        var $notification = $('.notification');
        if ($notification.length) {
            setTimeout(function() {
                $notification.addClass('show');
            }, 100);

            // Tự động ẩn sau 3 giây
            setTimeout(function() {
                $notification.removeClass('show');
                // Xóa notification sau khi animation kết thúc
                setTimeout(function() {
                    $notification.remove();
                }, 500);
            }, 3000);
        }
    });
    </script>
</body>

</html>