<?php
$host = 'sql.freedb.tech';
$dbname = 'freedb_Library';
$username = 'freedb_lmsnp';
$password = 'f@AFRTyMw!7Unk#';

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
