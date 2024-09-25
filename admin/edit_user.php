<?php
include('../includes/db.php');

// Fetch user information
if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$edit_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die('User not found');
    }
}

// Fetch roles
$rolesStmt = $pdo->query("SELECT * FROM roles");
$roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission for editing user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = $_POST['edit_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $role_id = $_POST['role_id'];

    // Check role restrictions (e.g., only master or librarian can assign an admin role)
    if (($role_id == 1) && !in_array($_SESSION['role_id'], [3])) {
        die('Only librarian can assign an admin role.');
    }

    // Handle profile image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = '../assets/profile_image/';
        $path = 'assets/profile_image/';
        $imageName = basename($_FILES['image']['name']);
        $imagePath = $uploadDir . $imageName;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $imageDBPath = $path . $imageName;
        } else {
            die('Error uploading the image.');
        }
    } else {
        $imageDBPath = $_POST['current_image'];
    }

    // Update user in database
    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ?, profile_image = ?, role_id = ? WHERE id = ?");
    $stmt->execute([$username, $email, $first_name, $last_name, $imageDBPath, $role_id, $edit_id]);

    header('Location: users.php');
    exit();
}
?>
<?php include('components/header.php'); ?>
<div class="admin-container">
    <?php include('components/sidebar.php'); ?>
    <div class="main-content">
        <h1>Edit User</h1>
        <form action="edit_user.php?id=<?php echo $edit_id; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
            <input type="hidden" name="current_image" value="<?php echo $user['profile_image']; ?>">

            <label for="username">Username:</label>
            <input type="text" name="username" value="<?php echo $user['username']; ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo $user['email']; ?>" required>

            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" value="<?php echo $user['first_name']; ?>" required>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" value="<?php echo $user['last_name']; ?>" required>

            <label for="role_id">Role:</label>
            <select name="role_id" required>
                <?php foreach ($roles as $role): ?>
                    <option value="<?php echo $role['id']; ?>" <?php echo $role['id'] == $user['role_id'] ? 'selected' : ''; ?>><?php echo $role['role_name']; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="image">Current Image:</label><br>
            <img src="../<?= htmlspecialchars($user['profile_image']) ?>" alt="Book Image" style="max-width: 200px;">
            <br><br>

            <label for="image">Replace Image:</label>
            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">

            <!-- Image preview section -->
            <div>
                <img id="imagePreview" src="" alt="New Image Preview" style="max-width: 200px; display: none;">
            </div>

            <button type="submit">Update User</button>
        </form>
    </div>
</div>