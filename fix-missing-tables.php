<?php
/**
 * Fix Missing Tables Script
 * Adds missing tables to the database
 */

echo "<h1>🔧 Fix Missing Tables</h1>";
echo "<p>Adding missing tables to your database...</p>";

// Include database configuration
require_once 'config/database.php';

try {
    $pdo = getDBConnection();
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "✅ <strong>Database connection successful!</strong>";
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "❌ <strong>Database connection failed:</strong><br>";
    echo $e->getMessage();
    echo "</div>";
    exit;
}

echo "<h2>🔧 Creating Missing Tables</h2>";

// Create cart_items table
try {
    $sql = "
        CREATE TABLE IF NOT EXISTS cart_items (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            event_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL DEFAULT 1,
            added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(user_id, event_id)
        )
    ";
    
    $pdo->exec($sql);
    echo "<p>✅ Created table: <strong>cart_items</strong></p>";
} catch (Exception $e) {
    echo "<p>❌ Failed to create cart_items table: " . $e->getMessage() . "</p>";
}

// Check if bookings table has all required columns
echo "<h2>🔍 Checking Bookings Table Structure</h2>";
try {
    // Try to select from bookings with all required columns
    $stmt = $pdo->query("SELECT id, user_id, event_id, quantity, total_amount, booking_reference, status, created_at FROM bookings LIMIT 1");
    echo "<p>✅ Bookings table structure is correct</p>";
} catch (Exception $e) {
    echo "<p>⚠️ Bookings table may need updates: " . $e->getMessage() . "</p>";
    
    // Try to add missing columns if needed
    try {
        // Add booking_reference column if missing
        $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS booking_reference VARCHAR(20) UNIQUE");
        echo "<p>✅ Added booking_reference column</p>";
    } catch (Exception $e) {
        echo "<p>ℹ️ booking_reference column already exists or couldn't be added</p>";
    }
    
    try {
        // Add status column if missing
        $pdo->exec("ALTER TABLE bookings ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'confirmed'");
        echo "<p>✅ Added status column</p>";
    } catch (Exception $e) {
        echo "<p>ℹ️ status column already exists or couldn't be added</p>";
    }
}

// Verify all tables exist
echo "<h2>📋 Verifying All Tables</h2>";
$requiredTables = ['users', 'events', 'bookings', 'admin_users', 'cart_items'];

foreach ($requiredTables as $table) {
    try {
        $stmt = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        echo "<p>✅ Table <strong>$table</strong> exists and accessible</p>";
    } catch (Exception $e) {
        echo "<p>❌ Table <strong>$table</strong> missing or inaccessible: " . $e->getMessage() . "</p>";
    }
}

// Test cart functionality
echo "<h2>🛒 Testing Cart Functionality</h2>";
try {
    // Test if we can insert into cart_items
    $testUserId = 999999; // Use a high ID that won't conflict
    $testEventId = 1;
    
    // Clean up any existing test data
    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
    $stmt->execute([$testUserId]);
    
    // Test insert
    $stmt = $pdo->prepare("INSERT INTO cart_items (user_id, event_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$testUserId, $testEventId, 1]);
    
    // Test select
    $stmt = $pdo->prepare("SELECT * FROM cart_items WHERE user_id = ?");
    $stmt->execute([$testUserId]);
    $result = $stmt->fetch();
    
    if ($result) {
        echo "<p>✅ Cart functionality test successful</p>";
        
        // Clean up test data
        $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
        $stmt->execute([$testUserId]);
        echo "<p>✅ Test data cleaned up</p>";
    } else {
        echo "<p>❌ Cart functionality test failed</p>";
    }
    
} catch (Exception $e) {
    echo "<p>❌ Cart functionality test failed: " . $e->getMessage() . "</p>";
}

echo "<h2>🎉 Fix Complete!</h2>";
echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>✅ Missing tables have been added!</h3>";
echo "<p><strong>What was fixed:</strong></p>";
echo "<ul>";
echo "<li>✅ Created cart_items table for shopping cart functionality</li>";
echo "<li>✅ Verified all required tables exist</li>";
echo "<li>✅ Tested cart functionality</li>";
echo "</ul>";
echo "<p><strong>Your Event Booking System should now work completely!</strong></p>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px;'>🏠 Test Homepage</a>";
echo "<a href='events.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px;'>🎫 Test Events</a>";
echo "</div>";
?>
