<?php
$host = 'localhost'; // Your database host
$dbname = 'epiz_123456_dbname'; // Replace with your actual database name
$username = 'epiz_123456_user'; // Replace with your database username
$password = 'your_password'; // Replace with your database password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
