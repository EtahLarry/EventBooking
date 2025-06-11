<?php
require_once '../includes/functions.php';

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

try {
    $pdo = getDBConnection();
    $userId = $_SESSION['user_id'];
    
    // Get unread notifications count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as unread_count
        FROM user_notifications 
        WHERE user_id = ? AND is_read = FALSE
    ");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    
    // Get recent notifications
    $notificationStmt = $pdo->prepare("
        SELECT id, title, content, type, is_read, created_at
        FROM user_notifications 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $notificationStmt->execute([$userId]);
    $notifications = $notificationStmt->fetchAll();
    
    // Format notifications for display
    $formattedNotifications = [];
    foreach ($notifications as $notification) {
        $formattedNotifications[] = [
            'id' => $notification['id'],
            'title' => $notification['title'],
            'content' => $notification['content'],
            'type' => $notification['type'],
            'is_read' => (bool)$notification['is_read'],
            'created_at' => $notification['created_at'],
            'time_ago' => timeAgo($notification['created_at'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'unread_count' => (int)$result['unread_count'],
        'notifications' => $formattedNotifications
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error checking notifications']);
}

/**
 * Convert timestamp to "time ago" format
 */
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' minutes ago';
    if ($time < 86400) return floor($time/3600) . ' hours ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    return floor($time/31536000) . ' years ago';
}
?>
