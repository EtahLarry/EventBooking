<?php
require_once 'includes/functions.php';

// Require login
requireLogin();

$pageTitle = 'Shopping Cart';

// Get cart items
$cartItems = getCartItems($_SESSION['user_id']);
$cartTotal = getCartTotal($_SESSION['user_id']);
?>

<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
            </h1>
        </div>
    </div>

    <?php if (empty($cartItems)): ?>
    <!-- Empty Cart -->
    <div class="row">
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-5x text-muted mb-4"></i>
                <h3>Your cart is empty</h3>
                <p class="text-muted mb-4">Looks like you haven't added any events to your cart yet.</p>
                <a href="events.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-calendar-alt me-2"></i>Browse Events
                </a>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Cart Items -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Cart Items (<?php echo count($cartItems); ?>)
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item border-bottom p-3" data-event-id="<?php echo $item['event_id']; ?>">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <img src="images/<?php echo $item['image'] ?: 'default-event.jpg'; ?>" 
                                     class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['name']); ?>">
                            </div>
                            <div class="col-md-4">
                                <h6 class="mb-1"><?php echo htmlspecialchars($item['name']); ?></h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?php echo formatDate($item['date']) . ' at ' . formatTime($item['time']); ?>
                                </small><br>
                                <small class="text-muted">
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    <?php echo htmlspecialchars($item['venue']); ?>
                                </small>
                            </div>
                            <div class="col-md-2">
                                <div class="text-center">
                                    <strong><?php echo formatPrice($item['price']); ?></strong><br>
                                    <small class="text-muted">per ticket</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="quantity-controls">
                                    <button class="btn btn-sm btn-outline-secondary quantity-update" 
                                            data-event-id="<?php echo $item['event_id']; ?>" 
                                            data-action="decrease">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" class="form-control form-control-sm text-center quantity-input" 
                                           value="<?php echo $item['quantity']; ?>" 
                                           min="1" max="10" readonly>
                                    <button class="btn btn-sm btn-outline-secondary quantity-update" 
                                            data-event-id="<?php echo $item['event_id']; ?>" 
                                            data-action="increase">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="text-center">
                                    <strong class="item-total">
                                        <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                    </strong>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <button class="btn btn-sm btn-outline-danger remove-from-cart" 
                                        data-event-id="<?php echo $item['event_id']; ?>"
                                        title="Remove from cart">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Continue Shopping -->
            <div class="mt-3">
                <a href="events.php" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                </a>
            </div>
        </div>

        <!-- Cart Summary -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>Order Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span>Subtotal:</span>
                        <strong class="cart-total"><?php echo formatPrice($cartTotal); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Service Fee:</span>
                        <span>$0.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span>Tax:</span>
                        <span>$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-4">
                        <strong>Total:</strong>
                        <strong class="cart-total text-primary h5"><?php echo formatPrice($cartTotal); ?></strong>
                    </div>

                    <a href="checkout.php" class="btn btn-success w-100 btn-lg mb-3">
                        <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                    </a>

                    <!-- Security Info -->
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="fas fa-shield-alt me-1"></i>
                            Secure checkout with SSL encryption
                        </small>
                    </div>
                </div>
            </div>

            <!-- Promo Code -->
            <div class="card mt-3">
                <div class="card-body">
                    <h6 class="card-title">
                        <i class="fas fa-tag me-2"></i>Promo Code
                    </h6>
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Enter promo code" id="promo-code">
                        <button class="btn btn-outline-primary" type="button" id="apply-promo">
                            Apply
                        </button>
                    </div>
                    <small class="text-muted">Enter a valid promo code to get discount</small>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    // Update item total when quantity changes
    function updateItemTotal(eventId, quantity, price) {
        const itemTotal = quantity * price;
        $(`.cart-item[data-event-id="${eventId}"] .item-total`).text('$' + itemTotal.toFixed(2));
    }

    // Quantity update handlers
    $('.quantity-update').on('click', function() {
        const eventId = $(this).data('event-id');
        const action = $(this).data('action');
        const quantityInput = $(this).closest('.quantity-controls').find('.quantity-input');
        let currentQuantity = parseInt(quantityInput.val());
        const cartItem = $(this).closest('.cart-item');
        const priceText = cartItem.find('.text-center strong').first().text();
        const price = parseFloat(priceText.replace('$', ''));
        
        if (action === 'increase' && currentQuantity < 10) {
            currentQuantity++;
        } else if (action === 'decrease' && currentQuantity > 1) {
            currentQuantity--;
        } else {
            return; // No change needed
        }
        
        quantityInput.val(currentQuantity);
        updateItemTotal(eventId, currentQuantity, price);
        updateCartItem(eventId, currentQuantity);
    });

    // Promo code application
    $('#apply-promo').on('click', function() {
        const promoCode = $('#promo-code').val().trim();
        
        if (!promoCode) {
            showAlert('warning', 'Please enter a promo code.');
            return;
        }
        
        // Simulate promo code validation
        if (promoCode.toLowerCase() === 'welcome10') {
            showAlert('success', 'Promo code applied! 10% discount added.');
        } else {
            showAlert('danger', 'Invalid promo code. Please try again.');
        }
    });

    // Clear cart confirmation
    $('#clear-cart').on('click', function(e) {
        e.preventDefault();
        
        if (confirm('Are you sure you want to clear your entire cart?')) {
            // Implementation for clearing entire cart
            showLoading();
            
            $.ajax({
                url: 'api/cart.php',
                method: 'POST',
                data: { action: 'clear' },
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    if (response.success) {
                        location.reload();
                    } else {
                        showAlert('danger', response.message || 'Failed to clear cart');
                    }
                },
                error: function() {
                    hideLoading();
                    showAlert('danger', 'An error occurred. Please try again.');
                }
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
