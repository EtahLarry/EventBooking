<?php
session_start();
require_once '../config/database.php';

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$pdo = getDBConnection();

// Get all users with booking statistics
$stmt = $pdo->query("
    SELECT u.*, 
           COUNT(b.id) as total_bookings,
           SUM(b.total_amount) as total_spent,
           MAX(b.booking_date) as last_booking
    FROM users u 
    LEFT JOIN bookings b ON u.id = b.user_id 
    GROUP BY u.id 
    ORDER BY u.created_at DESC
");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin: 2px 8px;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.2);
        }
        .main-content {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007bff, #6610f2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 16px;
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
                        <a class="nav-link active" href="users.php">
                            <i class="fas fa-users me-2"></i>Users
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
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1><i class="fas fa-users text-primary me-2"></i>Manage Users</h1>
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-primary">
                                <i class="fas fa-download me-2"></i>Export Users
                            </button>
                            <button class="btn btn-primary">
                                <i class="fas fa-user-plus me-2"></i>Add User
                            </button>
                        </div>
                    </div>

                    <!-- User Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3><?php echo count($users); ?></h3>
                                            <p class="mb-0">Total Users</p>
                                        </div>
                                        <i class="fas fa-users fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3><?php echo count(array_filter($users, fn($u) => $u['total_bookings'] > 0)); ?></h3>
                                            <p class="mb-0">Active Users</p>
                                        </div>
                                        <i class="fas fa-user-check fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3><?php echo count(array_filter($users, fn($u) => strtotime($u['created_at']) > strtotime('-30 days'))); ?></h3>
                                            <p class="mb-0">New This Month</p>
                                        </div>
                                        <i class="fas fa-user-plus fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h3>$<?php echo number_format(array_sum(array_column($users, 'total_spent')), 0); ?></h3>
                                            <p class="mb-0">Total Revenue</p>
                                        </div>
                                        <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Users Table -->
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Registered Users</h5>
                                <div class="d-flex gap-2">
                                    <input type="search" class="form-control form-control-sm" placeholder="Search users..." style="width: 200px;">
                                    <select class="form-select form-select-sm" style="width: 150px;">
                                        <option>All Users</option>
                                        <option>Active</option>
                                        <option>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>User</th>
                                            <th>Contact</th>
                                            <th>Joined</th>
                                            <th>Bookings</th>
                                            <th>Total Spent</th>
                                            <th>Last Activity</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-3">
                                                        <?php echo strtoupper(substr($user['first_name'] . $user['last_name'], 0, 2)); ?>
                                                    </div>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong>
                                                        <br><small class="text-muted">@<?php echo htmlspecialchars($user['username']); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($user['email']); ?>
                                                </div>
                                                <?php if ($user['phone']): ?>
                                                <div class="small text-muted">
                                                    <i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($user['phone']); ?>
                                                </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                                <br><small class="text-muted"><?php echo date('g:i A', strtotime($user['created_at'])); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo $user['total_bookings'] ?? 0; ?></span>
                                            </td>
                                            <td>
                                                <strong>$<?php echo number_format($user['total_spent'] ?? 0, 2); ?></strong>
                                            </td>
                                            <td>
                                                <?php if ($user['last_booking']): ?>
                                                    <?php echo date('M j, Y', strtotime($user['last_booking'])); ?>
                                                <?php else: ?>
                                                    <span class="text-muted">Never</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Active</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" title="View Profile">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-info" title="Send Message">
                                                        <i class="fas fa-envelope"></i>
                                                    </button>
                                                    <button class="btn btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" title="Suspend">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Search functionality
        document.querySelector('input[type="search"]').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
