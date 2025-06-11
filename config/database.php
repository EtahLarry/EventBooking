<?php
// Database configuration for Docker environment
// Docker container connects to 'db' service, external connections use localhost:3307
define('DB_HOST', 'db'); // Docker service name
define('DB_USER', 'root');
define('DB_PASS', 'rootpassword');
define('DB_NAME', 'event_booking_system');

// Create database connection
function getDBConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Test database connection
function testConnection() {
    try {
        $pdo = getDBConnection();
        return true;
    } catch(Exception $e) {
        return false;
    }
}
?>
