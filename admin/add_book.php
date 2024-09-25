<?php
include('../includes/db.php');

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

    // Handle the image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Define the upload directory and move the uploaded file
        $uploadDir = '../assets/book_images/';
        $path = 'assets/book_images/';
        $imageName = basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;

        // Check if the directory exists or create it
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the file to the specified directory
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $imageDBPath = $path . $imageName;
        } else {
            die('Error uploading the image.');
        }
    } else {
        die('Image file upload failed.');
    }

    // Insert data into the database
    $stmt = $pdo->prepare("INSERT INTO books (name, description, isbn, price, rating, image_path, category_id, author, publish_date, publisher) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $isbn, $price, $rating, $imageDBPath, $category_id, $author, $publish_date, $publisher]);

    // Redirect after success
    header('Location: book.php');
    exit();
}
?>

<?php include('components/header.php'); ?>
<div class="admin-container">
    <?php include('components/sidebar.php'); ?>

    <div class="main-content">
        <h1>Add New Book</h1>

        <!-- Form for adding a new book -->
        <form action="add_book.php" method="POST" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>

            <label for="isbn">ISBN:</label>
            <input type="text" id="isbn" name="isbn" required>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="rating">Rating:</label>
            <input type="number" id="rating" name="rating" step="0.01" min="0" max="5">

            <label for="image">Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required onchange="previewImage(event)">

            <!-- Image preview section -->
            <div>
                <img id="imagePreview" src="" alt="Image Preview" style="max-width: 200px; display: none;">
            </div>

            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" required>
                <?php
                // Fetch categories from the database
                $stmt = $pdo->query("SELECT * FROM categories");
                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($categories as $category) {
                    echo '<option value="' . $category['id'] . '">' . htmlspecialchars($category['name']) . '</option>';
                }
                ?>
            </select>

            <label for="author">Author:</label>
            <input type="text" id="author" name="author">

            <label for="publish_date">Publish Date:</label>
            <input type="date" id="publish_date" name="publish_date">

            <label for="publisher">Publisher:</label>
            <input type="text" id="publisher" name="publisher">

            <button type="submit">Add Book</button>
        </form>
    </div>
</div>

<script src="./js/script.js"></script>