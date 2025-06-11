<?php
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

try {
    $pdo = getDBConnection();
    $userId = $_SESSION['user_id'];
    
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    if ($action === 'mark_all_read') {
        // Mark all notifications as read for this user
        $stmt = $pdo->prepare("
            UPDATE user_notifications 
            SET is_read = TRUE 
            WHERE user_id = ? AND is_read = FALSE
        ");
        $stmt->execute([$userId]);
        
        $affectedRows = $stmt->rowCount();
        
        echo json_encode([
            'success' => true,
            'message' => "Marked $affectedRows notifications as read"
        ]);
        
    } elseif ($action === 'mark_single_read') {
        $notificationId = $input['notification_id'] ?? '';
        
        if (!$notificationId) {
            echo json_encode(['success' => false, 'message' => 'Notification ID required']);
            exit();
        }
        
        // Mark single notification as read (ensure user owns it)
        $stmt = $pdo->prepare("
            UPDATE user_notifications 
            SET is_read = TRUE 
            WHERE id = ? AND user_id = ? AND is_read = FALSE
        ");
        $stmt->execute([$notificationId, $userId]);
        
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Notification not found or already read']);
        }
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error updating notifications']);
}
?>
