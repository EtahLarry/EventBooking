<?php
require_once 'includes/admin-functions.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
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
    
    // Get message details
    $stmt = $pdo->prepare("
        SELECT m.*, u.username, u.first_name, u.last_name
        FROM messages m 
        LEFT JOIN users u ON m.user_id = u.id 
        WHERE m.id = ?
    ");
    $stmt->execute([$messageId]);
    $message = $stmt->fetch();
    
    if (!$message) {
        http_response_code(404);
        exit('Message not found');
    }
    
    // Get replies
    $repliesStmt = $pdo->prepare("
        SELECT mr.*, au.username as admin_username, au.full_name as admin_name
        FROM message_replies mr
        LEFT JOIN admin_users au ON mr.admin_id = au.id
        WHERE mr.message_id = ?
        ORDER BY mr.created_at ASC
    ");
    $repliesStmt->execute([$messageId]);
    $replies = $repliesStmt->fetchAll();
    
    // Mark message as read if it's new
    if ($message['status'] === 'new') {
        $pdo->prepare("UPDATE messages SET status = 'read', updated_at = NOW() WHERE id = ?")->execute([$messageId]);
        logAdminActivity("Viewed message ID: $messageId");
    }
    
} catch (Exception $e) {
    http_response_code(500);
    exit('Error loading message: ' . $e->getMessage());
}
?>

<div class="message-details">
    <!-- Message Header -->
    <div class="card mb-3">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="mb-0">
                        <strong>Message #<?php echo $message['id']; ?></strong>
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
                        
                        <?php
                        $priorityColors = [
                            'urgent' => 'danger',
                            'high' => 'warning',
                            'normal' => 'primary',
                            'low' => 'secondary'
                        ];
                        $priorityColor = $priorityColors[$message['priority']] ?? 'secondary';
                        ?>
                        <span class="badge bg-<?php echo $priorityColor; ?> ms-1"><?php echo ucfirst($message['priority']); ?></span>
                    </h6>
                </div>
                <div class="col-auto">
                    <small class="text-muted">
                        <?php echo formatDate($message['created_at']); ?> at <?php echo formatTime($message['created_at']); ?>
                    </small>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-user"></i> From:</h6>
                    <p>
                        <strong><?php echo htmlspecialchars($message['name']); ?></strong>
                        <?php if ($message['username']): ?>
                        <br><small class="text-muted">Username: @<?php echo htmlspecialchars($message['username']); ?></small>
                        <?php endif; ?>
                        <br><small><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($message['email']); ?></small>
                        <?php if ($message['phone']): ?>
                        <br><small><i class="fas fa-phone"></i> <?php echo htmlspecialchars($message['phone']); ?></small>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-tag"></i> Details:</h6>
                    <p>
                        <strong>Type:</strong> <span class="badge bg-secondary"><?php echo ucfirst($message['inquiry_type']); ?></span><br>
                        <strong>Subject:</strong> <?php echo htmlspecialchars($message['subject']); ?><br>
                        <strong>Created:</strong> <?php echo formatDate($message['created_at']); ?><br>
                        <strong>Updated:</strong> <?php echo formatDate($message['updated_at']); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Original Message -->
    <div class="card mb-3">
        <div class="card-header">
            <h6><i class="fas fa-comment"></i> Original Message</h6>
        </div>
        <div class="card-body">
            <div class="message-content">
                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
            </div>
        </div>
    </div>
    
    <!-- Replies -->
    <?php if (!empty($replies)): ?>
    <div class="card mb-3">
        <div class="card-header">
            <h6><i class="fas fa-comments"></i> Conversation (<?php echo count($replies); ?> replies)</h6>
        </div>
        <div class="card-body">
            <?php foreach ($replies as $reply): ?>
            <div class="reply-item mb-3 p-3 <?php echo $reply['is_admin_reply'] ? 'bg-light border-start border-primary border-3' : 'bg-info bg-opacity-10 border-start border-info border-3'; ?>">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <?php if ($reply['is_admin_reply']): ?>
                        <strong><i class="fas fa-user-shield text-primary"></i> <?php echo htmlspecialchars($reply['admin_name'] ?? 'Admin'); ?></strong>
                        <span class="badge bg-primary ms-2">Admin Reply</span>
                        <?php else: ?>
                        <strong><i class="fas fa-user text-info"></i> <?php echo htmlspecialchars($message['name']); ?></strong>
                        <span class="badge bg-info ms-2">Customer Reply</span>
                        <?php endif; ?>
                    </div>
                    <small class="text-muted">
                        <?php echo formatDate($reply['created_at']); ?> at <?php echo formatTime($reply['created_at']); ?>
                    </small>
                </div>
                <div class="reply-content">
                    <?php echo nl2br(htmlspecialchars($reply['reply_text'])); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h6><i class="fas fa-tools"></i> Quick Actions</h6>
        </div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-md-3">
                    <button type="button" class="btn btn-success w-100" onclick="replyToMessage(<?php echo $message['id']; ?>)">
                        <i class="fas fa-reply"></i> Reply
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-info w-100" onclick="updateStatus(<?php echo $message['id']; ?>, 'read')">
                        <i class="fas fa-eye"></i> Mark as Read
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-warning w-100" onclick="updatePriority(<?php echo $message['id']; ?>, 'high')">
                        <i class="fas fa-exclamation-triangle"></i> Set High Priority
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-secondary w-100" onclick="updateStatus(<?php echo $message['id']; ?>, 'closed')">
                        <i class="fas fa-times"></i> Close
                    </button>
                </div>
            </div>
            
            <?php if ($message['email']): ?>
            <div class="mt-3">
                <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>?subject=Re: <?php echo htmlspecialchars($message['subject']); ?>" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-external-link-alt"></i> Reply via Email
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.message-content {
    line-height: 1.6;
    font-size: 1rem;
}

.reply-item {
    border-radius: 8px;
}

.reply-content {
    line-height: 1.6;
    margin-top: 8px;
}

.card-header h6 {
    margin-bottom: 0;
    font-weight: 600;
}

.badge {
    font-size: 0.75em;
}

.border-3 {
    border-width: 3px !important;
}

.bg-opacity-10 {
    --bs-bg-opacity: 0.1;
}
</style>
