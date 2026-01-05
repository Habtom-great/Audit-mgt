
<?php
// db.php

try {
    // Update the database credentials accordingly
    $pdo = new PDO('mysql:host=localhost;dbname=audit_management', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
<?php
// Database credentials
$host = "localhost";       // Host name
$dbname = "audit_management"; // Database name
$username = "root";        // Database username
$password = "";            // Database password

// Create connection using MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>




