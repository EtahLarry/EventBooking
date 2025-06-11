<?php
// Test database connection
require_once 'config/database.php';

echo "<h1>Database Connection Test</h1>";

try {
    $pdo = getDBConnection();
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM events");
    $result = $stmt->fetch();
    echo "<p>📊 Events in database: " . $result['count'] . "</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "<p>👥 Users in database: " . $result['count'] . "</p>";
    
    echo "<p style='color: green;'>🎉 Database is working perfectly!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}
?>
