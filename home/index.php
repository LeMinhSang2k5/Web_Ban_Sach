<?php
    include 'db.php';

    $sql = "SELECT * FROM books";
    $result = $conn->query($sql);
?>

<?php include 'includes/header.php'; ?>

<main>
    <?php include 'pages/home.php'; ?>
</main>

<?php include 'includes/footer.php'; ?>
