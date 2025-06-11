<?php
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['new_replies' => 0]);
    exit();
}

try {
    $pdo = getDBConnection();
    $userId = $_SESSION['user_id'];
    
    // Count new admin replies since last read
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as new_replies
        FROM message_replies mr
        JOIN messages m ON mr.message_id = m.id
        WHERE (m.user_id = ? OR m.email = (SELECT email FROM users WHERE id = ?))
        AND mr.is_admin_reply = TRUE
        AND mr.created_at > COALESCE(m.last_read_at, '1970-01-01')
    ");
    $stmt->execute([$userId, $userId]);
    $result = $stmt->fetch();
    
    echo json_encode(['new_replies' => (int)$result['new_replies']]);
    
} catch (Exception $e) {
    echo json_encode(['new_replies' => 0]);
}
?>
