<?php
require_once '../includes/functions.php';

// Require login
requireLogin();

$pageTitle = 'Dashboard';

// Get user info
$user = getCurrentUser();

// Get user statistics
$pdo = getDBConnection();

// Get total bookings
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM bookings WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$totalBookings = $stmt->fetch()['total'];

// Get upcoming events
$stmt = $pdo->prepare("
    SELECT COUNT(*) as total 
    FROM bookings b 
    JOIN events e ON b.event_id = e.id 
    WHERE b.user_id = ? AND e.date >= CURDATE() AND b.status = 'confirmed'
");
$stmt->execute([$_SESSION['user_id']]);
$upcomingEvents = $stmt->fetch()['total'];

// Get total spent
$stmt = $pdo->prepare("SELECT SUM(total_amount) as total FROM bookings WHERE user_id = ? AND payment_status = 'completed'");
$stmt->execute([$_SESSION['user_id']]);
$totalSpent = $stmt->fetch()['total'] ?? 0;

// Get recent bookings
$recentBookings = getUserBookings($_SESSION['user_id']);
$recentBookings = array_slice($recentBookings, 0, 5);

// Get cart items count
$cartItems = getCartItems($_SESSION['user_id']);
$cartCount = count($cartItems);
?>

<?php include '../includes/header.php'; ?>

<div class="container my-4">
    <!-- Welcome Section -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mb-1">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
                    <p class="text-muted mb-0">Manage your bookings and profile from your dashboard</p>
                </div>
                <div>
                    <a href="../events.php" class="btn btn-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Browse Events
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?php echo $totalBookings; ?></h3>
                            <p class="mb-0">Total Bookings</p>
                        </div>
                        <div>
                            <i class="fas fa-ticket-alt fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?php echo $upcomingEvents; ?></h3>
                            <p class="mb-0">Upcoming Events</p>
                        </div>
                        <div>
                            <i class="fas fa-calendar-check fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?php echo formatPrice($totalSpent); ?></h3>
                            <p class="mb-0">Total Spent</p>
                        </div>
                        <div>
                            <i class="fas fa-dollar-sign fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0"><?php echo $cartCount; ?></h3>
                            <p class="mb-0">Items in Cart</p>
                        </div>
                        <div>
                            <i class="fas fa-shopping-cart fa-2x opacity-75"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Bookings -->
        <div class="col-lg-8">
            <div class="dashboard-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Recent Bookings
                        </h5>
                        <a href="bookings.php" class="btn btn-sm btn-outline-light">View All</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (empty($recentBookings)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h5>No Bookings Yet</h5>
                        <p class="text-muted">You haven't made any bookings yet. Start exploring events!</p>
                        <a href="../events.php" class="btn btn-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Browse Events
                        </a>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Date</th>
                                    <th>Tickets</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentBookings as $booking): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="../images/<?php echo $booking['image'] ?: 'default-event.jpg'; ?>" 
                                                 class="rounded me-2" style="width: 40px; height: 40px; object-fit: cover;" 
                                                 alt="<?php echo htmlspecialchars($booking['name']); ?>">
                                            <div>
                                                <h6 class="mb-0"><?php echo htmlspecialchars($booking['name']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($booking['venue']); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <?php echo formatDate($booking['date']); ?><br>
                                            <small class="text-muted"><?php echo formatTime($booking['time']); ?></small>
                                        </div>
                                    </td>
                                    <td><?php echo $booking['quantity']; ?></td>
                                    <td><?php echo formatPrice($booking['total_amount']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $booking['status'] === 'confirmed' ? 'success' : 'warning'; ?>">
                                            <?php echo ucfirst($booking['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="booking-details.php?id=<?php echo $booking['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
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

        <!-- Quick Actions & Profile -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="dashboard-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="../events.php" class="btn btn-primary">
                            <i class="fas fa-calendar-alt me-2"></i>Browse Events
                        </a>
                        <a href="../cart.php" class="btn btn-outline-primary">
                            <i class="fas fa-shopping-cart me-2"></i>View Cart 
                            <?php if ($cartCount > 0): ?>
                            <span class="badge bg-warning text-dark"><?php echo $cartCount; ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="bookings.php" class="btn btn-outline-primary">
                            <i class="fas fa-ticket-alt me-2"></i>My Bookings
                        </a>
                        <a href="messages.php" class="btn btn-outline-success">
                            <i class="fas fa-envelope me-2"></i>My Messages
                        </a>
                        <a href="profile.php" class="btn btn-outline-primary">
                            <i class="fas fa-user me-2"></i>Edit Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Summary -->
            <div class="dashboard-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Profile Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 80px; height: 80px;">
                            <i class="fas fa-user fa-2x text-white"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h5 class="mb-1"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h5>
                        <p class="text-muted mb-2"><?php echo htmlspecialchars($user['email']); ?></p>
                        <small class="text-muted">
                            Member since <?php echo date('F Y', strtotime($user['created_at'])); ?>
                        </small>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <h6 class="mb-0"><?php echo $totalBookings; ?></h6>
                            <small class="text-muted">Bookings</small>
                        </div>
                        <div class="col-6">
                            <h6 class="mb-0"><?php echo formatPrice($totalSpent); ?></h6>
                            <small class="text-muted">Spent</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
