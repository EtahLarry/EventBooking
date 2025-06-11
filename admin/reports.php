<?php
require_once 'includes/admin-functions.php';

// Require admin authentication
requireAdminLogin();

$pageTitle = 'Reports & Analytics';

// Get date range for reports
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-t'); // Last day of current month

try {
    $pdo = getDBConnection();
    
    // Overall Statistics
    $overall_stats = $pdo->query("
        SELECT 
            COUNT(*) as total_bookings,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
            SUM(CASE WHEN status = 'confirmed' THEN total_amount ELSE 0 END) as total_revenue,
            AVG(CASE WHEN status = 'confirmed' THEN total_amount ELSE NULL END) as avg_booking_value
        FROM bookings
    ")->fetch();
    
    // Date Range Statistics
    $stmt = $pdo->prepare("
        SELECT 
            COUNT(*) as period_bookings,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as period_confirmed,
            SUM(CASE WHEN status = 'confirmed' THEN total_amount ELSE 0 END) as period_revenue
        FROM bookings 
        WHERE DATE(booking_date) BETWEEN ? AND ?
    ");
    $stmt->execute([$start_date, $end_date]);
    $period_stats = $stmt->fetch();
    
    // Top Events by Bookings
    $top_events = $pdo->query("
        SELECT e.name, COUNT(b.id) as booking_count, SUM(b.total_amount) as revenue
        FROM events e
        LEFT JOIN bookings b ON e.id = b.event_id AND b.status = 'confirmed'
        GROUP BY e.id, e.name
        ORDER BY booking_count DESC
        LIMIT 5
    ")->fetchAll();
    
    // Monthly Revenue Trend (Last 6 months)
    $monthly_revenue = $pdo->query("
        SELECT 
            DATE_FORMAT(booking_date, '%Y-%m') as month,
            COUNT(*) as bookings,
            SUM(CASE WHEN status = 'confirmed' THEN total_amount ELSE 0 END) as revenue
        FROM bookings 
        WHERE booking_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(booking_date, '%Y-%m')
        ORDER BY month DESC
    ")->fetchAll();
    
    // User Registration Trend
    $user_registrations = $pdo->query("
        SELECT 
            DATE_FORMAT(created_at, '%Y-%m') as month,
            COUNT(*) as new_users
        FROM users 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(created_at, '%Y-%m')
        ORDER BY month DESC
    ")->fetchAll();
    
    // Event Performance
    $event_performance = $pdo->query("
        SELECT 
            e.name,
            e.total_tickets,
            e.available_tickets,
            (e.total_tickets - e.available_tickets) as sold_tickets,
            ROUND(((e.total_tickets - e.available_tickets) / e.total_tickets * 100), 2) as occupancy_rate,
            SUM(CASE WHEN b.status = 'confirmed' THEN b.total_amount ELSE 0 END) as revenue
        FROM events e
        LEFT JOIN bookings b ON e.id = b.event_id
        GROUP BY e.id, e.name, e.total_tickets, e.available_tickets
        ORDER BY occupancy_rate DESC
    ")->fetchAll();
    
} catch (Exception $e) {
    $error = "Error generating reports: " . $e->getMessage();
    $overall_stats = $period_stats = [];
    $top_events = $monthly_revenue = $user_registrations = $event_performance = [];
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-2px);
        }
        
        .stats-card h3 {
            color: #667eea;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .progress-custom {
            height: 8px;
            border-radius: 4px;
        }
        
        .metric-icon {
            font-size: 2.5rem;
            color: rgba(102, 126, 234, 0.3);
            position: absolute;
            right: 1rem;
            top: 1rem;
        }
        
        .stats-card {
            position: relative;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="mb-1">
                                <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
                            </h1>
                            <p class="mb-0">Comprehensive business insights and performance metrics</p>
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

    <div class="container">
        <!-- Date Range Filter -->
        <div class="filter-section">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="<?php echo htmlspecialchars($start_date); ?>">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Update Report
                    </button>
                </div>
            </form>
        </div>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
        </div>
        <?php endif; ?>

        <!-- Overall Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-3">Overall Performance</h3>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <i class="fas fa-ticket-alt metric-icon"></i>
                    <h3><?php echo number_format($overall_stats['total_bookings'] ?? 0); ?></h3>
                    <p class="mb-0 text-muted">Total Bookings</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <i class="fas fa-check-circle metric-icon"></i>
                    <h3><?php echo number_format($overall_stats['confirmed_bookings'] ?? 0); ?></h3>
                    <p class="mb-0 text-muted">Confirmed Bookings</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <i class="fas fa-dollar-sign metric-icon"></i>
                    <h3><?php echo formatPrice($overall_stats['total_revenue'] ?? 0); ?></h3>
                    <p class="mb-0 text-muted">Total Revenue</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <i class="fas fa-chart-line metric-icon"></i>
                    <h3><?php echo formatPrice($overall_stats['avg_booking_value'] ?? 0); ?></h3>
                    <p class="mb-0 text-muted">Avg. Booking Value</p>
                </div>
            </div>
        </div>

        <!-- Period Statistics -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="mb-3">Period Performance (<?php echo date('M j, Y', strtotime($start_date)) . ' - ' . date('M j, Y', strtotime($end_date)); ?>)</h3>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card">
                    <h3><?php echo number_format($period_stats['period_bookings'] ?? 0); ?></h3>
                    <p class="mb-0 text-muted">Period Bookings</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card">
                    <h3><?php echo number_format($period_stats['period_confirmed'] ?? 0); ?></h3>
                    <p class="mb-0 text-muted">Confirmed in Period</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card">
                    <h3><?php echo formatPrice($period_stats['period_revenue'] ?? 0); ?></h3>
                    <p class="mb-0 text-muted">Period Revenue</p>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Revenue Trend Chart -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h4 class="mb-3">
                        <i class="fas fa-chart-line me-2"></i>Monthly Revenue Trend
                    </h4>
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
            
            <!-- Event Performance Chart -->
            <div class="col-lg-6 mb-4">
                <div class="chart-container">
                    <h4 class="mb-3">
                        <i class="fas fa-chart-pie me-2"></i>Top Events by Bookings
                    </h4>
                    <canvas id="eventsChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="row">
            <!-- Top Events Table -->
            <div class="col-lg-6 mb-4">
                <div class="table-container">
                    <h4 class="mb-3">
                        <i class="fas fa-trophy me-2"></i>Top Performing Events
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Event Name</th>
                                    <th>Bookings</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_events as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($event['name']); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo $event['booking_count']; ?></span>
                                    </td>
                                    <td><?php echo formatPrice($event['revenue']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Event Occupancy Table -->
            <div class="col-lg-6 mb-4">
                <div class="table-container">
                    <h4 class="mb-3">
                        <i class="fas fa-percentage me-2"></i>Event Occupancy Rates
                    </h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Sold/Total</th>
                                    <th>Occupancy</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($event_performance as $event): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(substr($event['name'], 0, 20)) . (strlen($event['name']) > 20 ? '...' : ''); ?></td>
                                    <td><?php echo $event['sold_tickets'] . '/' . $event['total_tickets']; ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress progress-custom flex-grow-1 me-2">
                                                <div class="progress-bar bg-<?php echo $event['occupancy_rate'] > 80 ? 'success' : ($event['occupancy_rate'] > 50 ? 'warning' : 'danger'); ?>" 
                                                     style="width: <?php echo $event['occupancy_rate']; ?>%"></div>
                                            </div>
                                            <small><?php echo number_format($event['occupancy_rate'], 1); ?>%</small>
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

        <!-- Export Options -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="table-container">
                    <h4 class="mb-3">
                        <i class="fas fa-download me-2"></i>Export Reports
                    </h4>
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-outline-primary w-100" onclick="exportToPDF()">
                                <i class="fas fa-file-pdf me-2"></i>Export to PDF
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-outline-success w-100" onclick="exportToExcel()">
                                <i class="fas fa-file-excel me-2"></i>Export to Excel
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-outline-info w-100" onclick="printReport()">
                                <i class="fas fa-print me-2"></i>Print Report
                            </button>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button class="btn btn-outline-secondary w-100" onclick="emailReport()">
                                <i class="fas fa-envelope me-2"></i>Email Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Revenue Trend Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: [<?php echo "'" . implode("','", array_reverse(array_column($monthly_revenue, 'month'))) . "'"; ?>],
                datasets: [{
                    label: 'Revenue',
                    data: [<?php echo implode(',', array_reverse(array_column($monthly_revenue, 'revenue'))); ?>],
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Top Events Chart
        const eventsCtx = document.getElementById('eventsChart').getContext('2d');
        const eventsChart = new Chart(eventsCtx, {
            type: 'doughnut',
            data: {
                labels: [<?php echo "'" . implode("','", array_map('htmlspecialchars', array_column($top_events, 'name'))) . "'"; ?>],
                datasets: [{
                    data: [<?php echo implode(',', array_column($top_events, 'booking_count')); ?>],
                    backgroundColor: [
                        '#667eea',
                        '#764ba2',
                        '#f093fb',
                        '#f5576c',
                        '#4facfe'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Export Functions
        function exportToPDF() {
            alert('PDF export functionality would be implemented here.');
        }

        function exportToExcel() {
            alert('Excel export functionality would be implemented here.');
        }

        function printReport() {
            window.print();
        }

        function emailReport() {
            alert('Email report functionality would be implemented here.');
        }
    </script>
</body>
</html>
