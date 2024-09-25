<?php
session_start();
include('../includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['auser_id'])) {
    header("Location: login.php");
    exit;
}
// Fetch total users
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmt->fetchColumn();

// Fetch total revenue
$stmt = $pdo->query("SELECT SUM(price) FROM transactions");
$totalRevenue = $stmt->fetchColumn();

// Fetch total books
$stmt = $pdo->query("SELECT COUNT(*) FROM books");
$totalBooks = $stmt->fetchColumn();

// Fetch pending orders (you can adjust this query based on your actual needs)
$stmt = $pdo->query("SELECT COUNT(*) FROM transactions WHERE status = 'rented'");
$pendingOrders = $stmt->fetchColumn();
?>

<?php include('components/header.php'); ?>
<div class="admin-container">
    <!-- Sidebar -->
    <?php include('components/sidebar.php'); ?>

    <!-- Main Content -->
    <div class="main-content">
        <div class="card-container">
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="card-details">
                    <h3>Total Users</h3>
                    <p><?php echo htmlspecialchars($totalUsers); ?></p>
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-book"></i>
                </div>
                <div class="card-details">
                    <h3>Total Books</h3>
                    <p><?php echo htmlspecialchars($totalBooks); ?></p>
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fa-solid fa-money-check-dollar"></i>
                </div>
                <div class="card-details">
                    <h3>Total Revenue</h3>
                    <p>$<?php echo htmlspecialchars($totalRevenue); ?></p>
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="card-details">
                    <h3>Total Rented</h3>
                    <p><?php echo htmlspecialchars($pendingOrders); ?></p>
                </div>
            </div>
        </div>

        <!-- Add a canvas element for the chart -->
        <canvas id="myChart" width="380" height="150"></canvas>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Data from PHP
    const totalUsers = <?php echo json_encode($totalUsers); ?>;
    const totalBooks = <?php echo json_encode($totalBooks); ?>;
    const totalRevenue = <?php echo json_encode($totalRevenue); ?>;
    const pendingOrders = <?php echo json_encode($pendingOrders); ?>;

    // Chart.js setup
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Total Users', 'Total Books', 'Total Revenue', 'Total Rented'],
            datasets: [{
                label: 'Admin Data Overview',
                data: [totalUsers, totalBooks, totalRevenue, pendingOrders],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            responsive: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });
</script>