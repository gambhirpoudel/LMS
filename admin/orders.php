<?php
include('../includes/db.php');

// Search functionality
$saleId = '';
if (isset($_GET['search'])) {
    $saleId = trim($_GET['sale_id']);
}

// Fetch orders from the database
$query = "SELECT transactions.sale_id, transactions.rental_period, transactions.status, transactions.price, 
                 books.name AS book_name, books.image_path AS book_image, 
                 users.username AS user_name 
          FROM transactions 
          JOIN books ON transactions.book_id = books.id 
          JOIN users ON transactions.user_id = users.id";

if (!empty($saleId)) {
    $query .= " WHERE transactions.sale_id LIKE ?";
}

$query .= " ORDER BY transactions.rental_date DESC";

$stmt = $pdo->prepare($query);
if (!empty($saleId)) {
    $stmt->execute(['%' . $saleId . '%']);
} else {
    $stmt->execute();
}

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<script src="./js/script.js"></script>
<?php include('components/header.php'); ?>

<div class="admin-container">
    <?php include('components/sidebar.php'); ?>
    <div class="main-content">
        <header>
            <h1 class="page-title">Manage Orders</h1>
            <div>
                <?php include('./sales_search.php') ?>

            </div>
        </header>

        <table>
            <thead>
                <tr>
                    <th>Sale ID</th>
                    <th>Book Image</th>
                    <th>Book Name</th>
                    <th>User Name</th>
                    <th>Rental Peroid</th>
                    <th>Status</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>

                        <tr>
                            <td> <a href="./sales_details.php?sale_id=<?php echo htmlspecialchars($order['sale_id']); ?> "><?php echo htmlspecialchars($order['sale_id']); ?></a></td>
                            <td><img src="../<?php echo htmlspecialchars($order['book_image']); ?>" alt="<?php echo htmlspecialchars($order['book_name']); ?>" style="width: 100px;"></td>
                            <td><?php echo htmlspecialchars($order['book_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($order['rental_period']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td>$<?php echo number_format($order['price'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No orders found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>