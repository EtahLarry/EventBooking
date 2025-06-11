<?php
require_once '../includes/functions.php';

// Require login
requireLogin();

$pageTitle = 'My Bookings';

// Get user bookings
$bookings = getUserBookings($_SESSION['user_id']);

// Filter bookings
$filter = $_GET['filter'] ?? 'all';
$filteredBookings = [];

foreach ($bookings as $booking) {
    $eventDate = strtotime($booking['date']);
    $today = strtotime('today');
    
    switch ($filter) {
        case 'upcoming':
            if ($eventDate >= $today && $booking['status'] === 'confirmed') {
                $filteredBookings[] = $booking;
            }
            break;
        case 'past':
            if ($eventDate < $today) {
                $filteredBookings[] = $booking;
            }
            break;
        case 'cancelled':
            if ($booking['status'] === 'cancelled') {
                $filteredBookings[] = $booking;
            }
            break;
        default:
            $filteredBookings[] = $booking;
            break;
    }
}
?>

<?php include '../includes/header.php'; ?>

<style>
/* Enhanced Booking History Styles */
.booking-hero {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)),
                url('https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
    color: white;
    padding: 60px 0;
    margin-bottom: 40px;
    position: relative;
    overflow: hidden;
}

.booking-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
    animation: heroGlow 4s ease-in-out infinite;
}

@keyframes heroGlow {
    0%, 100% { opacity: 0.5; }
    50% { opacity: 1; }
}

.booking-hero > * {
    position: relative;
    z-index: 1;
}

.booking-item {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    margin-bottom: 30px;
}

.booking-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.booking-status {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 2;
}

.booking-status .badge {
    font-size: 0.9rem;
    padding: 8px 16px;
    border-radius: 25px;
    font-weight: 600;
}

.booking-image {
    height: 200px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    position: relative;
    overflow: hidden;
}

.booking-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.booking-item:hover .booking-image img {
    transform: scale(1.05);
}

.booking-content {
    padding: 25px;
}

.booking-title {
    font-size: 1.4rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
}

.booking-meta {
    background: rgba(102, 126, 234, 0.1);
    border-radius: 15px;
    padding: 20px;
    margin-bottom: 20px;
}

.booking-meta-item {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
    font-size: 0.95rem;
}

.booking-meta-item:last-child {
    margin-bottom: 0;
}

.booking-meta-item i {
    color: #667eea;
    width: 20px;
    margin-right: 10px;
}

.booking-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 20px;
}

.btn-enhanced {
    border-radius: 25px;
    padding: 8px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    border: none;
}

.btn-enhanced::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-enhanced:hover::before {
    left: 100%;
}

.btn-enhanced:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
}

.qr-section {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
    border-radius: 15px;
    padding: 20px;
    text-align: center;
    margin-top: 20px;
    border: 2px dashed rgba(102, 126, 234, 0.2);
}

.qr-section img {
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    background: white;
    padding: 10px;
}

.filter-pills {
    background: white;
    border-radius: 20px;
    padding: 20px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.nav-pills .nav-link {
    border-radius: 25px;
    padding: 12px 24px;
    font-weight: 600;
    margin-right: 10px;
    margin-bottom: 10px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.nav-pills .nav-link:not(.active) {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
}

.nav-pills .nav-link.active {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
}

.empty-state {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border-radius: 20px;
    padding: 60px 40px;
    text-align: center;
}

.empty-state i {
    font-size: 4rem;
    color: rgba(102, 126, 234, 0.3);
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .booking-hero {
        padding: 40px 0;
    }

    .booking-content {
        padding: 20px;
    }

    .booking-actions {
        justify-content: center;
    }
}
</style>

<!-- Booking Hero Section -->
<div class="booking-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-5 fw-bold mb-3">
                    <i class="fas fa-ticket-alt me-3"></i>
                    My Booking History
                </h1>
                <p class="lead mb-0">
                    Manage your event bookings, download tickets, and track your event journey
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container my-4">
    <!-- Enhanced Filter Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1 text-primary">
                        <i class="fas fa-list me-2"></i>Your Bookings
                    </h2>
                    <p class="text-muted mb-0">
                        Total: <strong><?php echo count($bookings); ?></strong> bookings |
                        Showing: <strong><?php echo count($filteredBookings); ?></strong> results
                    </p>
                </div>
                <div>
                    <a href="../events.php" class="btn btn-primary btn-enhanced">
                        <i class="fas fa-plus me-2"></i>Book New Event
                    </a>
                </div>
            </div>

            <div class="filter-pills">
                <ul class="nav nav-pills mb-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $filter === 'all' ? 'active' : ''; ?>"
                           href="?filter=all">
                            <i class="fas fa-list me-2"></i>All Bookings
                            <span class="badge bg-light text-dark ms-1"><?php echo count($bookings); ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $filter === 'upcoming' ? 'active' : ''; ?>"
                           href="?filter=upcoming">
                            <i class="fas fa-calendar-plus me-2"></i>Upcoming
                            <span class="badge bg-light text-dark ms-1">
                                <?php
                                $upcomingCount = 0;
                                foreach ($bookings as $b) {
                                    if (strtotime($b['date']) >= strtotime('today') && $b['status'] === 'confirmed') {
                                        $upcomingCount++;
                                    }
                                }
                                echo $upcomingCount;
                                ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $filter === 'past' ? 'active' : ''; ?>"
                           href="?filter=past">
                            <i class="fas fa-history me-2"></i>Past Events
                            <span class="badge bg-light text-dark ms-1">
                                <?php
                                $pastCount = 0;
                                foreach ($bookings as $b) {
                                    if (strtotime($b['date']) < strtotime('today')) {
                                        $pastCount++;
                                    }
                                }
                                echo $pastCount;
                                ?>
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $filter === 'cancelled' ? 'active' : ''; ?>"
                           href="?filter=cancelled">
                            <i class="fas fa-times-circle me-2"></i>Cancelled
                            <span class="badge bg-light text-dark ms-1">
                                <?php
                                $cancelledCount = 0;
                                foreach ($bookings as $b) {
                                    if ($b['status'] === 'cancelled') {
                                        $cancelledCount++;
                                    }
                                }
                                echo $cancelledCount;
                                ?>
                            </span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Enhanced Bookings List -->
    <?php if (empty($filteredBookings)): ?>
    <div class="row">
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-ticket-alt"></i>
                <h3 class="mb-3">No Bookings Found</h3>
                <p class="text-muted mb-4">
                    <?php if ($filter === 'all'): ?>
                        You haven't made any bookings yet. Start exploring amazing events and create unforgettable memories!
                    <?php elseif ($filter === 'upcoming'): ?>
                        No upcoming events found. Book your next adventure today!
                    <?php elseif ($filter === 'past'): ?>
                        No past events found. Your event journey starts here!
                    <?php else: ?>
                        No cancelled bookings found. That's great news!
                    <?php endif; ?>
                </p>
                <a href="../events.php" class="btn btn-primary btn-enhanced btn-lg">
                    <i class="fas fa-calendar-alt me-2"></i>Discover Events
                </a>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="row">
        <?php
        // Define beautiful event images based on event type/name
        $eventImages = [
            'tech' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            'music' => 'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            'business' => 'https://images.unsplash.com/photo-1559136555-9303baea8ebd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            'art' => 'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            'food' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
            'default' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
        ];

        foreach ($filteredBookings as $booking):
            // Determine image based on event name/type
            $eventImage = $eventImages['default'];
            $eventName = strtolower($booking['name']);
            if (strpos($eventName, 'tech') !== false || strpos($eventName, 'conference') !== false) {
                $eventImage = $eventImages['tech'];
            } elseif (strpos($eventName, 'music') !== false || strpos($eventName, 'festival') !== false) {
                $eventImage = $eventImages['music'];
            } elseif (strpos($eventName, 'business') !== false || strpos($eventName, 'workshop') !== false) {
                $eventImage = $eventImages['business'];
            } elseif (strpos($eventName, 'art') !== false || strpos($eventName, 'exhibition') !== false) {
                $eventImage = $eventImages['art'];
            } elseif (strpos($eventName, 'food') !== false || strpos($eventName, 'wine') !== false) {
                $eventImage = $eventImages['food'];
            }

            $isUpcoming = strtotime($booking['date']) >= strtotime('today');
            $isPast = strtotime($booking['date']) < strtotime('today');
        ?>
        <div class="col-lg-6 mb-4">
            <div class="booking-item">
                <!-- Status Badge -->
                <div class="booking-status">
                    <span class="badge bg-<?php echo $booking['status'] === 'confirmed' ? 'success' : ($booking['status'] === 'cancelled' ? 'danger' : 'warning'); ?>">
                        <?php echo ucfirst($booking['status']); ?>
                    </span>
                    <?php if ($isUpcoming && $booking['status'] === 'confirmed'): ?>
                    <span class="badge bg-info ms-2">Upcoming</span>
                    <?php elseif ($isPast): ?>
                    <span class="badge bg-secondary ms-2">Past Event</span>
                    <?php endif; ?>
                </div>

                <!-- Event Image -->
                <div class="booking-image">
                    <img src="<?php echo $eventImage; ?>"
                         alt="<?php echo htmlspecialchars($booking['name']); ?>"
                         onerror="this.src='<?php echo $eventImages['default']; ?>'; this.onerror=null;">
                </div>

                <!-- Booking Content -->
                <div class="booking-content">
                    <h3 class="booking-title"><?php echo htmlspecialchars($booking['name']); ?></h3>

                    <!-- Event Meta Information -->
                    <div class="booking-meta">
                        <div class="booking-meta-item">
                            <i class="fas fa-calendar"></i>
                            <span><strong><?php echo formatDate($booking['date']) . ' at ' . formatTime($booking['time']); ?></strong></span>
                        </div>
                        <div class="booking-meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($booking['venue'] . ', ' . $booking['location']); ?></span>
                        </div>
                        <div class="booking-meta-item">
                            <i class="fas fa-ticket-alt"></i>
                            <span><?php echo $booking['quantity']; ?> ticket(s) • <?php echo formatPrice($booking['total_amount']); ?></span>
                        </div>
                        <div class="booking-meta-item">
                            <i class="fas fa-hashtag"></i>
                            <span>Booking Ref: <strong><?php echo $booking['booking_reference']; ?></strong></span>
                        </div>
                        <div class="booking-meta-item">
                            <i class="fas fa-clock"></i>
                            <span>Booked on <?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></span>
                        </div>
                    </div>

                    <!-- QR Code Section for Confirmed Bookings -->
                    <?php if ($booking['status'] === 'confirmed'): ?>
                    <div class="qr-section">
                        <h6 class="mb-3">
                            <i class="fas fa-qrcode me-2"></i>Entry QR Code
                        </h6>
                        <img src="<?php echo generateQRCode($booking['booking_reference']); ?>"
                             alt="QR Code" style="width: 120px; height: 120px;">
                        <p class="small text-muted mt-2 mb-0">
                            <i class="fas fa-mobile-alt me-1"></i>
                            Show this at the event entrance
                        </p>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="booking-actions">
                        <a href="../event-details.php?id=<?php echo $booking['event_id']; ?>"
                           class="btn btn-outline-primary btn-enhanced">
                            <i class="fas fa-eye me-1"></i>View Event
                        </a>

                        <?php if ($booking['status'] === 'confirmed'): ?>
                        <a href="download-ticket.php?id=<?php echo $booking['id']; ?>"
                           class="btn btn-success btn-enhanced" target="_blank">
                            <i class="fas fa-download me-1"></i>Download Ticket
                        </a>

                        <a href="download-ticket.php?id=<?php echo $booking['id']; ?>&print=1"
                           class="btn btn-info btn-enhanced" target="_blank">
                            <i class="fas fa-print me-1"></i>Print Ticket
                        </a>

                        <?php if ($isUpcoming): ?>
                        <button class="btn btn-warning btn-enhanced"
                                onclick="cancelBooking(<?php echo $booking['id']; ?>)">
                            <i class="fas fa-times me-1"></i>Cancel Booking
                        </button>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
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
                    <strong>Note:</strong> Cancellation may be subject to fees according to our refund policy.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Keep Booking</button>
                <button type="button" class="btn btn-danger" id="confirmCancel">Cancel Booking</button>
            </div>
        </div>
    </div>
</div>

<script>
let bookingToCancel = null;

function cancelBooking(bookingId) {
    bookingToCancel = bookingId;
    const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
    modal.show();
}

function downloadTicket(bookingId) {
    // Open ticket download page
    window.open('download-ticket.php?id=' + bookingId, '_blank');
}

$(document).ready(function() {
    $('#confirmCancel').on('click', function() {
        if (bookingToCancel) {
            // Simulate booking cancellation
            showLoading();
            
            // In a real implementation, this would make an AJAX call to cancel the booking
            setTimeout(() => {
                hideLoading();
                $('#cancelModal').modal('hide');
                showAlert('success', 'Booking cancelled successfully. Refund will be processed within 3-5 business days.');
                
                // Reload page to reflect changes
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }, 2000);
        }
    });
});
</script>

<?php include '../includes/footer.php'; ?>
