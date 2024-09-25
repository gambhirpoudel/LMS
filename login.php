<?php
session_start();

include './includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Regex patterns for validation
    $usernamePattern = '/^[a-zA-Z0-9]{3,16}$/';

    // Validate username format
    if (!preg_match($usernamePattern, $user)) {
        $error = 'Invalid username format. Only letters and numbers allowed, 3-16 characters long.';
    } else {
        try {
            // Use prepared statements to prevent SQL injection
            $stmt = $pdo->prepare("SELECT id, password_hash, role_id FROM users WHERE username = :username");
            $stmt->bindParam(':username', $user);
            $stmt->execute();

            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify password and start session
            if ($userData && password_verify($pass, $userData['password_hash'])) {
                session_name('UserSession');
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['username'] = $user;
                header('Location: index.php');
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            echo "Query failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Base Styles */
        * {
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            color: #f0f4f8;
        }

        body {
            background-color: #0a192f;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #233554;
            border-radius: 12px;
            padding: 2rem;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .login-title {
            font-size: 1.75rem;
            margin-bottom: 2rem;
            color: #ccd6f6;
            text-align: center;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            color: #ccd6f6;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            border: none;
            background-color: #ccd6f6;
            color: #333;
            font-size: 1rem;
        }

        .form-group input::placeholder {
            color: #333;
        }

        .form-group input:focus {
            outline: none;
            background-color: #ccd6f6;
        }

        .login-button {
            background-color: #64ffda;
            color: #233554;
            border: none;
            padding: 0.75rem 1.5rem;
            width: 100%;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
        }

        .login-button:focus {
            outline: none;
        }

        .login-button:active {
            background-color: #52e2b9;
        }

        .text-center {
            text-align: center;
            margin-top: 1.5rem;
        }

        .text-center a {
            color: #64ffda;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .error {
            color: #ff6b6b;
            font-size: 0.9rem;
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>

<body>

    <div class="container">
        <h2 class="login-title">Login</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <button type="submit" class="login-button">Login</button>

            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </form>

        <div class="text-center">
            <p>Don't have an account? <a href="register.php">Sign up</a></p>
        </div>
    </div>

</body>

</html>