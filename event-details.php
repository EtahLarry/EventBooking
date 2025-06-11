<?php
require_once 'includes/functions.php';

$eventId = $_GET['id'] ?? 0;
$event = getEventById($eventId);

if (!$event) {
    $_SESSION['error_message'] = 'Event not found.';
    header('Location: events.php');
    exit();
}

$pageTitle = $event['name'];
?>

<?php include 'includes/header.php'; ?>

<div class="container my-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="events.php">Events</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($event['name']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Event Image and Details -->
        <div class="col-lg-8">
            <div class="event-details">
                <img src="images/<?php echo $event['image'] ?: 'default-event.jpg'; ?>" 
                     class="img-fluid" alt="<?php echo htmlspecialchars($event['name']); ?>">
                
                <div class="content">
                    <h1 class="mb-3"><?php echo htmlspecialchars($event['name']); ?></h1>
                    
                    <!-- Event Meta Information -->
                    <div class="event-meta">
                        <div class="event-meta-item">
                            <i class="fas fa-calendar-alt"></i>
                            <span><?php echo formatDate($event['date']); ?></span>
                        </div>
                        <div class="event-meta-item">
                            <i class="fas fa-clock"></i>
                            <span><?php echo formatTime($event['time']); ?></span>
                        </div>
                        <div class="event-meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo htmlspecialchars($event['venue']); ?></span>
                        </div>
                        <div class="event-meta-item">
                            <i class="fas fa-map"></i>
                            <span><?php echo htmlspecialchars($event['location']); ?></span>
                        </div>
                        <div class="event-meta-item">
                            <i class="fas fa-user"></i>
                            <span><?php echo htmlspecialchars($event['organizer']); ?></span>
                        </div>
                        <?php if ($event['organizer_contact']): ?>
                        <div class="event-meta-item">
                            <i class="fas fa-envelope"></i>
                            <span><?php echo htmlspecialchars($event['organizer_contact']); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Event Description -->
                    <div class="mb-4">
                        <h3>About This Event</h3>
                        <p class="lead"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                    </div>

                    <!-- Interactive Map Section -->
                    <div class="mb-4">
                        <h3><i class="fas fa-map-marked-alt me-2"></i>Location</h3>
                        <div class="location-info mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-building me-2 text-primary"></i><?php echo htmlspecialchars($event['venue']); ?></h5>
                                    <p class="text-muted"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($event['location']); ?></p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <button class="btn btn-outline-primary btn-sm" onclick="getDirections()">
                                        <i class="fas fa-directions me-2"></i>Get Directions
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm ms-2" onclick="shareLocation()">
                                        <i class="fas fa-share me-2"></i>Share Location
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Interactive Map -->
                        <div id="event-map" style="height: 350px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.15); border: 3px solid #f8f9fa;"></div>

                        <!-- Map Fallback -->
                        <div id="event-map-fallback" style="display: none; height: 350px; border-radius: 15px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center;">
                            <i class="fas fa-map-marked-alt fa-4x mb-3"></i>
                            <h4><?php echo htmlspecialchars($event['venue']); ?></h4>
                            <p class="mb-3"><?php echo htmlspecialchars($event['location']); ?></p>
                            <a href="https://www.openstreetmap.org/search?query=<?php echo urlencode($event['venue'] . ' ' . $event['location']); ?>"
                               target="_blank" class="btn btn-light">
                                <i class="fas fa-external-link-alt me-2"></i>View on OpenStreetMap
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Sidebar -->
        <div class="col-lg-4">
            <div class="card sticky-top" style="top: 20px;">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-ticket-alt me-2"></i>Book Tickets
                    </h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h2 class="text-primary mb-0"><?php echo formatPrice($event['price']); ?></h2>
                        <small class="text-muted">per ticket</small>
                    </div>

                    <!-- Ticket Availability -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Available Tickets:</span>
                            <strong><?php echo $event['available_tickets']; ?></strong>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <?php 
                            $soldPercentage = (($event['total_tickets'] - $event['available_tickets']) / $event['total_tickets']) * 100;
                            ?>
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: <?php echo $soldPercentage; ?>%"></div>
                        </div>
                        <small class="text-muted">
                            <?php echo $event['total_tickets'] - $event['available_tickets']; ?> of <?php echo $event['total_tickets']; ?> tickets sold
                        </small>
                    </div>

                    <?php if ($event['available_tickets'] > 0): ?>
                        <?php if (isLoggedIn()): ?>
                        <!-- Quantity Selection -->
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity:</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary quantity-update" type="button" data-action="decrease">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" class="form-control text-center quantity-input" 
                                       id="quantity" value="1" min="1" max="<?php echo min(10, $event['available_tickets']); ?>">
                                <button class="btn btn-outline-secondary quantity-update" type="button" data-action="increase">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Total Price -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Total:</span>
                                <strong class="total-price"><?php echo formatPrice($event['price']); ?></strong>
                            </div>
                        </div>

                        <!-- Add to Cart Button -->
                        <button class="btn btn-primary w-100 mb-3 add-to-cart" 
                                data-event-id="<?php echo $event['id']; ?>">
                            <i class="fas fa-cart-plus me-2"></i>Add to Cart
                        </button>

                        <!-- Quick Book Button -->
                        <a href="checkout.php?event_id=<?php echo $event['id']; ?>" 
                           class="btn btn-success w-100">
                            <i class="fas fa-bolt me-2"></i>Book Now
                        </a>
                        <?php else: ?>
                        <div class="text-center">
                            <p class="mb-3">Please login to book tickets</p>
                            <a href="login.php" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </a>
                            <a href="register.php" class="btn btn-outline-primary w-100">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </a>
                        </div>
                        <?php endif; ?>
                    <?php else: ?>
                    <div class="text-center">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Sold Out!</strong><br>
                            This event is currently sold out.
                        </div>
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="fas fa-times me-2"></i>Unavailable
                        </button>
                    </div>
                    <?php endif; ?>

                    <!-- Event Info -->
                    <hr>
                    <div class="small text-muted">
                        <div class="mb-2">
                            <i class="fas fa-info-circle me-2"></i>
                            Instant confirmation
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-mobile-alt me-2"></i>
                            Mobile tickets available
                        </div>
                        <div>
                            <i class="fas fa-shield-alt me-2"></i>
                            Secure payment
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    const pricePerTicket = <?php echo $event['price']; ?>;
    const maxQuantity = <?php echo min(10, $event['available_tickets']); ?>;
    
    // Update total price when quantity changes
    function updateTotalPrice() {
        const quantity = parseInt($('#quantity').val());
        const total = quantity * pricePerTicket;
        $('.total-price').text('$' + total.toFixed(2));
    }
    
    // Quantity controls
    $('.quantity-update').on('click', function() {
        const action = $(this).data('action');
        const quantityInput = $('#quantity');
        let currentQuantity = parseInt(quantityInput.val());
        
        if (action === 'increase' && currentQuantity < maxQuantity) {
            currentQuantity++;
        } else if (action === 'decrease' && currentQuantity > 1) {
            currentQuantity--;
        }
        
        quantityInput.val(currentQuantity);
        updateTotalPrice();
    });
    
    // Manual quantity input
    $('#quantity').on('input', function() {
        let quantity = parseInt($(this).val());
        if (quantity < 1) quantity = 1;
        if (quantity > maxQuantity) quantity = maxQuantity;
        $(this).val(quantity);
        updateTotalPrice();
    });
});
</script>

<!-- Leaflet.js for Event Location Map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<!-- Event Map Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get event location data
    const eventVenue = "<?php echo addslashes($event['venue']); ?>";
    const eventLocation = "<?php echo addslashes($event['location']); ?>";
    const eventName = "<?php echo addslashes($event['name']); ?>";

    // Coordinates for Cameroon locations (you can expand this)
    const locationCoordinates = {
        'yaoundé': [3.8480, 11.5174],
        'douala': [4.0511, 9.7679],
        'bamenda': [5.9597, 10.1494],
        'bafoussam': [5.4737, 10.4176],
        'garoua': [9.3265, 13.3991],
        'maroua': [10.5913, 14.3153],
        'ngaoundéré': [7.3167, 13.5833],
        'bertoua': [4.5833, 13.6833],
        'buea': [4.1559, 9.2669],
        'limbe': [4.0186, 9.2056]
    };

    // Try to find coordinates based on location
    let coordinates = [3.8480, 11.5174]; // Default to Yaoundé
    const locationLower = eventLocation.toLowerCase();

    for (const [city, coords] of Object.entries(locationCoordinates)) {
        if (locationLower.includes(city)) {
            coordinates = coords;
            break;
        }
    }

    try {
        // Initialize the event map
        var eventMap = L.map('event-map', {
            center: coordinates,
            zoom: 15,
            zoomControl: true,
            scrollWheelZoom: true
        });

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(eventMap);

        // Custom event marker
        var eventIcon = L.divIcon({
            html: `
                <div style="
                    background: linear-gradient(135deg, #dc3545, #fd7e14);
                    color: white;
                    width: 60px;
                    height: 60px;
                    border-radius: 50% 50% 50% 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 24px;
                    box-shadow: 0 6px 15px rgba(0,0,0,0.4);
                    border: 4px solid white;
                    transform: rotate(-45deg);
                    animation: bounce 2s infinite;
                ">
                    <i class="fas fa-calendar-check" style="transform: rotate(45deg);"></i>
                </div>
            `,
            className: 'event-marker-icon',
            iconSize: [60, 60],
            iconAnchor: [30, 60],
            popupAnchor: [0, -60]
        });

        // Add event marker
        var eventMarker = L.marker(coordinates, {icon: eventIcon}).addTo(eventMap);

        // Enhanced event popup
        eventMarker.bindPopup(`
            <div style="
                text-align: center;
                padding: 20px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                min-width: 280px;
                max-width: 350px;
            ">
                <div style="
                    background: linear-gradient(135deg, #dc3545, #fd7e14);
                    color: white;
                    padding: 15px;
                    margin: -20px -20px 20px -20px;
                    border-radius: 12px 12px 0 0;
                ">
                    <h5 style="margin: 0; font-weight: bold;">
                        <i class="fas fa-calendar-check me-2"></i>${eventName}
                    </h5>
                </div>

                <div style="text-align: left; line-height: 1.8;">
                    <p style="margin-bottom: 12px; color: #333;">
                        <i class="fas fa-building me-2" style="color: #dc3545;"></i>
                        <strong>Venue:</strong><br>
                        ${eventVenue}
                    </p>

                    <p style="margin-bottom: 12px; color: #333;">
                        <i class="fas fa-map-marker-alt me-2" style="color: #dc3545;"></i>
                        <strong>Location:</strong><br>
                        ${eventLocation}
                    </p>

                    <p style="margin-bottom: 12px; color: #333;">
                        <i class="fas fa-calendar me-2" style="color: #dc3545;"></i>
                        <strong>Date:</strong> <?php echo formatDate($event['date']); ?>
                    </p>

                    <p style="margin-bottom: 20px; color: #333;">
                        <i class="fas fa-clock me-2" style="color: #dc3545;"></i>
                        <strong>Time:</strong> <?php echo formatTime($event['time']); ?>
                    </p>

                    <div style="text-align: center;">
                        <button onclick="getDirections()"
                                style="
                                    background: linear-gradient(135deg, #dc3545, #fd7e14);
                                    color: white;
                                    padding: 10px 20px;
                                    border: none;
                                    border-radius: 25px;
                                    font-size: 14px;
                                    cursor: pointer;
                                    margin-right: 10px;
                                ">
                            <i class="fas fa-directions me-1"></i>Get Directions
                        </button>
                        <button onclick="shareLocation()"
                                style="
                                    background: #6c757d;
                                    color: white;
                                    padding: 10px 20px;
                                    border: none;
                                    border-radius: 25px;
                                    font-size: 14px;
                                    cursor: pointer;
                                ">
                            <i class="fas fa-share me-1"></i>Share
                        </button>
                    </div>
                </div>
            </div>
        `, {
            maxWidth: 400,
            className: 'event-popup'
        }).openPopup();

        // Add a circle to highlight the event area
        L.circle(coordinates, {
            color: '#dc3545',
            fillColor: '#dc3545',
            fillOpacity: 0.2,
            radius: 500,
            weight: 3,
            opacity: 0.8
        }).addTo(eventMap);

        console.log('Event map loaded successfully');

    } catch (error) {
        console.error('Error loading event map:', error);
        // Show fallback if map fails to load
        document.getElementById('event-map').style.display = 'none';
        document.getElementById('event-map-fallback').style.display = 'block';
    }
});

// Get directions function
function getDirections() {
    const eventLocation = "<?php echo addslashes($event['venue'] . ', ' . $event['location']); ?>";
    const encodedLocation = encodeURIComponent(eventLocation);

    // Try to use user's current location for directions
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const userLat = position.coords.latitude;
            const userLng = position.coords.longitude;
            const directionsUrl = `https://www.openstreetmap.org/directions?from=${userLat},${userLng}&to=${encodedLocation}`;
            window.open(directionsUrl, '_blank');
        }, function(error) {
            // Fallback if geolocation fails
            const directionsUrl = `https://www.openstreetmap.org/search?query=${encodedLocation}`;
            window.open(directionsUrl, '_blank');
        });
    } else {
        // Fallback for browsers without geolocation
        const directionsUrl = `https://www.openstreetmap.org/search?query=${encodedLocation}`;
        window.open(directionsUrl, '_blank');
    }
}

// Share location function
function shareLocation() {
    const eventName = "<?php echo addslashes($event['name']); ?>";
    const eventVenue = "<?php echo addslashes($event['venue']); ?>";
    const eventLocation = "<?php echo addslashes($event['location']); ?>";
    const shareText = `📍 ${eventName} at ${eventVenue}, ${eventLocation}`;

    if (navigator.share) {
        navigator.share({
            title: eventName,
            text: shareText,
            url: window.location.href
        });
    } else {
        // Fallback for browsers without Web Share API
        if (navigator.clipboard) {
            navigator.clipboard.writeText(shareText + ' - ' + window.location.href);
            alert('Location copied to clipboard!');
        } else {
            // Final fallback
            const textArea = document.createElement('textarea');
            textArea.value = shareText + ' - ' + window.location.href;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('Location copied to clipboard!');
        }
    }
}
</script>

<!-- Event Map Styles -->
<style>
@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0) rotate(-45deg);
    }
    40% {
        transform: translateY(-10px) rotate(-45deg);
    }
    60% {
        transform: translateY(-5px) rotate(-45deg);
    }
}

.event-marker-icon {
    background: transparent;
    border: none;
}

.leaflet-popup-content-wrapper.event-popup {
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.leaflet-popup-tip {
    background: white;
}

#event-map {
    transition: all 0.3s ease;
}

#event-map:hover {
    border-color: #dc3545 !important;
}

.location-info {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    padding: 20px;
    border-radius: 12px;
    border-left: 4px solid #dc3545;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #event-map {
        height: 250px !important;
    }

    .leaflet-popup-content-wrapper {
        max-width: 280px;
    }

    .location-info .col-md-6:last-child {
        text-align: left !important;
        margin-top: 15px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
