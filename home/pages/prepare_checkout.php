<?php
session_start();
if (isset($_POST['selected_items'])) {
    $_SESSION['checkout_items'] = json_decode($_POST['selected_items'], true);
}
?> 