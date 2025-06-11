<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check admin authentication
if (!isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit();
}

$pdo = getDBConnection();

// Handle actions
$action = $_GET['action'] ?? 'list';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_event'])) {
            // Add new event
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $date = $_POST['date'];
            $time = $_POST['time'];
            $venue = trim($_POST['venue']);
            $location = trim($_POST['location']);
            $price = floatval($_POST['price']);
            $capacity = intval($_POST['capacity']);
            $category = $_POST['category'] ?? 'general';

            // Validation
            if (empty($name) || empty($description) || empty($date) || empty($time) || empty($venue) || empty($location)) {
                throw new Exception('All fields are required');
            }

            if ($price < 0) {
                throw new Exception('Price cannot be negative');
            }

            if ($capacity < 1) {
                throw new Exception('Capacity must be at least 1');
            }

            // Handle image upload
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/events/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $imageExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($imageExtension, $allowedExtensions)) {
                    $imageName = 'event_' . time() . '_' . uniqid() . '.' . $imageExtension;
                    $imagePath = $uploadDir . $imageName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                        $imagePath = 'uploads/events/' . $imageName; // Store relative path
                    } else {
                        $imagePath = null;
                    }
                }
            }

            // Insert event into database
            $stmt = $pdo->prepare("
                INSERT INTO events (name, description, date, time, venue, location, price, capacity, available_tickets, image, category, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW())
            ");

            $stmt->execute([
                $name, $description, $date, $time, $venue, $location,
                $price, $capacity, $capacity, $imagePath, $category
            ]);

            $message = 'Event added successfully!';

        } elseif (isset($_POST['edit_event'])) {
            // Edit existing event
            $eventId = intval($_POST['event_id']);
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $date = $_POST['date'];
            $time = $_POST['time'];
            $venue = trim($_POST['venue']);
            $location = trim($_POST['location']);
            $price = floatval($_POST['price']);
            $capacity = intval($_POST['capacity']);
            $category = $_POST['category'] ?? 'general';

            // Validation
            if (empty($name) || empty($description) || empty($date) || empty($time) || empty($venue) || empty($location)) {
                throw new Exception('All fields are required');
            }

            // Handle image upload for edit
            $imagePath = $_POST['existing_image'] ?? null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../uploads/events/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $imageExtension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($imageExtension, $allowedExtensions)) {
                    $imageName = 'event_' . time() . '_' . uniqid() . '.' . $imageExtension;
                    $newImagePath = $uploadDir . $imageName;

                    if (move_uploaded_file($_FILES['image']['tmp_name'], $newImagePath)) {
                        // Delete old image if exists
                        if ($imagePath && file_exists('../' . $imagePath)) {
                            unlink('../' . $imagePath);
                        }
                        $imagePath = 'uploads/events/' . $imageName;
                    }
                }
            }

            // Update event in database
            $stmt = $pdo->prepare("
                UPDATE events
                SET name = ?, description = ?, date = ?, time = ?, venue = ?, location = ?,
                    price = ?, capacity = ?, image = ?, category = ?, updated_at = NOW()
                WHERE id = ?
            ");

            $stmt->execute([
                $name, $description, $date, $time, $venue, $location,
                $price, $capacity, $imagePath, $category, $eventId
            ]);

            $message = 'Event updated successfully!';

        } elseif (isset($_POST['delete_event'])) {
            // Delete event
            $eventId = intval($_POST['event_id']);

            // Get event image to delete
            $stmt = $pdo->prepare("SELECT image FROM events WHERE id = ?");
            $stmt->execute([$eventId]);
            $event = $stmt->fetch();

            // Delete event from database
            $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$eventId]);

            // Delete image file if exists
            if ($event && $event['image'] && file_exists('../' . $event['image'])) {
                unlink('../' . $event['image']);
            }

            $message = 'Event deleted successfully!';

        } elseif (isset($_POST['toggle_status'])) {
            // Toggle event status
            $eventId = intval($_POST['event_id']);
            $newStatus = $_POST['new_status'];

            $stmt = $pdo->prepare("UPDATE events SET status = ? WHERE id = ?");
            $stmt->execute([$newStatus, $eventId]);

            $message = 'Event status updated successfully!';
        }

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Get all events
$stmt = $pdo->query("
    SELECT e.*, 
           COUNT(b.id) as booking_count,
           SUM(b.total_amount) as revenue
    FROM events e 
    LEFT JOIN bookings b ON e.id = b.event_id 
    GROUP BY e.id 
    ORDER BY e.created_at DESC
");
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events - Admin Panel</title>
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
                        <a class="nav-link active" href="events.php">
                            <i class="fas fa-calendar-alt me-2"></i>Events
                        </a>
                        <a class="nav-link" href="bookings.php">
                            <i class="fas fa-ticket-alt me-2"></i>Bookings
                        </a>
                        <a class="nav-link" href="users.php">
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
                        <h1><i class="fas fa-calendar-alt text-primary me-2"></i>Manage Events</h1>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal">
                            <i class="fas fa-plus me-2"></i>Add New Event
                        </button>
                    </div>

                    <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Events Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Event Name</th>
                                            <th>Date</th>
                                            <th>Venue</th>
                                            <th>Price</th>
                                            <th>Tickets</th>
                                            <th>Bookings</th>
                                            <th>Revenue</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($events as $event): ?>
                                        <tr>
                                            <td><?php echo $event['id']; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($event['name']); ?></strong>
                                                <br><small class="text-muted"><?php echo htmlspecialchars(substr($event['description'], 0, 50)); ?>...</small>
                                            </td>
                                            <td>
                                                <?php echo date('M j, Y', strtotime($event['date'])); ?>
                                                <br><small class="text-muted"><?php echo date('g:i A', strtotime($event['time'])); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($event['venue']); ?></td>
                                            <td>$<?php echo number_format($event['price'], 2); ?></td>
                                            <td>
                                                <?php echo $event['available_tickets']; ?> / <?php echo $event['total_tickets']; ?>
                                                <br><small class="text-muted">
                                                    <?php echo round(($event['available_tickets'] / $event['total_tickets']) * 100); ?>% available
                                                </small>
                                            </td>
                                            <td><?php echo $event['booking_count'] ?? 0; ?></td>
                                            <td>$<?php echo number_format($event['revenue'] ?? 0, 2); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $event['status'] === 'active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo ucfirst($event['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
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

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus me-2"></i>Add New Event
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Event Name</label>
                                    <input type="text" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Venue</label>
                                    <input type="text" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Time</label>
                                    <input type="time" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Price</label>
                                    <input type="number" class="form-control" step="0.01" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Total Tickets</label>
                                    <input type="number" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Event
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
