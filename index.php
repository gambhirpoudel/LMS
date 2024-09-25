<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Management System</title>
    <link rel="stylesheet" href="styles/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <section class="recent-books">
        <div class="container">
            <h2 class="section-title">Latest Books</h2>
            <div class="books-list">
                <?php include 'components/latest_books.php'; ?>
            </div>
        </div>
    </section>
    <section class="popular-books">
        <div class="container">
            <h2 class="section-title">Popular Books</h2>
            <div class="books-list">
                <?php include 'components/popular_books.php'; ?>
            </div>
        </div>
    </section>
</body>

</html>