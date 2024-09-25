<?php
session_start();

include './includes/db.php';
include 'components/navbar.php';
// Get the category ID from the query string
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;

// Validate the category ID
if ($category_id <= 0) {
    die('Invalid category ID.');
}

// Fetch books from the database based on the category ID
$stmt = $pdo->prepare("SELECT * FROM books WHERE category_id = :category_id");
$stmt->execute(['category_id' => $category_id]);

// Fetch category name for display
$categoryStmt = $pdo->prepare("SELECT name FROM categories WHERE id = :category_id");
$categoryStmt->execute(['category_id' => $category_id]);
$category = $categoryStmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books in Category</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
    <div class="container mt">
        <h1 class="">Books in <?php echo htmlspecialchars($category['name']); ?></h1>
        <div class="books-list">
            <div class="card-container">
                <?php
                if ($stmt->rowCount() > 0) {
                    while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $bookUrl = './book_details.php?id=' . urlencode($book['id']);
                        echo '<a href="' . htmlspecialchars($bookUrl) . '" class="card-link">';
                        echo '<div class="card">';
                        echo '<img src="' . htmlspecialchars($book['image_path']) . '" alt="' . htmlspecialchars($book['name']) . '" class="card-image">';
                        echo '<div class="card-content">';
                        echo '<h3 class="card-title">' . htmlspecialchars($book['name']) . '</h3>';
                        echo '<p class="price">$' . number_format($book['price'], 2) . '</p>';
                        echo '<div class="card-rat">';
                        echo '<p class="rating">' . str_repeat('★', floor($book['rating'])) . str_repeat('☆', 5 - floor($book['rating'])) . '</p>';
                        echo '<i class="fa-regular fa-heart"></i>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</a>';
                    }
                } else {
                    echo '<p>No books found in this category.</p>';
                }
                ?>
            </div>
        </div>
    </div>

</body>

</html>