<?php
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$messageId = $_POST['message_id'] ?? '';
$replyText = trim($_POST['reply_text'] ?? '');

// Validation
if (empty($messageId) || empty($replyText)) {
    echo json_encode(['success' => false, 'message' => 'Message ID and reply text are required']);
    exit();
}

try {
    $pdo = getDBConnection();
    $userId = $_SESSION['user_id'];
    
    // Verify user owns this message and it's not closed
    $stmt = $pdo->prepare("
        SELECT m.* 
        FROM messages m 
        WHERE m.id = ? AND (m.user_id = ? OR m.email = (SELECT email FROM users WHERE id = ?)) AND m.status != 'closed'
    ");
    $stmt->execute([$messageId, $userId, $userId]);
    $message = $stmt->fetch();
    
    if (!$message) {
        echo json_encode(['success' => false, 'message' => 'Message not found, access denied, or conversation is closed']);
        exit();
    }
    
    // Insert user reply
    $replyStmt = $pdo->prepare("
        INSERT INTO message_replies (message_id, reply_text, is_admin_reply) 
        VALUES (?, ?, FALSE)
    ");
    $replyStmt->execute([$messageId, $replyText]);
    
    // Update message status and timestamp
    $updateStmt = $pdo->prepare("
        UPDATE messages 
        SET status = CASE 
            WHEN status = 'new' THEN 'read' 
            WHEN status = 'closed' THEN 'read'
            ELSE status 
        END,
        updated_at = NOW() 
        WHERE id = ?
    ");
    $updateStmt->execute([$messageId]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Your reply has been sent successfully! Our support team will respond soon.'
    ]);
    
} catch (Exception $e) {
    error_log("User reply error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error sending reply. Please try again.']);
}
?>
