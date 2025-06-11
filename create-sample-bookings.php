<?php
// Create sample bookings for testing
require_once 'config/database.php';

echo "<h1>Creating Sample Bookings</h1>";

try {
    $pdo = getDBConnection();
    
    // First, let's check if we have users and events
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $userCount = $stmt->fetch()['count'];
    echo "<p>Users in database: $userCount</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM events");
    $eventCount = $stmt->fetch()['count'];
    echo "<p>Events in database: $eventCount</p>";
    
    if ($userCount == 0) {
        echo "<p style='color: red;'>No users found. Creating sample user...</p>";
        
        // Create sample user
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, first_name, last_name, phone) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            'john_doe',
            'john@example.com',
            password_hash('password123', PASSWORD_DEFAULT),
            'John',
            'Doe',
            '123-456-7890'
        ]);
        echo "<p style='color: green;'>Sample user created: john_doe / password123</p>";
    }
    
    // Get user ID
    $stmt = $pdo->prepare("SELECT id FROM users LIMIT 1");
    $stmt->execute();
    $userId = $stmt->fetch()['id'];
    
    // Get event IDs
    $stmt = $pdo->query("SELECT id, name FROM events LIMIT 3");
    $events = $stmt->fetchAll();
    
    if (empty($events)) {
        echo "<p style='color: red;'>No events found. Please run the database setup first.</p>";
        exit;
    }
    
    // Check if bookings already exist
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
    $bookingCount = $stmt->fetch()['count'];
    
    if ($bookingCount > 0) {
        echo "<p style='color: blue;'>Sample bookings already exist ($bookingCount bookings found).</p>";
    } else {
        echo "<p>Creating sample bookings...</p>";
        
        // Create sample bookings
        foreach ($events as $index => $event) {
            $bookingRef = 'BK' . date('Ymd') . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            
            // Vary the booking dates and statuses
            $statuses = ['confirmed', 'confirmed', 'cancelled'];
            $status = $statuses[$index % 3];
            
            $stmt = $pdo->prepare("
                INSERT INTO bookings (
                    user_id, event_id, quantity, total_amount, booking_reference, 
                    attendee_name, attendee_email, attendee_phone, status, payment_status,
                    booking_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $userId,
                $event['id'],
                rand(1, 3), // Random quantity 1-3
                rand(50, 300), // Random total amount
                $bookingRef,
                'John Doe',
                'john@example.com',
                '123-456-7890',
                $status,
                $status === 'confirmed' ? 'completed' : 'refunded'
            ]);
            
            echo "<p>✅ Created booking for: " . htmlspecialchars($event['name']) . " (Status: $status)</p>";
        }
    }
    
    // Show final count
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
    $finalCount = $stmt->fetch()['count'];
    echo "<p style='color: green;'>🎉 Total bookings in database: $finalCount</p>";
    
    echo "<p><a href='user/bookings.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Booking History</a></p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
