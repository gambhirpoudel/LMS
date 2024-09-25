<?php
// Start the session
session_start();

// Include database connection
include './includes/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user cart items
$user_id = $_SESSION['user_id'];
$query = "
    SELECT c.id AS cart_id, b.id AS book_id, b.name AS title, c.quantity, b.price, b.image_path 
    FROM cart c 
    JOIN books b ON c.book_id = b.id 
    WHERE c.user_id = ?
";
$stmt = $pdo->prepare($query);
$stmt->execute([$user_id]);
$cartItems = $stmt->fetchAll();

// Check if cart is empty
$message = empty($cartItems) ? "Your cart is empty." : null;

// Initialize variable to control modal display
$showModal = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_item'])) {
        // Remove item from cart
        $cart_item_id = $_POST['cart_item_id'];
        $remove_query = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $remove_stmt = $pdo->prepare($remove_query);
        $remove_stmt->execute([$cart_item_id, $user_id]);
    } elseif (isset($_POST['update_quantity'])) {
        // Update quantity
        $cart_item_id = $_POST['cart_item_id'];

        // Fetch the current quantity of the item to update
        $current_item_query = "SELECT quantity FROM cart WHERE id = ? AND user_id = ?";
        $current_item_stmt = $pdo->prepare($current_item_query);
        $current_item_stmt->execute([$cart_item_id, $user_id]);
        $current_item = $current_item_stmt->fetch();

        if ($current_item) {
            $current_quantity = $current_item['quantity'];

            // Check if increment or decrement
            if ($_POST['update_quantity'] === 'increment') {
                $new_quantity = $current_quantity + 1;
            } else {
                $new_quantity = max(1, $current_quantity - 1); // Prevent quantity from going below 1
            }

            // Update the quantity in the database
            $update_query = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
            $update_stmt = $pdo->prepare($update_query);
            $update_stmt->execute([$new_quantity, $cart_item_id, $user_id]);
        }
    } elseif (isset($_POST['checkout'])) {
        // Calculate total in cart.php and store it in the session
        $total = array_reduce($cartItems, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);

        $_SESSION['total_price'] = $total;
        // No need to store rental_period here; it's handled in the modal's form
        $showModal = true;
    }

    // Refresh the cart items after modification
    $stmt->execute([$user_id]);
    $cartItems = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles/styles.css">
    <title>Cart</title>
    <script src="https://khalti.com/static/khalti-checkout.js"></script>
    <style>
        .modal {
            display: none;
            justify-content: center;
            align-items: center;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(10, 25, 47, 0.8);
        }

        .modal-content {
            background-color: #233554;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .close {
            color: #64ffda;
            float: right;
            font-size: 1.5rem;
            cursor: pointer;
        }

        .section-title {
            color: #64ffda;
        }

        .total {
            font-size: 1.5rem;
            color: #64ffda;
            margin: 1rem 0;
        }

        .payment-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            background-color: #ccd6f6;
            color: #333;
            border: none;
            margin-bottom: 1.5rem;
        }

        .payment-select:focus {
            background-color: #e2eafc;
        }

        .checkout-btn {
            text-align: center;
        }

        option {
            background-color: rgba(10, 25, 47, 0.8);
            color: #64ffda;
        }
    </style>
</head>

<body>

    <?php include 'components/navbar.php'; ?>

    <div class="container mt">
        <h2>Your Cart</h2>

        <?php if ($message): ?>
            <div class="empty-cart">
                <p><?php echo htmlspecialchars($message); ?></p>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Book Image</th>
                        <th>Book Title</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td><img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width: 50px; height: auto;"></td>
                            <td><?php echo htmlspecialchars($item['title']); ?></td>
                            <td>
                                <form method="post" style="display: flex; align-items: center; justify-content: center;">
                                    <input type="hidden" name="cart_item_id" value="<?php echo htmlspecialchars($item['cart_id']); ?>">
                                    <button type="submit" class="qbtn" name="update_quantity" value="decrement" style="margin-right: 5px;">-</button>
                                    <span style="margin: 0 5px;"><?php echo htmlspecialchars($item['quantity']); ?></span>
                                    <button type="submit" class="qbtn" name="update_quantity" value="increment" style="margin-left: 5px;">+</button>
                                </form>
                            </td>

                            <td><?php echo htmlspecialchars($item['price']); ?></td>
                            <td><?php echo htmlspecialchars($item['price'] * $item['quantity']); ?></td>
                            <td>
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="cart_item_id" value="<?php echo htmlspecialchars($item['cart_id']); ?>">
                                    <button type="submit" class="btn btn-danger" name="remove_item">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="total">
                <strong>Total:</strong>
                <?php
                $total = array_reduce($cartItems, function ($sum, $item) {
                    return $sum + ($item['price'] * $item['quantity']);
                }, 0);
                echo htmlspecialchars($total);
                ?>
            </div>
            <div class="checkout">
                <form method="post">
                    <button id="checkoutBtn" class="btn" name="checkout">Pay through Khalti</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- The Modal -->
    <div id="checkoutModal" class="modal" style="display: <?php echo $showModal ? 'flex' : 'none'; ?>;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h3 class="section-title">Checkout</h3>
            <p class="total">Total: <?php echo htmlspecialchars($total); ?></p>
            <form id="paymentForm" action="checkout.php" method="post">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select name="payment_method" id="payment_method" required class="payment-select">
                    <option value="khalti">Khalti</option>
                    <option value="cash_on_delivery">Cash on Delivery</option>
                </select>
                <div class="checkout-btn">
                    <button type="submit" class="btn">Confirm Payment</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Get the modal and close button
        var modal = document.getElementById('checkoutModal');
        var closeModalBtn = document.getElementsByClassName('close')[0];

        // When the user clicks on the close button, close the modal
        closeModalBtn.onclick = function() {
            modal.style.display = 'none';
        }

        // Close the modal if clicked outside of it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }

        // Khalti Payment Integration
        var checkoutBtn = document.getElementById('checkoutBtn');
        var paymentForm = document.getElementById('paymentForm');

        checkoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            var selectedMethod = document.getElementById('payment_method').value;

            if (selectedMethod === 'khalti') {
                var khaltiConfig = {
                    // Replace the public key with your Khalti public key
                    "publicKey": "YOUR_PUBLIC_KEY_HERE",
                    "productIdentity": "1234567890",
                    "productName": "Online Book Store",
                    "productUrl": "http://yourwebsite.com/product/123",
                    "paymentPreference": ["KHALTI"],
                    "eventHandler": {
                        onSuccess(payload) {
                            console.log(payload);
                            // Handle payment success, submit to server
                            paymentForm.submit();
                        },
                        onError(error) {
                            console.log(error);
                            alert('Payment failed!');
                        }
                    }
                };
                var checkout = new KhaltiCheckout(khaltiConfig);
                checkout.show({
                    amount: <?php echo $total * 100; ?>
                });
            } else {
                paymentForm.submit();
            }
        });
    </script>

</body>

</html>