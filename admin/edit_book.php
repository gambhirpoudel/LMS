<?php
include('../includes/db.php');

// Fetch the book details for the given book ID
if (isset($_GET['id'])) {
    $book_id = $_GET['id'];

    // Fetch book data
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$book_id]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        die('Book not found.');
    }
} else {
    die('No book ID specified.');
}

// Handle the form submission to update the book
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $isbn = $_POST['isbn'];
    $price = $_POST['price'];
    $rating = $_POST['rating'];
    $category_id = $_POST['category_id'];
    $author = $_POST['author'];
    $publish_date = $_POST['publish_date'];
    $publisher = $_POST['publisher'];
    $imageDBPath = $book['image_path']; // Default to the existing image

    // Handle image upload if a new file is provided
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/book_images/';
        $path = 'assets/book_images/';
        $imageName = basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Replace the old image with the new one if uploaded successfully
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $imageDBPath = $path . $imageName; // Save new image path to DB
        } else {
            die('Error uploading the image.');
        }
    }

    // Update the book data in the database
    $stmt = $pdo->prepare("UPDATE books SET name = ?, description = ?, isbn = ?, price = ?, rating = ?, image_path = ?, category_id = ?, author = ?, publish_date = ?, publisher = ? WHERE id = ?");
    $stmt->execute([$name, $description, $isbn, $price, $rating, $imageDBPath, $category_id, $author, $publish_date, $publisher, $book_id]);

    // Redirect to the book listing page after updating
    header('Location: book.php');
    exit();
}
?>
<?php include('components/header.php'); ?>
<div class="admin-container">
    <?php include('components/sidebar.php'); ?>

    <div class="main-content">
        <h1>Edit Book</h1>

        <!-- Form for editing the book -->
        <form action="edit_book.php?id=<?= htmlspecialchars($book_id) ?>" method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($book['name']) ?>" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description"><?= htmlspecialchars($book['description']) ?></textarea>

            <label for="isbn">ISBN:</label>
            <input type="text" id="isbn" name="isbn" value="<?= htmlspecialchars($book['isbn']) ?>" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" value="<?= htmlspecialchars($book['price']) ?>" step="0.01" required>

            <label for="rating">Rating:</label>
            <input type="number" id="rating" name="rating" value="<?= htmlspecialchars($book['rating']) ?>" step="0.01" min="0" max="5">

            <label for="image">Current Image:</label><br>
            <img src="../<?= htmlspecialchars($book['image_path']) ?>" alt="Book Image" style="max-width: 200px;">
            <br><br>

            <label for="image">Replace Image:</label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">

            <!-- Image preview section -->
            <div>
                <img id="imagePreview" src="" alt="New Image Preview" style="max-width: 200px; display: none;">
            </div>

            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" required>
                <?php
                // Fetch categories from the database
                $stmt = $pdo->query("SELECT * FROM categories");
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($categories as $category) {
                    echo '<option value="' . $category['id'] . '"';
                    if ($category['id'] == $book['category_id']) {
                        echo ' selected';
                    }
                    echo '>' . htmlspecialchars($category['name']) . '</option>';
                }
                ?>
            </select>

            <label for="author">Author:</label>
            <input type="text" id="author" name="author" value="<?= htmlspecialchars($book['author']) ?>">

            <label for="publish_date">Publish Date:</label>
            <input type="date" id="publish_date" name="publish_date" value="<?= htmlspecialchars($book['publish_date']) ?>">

            <label for="publisher">Publisher:</label>
            <input type="text" id="publisher" name="publisher" value="<?= htmlspecialchars($book['publisher']) ?>">

            <button type="submit">Update Book</button>
        </form>
    </div>
</div>

<script src="./js/script.js"></script>