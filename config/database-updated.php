<?php
// Database configuration - supports both Docker and cloud deployment
// Priority: Environment variables > Docker defaults

// Initialize default values
$db_host = 'db';
$db_user = 'root';
$db_pass = 'rootpassword';
$db_name = 'event_booking_system';
$db_port = 3306;
$db_type = 'mysql';

// Check for DATABASE_URL first (cloud deployment)
$database_url = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL') ?? null;

if ($database_url) {
    $parsed = parse_url($database_url);
    
    if ($parsed && isset($parsed['host'])) {
        $db_host = $parsed['host'];
        $db_name = ltrim($parsed['path'] ?? '', '/') ?: $db_name;
        $db_user = $parsed['user'] ?? $db_user;
        $db_pass = $parsed['pass'] ?? $db_pass;
        $db_port = $parsed['port'] ?? ($parsed['scheme'] === 'postgresql' ? 5432 : 3306);
        $db_type = $parsed['scheme'] === 'postgresql' ? 'pgsql' : 'mysql';
    }
} else {
    // Check individual environment variables
    $db_host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? $db_host;
    $db_user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? $db_user;
    $db_pass = $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?? $db_pass;
    $db_name = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? $db_name;
    $db_port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? $db_port;
    $db_type = $_ENV['DB_TYPE'] ?? getenv('DB_TYPE') ?? $db_type;
}

// Define constants for backward compatibility
define('DB_HOST', $db_host);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_NAME', $db_name);
define('DB_PORT', $db_port);
define('DB_TYPE', $db_type);

// Create database connection
function getDBConnection() {
    try {
        // Build DSN based on database type
        if (DB_TYPE === 'pgsql') {
            // PostgreSQL connection
            $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ];
        } else {
            // MySQL connection (default)
            $dsn = "mysql:host=" . DB_HOST;
            if (DB_PORT && DB_PORT != 3306) {
                $dsn .= ";port=" . DB_PORT;
            }
            $dsn .= ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
            ];
        }
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
        
    } catch(PDOException $e) {
        // Log error details for debugging
        error_log("Database connection error: " . $e->getMessage());
        error_log("Attempting to connect to: " . DB_TYPE . "://" . DB_HOST . ":" . DB_PORT . "/" . DB_NAME . " with user: " . DB_USER);
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
