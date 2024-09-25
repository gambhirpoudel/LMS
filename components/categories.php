<?php
include './includes/db.php';

// Fetch categories from the database
$stmt = $pdo->query("SELECT * FROM categories");
if ($stmt->rowCount() > 0) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<a href="./categories_book.php?category_id=' . $row['id'] . '" class="category-link">' . htmlspecialchars($row['name']) . '</a>';
    }
} else {
    echo '<a href="#" class="no-results">No categories found.</a>';
}
