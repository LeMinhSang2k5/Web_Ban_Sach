<?php
// Chỉ start session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$error = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? ''
];
$activeForm = $_SESSION['active_form'] ?? 'login';

unset($_SESSION['login_error'], $_SESSION['register_error'], $_SESSION['active_form']);

function showError($error)
{
    return !empty($error) ? "<p class='error-message'>$error</p>" : '';
}

function isActiveForm($formName, $activeForm)
{
    return $formName === $activeForm ? 'active' : '';
}

// Xử lý trang hiển thị
$page = $_GET['page'] ?? 'home';

include 'db.php';
?>

<?php include 'includes/header.php'; ?>
<main>
    <?php 
    switch($page) {
        case 'children':
            include 'pages/chilldrenBook.php';
            break;
        case 'bookDetail':
            include 'pages/bookDetail.php';
            break;
        case 'cart':
            include 'pages/cart.php';
            break;
        case 'literature':
            include 'pages/literatureBook.php';
            break;
        case "science":
            include 'pages/scienceBook.php';
            break;
        case 'checkout':
            include 'pages/checkout.php';
            break;
        case 'category':
            include 'pages/category.php';
            break;
        default:
            include 'pages/home.php';
            break;
    }
    ?>
</main>
<?php include 'includes/footer.php'; ?>


