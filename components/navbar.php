<?php
// Include database connection
include './includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$query = "SELECT first_name, last_name, role_id, profile_image FROM users WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!-- HTML Navigation Bar -->
<nav class="navbar">
    <div class="container">
        <div class="logo">
            <h1>Library System</h1>
        </div>
        <ul class="nav-links">
            <li><a href="index.php" class="nav-link">Home</a></li>
            <li><a href="cart.php" class="nav-link">Cart</a></li>
            <li class="dropdown">
                <a href="#" class="nav-link dropbtn">Categories</a>
                <div class="dropdown-content">
                    <?php include 'categories.php'; ?>
                </div>
            </li>

            <?php include('search.php') ?>

        </ul>
        <div class="user-profile">
            <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="User Image" class="user-image" />
            <span class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
            <div class="logout-dropdown">
                <?php if ($user['role_id'] == 1 || $user['role_id'] == 3): ?>
                    <a href="admin/" class="logout-option"><?php echo htmlspecialchars("Admin Panel"); ?></a>
                <?php endif; ?>
                <a href="logout.php" class="logout-option">Logout</a>
            </div>
        </div>
    </div>
</nav>

<script>
    let dropdownTimeout;

    // Show the logout dropdown on mouse enter
    document.querySelector('.user-profile').addEventListener('mouseenter', function() {
        clearTimeout(dropdownTimeout);
        document.querySelector('.logout-dropdown').style.display = 'block';
    });

    // Hide the logout dropdown on mouse leave
    document.querySelector('.user-profile').addEventListener('mouseleave', function() {
        dropdownTimeout = setTimeout(function() {
            document.querySelector('.logout-dropdown').style.display = 'none';
        }, 300); // Delay for hiding dropdown
    });

    // Prevent dropdown from closing when hovering over it
    document.querySelector('.logout-dropdown').addEventListener('mouseenter', function() {
        clearTimeout(dropdownTimeout);
    });

    document.querySelector('.logout-dropdown').addEventListener('mouseleave', function() {
        dropdownTimeout = setTimeout(function() {
            document.querySelector('.logout-dropdown').style.display = 'none';
        }, 300); // Delay for hiding dropdown
    });
</script>