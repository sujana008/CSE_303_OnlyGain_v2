<?php
// Database connection settings
$host = 'localhost'; // XAMPP default host
$dbname = 'only_gains'; // Database name
$username = 'root'; // XAMPP default username
$password = ''; // XAMPP default password (empty by default)

// Establishing connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!<br>";
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

?>