<?php
include './includes/db.php';

// Fetch popular books from the database
$stmt = $pdo->query("SELECT * FROM books ORDER BY rating DESC LIMIT 6");
if ($stmt->rowCount() > 0) {
    echo '<div class="card-container">';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Ensure that the URL to the book's details page is correctly formed
        $bookUrl = './book_details.php?id=' . urlencode($row['id']);

        echo '<a href="' . htmlspecialchars($bookUrl) . '" class="card-link">';
        echo '<div class="card">';
        echo '<img src="' . htmlspecialchars($row['image_path']) . '" alt="' . htmlspecialchars($row['name']) . '" class="card-image">';
        echo '<div class="card-content">';
        echo '<h3 class="card-title">' . htmlspecialchars($row['name']) . '</h3>';
        echo '<p class="price">$' . number_format($row['price'], 2) . '</p>';
        echo '<div class="card-rat">';
        echo '<p class="rating">' . str_repeat('★', floor($row['rating'])) . str_repeat('☆', 5 - floor($row['rating'])) . '</p>';
        echo '<i class="fa-regular fa-heart"></i>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</a>';
    }
    echo '</div>';
} else {
    echo '<p class="no-results">No latest books found.</p>';
}
