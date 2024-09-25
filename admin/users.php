<?php
include('../includes/db.php');

// handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$deleteId]);
    header('Location: users.php');
    exit();
}


// Fetch users and roles from the database
$usersStmt = $pdo->query("SELECT * FROM users");
$users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);

$rolesStmt = $pdo->query("SELECT * FROM roles");
$roles = $rolesStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include('components/header.php'); ?>

<div class="admin-container">
    <?php include('components/sidebar.php'); ?>

    <div class="main-content">

        <header>
            <h1 class="page-title">Manage Users</h1>
            <div class="">
                <a href="add_user.php" class="btn btn-primary">Add User</a>
            </div>
        </header>
        <table>
            <thead>
                <tr>
                    <th>Profile Image</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><img src="../<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Profile Image" class="user-image"></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></td>
                        <td>
                            <?php
                            $roleStmt = $pdo->prepare("SELECT role_name FROM roles WHERE id = ?");
                            $roleStmt->execute([$user['role_id']]);
                            $role = $roleStmt->fetchColumn();
                            echo htmlspecialchars($role);
                            ?>
                        </td>
                        <td class="actions">
                            <a href="edit_user.php?id=<?php echo htmlspecialchars($user['id']); ?>" class="btn btn-primary">Edit</a>
                            <a href="users.php?delete_id=<?php echo $user['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>