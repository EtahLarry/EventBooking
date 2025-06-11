<?php
require_once 'includes/functions.php';

// Redirect to login page if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$pageTitle = 'Home';

// Get featured events (latest 6 events)
$featuredEvents = getAllEvents();
$featuredEvents = array_slice($featuredEvents, 0, 6);
?>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h1 class="display-4 fw-bold">Discover Amazing Events</h1>
                <p class="lead">Find and book tickets for concerts, conferences, workshops, and more!</p>
                <a href="events.php" class="btn btn-light btn-lg me-3">
                    <i class="fas fa-calendar-alt me-2"></i>Browse Events
                </a>
                <?php if (!isLoggedIn()): ?>
                <a href="register.php" class="btn btn-outline-light btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Join Now
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <div class="search-form">
            <h3 class="text-center mb-4">Find Your Perfect Event</h3>
            <form id="search-form" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Search Events</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Event name or keyword">
                </div>
                <div class="col-md-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" placeholder="City or venue">
                </div>
                <div class="col-md-3">
                    <label for="date" class="form-label">Date</label>
                    <input type="date" class="form-control" id="date" name="date">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Featured Events Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-5">Featured Events</h2>
            </div>
        </div>
        
        <?php if (empty($featuredEvents)): ?>
        <div class="row">
            <div class="col-12 text-center">
                <div class="alert alert-info">
                    <h4>No Events Available</h4>
                    <p>There are currently no events available. Please check back later!</p>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="row">
            <?php foreach ($featuredEvents as $event): ?>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card event-card h-100">
                    <img src="images/<?php echo $event['image'] ?: 'default-event.jpg'; ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($event['name']); ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h5>
                        <div class="event-date mb-2">
                            <i class="fas fa-calendar me-2"></i>
                            <?php echo formatDate($event['date']) . ' at ' . formatTime($event['time']); ?>
                        </div>
                        <div class="event-location mb-3">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <?php echo htmlspecialchars($event['venue'] . ', ' . $event['location']); ?>
                        </div>
                        <p class="card-text flex-grow-1">
                            <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <span class="event-price"><?php echo formatPrice($event['price']); ?></span>
                            <div>
                                <a href="event-details.php?id=<?php echo $event['id']; ?>" 
                                   class="btn btn-outline-primary btn-sm me-2">View Details</a>
                                <?php if (isLoggedIn()): ?>
                                <button class="btn btn-primary btn-sm add-to-cart" 
                                        data-event-id="<?php echo $event['id']; ?>">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                                <?php else: ?>
                                <a href="login.php" class="btn btn-primary btn-sm">Login to Book</a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <?php echo $event['available_tickets']; ?> tickets available
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="row">
            <div class="col-12 text-center mt-4">
                <a href="events.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-eye me-2"></i>View All Events
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="text-center mb-5">Why Choose EventBooking?</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-search fa-3x text-primary"></i>
                    </div>
                    <h4>Easy Discovery</h4>
                    <p>Find events that match your interests with our powerful search and filter options.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-shield-alt fa-3x text-primary"></i>
                    </div>
                    <h4>Secure Booking</h4>
                    <p>Book with confidence using our secure payment system and instant confirmation.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="text-center">
                    <div class="feature-icon mb-3">
                        <i class="fas fa-mobile-alt fa-3x text-primary"></i>
                    </div>
                    <h4>Mobile Friendly</h4>
                    <p>Access your tickets anywhere with our mobile-optimized platform and QR codes.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <h3>1000+</h3>
                    <p>Events Hosted</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <h3>50K+</h3>
                    <p>Happy Customers</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <h3>100+</h3>
                    <p>Cities Covered</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-card">
                    <h3>24/7</h3>
                    <p>Customer Support</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
