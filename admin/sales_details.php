<?php
include('../includes/db.php');

// Check if sale_id is provided via GET request
$saleId = '';
if (isset($_GET['sale_id'])) {
    $saleId = trim($_GET['sale_id']);
} else {
    // Redirect back or show a message if sale_id is not provided
    echo "Invalid request. No Sale ID provided.";
    exit();
}

// Fetch order details for the specific sale_id from the database
$query = "SELECT transactions.sale_id, transactions.status, transactions.rental_period, transactions.price, transactions.rental_date,
                 books.name AS book_name, books.image_path AS book_image, 
                 users.username AS user_name 
          FROM transactions 
          JOIN books ON transactions.book_id = books.id 
          JOIN users ON transactions.user_id = users.id
          WHERE transactions.sale_id = ?";

$stmt = $pdo->prepare($query);
$stmt->execute([$saleId]);

$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the order exists
if (!$order) {
    echo "No order found for Sale ID: " . htmlspecialchars($saleId);
    exit();
}
?>

<?php include('components/header.php'); ?>

<div class="admin-container">
    <?php include('components/sidebar.php'); ?>
    <div class="main-content">
        <header>
            <h1 class="page-title">Sale Details (Sale ID: <?php echo htmlspecialchars($order['sale_id']); ?>)</h1>
        </header>

        <div class="order-details">
            <h2>Order Summary</h2>
            <div class="order-summary">

                <div class="order-item">
                    <div class="order-image">
                        <img src="../<?php echo htmlspecialchars($order['book_image']); ?>" alt="<?php echo htmlspecialchars($order['book_name']); ?>" style="width: 150px; height: 200px; object-fit: cover;">
                    </div>
                    <div class="order-info">
                        <p><strong>Book Name:</strong> <?php echo htmlspecialchars($order['book_name']); ?></p>
                        <p><strong>User:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                        <p><strong>Rented for:</strong> <?php echo htmlspecialchars($order['rental_period']); ?>days</p>
                        <p><strong>Price:</strong> $<?php echo number_format($order['price'], 2); ?></p>
                        <p><strong>Rental Date:</strong> <?php echo date('F j, Y, g:i a', strtotime($order['rental_date'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>