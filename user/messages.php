<?php
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit();
}

$pageTitle = 'My Messages';

try {
    $pdo = getDBConnection();
    $userId = $_SESSION['user_id'];
    
    // Get user's messages with reply counts
    $stmt = $pdo->prepare("
        SELECT m.*, 
               (SELECT COUNT(*) FROM message_replies mr WHERE mr.message_id = m.id) as reply_count,
               (SELECT COUNT(*) FROM message_replies mr WHERE mr.message_id = m.id AND mr.is_admin_reply = TRUE AND mr.created_at > COALESCE(m.last_read_at, '1970-01-01')) as unread_replies
        FROM messages m 
        WHERE m.user_id = ? OR m.email = (SELECT email FROM users WHERE id = ?)
        ORDER BY m.updated_at DESC
    ");
    $stmt->execute([$userId, $userId]);
    $messages = $stmt->fetchAll();
    
    // Get user's notifications
    $notificationStmt = $pdo->prepare("
        SELECT * FROM user_notifications 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT 10
    ");
    $notificationStmt->execute([$userId]);
    $notifications = $notificationStmt->fetchAll();
    
    // Mark notifications as read
    $pdo->prepare("UPDATE user_notifications SET is_read = TRUE WHERE user_id = ?")->execute([$userId]);
    
} catch (Exception $e) {
    $error = 'Error loading messages: ' . $e->getMessage();
    $messages = [];
    $notifications = [];
}

include '../includes/header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1><i class="fas fa-envelope"></i> My Messages</h1>
                <a href="../contact.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Send New Message
                </a>
            </div>

            <?php if (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
            </div>
            <?php endif; ?>

            <!-- Notifications -->
            <?php if (!empty($notifications)): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-bell"></i> Recent Notifications</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item p-3 mb-2 <?php echo $notification['is_read'] ? 'bg-light' : 'bg-info bg-opacity-10'; ?> rounded">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                <p class="mb-1 text-muted"><?php echo htmlspecialchars($notification['content']); ?></p>
                            </div>
                            <small class="text-muted">
                                <?php echo formatDate($notification['created_at']); ?>
                            </small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Messages -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-comments"></i> My Conversations (<?php echo count($messages); ?>)</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($messages)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5>No messages yet</h5>
                        <p class="text-muted">You haven't sent any messages yet.</p>
                        <a href="../contact.php" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Send Your First Message
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="row">
                        <?php foreach ($messages as $message): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 message-card <?php echo $message['unread_replies'] > 0 ? 'border-primary' : ''; ?>">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>#<?php echo $message['id']; ?></strong>
                                        <?php
                                        $statusColors = [
                                            'new' => 'warning',
                                            'read' => 'info',
                                            'replied' => 'success',
                                            'closed' => 'secondary'
                                        ];
                                        $statusColor = $statusColors[$message['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?php echo $statusColor; ?> ms-2"><?php echo ucfirst($message['status']); ?></span>
                                        <?php if ($message['unread_replies'] > 0): ?>
                                        <span class="badge bg-danger ms-1"><?php echo $message['unread_replies']; ?> new</span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">
                                        <?php echo formatDate($message['created_at']); ?>
                                    </small>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title"><?php echo htmlspecialchars($message['subject']); ?></h6>
                                    <p class="card-text text-muted">
                                        <?php echo htmlspecialchars(substr($message['message'], 0, 150)); ?>...
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="badge bg-secondary"><?php echo ucfirst($message['inquiry_type']); ?></span>
                                            <?php if ($message['reply_count'] > 0): ?>
                                            <span class="badge bg-info ms-1">
                                                <i class="fas fa-comments"></i> <?php echo $message['reply_count']; ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                        <button class="btn btn-primary btn-sm" onclick="viewMessage(<?php echo $message['id']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Message View Modal -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Message Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="messageContent">
                <!-- Message content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<style>
.message-card {
    transition: all 0.3s ease;
    border-radius: 10px;
}

.message-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.notification-item {
    border-left: 4px solid #007bff;
    transition: all 0.3s ease;
}

.notification-item:hover {
    background-color: rgba(0,123,255,0.1) !important;
}

.badge {
    font-size: 0.75em;
}

.card-header {
    background: linear-gradient(135deg, rgba(0,123,255,0.1), rgba(108,117,125,0.1));
    border-bottom: 1px solid rgba(0,0,0,0.125);
}

.border-primary {
    border-color: #007bff !important;
    border-width: 2px !important;
}
</style>

<script>
// View message details
function viewMessage(messageId) {
    fetch(`message-conversation.php?id=${messageId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('messageContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('messageModal')).show();
            
            // Mark message as read
            fetch(`mark-message-read.php?id=${messageId}`, {method: 'POST'});
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading message details');
        });
}

// Auto-refresh for new replies
setInterval(function() {
    // Check for new replies every 30 seconds
    fetch('check-new-replies.php')
        .then(response => response.json())
        .then(data => {
            if (data.new_replies > 0) {
                // Reload page to show new replies
                location.reload();
            }
        })
        .catch(error => console.error('Error checking for new replies:', error));
}, 30000);
</script>

<?php include '../includes/footer.php'; ?>
