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
            <span class="icon-close"><svg style="width:28px;height: 28px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#ffffff" d="M342.6 150.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0L192 210.7 86.6 105.4c-12.5-12.5-32.8-12.5-45.3 0s-12.5 32.8 0 45.3L146.7 256 41.4 361.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0L192 301.3 297.4 406.6c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L237.3 256 342.6 150.6z"/></svg></span>
            <div class="form-box <?= isActiveForm('login', $activeForm); ?> login ">
                <h2>Đăng Nhập</h2>
                <?= showError($error['login']); ?>
                <form action="config.php" method="POST">
                    <div class="input-box">
                        <span class="icon"><svg style="width:24px;height: 24px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg></span>
                        <input type="text" name="username" required>
                        <label>Tên đăng nhập</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><svg style="width:24px;height: 24px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#000000" d="M144 144l0 48 160 0 0-48c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192l0-48C80 64.5 144.5 0 224 0s144 64.5 144 144l0 48 16 0c35.3 0 64 28.7 64 64l0 192c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 256c0-35.3 28.7-64 64-64l16 0z"/></svg></span>
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
                        <span class="icon"><svg style="width:24px;height: 24px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/></svg></span>
                        <input type="text" name="username" required>
                        <label>Tên người dùng</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><svg style="width:24px;height: 24px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48L48 64zM0 176L0 384c0 35.3 28.7 64 64 64l384 0c35.3 0 64-28.7 64-64l0-208L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/></svg></span>
                        <input type="email" name="email" required>
                        <label>Email</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><svg style="width:24px;height: 24px;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path fill="#000000" d="M144 144l0 48 160 0 0-48c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192l0-48C80 64.5 144.5 0 224 0s144 64.5 144 144l0 48 16 0c35.3 0 64 28.7 64 64l0 192c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 256c0-35.3 28.7-64 64-64l16 0z"/></svg></span>
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