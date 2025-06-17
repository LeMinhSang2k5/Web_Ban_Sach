<?php
// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

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
        default:
            include 'pages/home.php';
            break;
    }
    ?>
</main>
<?php include 'includes/footer.php'; ?>


