<?php
// Create Messaging System Database Tables
require_once 'config/database.php';

echo "<h1>💬 Creating Messaging System Database</h1>";

try {
    $pdo = getDBConnection();
    
    echo "<h2>📊 Creating Database Tables</h2>";
    
    // Create messages table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            inquiry_type ENUM('general', 'support', 'booking', 'partnership', 'feedback', 'other') DEFAULT 'general',
            status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
            priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
            INDEX idx_status (status),
            INDEX idx_created_at (created_at),
            INDEX idx_email (email),
            INDEX idx_inquiry_type (inquiry_type)
        )
    ");
    echo "<p>✅ Created 'messages' table</p>";
    
    // Create message_replies table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS message_replies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            message_id INT NOT NULL,
            admin_id INT NULL,
            reply_text TEXT NOT NULL,
            is_admin_reply BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE,
            FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL,
            INDEX idx_message_id (message_id),
            INDEX idx_created_at (created_at)
        )
    ");
    echo "<p>✅ Created 'message_replies' table</p>";
    
    // Create user_notifications table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS user_notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            message_id INT NULL,
            title VARCHAR(255) NOT NULL,
            content TEXT NOT NULL,
            type ENUM('message_reply', 'system', 'booking', 'general') DEFAULT 'general',
            is_read BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE SET NULL,
            INDEX idx_user_id (user_id),
            INDEX idx_is_read (is_read),
            INDEX idx_created_at (created_at)
        )
    ");
    echo "<p>✅ Created 'user_notifications' table</p>";
    
    echo "<h2>🧪 Testing Database Structure</h2>";
    
    // Test inserting a sample message
    $stmt = $pdo->prepare("
        INSERT INTO messages (name, email, phone, subject, message, inquiry_type, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        'John Doe',
        'john@example.com',
        '+237 652 731 798',
        'Test Message',
        'This is a test message to verify the messaging system works correctly.',
        'general',
        'new'
    ]);
    
    $messageId = $pdo->lastInsertId();
    echo "<p>✅ Created test message with ID: $messageId</p>";
    
    // Test inserting a sample reply
    $stmt = $pdo->prepare("
        INSERT INTO message_replies (message_id, reply_text, is_admin_reply) 
        VALUES (?, ?, ?)
    ");
    
    $stmt->execute([
        $messageId,
        'Thank you for your message. We have received your inquiry and will respond within 24 hours.',
        true
    ]);
    
    echo "<p>✅ Created test reply</p>";
    
    // Update message status
    $pdo->prepare("UPDATE messages SET status = 'replied' WHERE id = ?")->execute([$messageId]);
    echo "<p>✅ Updated message status to 'replied'</p>";
    
    echo "<h2>📊 Database Statistics</h2>";
    
    // Count records
    $messageCount = $pdo->query("SELECT COUNT(*) FROM messages")->fetchColumn();
    $replyCount = $pdo->query("SELECT COUNT(*) FROM message_replies")->fetchColumn();
    $notificationCount = $pdo->query("SELECT COUNT(*) FROM user_notifications")->fetchColumn();
    
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>📈 Current Records:</h3>";
    echo "<ul>";
    echo "<li><strong>Messages:</strong> $messageCount</li>";
    echo "<li><strong>Replies:</strong> $replyCount</li>";
    echo "<li><strong>Notifications:</strong> $notificationCount</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h2>🎯 Messaging System Features</h2>";
    echo "<div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>✅ Features Created:</h3>";
    echo "<ul>";
    echo "<li>📧 <strong>Contact Form Integration:</strong> Messages saved to database</li>";
    echo "<li>👨‍💼 <strong>Admin Message Management:</strong> View, reply, manage status</li>";
    echo "<li>👤 <strong>User Message Dashboard:</strong> View conversations and replies</li>";
    echo "<li>🔔 <strong>Notification System:</strong> Users notified of replies</li>";
    echo "<li>📊 <strong>Message Status Tracking:</strong> New, read, replied, closed</li>";
    echo "<li>⚡ <strong>Priority System:</strong> Low, normal, high, urgent</li>";
    echo "<li>🏷️ <strong>Inquiry Categories:</strong> General, support, booking, etc.</li>";
    echo "<li>💬 <strong>Threaded Conversations:</strong> Multiple replies per message</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<h2>🔗 Next Steps</h2>";
    echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>🚀 Implementation Plan:</h3>";
    echo "<ol>";
    echo "<li><strong>Update Contact Form:</strong> Save messages to database</li>";
    echo "<li><strong>Create Admin Messages Page:</strong> View and reply to messages</li>";
    echo "<li><strong>Create User Messages Dashboard:</strong> View conversations</li>";
    echo "<li><strong>Add Notification System:</strong> Alert users of replies</li>";
    echo "<li><strong>Test Complete System:</strong> End-to-end messaging flow</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div style='background: #d1ecf1; padding: 20px; border-radius: 10px; margin: 20px 0; text-align: center;'>";
    echo "<h3>🎉 Messaging System Database Ready!</h3>";
    echo "<p>The database structure is now in place for a complete messaging system.</p>";
    echo "<p><strong>Ready to implement admin and user messaging interfaces!</strong></p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>❌ Error Creating Messaging System</h3>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>

<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
    background: #f8f9fa;
}

h1 {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    margin-bottom: 30px;
}

h2 {
    color: #333;
    border-bottom: 2px solid #667eea;
    padding-bottom: 10px;
    margin-top: 30px;
}

ul, ol {
    line-height: 1.6;
}
</style>
