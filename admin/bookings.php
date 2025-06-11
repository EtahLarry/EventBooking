<?php
require_once 'includes/admin-functions.php';

// Require admin authentication
requireAdminLogin();

$pageTitle = 'Manage Bookings';

// Get filter parameters
$status_filter = $_GET['status'] ?? 'all';
$date_filter = $_GET['date'] ?? '';
$search = $_GET['search'] ?? '';

// Get all bookings with filters
try {
    $pdo = getDBConnection();
    
    $sql = "
        SELECT b.*, e.name as event_name, e.date as event_date, e.time as event_time, 
               e.venue, e.location, u.username, u.email as user_email, u.first_name, u.last_name
        FROM bookings b 
        JOIN events e ON b.event_id = e.id 
        JOIN users u ON b.user_id = u.id 
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($status_filter !== 'all') {
        $sql .= " AND b.status = ?";
        $params[] = $status_filter;
    }
    
    if (!empty($date_filter)) {
        $sql .= " AND DATE(e.date) = ?";
        $params[] = $date_filter;
    }
    
    if (!empty($search)) {
        $sql .= " AND (e.name LIKE ? OR u.username LIKE ? OR b.booking_reference LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY b.booking_date DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll();
    
    // Get statistics
    $stats_sql = "
        SELECT
            COALESCE(COUNT(*), 0) as total_bookings,
            COALESCE(SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END), 0) as confirmed_bookings,
            COALESCE(SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END), 0) as cancelled_bookings,
            COALESCE(SUM(CASE WHEN status = 'confirmed' THEN total_amount ELSE 0 END), 0) as total_revenue
        FROM bookings
    ";
    $stats = $pdo->query($stats_sql)->fetch();

    // Ensure all stats are integers/floats, not null
    $stats = [
        'total_bookings' => (int)($stats['total_bookings'] ?? 0),
        'confirmed_bookings' => (int)($stats['confirmed_bookings'] ?? 0),
        'cancelled_bookings' => (int)($stats['cancelled_bookings'] ?? 0),
        'total_revenue' => (float)($stats['total_revenue'] ?? 0)
    ];
    
} catch (Exception $e) {
    $bookings = [];
    $stats = [
        'total_bookings' => 0,
        'confirmed_bookings' => 0,
        'cancelled_bookings' => 0,
        'total_revenue' => 0.0
    ];
    $error = "Error fetching bookings: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .admin-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            border-left: 4px solid #667eea;
        }
        
        .stats-card h3 {
            color: #667eea;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .booking-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #28a745;
        }
        
        .booking-card.cancelled {
            border-left-color: #dc3545;
        }
        
        .booking-card.pending {
            border-left-color: #ffc107;
        }
        
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .badge-status {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
        }

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
                        <a class="nav-link active" href="bookings.php">
                            <i class="fas fa-ticket-alt me-2"></i>Bookings
                        </a>
                        <a class="nav-link" href="users.php">
                            <i class="fas fa-users me-2"></i>Users
                        </a>
                        <a class="nav-link" href="reports.php">
                            <i class="fas fa-chart-bar me-2"></i>Reports
                        </a>
                        <a class="nav-link" href="messages.php">
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
                <div class="p-4">
                    <!-- Admin Header -->
                    <div class="admin-header mb-4">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h1 class="mb-1">
                                                <i class="fas fa-ticket-alt me-2"></i>Booking Management
                                            </h1>
                                            <p class="mb-0">Monitor and manage all event bookings</p>
                                        </div>
                                        <div>
                                            <a href="dashboard.php" class="btn btn-light">
                                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <h3><?php echo number_format($stats['total_bookings']); ?></h3>
                    <p class="mb-0 text-muted">Total Bookings</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <h3><?php echo number_format($stats['confirmed_bookings']); ?></h3>
                    <p class="mb-0 text-muted">Confirmed Bookings</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <h3><?php echo number_format($stats['cancelled_bookings']); ?></h3>
                    <p class="mb-0 text-muted">Cancelled Bookings</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <h3><?php echo formatPrice($stats['total_revenue']); ?></h3>
                    <p class="mb-0 text-muted">Total Revenue</p>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All Status</option>
                        <option value="confirmed" <?php echo $status_filter === 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="cancelled" <?php echo $status_filter === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        <option value="pending" <?php echo $status_filter === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date" class="form-label">Event Date</label>
                    <input type="date" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date_filter); ?>">
                </div>
                <div class="col-md-4">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Event name, username, or booking reference" 
                           value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bookings List -->
        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
        </div>
        <?php endif; ?>

        <?php if (empty($bookings)): ?>
        <div class="text-center py-5">
            <i class="fas fa-ticket-alt fa-5x text-muted mb-3"></i>
            <h3>No Bookings Found</h3>
            <p class="text-muted">No bookings match your current filters.</p>
        </div>
        <?php else: ?>
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3">
                    Bookings (<?php echo count($bookings); ?> results)
                </h4>
            </div>
        </div>

        <?php foreach ($bookings as $booking): ?>
        <div class="booking-card <?php echo $booking['status']; ?>">
            <div class="row">
                <div class="col-md-8">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="mb-1"><?php echo htmlspecialchars($booking['event_name']); ?></h5>
                        <span class="badge bg-<?php echo $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'cancelled' ? 'danger' : 'warning'); ?> badge-status">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1">
                                <i class="fas fa-user text-primary me-2"></i>
                                <strong><?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?></strong>
                                (<?php echo htmlspecialchars($booking['username']); ?>)
                            </p>
                            <p class="mb-1">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <?php echo htmlspecialchars($booking['user_email']); ?>
                            </p>
                            <p class="mb-1">
                                <i class="fas fa-hashtag text-primary me-2"></i>
                                Ref: <strong><?php echo $booking['booking_reference']; ?></strong>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1">
                                <i class="fas fa-calendar text-primary me-2"></i>
                                <?php echo formatDate($booking['event_date']) . ' at ' . formatTime($booking['event_time']); ?>
                            </p>
                            <p class="mb-1">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <?php echo htmlspecialchars($booking['venue'] . ', ' . $booking['location']); ?>
                            </p>
                            <p class="mb-1">
                                <i class="fas fa-ticket-alt text-primary me-2"></i>
                                <?php echo $booking['quantity']; ?> ticket(s) - 
                                <strong><?php echo formatPrice($booking['total_amount']); ?></strong>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <p class="mb-2">
                        <small class="text-muted">
                            Booked on <?php echo date('M j, Y g:i A', strtotime($booking['booking_date'])); ?>
                        </small>
                    </p>
                    
                    <div class="btn-group-vertical d-grid gap-1">
                        <a href="../user/download-ticket.php?id=<?php echo $booking['id']; ?>" 
                           class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fas fa-download me-1"></i>View Ticket
                        </a>
                        
                        <?php if ($booking['status'] === 'confirmed'): ?>
                        <button class="btn btn-sm btn-outline-danger" 
                                onclick="cancelBooking(<?php echo $booking['id']; ?>)">
                            <i class="fas fa-times me-1"></i>Cancel Booking
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Booking Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancel Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this booking?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> This action will mark the booking as cancelled and may trigger a refund process.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Booking</button>
                    <button type="button" class="btn btn-danger" id="confirmCancel">Cancel Booking</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let bookingToCancel = null;

        function cancelBooking(bookingId) {
            bookingToCancel = bookingId;
            const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
            modal.show();
        }

        document.getElementById('confirmCancel').addEventListener('click', function() {
            if (bookingToCancel) {
                // In a real implementation, this would make an AJAX call to cancel the booking
                alert('Booking cancellation functionality would be implemented here.');
                
                // For demo purposes, reload the page
                location.reload();
            }
        });
    </script>
</body>
</html>
