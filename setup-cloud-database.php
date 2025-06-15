<?php
/**
 * Cloud Database Setup Script
 * Sets up database for cloud deployment (Render, Heroku, etc.)
 */

echo "<h1>🚀 Cloud Database Setup</h1>";
echo "<p>Setting up database for cloud deployment...</p>";

// Include database configuration
require_once 'config/database.php';

echo "<h2>📊 Database Configuration</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
echo "<p><strong>Host:</strong> " . DB_HOST . "</p>";
echo "<p><strong>Database:</strong> " . DB_NAME . "</p>";
echo "<p><strong>User:</strong> " . DB_USER . "</p>";
echo "<p><strong>Port:</strong> " . DB_PORT . "</p>";
echo "</div>";

// Test database connection
echo "<h2>🔌 Testing Database Connection</h2>";
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
    
    echo "<h3>🔧 Troubleshooting Steps:</h3>";
    echo "<ol>";
    echo "<li><strong>Check Environment Variables:</strong> Ensure DATABASE_URL or DB_* variables are set</li>";
    echo "<li><strong>Verify Database Service:</strong> Make sure your database service is running</li>";
    echo "<li><strong>Check Credentials:</strong> Verify username, password, and database name</li>";
    echo "<li><strong>Network Access:</strong> Ensure your app can reach the database host</li>";
    echo "</ol>";
    
    echo "<h3>📋 Required Environment Variables:</h3>";
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "<p><strong>Option 1 - DATABASE_URL (Recommended for Render):</strong></p>";
    echo "<code>DATABASE_URL=postgresql://username:password@host:port/database_name</code>";
    echo "<p><strong>Option 2 - Individual Variables:</strong></p>";
    echo "<code>DB_HOST=your-database-host<br>";
    echo "DB_USER=your-username<br>";
    echo "DB_PASS=your-password<br>";
    echo "DB_NAME=your-database-name<br>";
    echo "DB_PORT=5432</code>";
    echo "</div>";
    
    exit;
}

// Check if tables exist
echo "<h2>📋 Checking Database Tables</h2>";
$tables = ['users', 'events', 'bookings', 'admin_users', 'messages'];
$existingTables = [];
$missingTables = [];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        $existingTables[] = $table;
    } catch (Exception $e) {
        $missingTables[] = $table;
    }
}

if (!empty($existingTables)) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "✅ <strong>Existing Tables:</strong> " . implode(', ', $existingTables);
    echo "</div>";
}

if (!empty($missingTables)) {
    echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "⚠️ <strong>Missing Tables:</strong> " . implode(', ', $missingTables);
    echo "</div>";
    
    echo "<h3>🔧 Creating Missing Tables</h3>";
    
    // Create tables for PostgreSQL
    $sqlCommands = [
        'users' => "
            CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                phone VARCHAR(20),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ",
        'events' => "
            CREATE TABLE IF NOT EXISTS events (
                id SERIAL PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                description TEXT,
                date DATE NOT NULL,
                time TIME NOT NULL,
                venue VARCHAR(200) NOT NULL,
                location VARCHAR(200) NOT NULL,
                organizer VARCHAR(100) NOT NULL,
                organizer_contact VARCHAR(100),
                price DECIMAL(10,2) NOT NULL,
                total_tickets INTEGER NOT NULL,
                available_tickets INTEGER NOT NULL,
                image VARCHAR(255),
                status VARCHAR(20) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ",
        'bookings' => "
            CREATE TABLE IF NOT EXISTS bookings (
                id SERIAL PRIMARY KEY,
                user_id INTEGER NOT NULL,
                event_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL,
                total_amount DECIMAL(10,2) NOT NULL,
                booking_reference VARCHAR(20) UNIQUE NOT NULL,
                status VARCHAR(20) DEFAULT 'confirmed',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ",
        'admin_users' => "
            CREATE TABLE IF NOT EXISTS admin_users (
                id SERIAL PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                full_name VARCHAR(100) NOT NULL,
                role VARCHAR(20) DEFAULT 'admin',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ",
        'messages' => "
            CREATE TABLE IF NOT EXISTS messages (
                id SERIAL PRIMARY KEY,
                user_id INTEGER NOT NULL,
                subject VARCHAR(200) NOT NULL,
                message TEXT NOT NULL,
                status VARCHAR(20) DEFAULT 'unread',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        "
    ];
    
    foreach ($missingTables as $table) {
        if (isset($sqlCommands[$table])) {
            try {
                $pdo->exec($sqlCommands[$table]);
                echo "<p>✅ Created table: <strong>$table</strong></p>";
            } catch (Exception $e) {
                echo "<p>❌ Failed to create table <strong>$table</strong>: " . $e->getMessage() . "</p>";
            }
        }
    }
}

// Create default admin user if not exists
echo "<h2>👤 Setting Up Admin User</h2>";
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = 'admin'");
    $stmt->execute();
    $adminExists = $stmt->fetchColumn() > 0;
    
    if (!$adminExists) {
        $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO admin_users (username, email, password, full_name, role) 
            VALUES ('admin', 'admin@eventbooking.com', ?, 'System Administrator', 'super_admin')
        ");
        $stmt->execute([$adminPassword]);
        
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
        echo "✅ <strong>Default admin user created:</strong><br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
        echo "<em>Please change this password after first login!</em>";
        echo "</div>";
    } else {
        echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
        echo "ℹ️ Admin user already exists";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "❌ Failed to create admin user: " . $e->getMessage();
    echo "</div>";
}

// Add some sample events if none exist
echo "<h2>🎪 Adding Sample Events</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM events");
    $eventCount = $stmt->fetchColumn();
    
    if ($eventCount == 0) {
        $sampleEvents = [
            [
                'name' => 'Welcome Concert 2025',
                'description' => 'A spectacular welcome concert to celebrate the launch of our event booking system!',
                'date' => '2025-12-31',
                'time' => '20:00:00',
                'venue' => 'Grand Theater',
                'location' => 'Yaoundé, Cameroon',
                'organizer' => 'Event Booking Team',
                'organizer_contact' => 'events@eventbooking.com',
                'price' => 10000,
                'total_tickets' => 1000,
                'available_tickets' => 1000,
                'image' => 'events/music-festival.jpg'
            ]
        ];
        
        foreach ($sampleEvents as $event) {
            $stmt = $pdo->prepare("
                INSERT INTO events (name, description, date, time, venue, location, organizer, organizer_contact, price, total_tickets, available_tickets, image, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
            ");
            $stmt->execute([
                $event['name'], $event['description'], $event['date'], $event['time'],
                $event['venue'], $event['location'], $event['organizer'], $event['organizer_contact'],
                $event['price'], $event['total_tickets'], $event['available_tickets'], $event['image']
            ]);
        }
        
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
        echo "✅ Sample events added successfully!";
        echo "</div>";
    } else {
        echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
        echo "ℹ️ Events already exist ($eventCount events found)";
        echo "</div>";
    }
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin: 10px 0;'>";
    echo "❌ Failed to add sample events: " . $e->getMessage();
    echo "</div>";
}

echo "<h2>🎉 Setup Complete!</h2>";
echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>✅ Your Event Booking System is ready!</h3>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>Visit your homepage to see the application</li>";
echo "<li>Login to admin panel with: admin / admin123</li>";
echo "<li>Create your first user account</li>";
echo "<li>Add more events and start booking!</li>";
echo "</ol>";
echo "</div>";

echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #28a745; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px;'>🏠 Go to Homepage</a>";
echo "<a href='admin/index.php' style='background: #007bff; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 10px;'>🛡️ Admin Panel</a>";
echo "</div>";
?>
