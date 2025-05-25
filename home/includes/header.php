<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiệm sách của giấc mơ</title>
    <link rel="icon" href="img/ico_logo.png" type="image/x-icon">
    <link rel="stylesheet" href="assets/css/main.css">
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
                    <li><a href="#van-hoc" class="nav-link"><i class="fas fa-user"></i>Tài Khoản</a></li>
                </ul>
            </nav>
        </div>
    </header>
