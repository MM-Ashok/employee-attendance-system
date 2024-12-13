<?php
// Define the base URL for your application
define('BASE_URL', 'http://localhost/employee-attendance-system');

// Database configuration
$host = 'localhost'; // Database host
$dbname = 'miraiemployeesystem'; // Database name
$username = 'root'; // Database username
$password = ''; // Database password (leave empty if no password)

// Establish a PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set error mode to throw exceptions for any issues
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Database connection Done:";
} catch (PDOException $e) {
    // Display an error message if the connection fails
    die("Database connection failed: " . $e->getMessage());
}
?>
