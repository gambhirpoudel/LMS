<?php
session_start();
include './includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'];
    $rental_period = isset($_POST['rental_period']) ? (int)$_POST['rental_period'] : 7;

    $book_ids = $_POST['book_ids'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $user_id = $_SESSION['user_id'];

    if (empty($book_ids) || empty($quantities) || count($book_ids) !== count($quantities)) {
        echo "Invalid order data.";
        exit;
    }

    $total_price = 0;
    $book_prices = [];

    foreach ($book_ids as $index => $book_id) {
        $quantity = (int)$quantities[$index];

        // Fetch the price of the book once and store it
        $price_query = "SELECT price FROM books WHERE id = ?";
        $price_stmt = $pdo->prepare($price_query);
        $price_stmt->execute([$book_id]);
        $price_result = $price_stmt->fetch();

        if ($price_result) {
            $book_prices[$book_id] = $price_result['price'];
            $total_price += $price_result['price'] * $quantity;
        } else {
            echo "Book with ID $book_id not found.";
            exit;
        }
    }

    $sale_id = 'lms' . strtoupper(uniqid());

    if ($payment_method === 'esewa') {
        include './khalti.php';
    } else {
        try {
            $pdo->beginTransaction();

            $insert_query = "
                INSERT INTO transactions 
                (book_id, user_id, rental_period, price, sale_id, payment_method) 
                VALUES (?, ?, ?, ?, ?, ?)
            ";
            $stmt = $pdo->prepare($insert_query);

            foreach ($book_ids as $index => $book_id) {
                $quantity = (int)$quantities[$index];

                if (isset($book_prices[$book_id])) {
                    $book_price = $book_prices[$book_id];
                } else {
                    throw new Exception("Book with ID $book_id not found.");
                }

                $stmt->execute([
                    $book_id,
                    $user_id,
                    $rental_period,
                    $book_price * $quantity,
                    $sale_id,
                    $payment_method
                ]);
            }

            // Clear the cart after successful transaction
            $remove_query = "DELETE FROM cart WHERE user_id = ?";
            $remove_stmt = $pdo->prepare($remove_query);
            $remove_stmt->execute([$user_id]);

            $pdo->commit();
            unset($_SESSION['total_price'], $_SESSION['rental_period']);

            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "Failed to process the order: " . htmlspecialchars($e->getMessage());
            exit;
        }
    }
}
