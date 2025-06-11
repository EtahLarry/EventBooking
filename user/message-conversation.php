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
    
    // Get message details - ensure user owns this message
    $stmt = $pdo->prepare("
        SELECT m.* 
        FROM messages m 
        WHERE m.id = ? AND (m.user_id = ? OR m.email = (SELECT email FROM users WHERE id = ?))
    ");
    $stmt->execute([$messageId, $userId, $userId]);
    $message = $stmt->fetch();
    
    if (!$message) {
        http_response_code(404);
        exit('Message not found or access denied');
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
    
} catch (Exception $e) {
    http_response_code(500);
    exit('Error loading message: ' . $e->getMessage());
}
?>

<div class="message-conversation">
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
                <div class="col-md-8">
                    <h6><i class="fas fa-tag"></i> Subject:</h6>
                    <p><strong><?php echo htmlspecialchars($message['subject']); ?></strong></p>
                    
                    <h6><i class="fas fa-info-circle"></i> Type:</h6>
                    <p><span class="badge bg-secondary"><?php echo ucfirst($message['inquiry_type']); ?></span></p>
                </div>
                <div class="col-md-4">
                    <h6><i class="fas fa-clock"></i> Timeline:</h6>
                    <p>
                        <strong>Created:</strong> <?php echo formatDate($message['created_at']); ?><br>
                        <strong>Last Updated:</strong> <?php echo formatDate($message['updated_at']); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Original Message -->
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0"><i class="fas fa-comment"></i> Your Original Message</h6>
        </div>
        <div class="card-body">
            <div class="message-content">
                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
            </div>
            <div class="mt-3 text-muted">
                <small>
                    <i class="fas fa-user"></i> Sent by: <?php echo htmlspecialchars($message['name']); ?> 
                    (<i class="fas fa-envelope"></i> <?php echo htmlspecialchars($message['email']); ?>)
                    <?php if ($message['phone']): ?>
                    | <i class="fas fa-phone"></i> <?php echo htmlspecialchars($message['phone']); ?>
                    <?php endif; ?>
                </small>
            </div>
        </div>
    </div>
    
    <!-- Conversation -->
    <?php if (!empty($replies)): ?>
    <div class="card mb-3">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-comments"></i> Conversation (<?php echo count($replies); ?> replies)</h6>
        </div>
        <div class="card-body">
            <div class="conversation-timeline">
                <?php foreach ($replies as $index => $reply): ?>
                <div class="reply-item mb-4 position-relative">
                    <!-- Timeline connector -->
                    <?php if ($index < count($replies) - 1): ?>
                    <div class="timeline-connector"></div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-1 text-center">
                            <div class="timeline-icon <?php echo $reply['is_admin_reply'] ? 'bg-success' : 'bg-primary'; ?>">
                                <i class="fas <?php echo $reply['is_admin_reply'] ? 'fa-user-shield' : 'fa-user'; ?>"></i>
                            </div>
                        </div>
                        <div class="col-11">
                            <div class="reply-card <?php echo $reply['is_admin_reply'] ? 'admin-reply' : 'user-reply'; ?>">
                                <div class="reply-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <?php if ($reply['is_admin_reply']): ?>
                                            <strong class="text-success">
                                                <i class="fas fa-user-shield"></i> 
                                                <?php echo htmlspecialchars($reply['admin_name'] ?? 'Support Team'); ?>
                                            </strong>
                                            <span class="badge bg-success ms-2">Admin Reply</span>
                                            <?php else: ?>
                                            <strong class="text-primary">
                                                <i class="fas fa-user"></i> 
                                                <?php echo htmlspecialchars($message['name']); ?>
                                            </strong>
                                            <span class="badge bg-primary ms-2">Your Reply</span>
                                            <?php endif; ?>
                                        </div>
                                        <small class="text-muted">
                                            <?php echo formatDate($reply['created_at']); ?> at <?php echo formatTime($reply['created_at']); ?>
                                        </small>
                                    </div>
                                </div>
                                <div class="reply-content mt-2">
                                    <?php echo nl2br(htmlspecialchars($reply['reply_text'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Reply Form (only if message is not closed) -->
    <?php if ($message['status'] !== 'closed'): ?>
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0"><i class="fas fa-reply"></i> Send a Reply</h6>
        </div>
        <div class="card-body">
            <form id="replyForm" method="POST" action="send-reply.php">
                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                <div class="mb-3">
                    <label for="reply_text" class="form-label">Your Reply</label>
                    <textarea class="form-control" name="reply_text" id="reply_text" rows="4" 
                              placeholder="Type your reply here..." required></textarea>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> 
                        Our support team will be notified and will respond as soon as possible.
                    </small>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> 
        This conversation has been closed. If you need further assistance, please send a new message.
    </div>
    <?php endif; ?>
</div>

<style>
.message-content {
    line-height: 1.6;
    font-size: 1rem;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.conversation-timeline {
    position: relative;
}

.timeline-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    position: relative;
    z-index: 2;
}

.timeline-connector {
    position: absolute;
    left: 20px;
    top: 40px;
    bottom: -20px;
    width: 2px;
    background: #dee2e6;
    z-index: 1;
}

.reply-card {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    border-left: 4px solid #007bff;
    margin-left: 10px;
}

.admin-reply {
    background: #d4edda;
    border-left-color: #28a745;
}

.user-reply {
    background: #e7f3ff;
    border-left-color: #007bff;
}

.reply-header {
    border-bottom: 1px solid rgba(0,0,0,0.1);
    padding-bottom: 8px;
    margin-bottom: 8px;
}

.reply-content {
    line-height: 1.6;
}

.badge {
    font-size: 0.75em;
}

#replyForm {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border: 2px dashed #dee2e6;
}

#replyForm:focus-within {
    border-color: #007bff;
    background: white;
}
</style>

<script>
// Handle reply form submission
document.getElementById('replyForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    submitBtn.disabled = true;
    
    fetch('send-reply.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear form
            document.getElementById('reply_text').value = '';
            
            // Show success message
            const alert = document.createElement('div');
            alert.className = 'alert alert-success alert-dismissible fade show';
            alert.innerHTML = `
                <i class="fas fa-check-circle"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            this.parentNode.insertBefore(alert, this);
            
            // Reload conversation after 2 seconds
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            throw new Error(data.message || 'Failed to send reply');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger alert-dismissible fade show';
        alert.innerHTML = `
            <i class="fas fa-exclamation-triangle"></i> Error: ${error.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        this.parentNode.insertBefore(alert, this);
    })
    .finally(() => {
        // Restore button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});
</script>
