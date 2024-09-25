<?php
include('../includes/db.php');

// Fetch books from the database
$stmt = $pdo->query("SELECT * FROM books ORDER BY publish_date DESC");
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM books WHERE id = ?");
    $stmt->execute([$deleteId]);
    header('Location: book.php');
    exit();
}
?>
<?php include('components/header.php'); ?>
<div class="admin-container">
    <?php include('components/sidebar.php'); ?>
    <div class="main-content">
        <header>
            <h1 class="page-title">Manage Books</h1>
            <div class="">
                <a href="add_book.php" class="btn btn-primary">Add New Book</a>
            </div>
        </header>


        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Author</th>
                    <th>ISBN</th>
                    <th>Price</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><img src="../<?php echo htmlspecialchars($book['image_path']); ?>" alt="<?php echo htmlspecialchars($book['name']); ?>"></td>
                        <td><?php echo htmlspecialchars($book['name']); ?></td>
                        <td><?php echo htmlspecialchars($book['author']); ?></td>
                        <td><?php echo htmlspecialchars($book['isbn']); ?></td>
                        <td>$<?php echo number_format($book['price'], 2); ?></td>
                        <td><?php echo number_format($book['rating'], 2); ?>/5</td>
                        <td class="actions">
                            <a href="edit_book.php?id=<?php echo $book['id']; ?> " class="btn btn-primary edit-btn">Edit</a>
                            <a href="book.php?delete_id=<?php echo $book['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this book?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>