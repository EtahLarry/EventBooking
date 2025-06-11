<?php
require_once 'includes/functions.php';

// Require login
requireLogin();

$pageTitle = 'Checkout';

// Get cart items or single event
$cartItems = [];
$total = 0;

if (isset($_GET['event_id'])) {
    // Quick checkout for single event
    $eventId = $_GET['event_id'];
    $quantity = $_GET['quantity'] ?? 1;
    $event = getEventById($eventId);
    
    if ($event) {
        $cartItems[] = [
            'event_id' => $event['id'],
            'name' => $event['name'],
            'date' => $event['date'],
            'time' => $event['time'],
            'venue' => $event['venue'],
            'price' => $event['price'],
            'quantity' => $quantity,
            'image' => $event['image']
        ];
        $total = $event['price'] * $quantity;
    }
} else {
    // Regular checkout from cart
    $cartItems = getCartItems($_SESSION['user_id']);
    $total = getCartTotal($_SESSION['user_id']);
}

if (empty($cartItems)) {
    $_SESSION['error_message'] = 'No items to checkout.';
    header('Location: cart.php');
    exit();
}

// Get current user info
$user = getCurrentUser();

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $attendeeInfo = [
        'name' => sanitizeInput($_POST['attendee_name']),
        'email' => sanitizeInput($_POST['attendee_email']),
        'phone' => sanitizeInput($_POST['attendee_phone'])
    ];
    
    // Validation
    if (empty($attendeeInfo['name']) || empty($attendeeInfo['email'])) {
        $_SESSION['error_message'] = 'Please fill in all required fields.';
    } elseif (!filter_var($attendeeInfo['email'], FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = 'Please enter a valid email address.';
    } else {
        try {
            // Create bookings
            createBooking($_SESSION['user_id'], $cartItems, $attendeeInfo);
            $_SESSION['success_message'] = 'Booking confirmed! Check your email for tickets.';
            header('Location: user/bookings.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-credit-card me-2"></i>Checkout
            </h1>
        </div>
    </div>

    <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8">
            <form method="POST" id="checkout-form">
                <!-- Attendee Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Attendee Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="attendee_name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="attendee_name" name="attendee_name" 
                                           value="<?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>" 
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="attendee_email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="attendee_email" name="attendee_email" 
                                           value="<?php echo htmlspecialchars($user['email']); ?>" 
                                           required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="attendee_phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="attendee_phone" name="attendee_phone" 
                                           value="<?php echo htmlspecialchars($user['phone']); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Payment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Demo Mode:</strong> This is a simulation. No actual payment will be processed.
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="card_number" class="form-label">Card Number</label>
                                    <input type="text" class="form-control" id="card_number" 
                                           placeholder="1234 5678 9012 3456" maxlength="19">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="expiry" class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" id="expiry" 
                                           placeholder="MM/YY" maxlength="5">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="cvv" class="form-label">CVV</label>
                                    <input type="text" class="form-control" id="cvv" 
                                           placeholder="123" maxlength="4">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="card_name" class="form-label">Cardholder Name</label>
                            <input type="text" class="form-control" id="card_name" 
                                   placeholder="John Doe">
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a> 
                                and <a href="#" class="text-decoration-none">Refund Policy</a> *
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter">
                            <label class="form-check-label" for="newsletter">
                                Subscribe to our newsletter for event updates
                            </label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg w-100">
                    <i class="fas fa-lock me-2"></i>Complete Booking - <?php echo formatPrice($total); ?>
                </button>
            </form>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-receipt me-2"></i>Order Summary
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Items -->
                    <?php foreach ($cartItems as $item): ?>
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                        <img src="images/<?php echo $item['image'] ?: 'default-event.jpg'; ?>" 
                             class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                            <small class="text-muted">
                                <?php echo formatDate($item['date']) . ' at ' . formatTime($item['time']); ?>
                            </small><br>
                            <small class="text-muted">
                                Qty: <?php echo $item['quantity']; ?> × <?php echo formatPrice($item['price']); ?>
                            </small>
                        </div>
                        <div class="text-end">
                            <strong><?php echo formatPrice($item['price'] * $item['quantity']); ?></strong>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <!-- Totals -->
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span><?php echo formatPrice($total); ?></span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Service Fee:</span>
                        <span>$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tax:</span>
                        <span>$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="text-primary h5"><?php echo formatPrice($total); ?></strong>
                    </div>

                    <!-- Security Info -->
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Your payment information is secure and encrypted
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Format card number input
    $('#card_number').on('input', function() {
        let value = $(this).val().replace(/\s/g, '').replace(/[^0-9]/gi, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        $(this).val(formattedValue);
    });

    // Format expiry date input
    $('#expiry').on('input', function() {
        let value = $(this).val().replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        $(this).val(value);
    });

    // CVV input validation
    $('#cvv').on('input', function() {
        let value = $(this).val().replace(/[^0-9]/gi, '');
        $(this).val(value);
    });

    // Form submission
    $('#checkout-form').on('submit', function(e) {
        const terms = $('#terms').is(':checked');
        
        if (!terms) {
            e.preventDefault();
            showAlert('danger', 'Please accept the terms and conditions.');
            return false;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<span class="loading"></span> Processing...').prop('disabled', true);
        
        // Simulate processing delay
        setTimeout(() => {
            // Form will submit normally
        }, 1000);
    });
});
</script>

<?php include 'includes/footer.php'; ?>
