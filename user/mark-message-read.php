<?php
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(403);
    exit('Access denied');
}

$messageId = $_GET['id'] ?? '';

if (!$messageId) {
    http_response_code(400);
    exit('Message ID required');
}

try {
    $pdo = getDBConnection();
    $userId = $_SESSION['user_id'];
    
    // Update last read timestamp for this message (ensure user owns it)
    $stmt = $pdo->prepare("
        UPDATE messages 
        SET last_read_at = NOW() 
        WHERE id = ? AND (user_id = ? OR email = (SELECT email FROM users WHERE id = ?))
    ");
    $stmt->execute([$messageId, $userId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        echo 'success';
    } else {
        http_response_code(404);
        echo 'Message not found or access denied';
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo 'Error updating message: ' . $e->getMessage();
}
?>
