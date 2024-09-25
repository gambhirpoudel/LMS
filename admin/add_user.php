<?php
include('../includes/db.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect user data from the form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role_id = $_POST['role_id'];

    // Handle the image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        // Define the upload directory and move the uploaded file
        $uploadDir = '../assets/profile_image/';
        $path = 'assets/profile_image/';
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
        $imagePath = 'assets/profile_image/default.jpg';
    }

    // Insert user into the database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, first_name, last_name, password_hash, role_id, profile_image) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$username, $email, $first_name, $last_name, $password, $role_id, $imageDBPath])) {
        // Redirect after success
        header('Location: users.php');
        exit();
    } else {
        $error = "Failed to add user.";
    }
}

// Fetch roles for the select dropdown
$rolesStmt = $pdo->query("SELECT * FROM roles");
$roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include('components/header.php'); ?>
<div class="admin-container">
    <?php include('components/sidebar.php'); ?>
    <div class="main-content">
        <header>
            <h1 class="page-title">Add User</h1>
        </header>

        <!-- Display any error messages -->
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <!-- User form -->
        <form action="add_user.php" method="POST" enctype="multipart/form-data">
            <div>
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div>
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div>
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" id="first_name" required>
            </div>
            <div>
                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" id="last_name" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div>
                <label for="role_id">Role:</label>
                <select name="role_id" id="role_id" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo htmlspecialchars($role['id']); ?>">
                            <?php echo htmlspecialchars($role['role_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <label for="image">Image:</label>
            <input type="file" id="image" name="image" accept="image/*" required onchange="previewImage(event)">

            <!-- Image preview section -->
            <div>
                <img id="imagePreview" src="" alt="Image Preview" style="max-width: 200px; display: none;">
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Add User</button>
            </div>
        </form>
    </div>
</div>
<script src="./js/script.js"></script>