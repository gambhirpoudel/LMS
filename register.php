<?php
include './includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'];
    $email = $_POST['email'];
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $pass = $_POST['password'];

    // Regex patterns
    $usernamePattern = '/^[a-zA-Z0-9]{3,16}$/';
    $emailPattern = '/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,4}$/';
    $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\.@$])[A-Za-z\d\.@$]{8,}$/';
    $namePattern = '/^[a-zA-Z\s]+$/';

    // Validate username
    if (!preg_match($usernamePattern, $user)) {
        $error = 'Username must be 3-16 characters long and contain only letters and numbers.';
    }
    // Validate email
    elseif (!preg_match($emailPattern, $email)) {
        $error = 'Invalid email format.';
    }
    // Validate first name
    elseif (!preg_match($namePattern, $firstName)) {
        $error = 'First name must contain only letters and spaces.';
    }
    // Validate last name
    elseif (!preg_match($namePattern, $lastName)) {
        $error = 'Last name must contain only letters and spaces.';
    }
    // Validate password
    elseif (!preg_match($passwordPattern, $pass)) {
        $error = 'Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, one number, and one of these symbols: ., @, $.';
    } else {
        // Handle file upload and database insertion if all validations pass
        $profileImage = null;
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = './assets/profile_image/';
            $dbPath = 'assets/profile_image/';
            $imageName = basename($_FILES['profile_image']['name']);
            $imagePath = $uploadDir . $imageName;

            // Validate the file type
            $fileType = mime_content_type($_FILES['profile_image']['tmp_name']);
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp']; // Add allowed types here

            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $imagePath)) {
                    $imageDBPath = $dbPath . $imageName;
                } else {
                    die('Error uploading the image.');
                }
            } else {
                $error = 'Uploaded file is not a valid image. Please upload a JPEG, PNG, GIF, or WEBP image.';
            }
        } else {
            $error = 'Image file upload failed.';
        }

        // Proceed with database insertion only if no errors
        if (!isset($error)) {
            $hashedPassword = password_hash($pass, PASSWORD_DEFAULT);

            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
                $stmt->bindParam(':username', $user);
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    $error = "Username or email already exists.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash, first_name, last_name, profile_image, role_id) VALUES (:username, :email, :password_hash, :first_name, :last_name, :profile_image, DEFAULT)");
                    $stmt->bindParam(':username', $user);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':password_hash', $hashedPassword);
                    $stmt->bindParam(':first_name', $firstName);
                    $stmt->bindParam(':last_name', $lastName);
                    $stmt->bindParam(':profile_image', $imageDBPath);
                    $stmt->execute();
                    header('Location: login.php');
                    exit;
                }
            } catch (PDOException $e) {
                echo "Query failed: " . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
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

        .register-title {
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

        .register-button {
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

        .register-button:focus {
            outline: none;
        }

        .register-button:active {
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
        <h2 class="register-title">Register</h2>
        <form action="register.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="Choose a username" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" required>
            </div>

            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="form-group">
                <label for="profile_image">Profile Image</label>
                <input type="file" id="profile_image" name="profile_image" accept="image/*">
            </div>

            <button type="submit" class="register-button">Register</button>

            <?php if (isset($error)): ?>
                <div class="error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </form>

        <div class="text-center">
            <p>Already have an account? <a href="login.php">Log in</a></p>
        </div>
    </div>

</body>

</html>