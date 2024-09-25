
<?php
session_name('AdminSession');
session_start(); // Start the admin session
session_destroy(); // Destroy the admin session
header('Location: login.php'); // Redirect to login page
exit;
