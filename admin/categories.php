<?php
include('../includes/db.php');

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // First, remove the category_id reference from books table
    $updateStmt = $pdo->prepare("UPDATE books SET category_id = NULL WHERE category_id = ?");
    $updateStmt->execute([$delete_id]);

    // Now delete the category
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$delete_id]);
    header('Location: categories.php'); // Redirect to avoid re-submission on refresh
    exit();
}

// Handle edit request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $new_name = $_POST['name'];

    $stmt = $pdo->prepare("UPDATE categories SET name = ? WHERE id = ?");
    $stmt->execute([$new_name, $edit_id]);
    header('Location: categories.php');
    exit();
}

// Handle add request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = $_POST['name'];

    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
    $stmt->execute([$name]);
    header('Location: categories.php');
    exit();
}

// Fetch categories from the database
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include('components/header.php'); ?>

<div class="admin-container">
    <?php include('components/sidebar.php'); ?>
    <div class="main-content">
        <header>
            <h1 class="page-title">Manage Categories</h1>
            <div class="">
                <button class="btn btn-primary" id="openAddModal">Add Category</button>
            </div>
        </header>

        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td>
                            <button class="btn btn-primary edit-btn" data-id="<?php echo htmlspecialchars($category['id']); ?>" data-name="<?php echo htmlspecialchars($category['name']); ?>">Edit</button>
                            <a href="categories.php?delete_id=<?php echo htmlspecialchars($category['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Category Modal -->
<div id="addCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddModal">&times;</span>
        <h2>Add Category</h2>
        <form action="categories.php" method="POST">
            <input type="hidden" name="add_category" value="1">
            <label for="add-name">Category Name:</label>
            <input type="text" id="add-name" name="name" required>
            <button type="submit" class="btn">Add Category</button>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="editCategoryModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeEditModal">&times;</span>
        <h2>Edit Category</h2>
        <form action="categories.php" method="POST">
            <input type="hidden" name="edit_id" id="edit-id">
            <label for="edit-name">Category Name:</label>
            <input type="text" id="edit-name" name="name" required>
            <button type="submit" class="btn">Update Category</button>
        </form>
    </div>
</div>

<script>
    // Add Category Modal
    var addModal = document.getElementById("addCategoryModal");
    var openAddModal = document.getElementById("openAddModal");
    var closeAddModal = document.getElementById("closeAddModal");

    openAddModal.onclick = function() {
        addModal.style.display = "block";
    };

    closeAddModal.onclick = function() {
        addModal.style.display = "none";
    };

    window.onclick = function(event) {
        if (event.target == addModal) {
            addModal.style.display = "none";
        }
    };
    // Edit Category Modal
    var editModal = document.getElementById("editCategoryModal");
    var closeEditModal = document.getElementById("closeEditModal");

    document.querySelectorAll(".edit-btn").forEach((button) => {
        button.onclick = function() {
            var id = this.getAttribute("data-id");
            var name = this.getAttribute("data-name");
            document.getElementById("edit-id").value = id;
            document.getElementById("edit-name").value = name;
            editModal.style.display = "block";
        };
    });

    closeEditModal.onclick = function() {
        editModal.style.display = "none";
    };

    window.onclick = function(event) {
        if (event.target == editModal) {
            editModal.style.display = "none";
        }
    };
</script>
</body>

</html>