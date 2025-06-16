<?php
require_once 'includes/admin-functions.php';

// Require admin authentication
requireAdminLogin();

// Get statistics
$pdo = getDBConnection();

// Total events
$stmt = $pdo->query("SELECT COUNT(*) as total FROM events");
$totalEvents = $stmt->fetch()['total'];

// Total users
$stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $stmt->fetch()['total'];

// Total bookings
$stmt = $pdo->query("SELECT COUNT(*) as total FROM bookings");
$totalBookings = $stmt->fetch()['total'];

// Total revenue
$stmt = $pdo->query("SELECT SUM(total_amount) as total FROM bookings WHERE status = 'confirmed'");
$totalRevenue = $stmt->fetch()['total'] ?? 0;

// Recent bookings
$stmt = $pdo->query("
    SELECT b.*, e.name as event_name, u.username, u.email 
    FROM bookings b 
    JOIN events e ON b.event_id = e.id 
    JOIN users u ON b.user_id = u.id 
    ORDER BY b.created_at DESC 
    LIMIT 10
");
$recentBookings = $stmt->fetchAll();

// Popular events
$stmt = $pdo->query("
    SELECT e.name, COUNT(b.id) as booking_count, SUM(b.total_amount) as revenue
    FROM events e 
    LEFT JOIN bookings b ON e.id = b.event_id 
    GROUP BY e.id 
    ORDER BY booking_count DESC 
    LIMIT 5
");
$popularEvents = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Event Booking System</title>
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
        .stat-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255,255,255,0.1), transparent);
            pointer-events: none;
        }
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .main-content {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        .admin-header {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 2rem;
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
        .table {
            border-radius: 10px;
            overflow: hidden;
        }
        .badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-weight: 500;
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
        .quick-action-btn {
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .quick-action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
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
                        <a class="nav-link active" href="dashboard.php">
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
                <div class="main-content p-4">
                    <!-- Enhanced Header -->
                    <div class="admin-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="mb-1">
                                    <i class="fas fa-tachometer-alt text-primary me-2"></i>
                                    Dashboard Overview
                                </h1>
                                <p class="text-muted mb-0">
                                    Monitor your event booking system performance
                                </p>
                            </div>
                            <div class="text-end">
                                <div class="text-muted small">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?php echo date('F j, Y'); ?>
                                </div>
                                <div class="text-muted small">
                                    <i class="fas fa-clock me-1"></i>
                                    <?php echo date('g:i A'); ?>
                                </div>
                                <div class="mt-2">
                                    <span class="badge bg-success">
                                        <i class="fas fa-circle me-1" style="font-size: 8px;"></i>
                                        System Online
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card stat-card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h3 class="mb-0"><?php echo $totalEvents; ?></h3>
                                            <p class="mb-0">Total Events</p>
                                        </div>
                                        <i class="fas fa-calendar-alt fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card stat-card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h3 class="mb-0"><?php echo $totalUsers; ?></h3>
                                            <p class="mb-0">Registered Users</p>
                                        </div>
                                        <i class="fas fa-users fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card stat-card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h3 class="mb-0"><?php echo $totalBookings; ?></h3>
                                            <p class="mb-0">Total Bookings</p>
                                        </div>
                                        <i class="fas fa-ticket-alt fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card stat-card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h3 class="mb-0">$<?php echo number_format($totalRevenue, 2); ?></h3>
                                            <p class="mb-0">Total Revenue</p>
                                        </div>
                                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Recent Bookings -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-clock me-2"></i>Recent Bookings
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recentBookings)): ?>
                                    <p class="text-muted text-center py-3">No bookings yet.</p>
                                    <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Booking ID</th>
                                                    <th>Event</th>
                                                    <th>User</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                    <th>Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recentBookings as $booking): ?>
                                                <tr>
                                                    <td>#<?php echo $booking['booking_reference']; ?></td>
                                                    <td><?php echo htmlspecialchars($booking['event_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                                    <td>$<?php echo number_format($booking['total_amount'], 2); ?></td>
                                                    <td>
                                                        <span class="badge bg-<?php echo $booking['status'] === 'confirmed' ? 'success' : 'warning'; ?>">
                                                            <?php echo ucfirst($booking['status']); ?>
                                                        </span>
                                                    </td>
                                                    <td><?php echo date('M j, Y', strtotime($booking['created_at'])); ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Popular Events -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-star me-2"></i>Popular Events
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (empty($popularEvents)): ?>
                                    <p class="text-muted text-center py-3">No events yet.</p>
                                    <?php else: ?>
                                    <?php foreach ($popularEvents as $event): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($event['name']); ?></h6>
                                            <small class="text-muted">
                                                <?php echo $event['booking_count']; ?> bookings
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <strong>$<?php echo number_format($event['revenue'] ?? 0, 2); ?></strong>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="fas fa-bolt me-2"></i>Quick Actions
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="events.php?action=add" class="btn btn-primary quick-action-btn">
                                            <i class="fas fa-plus me-2"></i>Add New Event
                                        </a>
                                        <a href="bookings.php" class="btn btn-outline-primary quick-action-btn">
                                            <i class="fas fa-ticket-alt me-2"></i>View All Bookings
                                        </a>
                                        <a href="reports.php" class="btn btn-outline-success quick-action-btn">
                                            <i class="fas fa-chart-bar me-2"></i>Generate Report
                                        </a>
                                        <a href="users.php" class="btn btn-outline-info quick-action-btn">
                                            <i class="fas fa-users me-2"></i>Manage Users
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Real-time clock update
            function updateClock() {
                const now = new Date();
                const timeElement = document.querySelector('.admin-header .text-end .text-muted:last-of-type');
                if (timeElement) {
                    timeElement.innerHTML = '<i class="fas fa-clock me-1"></i>' +
                        now.toLocaleTimeString('en-US', {
                            hour: 'numeric',
                            minute: '2-digit',
                            hour12: true
                        });
                }
            }

            // Update clock every minute
            setInterval(updateClock, 60000);

            // Add hover effects to stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });

                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Add click animation to quick action buttons
            const quickActionBtns = document.querySelectorAll('.quick-action-btn');
            quickActionBtns.forEach(btn => {
                btn.addEventListener('click', function(e) {
                    // Create ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255,255,255,0.5);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        pointer-events: none;
                    `;

                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });

            // Add CSS for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);

            // Auto-refresh data every 5 minutes
            setTimeout(() => {
                if (confirm('Refresh dashboard data?')) {
                    window.location.reload();
                }
            }, 300000); // 5 minutes

            // Add loading states for navigation links
            const navLinks = document.querySelectorAll('.sidebar .nav-link');
            navLinks.forEach(link => {
                if (!link.href.includes('#') && !link.href.includes('logout')) {
                    link.addEventListener('click', function() {
                        const icon = this.querySelector('i');
                        if (icon && !icon.classList.contains('fa-spin')) {
                            icon.classList.add('fa-spin');
                            setTimeout(() => {
                                icon.classList.remove('fa-spin');
                            }, 2000);
                        }
                    });
                }
            });

            // Console welcome message
            console.log('%c🛡️ Admin Dashboard Loaded', 'color: #dc3545; font-size: 16px; font-weight: bold;');
            console.log('%cEvent Booking System - Administrator Panel', 'color: #6f42c1; font-size: 12px;');
        });
    </script>
</body>
</html>
