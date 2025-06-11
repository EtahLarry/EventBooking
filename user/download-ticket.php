<?php
require_once '../includes/functions.php';
requireLogin();

// Get booking ID
$bookingId = $_GET['id'] ?? null;
if (!$bookingId) {
    header('Location: bookings.php');
    exit();
}

// Get booking details
$pdo = getDBConnection();
$stmt = $pdo->prepare("
    SELECT b.*, e.name, e.description, e.date, e.time, e.venue, e.location, e.organizer, e.organizer_contact
    FROM bookings b 
    JOIN events e ON b.event_id = e.id 
    WHERE b.id = ? AND b.user_id = ? AND b.status = 'confirmed'
");
$stmt->execute([$bookingId, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    $_SESSION['error_message'] = 'Booking not found or access denied.';
    header('Location: bookings.php');
    exit();
}

// Set headers for PDF download
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Ticket - <?php echo htmlspecialchars($booking['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
            .ticket-container { box-shadow: none !important; margin: 0 !important; }
        }
        
        .ticket-container {
            max-width: 800px;
            margin: 20px auto;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .ticket-header {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .ticket-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            opacity: 0.3;
        }
        
        .ticket-body {
            background: white;
            padding: 40px;
            position: relative;
        }
        
        .ticket-perforations {
            position: absolute;
            left: 0;
            right: 0;
            height: 20px;
            background: repeating-linear-gradient(
                90deg,
                transparent,
                transparent 10px,
                #667eea 10px,
                #667eea 20px
            );
            top: -10px;
        }
        
        .qr-section {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            margin-top: 30px;
        }
        
        .event-details {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        .detail-row:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: #667eea;
            display: flex;
            align-items: center;
        }
        
        .detail-value {
            font-weight: 500;
            text-align: right;
        }
        
        .ticket-footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 2px dashed #dee2e6;
        }
        
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(102, 126, 234, 0.05);
            font-weight: bold;
            z-index: 1;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Print/Download Controls -->
        <div class="no-print text-center py-3">
            <button onclick="window.print()" class="btn btn-primary me-2">
                <i class="fas fa-print me-2"></i>Print Ticket
            </button>
            <button onclick="downloadAsPDF()" class="btn btn-success me-2">
                <i class="fas fa-download me-2"></i>Download PDF
            </button>
            <a href="bookings.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Bookings
            </a>
        </div>

        <!-- Ticket -->
        <div class="ticket-container">
            <div class="watermark">VALID TICKET</div>
            
            <!-- Header -->
            <div class="ticket-header">
                <h1 class="mb-3">🎫 EVENT TICKET</h1>
                <h2 class="mb-2"><?php echo htmlspecialchars($booking['name']); ?></h2>
                <p class="mb-0 fs-5">Booking Reference: <strong><?php echo $booking['booking_reference']; ?></strong></p>
            </div>

            <!-- Body -->
            <div class="ticket-body">
                <div class="ticket-perforations"></div>
                
                <!-- Event Details -->
                <div class="event-details">
                    <h4 class="text-center mb-4 text-primary">
                        <i class="fas fa-calendar-star me-2"></i>Event Details
                    </h4>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-calendar me-2"></i>Date & Time
                        </div>
                        <div class="detail-value">
                            <?php echo formatDate($booking['date']) . ' at ' . formatTime($booking['time']); ?>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Venue
                        </div>
                        <div class="detail-value">
                            <?php echo htmlspecialchars($booking['venue']); ?>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-location-dot me-2"></i>Location
                        </div>
                        <div class="detail-value">
                            <?php echo htmlspecialchars($booking['location']); ?>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-user-tie me-2"></i>Organizer
                        </div>
                        <div class="detail-value">
                            <?php echo htmlspecialchars($booking['organizer']); ?>
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-ticket-alt me-2"></i>Tickets
                        </div>
                        <div class="detail-value">
                            <?php echo $booking['quantity']; ?> ticket(s)
                        </div>
                    </div>
                    
                    <div class="detail-row">
                        <div class="detail-label">
                            <i class="fas fa-dollar-sign me-2"></i>Total Amount
                        </div>
                        <div class="detail-value fs-5 text-success fw-bold">
                            <?php echo formatPrice($booking['total_amount']); ?>
                        </div>
                    </div>
                </div>

                <!-- Attendee Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-user me-2"></i>Attendee Information
                        </h5>
                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($booking['attendee_name']); ?></p>
                        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($booking['attendee_email']); ?></p>
                        <p class="mb-0"><strong>Phone:</strong> <?php echo htmlspecialchars($booking['attendee_phone'] ?: 'Not provided'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i>Booking Information
                        </h5>
                        <p class="mb-1"><strong>Booked on:</strong> <?php echo date('M j, Y g:i A', strtotime($booking['booking_date'])); ?></p>
                        <p class="mb-1"><strong>Status:</strong> <span class="badge bg-success">Confirmed</span></p>
                        <p class="mb-0"><strong>Payment:</strong> <span class="badge bg-info">Completed</span></p>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="qr-section">
                    <h5 class="text-primary mb-3">
                        <i class="fas fa-qrcode me-2"></i>Entry QR Code
                    </h5>
                    <img src="<?php echo generateQRCode($booking['booking_reference']); ?>" 
                         alt="QR Code" class="img-fluid mb-3" style="max-width: 200px;">
                    <p class="text-muted mb-0">
                        <i class="fas fa-mobile-alt me-2"></i>
                        Show this QR code at the event entrance for quick check-in
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="ticket-footer">
                <div class="row text-center">
                    <div class="col-md-4">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Secure Ticket
                        </small>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            Valid for Event Date Only
                        </small>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">
                            <i class="fas fa-envelope me-1"></i>
                            Support: nkumbelarry@gmail.com
                        </small>
                    </div>
                </div>
                <hr class="my-3">
                <p class="mb-0 text-muted">
                    <small>
                        This ticket is non-transferable and must be presented with valid ID. 
                        For support, contact us at nkumbelarry@gmail.com or 652731798.
                    </small>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script>
        function downloadAsPDF() {
            const element = document.querySelector('.ticket-container');
            const opt = {
                margin: 0.5,
                filename: 'ticket-<?php echo $booking['booking_reference']; ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
            };
            
            // Hide no-print elements
            document.querySelectorAll('.no-print').forEach(el => el.style.display = 'none');
            
            html2pdf().set(opt).from(element).save().then(() => {
                // Show no-print elements again
                document.querySelectorAll('.no-print').forEach(el => el.style.display = 'block');
            });
        }
        
        // Auto-print if print parameter is set
        if (new URLSearchParams(window.location.search).get('print') === '1') {
            setTimeout(() => window.print(), 1000);
        }
    </script>
</body>
</html>
