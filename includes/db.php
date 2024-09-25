<?php
$host = 'localhost';
$dbname = 'lms';
$username = 'root';
$password = 'root';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Connection successful
    // echo "Connected to the database successfully.";
} catch (PDOException $e) {
    // Connection failed
    echo "Connection failed: " . $e->getMessage();
}
