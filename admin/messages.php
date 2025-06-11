<?php
require_once 'includes/admin-functions.php';
require_once '../includes/email-functions.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Messages Management';

// Handle message actions
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $messageId = $_POST['message_id'] ?? '';
    
    try {
        $pdo = getDBConnection();
        
        if ($action === 'reply' && !empty($_POST['reply_text'])) {
            // Add reply to message
            $stmt = $pdo->prepare("
                INSERT INTO message_replies (message_id, admin_id, reply_text, is_admin_reply) 
                VALUES (?, ?, ?, TRUE)
            ");
            $stmt->execute([$messageId, $_SESSION['admin_id'], $_POST['reply_text']]);
            
            // Update message status
            $pdo->prepare("UPDATE messages SET status = 'replied', updated_at = NOW() WHERE id = ?")->execute([$messageId]);
            
            // Get message details for notification
            $msgStmt = $pdo->prepare("SELECT m.user_id, m.subject, m.name, m.email, u.email as user_email FROM messages m LEFT JOIN users u ON m.user_id = u.id WHERE m.id = ?");
            $msgStmt->execute([$messageId]);
            $msgData = $msgStmt->fetch();

            // Create notification for user if they have an account
            if ($msgData['user_id']) {
                $notificationStmt = $pdo->prepare("
                    INSERT INTO user_notifications (user_id, message_id, title, content, type)
                    VALUES (?, ?, ?, ?, 'message_reply')
                ");
                $notificationStmt->execute([
                    $msgData['user_id'],
                    $messageId,
                    'New Reply to Your Message',
                    'We have replied to your message: "' . substr($msgData['subject'], 0, 50) . '..."'
                ]);
            }

            // Send email notification
            $userEmail = $msgData['user_email'] ?? $msgData['email'];
            $userName = $msgData['name'];
            $messageSubject = $msgData['subject'];
            $adminReply = $_POST['reply_text'];

            if ($userEmail && $userName) {
                try {
                    sendAdminReplyNotification($userEmail, $userName, $messageId, $messageSubject, $adminReply);
                } catch (Exception $emailError) {
                    error_log("Email notification error: " . $emailError->getMessage());
                    // Don't fail the reply if email fails
                }
            }
            
            // Log admin activity
            logAdminActivity("Replied to message ID: $messageId");
            
            $success = 'Reply sent successfully!';
            
        } elseif ($action === 'update_status') {
            $newStatus = $_POST['status'] ?? '';
            $pdo->prepare("UPDATE messages SET status = ?, updated_at = NOW() WHERE id = ?")->execute([$newStatus, $messageId]);
            
            logAdminActivity("Updated message ID: $messageId to status: $newStatus");
            $success = 'Message status updated successfully!';
            
        } elseif ($action === 'update_priority') {
            $newPriority = $_POST['priority'] ?? '';
            $pdo->prepare("UPDATE messages SET priority = ?, updated_at = NOW() WHERE id = ?")->execute([$newPriority, $messageId]);
            
            logAdminActivity("Updated message ID: $messageId to priority: $newPriority");
            $success = 'Message priority updated successfully!';
        }
        
    } catch (Exception $e) {
        $error = 'Error processing request: ' . $e->getMessage();
    }
}

// Get filter parameters
$statusFilter = $_GET['status'] ?? '';
$priorityFilter = $_GET['priority'] ?? '';
$typeFilter = $_GET['type'] ?? '';
$searchQuery = $_GET['search'] ?? '';

// Build query with filters
$whereConditions = [];
$params = [];

if ($statusFilter) {
    $whereConditions[] = "m.status = ?";
    $params[] = $statusFilter;
}
if ($priorityFilter) {
    $whereConditions[] = "m.priority = ?";
    $params[] = $priorityFilter;
}
if ($typeFilter) {
    $whereConditions[] = "m.inquiry_type = ?";
    $params[] = $typeFilter;
}
if ($searchQuery) {
    $whereConditions[] = "(m.name LIKE ? OR m.email LIKE ? OR m.subject LIKE ? OR m.message LIKE ?)";
    $searchTerm = "%$searchQuery%";
    $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
}

$whereClause = $whereConditions ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

try {
    $pdo = getDBConnection();
    
    // Get messages with user information
    $stmt = $pdo->prepare("
        SELECT m.*, u.username, u.first_name, u.last_name,
               (SELECT COUNT(*) FROM message_replies mr WHERE mr.message_id = m.id) as reply_count
        FROM messages m 
        LEFT JOIN users u ON m.user_id = u.id 
        $whereClause
        ORDER BY 
            CASE m.priority 
                WHEN 'urgent' THEN 1 
                WHEN 'high' THEN 2 
                WHEN 'normal' THEN 3 
                WHEN 'low' THEN 4 
            END,
            m.created_at DESC
    ");
    $stmt->execute($params);
    $messages = $stmt->fetchAll();
    
    // Get statistics
    $statsStmt = $pdo->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as new_count,
            SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as read_count,
            SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as replied_count,
            SUM(CASE WHEN status = 'closed' THEN 1 ELSE 0 END) as closed_count,
            SUM(CASE WHEN priority = 'urgent' THEN 1 ELSE 0 END) as urgent_count,
            SUM(CASE WHEN priority = 'high' THEN 1 ELSE 0 END) as high_count
        FROM messages
    ");
    $stats = $statsStmt->fetch();
    
} catch (Exception $e) {
    $error = 'Error loading messages: ' . $e->getMessage();
    $messages = [];
    $stats = ['total' => 0, 'new_count' => 0, 'read_count' => 0, 'replied_count' => 0, 'closed_count' => 0, 'urgent_count' => 0, 'high_count' => 0];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages Management - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin: 2px 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        .sidebar .nav-link:hover {
            color: white;
            background: rgba(255,255,255,0.15);
            transform: translateX(5px);
        }
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .main-content {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        .admin-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dc3545, #6f42c1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar">
                    <div class="p-4 text-white">
                        <div class="d-flex align-items-center mb-3">
                            <div class="admin-avatar me-3">
                                <?php echo strtoupper(substr($_SESSION['admin_username'], 0, 2)); ?>
                            </div>
                            <div>
                                <h5 class="mb-0">Admin Panel</h5>
                                <small class="opacity-75">Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></small>
                            </div>
                        </div>
                        <div class="small opacity-75">
                            <i class="fas fa-shield-check me-1"></i>
                            Administrator Access
                        </div>
                    </div>
                    <nav class="nav flex-column">
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="events.php">
                            <i class="fas fa-calendar-alt me-2"></i>Events
                        </a>
                        <a class="nav-link" href="bookings.php">
                            <i class="fas fa-ticket-alt me-2"></i>Bookings
                        </a>
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                        <a class="nav-link active" href="messages.php">
                            <i class="fas fa-envelope me-2"></i>Messages
                        </a>
                        <hr class="text-white">
                        <a class="nav-link" href="../index.php" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>View Website
                        </a>
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><i class="fas fa-envelope"></i> Messages Management</h1>
                </div>

                <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-2">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $stats['total']; ?></h3>
                                <p>Total Messages</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $stats['new_count']; ?></h3>
                                <p>New</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $stats['read_count']; ?></h3>
                                <p>Read</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $stats['replied_count']; ?></h3>
                                <p>Replied</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $stats['urgent_count']; ?></h3>
                                <p>Urgent</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card bg-secondary text-white">
                            <div class="card-body text-center">
                                <h3><?php echo $stats['closed_count']; ?></h3>
                                <p>Closed</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="new" <?php echo $statusFilter === 'new' ? 'selected' : ''; ?>>New</option>
                                    <option value="read" <?php echo $statusFilter === 'read' ? 'selected' : ''; ?>>Read</option>
                                    <option value="replied" <?php echo $statusFilter === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                    <option value="closed" <?php echo $statusFilter === 'closed' ? 'selected' : ''; ?>>Closed</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="priority" class="form-select">
                                    <option value="">All Priority</option>
                                    <option value="urgent" <?php echo $priorityFilter === 'urgent' ? 'selected' : ''; ?>>Urgent</option>
                                    <option value="high" <?php echo $priorityFilter === 'high' ? 'selected' : ''; ?>>High</option>
                                    <option value="normal" <?php echo $priorityFilter === 'normal' ? 'selected' : ''; ?>>Normal</option>
                                    <option value="low" <?php echo $priorityFilter === 'low' ? 'selected' : ''; ?>>Low</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="type" class="form-select">
                                    <option value="">All Types</option>
                                    <option value="general" <?php echo $typeFilter === 'general' ? 'selected' : ''; ?>>General</option>
                                    <option value="support" <?php echo $typeFilter === 'support' ? 'selected' : ''; ?>>Support</option>
                                    <option value="booking" <?php echo $typeFilter === 'booking' ? 'selected' : ''; ?>>Booking</option>
                                    <option value="partnership" <?php echo $typeFilter === 'partnership' ? 'selected' : ''; ?>>Partnership</option>
                                    <option value="feedback" <?php echo $typeFilter === 'feedback' ? 'selected' : ''; ?>>Feedback</option>
                                    <option value="other" <?php echo $typeFilter === 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search messages..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Messages Table -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list"></i> Messages (<?php echo count($messages); ?>)</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($messages)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5>No messages found</h5>
                            <p class="text-muted">No messages match your current filters.</p>
                        </div>
                        <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>From</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Replies</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($messages as $message): ?>
                                    <tr class="<?php echo $message['status'] === 'new' ? 'table-warning' : ''; ?>">
                                        <td>
                                            <strong>#<?php echo $message['id']; ?></strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?php echo htmlspecialchars($message['name']); ?></strong>
                                                <?php if ($message['username']): ?>
                                                <br><small class="text-muted">@<?php echo htmlspecialchars($message['username']); ?></small>
                                                <?php endif; ?>
                                                <br><small><?php echo htmlspecialchars($message['email']); ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($message['subject']); ?></strong>
                                            <br><small class="text-muted"><?php echo htmlspecialchars(substr($message['message'], 0, 100)); ?>...</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo ucfirst($message['inquiry_type']); ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $statusColors = [
                                                'new' => 'warning',
                                                'read' => 'info',
                                                'replied' => 'success',
                                                'closed' => 'secondary'
                                            ];
                                            $statusColor = $statusColors[$message['status']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo $statusColor; ?>"><?php echo ucfirst($message['status']); ?></span>
                                        </td>
                                        <td>
                                            <?php
                                            $priorityColors = [
                                                'urgent' => 'danger',
                                                'high' => 'warning',
                                                'normal' => 'primary',
                                                'low' => 'secondary'
                                            ];
                                            $priorityColor = $priorityColors[$message['priority']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo $priorityColor; ?>"><?php echo ucfirst($message['priority']); ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $message['reply_count']; ?></span>
                                        </td>
                                        <td>
                                            <small>
                                                <?php echo formatDate($message['created_at']); ?><br>
                                                <?php echo formatTime($message['created_at']); ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-primary" onclick="viewMessage(<?php echo $message['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-success" onclick="replyToMessage(<?php echo $message['id']; ?>)">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $message['id']; ?>, 'read')">Mark as Read</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?php echo $message['id']; ?>, 'closed')">Close</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updatePriority(<?php echo $message['id']; ?>, 'urgent')">Set Urgent</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updatePriority(<?php echo $message['id']; ?>, 'high')">Set High</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updatePriority(<?php echo $message['id']; ?>, 'normal')">Set Normal</a></li>
                                                        <li><a class="dropdown-item" href="#" onclick="updatePriority(<?php echo $message['id']; ?>, 'low')">Set Low</a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reply to Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="reply">
                    <input type="hidden" name="message_id" id="replyMessageId">
                    <div class="mb-3">
                        <label for="reply_text" class="form-label">Your Reply</label>
                        <textarea class="form-control" name="reply_text" id="reply_text" rows="6" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// View message details
function viewMessage(messageId) {
    fetch(`message-details.php?id=${messageId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('messageContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('messageModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading message details');
        });
}

// Reply to message
function replyToMessage(messageId) {
    document.getElementById('replyMessageId').value = messageId;
    document.getElementById('reply_text').value = '';
    new bootstrap.Modal(document.getElementById('replyModal')).show();
}

// Update message status
function updateStatus(messageId, status) {
    if (confirm(`Are you sure you want to mark this message as ${status}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="update_status">
            <input type="hidden" name="message_id" value="${messageId}">
            <input type="hidden" name="status" value="${status}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Update message priority
function updatePriority(messageId, priority) {
    if (confirm(`Are you sure you want to set this message priority to ${priority}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
            <input type="hidden" name="action" value="update_priority">
            <input type="hidden" name="message_id" value="${messageId}">
            <input type="hidden" name="priority" value="${priority}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Auto-refresh for new messages
setInterval(function() {
    // Check for new messages every 30 seconds
    fetch('check-new-messages.php')
        .then(response => response.json())
        .then(data => {
            if (data.new_count > 0) {
                // Update the new messages count in the statistics
                const newCountElement = document.querySelector('.bg-warning h3');
                if (newCountElement) {
                    newCountElement.textContent = data.new_count;
                }

                // Show notification
                if (data.new_count > parseInt(newCountElement.textContent)) {
                    showNotification('New message received!', 'info');
                }
            }
        })
        .catch(error => console.error('Error checking for new messages:', error));
}, 30000);

// Show notification
function showNotification(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
