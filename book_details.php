<?php
session_start();
include './includes/db.php';

$book_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the book details from the database
$stmt = $pdo->prepare("SELECT b.*, c.name AS category_name FROM books b
                        LEFT JOIN categories c ON b.category_id = c.id
                        WHERE b.id = ?");
$stmt->execute([$book_id]);
$book = $stmt->fetch(PDO::FETCH_ASSOC);

if ($book) {
    // Book details
    $title = htmlspecialchars($book['name']);
    $description = htmlspecialchars($book['description']);
    $isbn = htmlspecialchars($book['isbn']);
    $price = number_format($book['price'], 2);
    $rating = $book['rating'];
    $image_path = htmlspecialchars($book['image_path']);
    $category = htmlspecialchars($book['category_name']);
    $author = htmlspecialchars($book['author']);
    $publish_date = date('F j, Y', strtotime($book['publish_date']));
    $publisher = htmlspecialchars($book['publisher']);

    // Fetch related books
    $related_stmt = $pdo->prepare("SELECT b.id, b.name, b.image_path, b.price, b.rating 
                                   FROM books b
                                   LEFT JOIN categories c ON b.category_id = c.id
                                   WHERE c.name = ? AND b.id != ?
                                   ORDER BY b.publish_date DESC
                                   LIMIT 6");
    $related_stmt->execute([$category, $book_id]);
    $related_books = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Sorting function
    function bubble_sort(&$books, $key)
    {
        $n = count($books);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n - $i - 1; $j++) {
                if ($books[$j][$key] > $books[$j + 1][$key]) {
                    $temp = $books[$j];
                    $books[$j] = $books[$j + 1];
                    $books[$j + 1] = $temp;
                }
            }
        }
    }

    bubble_sort($related_books, 'price');
} else {
    // Book not found
    echo '<p class="no-results">Book not found.</p>';
    exit;
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $quantity = 1; // Default quantity

        // Check if the item is already in the cart
        $check_query = "SELECT quantity FROM cart WHERE user_id = ? AND book_id = ?";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->execute([$user_id, $book_id]);
        $existing_item = $check_stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_item) {
            // If it exists, update the quantity
            $new_quantity = $existing_item['quantity'] + $quantity;
            $update_query = "UPDATE cart SET quantity = ? WHERE user_id = ? AND book_id = ?";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->execute([$new_quantity, $user_id, $book_id]);
        } else {
            // If it doesn't exist, insert a new record
            $insert_query = "INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, ?)";
            $insert_stmt = $pdo->prepare($insert_query);
            $insert_stmt->execute([$user_id, $book_id, $quantity]);
        }

        // Redirect to cart.php
        header("Location: cart.php");
        exit;
    } else {
        // Redirect to login if user is not logged in
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Book Details</title>
    <link rel="stylesheet" href="./styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>

<body>
    <?php include 'components/navbar.php'; ?>

    <div class="details_container book-details">
        <div class="book-image">
            <img src="<?php echo $image_path; ?>" alt="<?php echo $title; ?>">
        </div>
        <div class="book-info">
            <h1 class="book-title"><?php echo $title; ?></h1>
            <p class="book-author">Author: <?php echo $author; ?></p>
            <p class="book-category">Category: <?php echo $category; ?></p>
            <p class="book-publisher">Publisher: <?php echo $publisher; ?></p>
            <p class="book-publish-date">Publish Date: <?php echo $publish_date; ?></p>
            <p class="book-isbn">ISBN: <?php echo $isbn; ?></p>
            <p class="book-price">Price: $<?php echo $price; ?></p>
            <p class="book-rating">Rating: <?php echo str_repeat('★', floor($rating)) . str_repeat('☆', 5 - floor($rating)); ?></p>
            <p class="book-description"><?php echo nl2br($description); ?></p>

            <div class="add-to-cart">
                <form method="POST">
                    <button type="submit" name="add_to_cart" class="btn-add-to-cart">Add to Cart</button>
                </form>
            </div>
        </div>
    </div>

    <div class="related-books">
        <h2 class="related-books-title">Related Books</h2>
        <div class="related-books-container">
            <?php if (!empty($related_books)): ?>
                <?php foreach ($related_books as $related_book): ?>
                    <div class="related-book-card">
                        <a href="book_details.php?id=<?php echo $related_book['id']; ?>" class="book-card">
                            <img src="<?php echo htmlspecialchars($related_book['image_path']); ?>" alt="<?php echo htmlspecialchars($related_book['name']); ?>" class="book-image">
                            <div class="related-book-info">
                                <h3 class="related-book-title"><?php echo htmlspecialchars($related_book['name']); ?></h3>
                                <p class="related-book-price">Price: $<?php echo number_format($related_book['price'], 2); ?></p>
                                <p class="related-book-rating">Rating: <?php echo str_repeat('★', floor($related_book['rating'])) . str_repeat('☆', 5 - floor($related_book['rating'])); ?></p>
                            </div>

                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No related books found.</p>
            <?php endif; ?>
        </div>
    </div>




</body>

</html>