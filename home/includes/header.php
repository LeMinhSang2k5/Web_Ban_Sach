<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiệm sách của giấc mơ</title>
    <link rel="icon" href="img/ico_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <!-- HEADER -->
    <header>
        <div class="container">
            <a href="index.php" class="logo-link">
                <img src="assets/img/logo_uth.png" id="logo">
            </a>

            <!-- Dropdown loại sách -->
            <div class="dropdown">
                <button class="dropdown-toggle">
                    <img src="assets/img/ico_menu.svg">
                </button>
                <ul class="dropdown-menu">
                    <li><a href="#">Văn học</a></li>
                    <li><a href="#">Khoa học</a></li>
                    <li><a href="#">Thiếu nhi</a></li>
                    <li><a href="#">Lịch sử</a></li>
                    <li><a href="#">Kinh tế</a></li>
                </ul>
            </div>
            <div>
                <form class="search-bar" id="searchForm">
                    <input type="search" placeholder="Tìm kiếm sách..." aria-label="Tìm kiếm sách" maxlength="128">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <nav>
                <ul>
                    <li><a href="#sach-moi" class="nav-link"><i class="fa-solid fa-bell"></i>Thông báo</a></li>
                    <li><a href="#best-seller" class="nav-link"><i class="fas fa-shopping-cart"></i>Giỏ hàng</a></li>
                    <li class="btnLogin-popup"><a href="#van-hoc" class="nav-link "><i class="fas fa-user btnLogin-popup"></i>Tài Khoản</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <div class="main-contain">
        <div class="wrapper">
            <span class="icon-close"><i class="fa-solid fa-xmark"></i></span>
            <div class="form-box <?= isActiveForm('login', $activeForm); ?> login ">
                <h2>Login</h2>
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
                        <label>Password</label>
                    </div>
                    <div class="remember-forgot">
                        <label><input type="checkbox">
                            remember me</label>
                        <a href="#">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btnn" name="login">Login</button>
                    <div class="login-register">
                        <p> Don't have a account?<a href="#" class="register-link">Register</a></p>
                    </div>
                </form>
            </div>
            <div class="form-box register <?= isActiveForm('register', $activeForm); ?>">
                <h2>Registration</h2>
                <?= showError($error['register']); ?>
                <form action="config.php" method="POST">
                    <div class="input-box">
                        <span class="icon"><i class="fa-solid fa-user"></i></span>
                        <input type="text" name="username" required>
                        <label>Username</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><i class="fa-solid fa-envelope"></i></span>
                        <input type="email" name="email" required>
                        <label>Email</label>
                    </div>
                    <div class="input-box">
                        <span class="icon"><i class="fa-solid fa-lock"></i></span>
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>
                    <div class="remember-forgot">
                        <label><input type="checkbox">
                            I agree to the terms & conditions</label>

                    </div>
                    <button type="submit" class="btnn" name="register">Register</button>
                    <div class="login-register">
                        <p> Already have a account?<a href="#" class="login-link">Login</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
</body>

</html>