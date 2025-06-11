<?php
require_once 'includes/functions.php';

// Redirect to login page if not logged in
if (!isLoggedIn()) {
    header('Location: login.php?redirect=events.php');
    exit();
}

$pageTitle = 'Events';

// Get search parameters
$search = $_GET['search'] ?? '';
$location = $_GET['location'] ?? '';
$date = $_GET['date'] ?? '';

// Get events based on search criteria
$events = getAllEvents($search, $location, $date);

// Pagination
$page = $_GET['page'] ?? 1;
$eventsPerPage = 12;
$totalEvents = count($events);
$totalPages = ceil($totalEvents / $eventsPerPage);
$offset = ($page - 1) * $eventsPerPage;
$paginatedEvents = array_slice($events, $offset, $eventsPerPage);
?>

<?php include 'includes/header.php'; ?>

<style>
/* Enhanced Events Page Styles */
.events-hero {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9)),
                url('https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
    color: white;
    padding: 80px 0;
    margin-bottom: 40px;
    position: relative;
    overflow: hidden;
}

.events-hero::before {
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

.events-hero > * {
    position: relative;
    z-index: 1;
}

.search-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
}

.search-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
}

.event-card {
    border: none;
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    background: white;
    position: relative;
}

.event-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.event-card .card-img-top {
    height: 250px;
    object-fit: cover;
    transition: all 0.3s ease;
    position: relative;
}

.event-card:hover .card-img-top {
    transform: scale(1.05);
}

.event-card .card-body {
    padding: 25px;
    position: relative;
}

.event-price {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 8px 16px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 1.1rem;
    display: inline-block;
}

.event-meta {
    background: rgba(102, 126, 234, 0.1);
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
}

.event-meta i {
    color: #667eea;
    width: 20px;
}

.btn-enhanced {
    border-radius: 25px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
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

.progress-enhanced {
    height: 8px;
    border-radius: 10px;
    background: rgba(102, 126, 234, 0.1);
    overflow: hidden;
}

.progress-enhanced .progress-bar {
    background: linear-gradient(90deg, #667eea, #764ba2);
    border-radius: 10px;
    transition: width 0.3s ease;
}

.filter-chip {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 20px;
    padding: 5px 15px;
    font-size: 0.9rem;
    margin: 2px;
    display: inline-block;
}

.sort-select {
    border-radius: 15px;
    border: 2px solid rgba(102, 126, 234, 0.2);
    padding: 10px 15px;
    background: white;
    transition: all 0.3s ease;
}

.sort-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.pagination-enhanced .page-link {
    border-radius: 10px;
    margin: 0 5px;
    border: none;
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    transition: all 0.3s ease;
}

.pagination-enhanced .page-link:hover {
    background: #667eea;
    color: white;
    transform: translateY(-2px);
}

.pagination-enhanced .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.event-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255, 255, 255, 0.9);
    color: #667eea;
    padding: 5px 12px;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    backdrop-filter: blur(10px);
}

.no-events-illustration {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border-radius: 20px;
    padding: 60px 40px;
    text-align: center;
}

@media (max-width: 768px) {
    .events-hero {
        padding: 60px 0;
    }

    .event-card .card-img-top {
        height: 200px;
    }

    .event-card .card-body {
        padding: 20px;
    }
}
</style>

<!-- Events Hero Section -->
<div class="events-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">
                    <i class="fas fa-calendar-star me-3"></i>
                    <?php if ($search || $location || $date): ?>
                        Search Results
                    <?php else: ?>
                        Discover Amazing Events
                    <?php endif; ?>
                </h1>
                <p class="lead mb-0">
                    <?php if ($search || $location || $date): ?>
                        Found <?php echo $totalEvents; ?> events matching your criteria
                    <?php else: ?>
                        Find and book the perfect events for unforgettable experiences
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<div class="container my-4">
    <!-- Enhanced Search and Filter Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="search-card">
                <div class="card-body p-4">
                    <div class="row align-items-center mb-3">
                        <div class="col">
                            <h4 class="mb-0">
                                <i class="fas fa-filter me-2 text-primary"></i>
                                Find Your Perfect Event
                            </h4>
                        </div>
                        <?php if ($search || $location || $date): ?>
                        <div class="col-auto">
                            <span class="filter-chip">
                                <i class="fas fa-funnel-dollar me-1"></i>
                                Filters Active
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label fw-semibold">
                                <i class="fas fa-search me-2"></i>Search Events
                            </label>
                            <input type="text" class="form-control form-control-lg" id="search" name="search"
                                   value="<?php echo htmlspecialchars($search); ?>"
                                   placeholder="Event name, keyword, or organizer"
                                   style="border-radius: 15px; border: 2px solid rgba(102, 126, 234, 0.2);">
                        </div>
                        <div class="col-md-3">
                            <label for="location" class="form-label fw-semibold">
                                <i class="fas fa-map-marker-alt me-2"></i>Location
                            </label>
                            <input type="text" class="form-control form-control-lg" id="location" name="location"
                                   value="<?php echo htmlspecialchars($location); ?>"
                                   placeholder="City, venue, or address"
                                   style="border-radius: 15px; border: 2px solid rgba(102, 126, 234, 0.2);">
                        </div>
                        <div class="col-md-3">
                            <label for="date" class="form-label fw-semibold">
                                <i class="fas fa-calendar me-2"></i>Date
                            </label>
                            <input type="date" class="form-control form-control-lg" id="date" name="date"
                                   value="<?php echo htmlspecialchars($date); ?>"
                                   style="border-radius: 15px; border: 2px solid rgba(102, 126, 234, 0.2);">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-enhanced btn-lg">
                                    <i class="fas fa-search me-2"></i>Search
                                </button>
                                <?php if ($search || $location || $date): ?>
                                <a href="events.php" class="btn btn-outline-secondary btn-enhanced">
                                    <i class="fas fa-times me-1"></i>Clear
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Results Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center bg-light p-3 rounded-3">
                <div class="mb-2 mb-md-0">
                    <h5 class="mb-1">
                        <i class="fas fa-list me-2 text-primary"></i>
                        Event Results
                    </h5>
                    <p class="mb-0 text-muted">
                        Showing <strong><?php echo count($paginatedEvents); ?></strong> of <strong><?php echo $totalEvents; ?></strong> events
                        <?php if ($search || $location || $date): ?>
                            matching your search criteria
                        <?php endif; ?>
                    </p>
                </div>
                <div class="d-flex align-items-center">
                    <label for="sort-events" class="form-label me-2 mb-0 fw-semibold">
                        <i class="fas fa-sort me-1"></i>Sort:
                    </label>
                    <select class="form-select sort-select" id="sort-events" onchange="sortEvents(this.value)" style="min-width: 200px;">
                        <option value="date">📅 By Date</option>
                        <option value="price-low">💰 Price: Low to High</option>
                        <option value="price-high">💎 Price: High to Low</option>
                        <option value="name">🔤 Name: A to Z</option>
                        <option value="popularity">⭐ Most Popular</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Grid -->
    <?php if (empty($paginatedEvents)): ?>
    <div class="row">
        <div class="col-12">
            <div class="no-events-illustration">
                <i class="fas fa-calendar-times fa-5x text-muted mb-4" style="opacity: 0.5;"></i>
                <h3 class="mb-3">No Events Found</h3>
                <p class="text-muted mb-4">
                    <?php if ($search || $location || $date): ?>
                        No events match your search criteria. Try adjusting your filters or search terms.
                    <?php else: ?>
                        There are currently no events available. Please check back later for exciting new events!
                    <?php endif; ?>
                </p>
                <?php if ($search || $location || $date): ?>
                <a href="events.php" class="btn btn-primary btn-enhanced btn-lg">
                    <i class="fas fa-eye me-2"></i>View All Events
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="row" id="events-container">
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

        foreach ($paginatedEvents as $event):
            // Determine image based on event name/type
            $eventImage = $eventImages['default'];
            $eventName = strtolower($event['name']);
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

            // Calculate availability status
            $soldPercentage = (($event['total_tickets'] - $event['available_tickets']) / $event['total_tickets']) * 100;
            $availabilityStatus = $soldPercentage > 80 ? 'Almost Sold Out' : ($soldPercentage > 50 ? 'Selling Fast' : 'Available');
            $badgeClass = $soldPercentage > 80 ? 'bg-danger' : ($soldPercentage > 50 ? 'bg-warning' : 'bg-success');
        ?>
        <div class="col-lg-4 col-md-6 mb-4 event-item"
             data-date="<?php echo $event['date']; ?>"
             data-price="<?php echo $event['price']; ?>"
             data-name="<?php echo strtolower($event['name']); ?>">
            <div class="card event-card h-100">
                <div class="position-relative">
                    <img src="<?php echo $eventImage; ?>"
                         class="card-img-top" alt="<?php echo htmlspecialchars($event['name']); ?>"
                         loading="lazy"
                         onerror="this.src='https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'; this.onerror=null;"
                         style="height: 250px; object-fit: cover; width: 100%;">
                    <div class="event-badge <?php echo $badgeClass; ?> text-white">
                        <?php echo $availabilityStatus; ?>
                    </div>
                </div>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title fw-bold mb-3"><?php echo htmlspecialchars($event['name']); ?></h5>

                    <div class="event-meta">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-calendar me-2"></i>
                            <span class="fw-semibold"><?php echo formatDate($event['date']) . ' at ' . formatTime($event['time']); ?></span>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            <span><?php echo htmlspecialchars($event['venue'] . ', ' . $event['location']); ?></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user me-2"></i>
                            <small class="text-muted">by <?php echo htmlspecialchars($event['organizer']); ?></small>
                        </div>
                    </div>

                    <p class="card-text flex-grow-1 text-muted">
                        <?php echo htmlspecialchars(substr($event['description'], 0, 120)) . '...'; ?>
                    </p>

                    <div class="mt-3">
                        <div class="progress-enhanced mb-2">
                            <div class="progress-bar" role="progressbar"
                                 style="width: <?php echo $soldPercentage; ?>%"></div>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-ticket-alt me-1"></i>
                            <?php echo $event['available_tickets']; ?> of <?php echo $event['total_tickets']; ?> tickets available
                        </small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <span class="event-price"><?php echo formatPrice($event['price']); ?></span>
                        <div>
                            <a href="event-details.php?id=<?php echo $event['id']; ?>"
                               class="btn btn-outline-primary btn-enhanced btn-sm me-2">
                                <i class="fas fa-eye"></i> Details
                            </a>
                            <?php if (isLoggedIn()): ?>
                            <button class="btn btn-primary btn-enhanced btn-sm add-to-cart"
                                    data-event-id="<?php echo $event['id']; ?>">
                                <i class="fas fa-cart-plus"></i> Book
                            </button>
                            <?php else: ?>
                            <a href="login.php" class="btn btn-primary btn-enhanced btn-sm">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Enhanced Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="row mt-5">
        <div class="col-12">
            <nav aria-label="Events pagination">
                <ul class="pagination pagination-enhanced justify-content-center">
                    <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&location=<?php echo urlencode($location); ?>&date=<?php echo urlencode($date); ?>">
                            <i class="fas fa-chevron-left me-1"></i> Previous
                        </a>
                    </li>
                    <?php endif; ?>

                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&location=<?php echo urlencode($location); ?>&date=<?php echo urlencode($date); ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&location=<?php echo urlencode($location); ?>&date=<?php echo urlencode($date); ?>">
                            Next <i class="fas fa-chevron-right ms-1"></i>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </nav>

            <!-- Pagination Info -->
            <div class="text-center mt-3">
                <small class="text-muted">
                    Page <?php echo $page; ?> of <?php echo $totalPages; ?>
                    (<?php echo $totalEvents; ?> total events)
                </small>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<script>
// Enhanced sorting functionality
function sortEvents(sortBy) {
    const container = document.getElementById('events-container');
    const events = Array.from(container.children);

    // Add loading animation
    container.style.opacity = '0.7';
    container.style.transition = 'opacity 0.3s ease';

    setTimeout(() => {
        events.sort((a, b) => {
            switch(sortBy) {
                case 'date':
                    return new Date(a.dataset.date) - new Date(b.dataset.date);
                case 'price-low':
                    return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                case 'price-high':
                    return parseFloat(b.dataset.price) - parseFloat(a.dataset.price);
                case 'name':
                    return a.dataset.name.localeCompare(b.dataset.name);
                case 'popularity':
                    // Sort by tickets sold (higher percentage first)
                    const aProgress = a.querySelector('.progress-bar').style.width;
                    const bProgress = b.querySelector('.progress-bar').style.width;
                    return parseFloat(bProgress) - parseFloat(aProgress);
                default:
                    return 0;
            }
        });

        // Animate cards back in
        events.forEach((event, index) => {
            event.style.animation = `slideInUp 0.5s ease ${index * 0.1}s both`;
            container.appendChild(event);
        });

        container.style.opacity = '1';
    }, 300);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .event-card {
        animation: slideInUp 0.6s ease both;
    }

    .event-card:nth-child(1) { animation-delay: 0.1s; }
    .event-card:nth-child(2) { animation-delay: 0.2s; }
    .event-card:nth-child(3) { animation-delay: 0.3s; }
    .event-card:nth-child(4) { animation-delay: 0.4s; }
    .event-card:nth-child(5) { animation-delay: 0.5s; }
    .event-card:nth-child(6) { animation-delay: 0.6s; }
`;
document.head.appendChild(style);

// Enhanced search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Preload event images for better performance
    const eventImages = [
        'https://images.unsplash.com/photo-1540575467063-178a50c2df87?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1493225457124-a3eb161ffa5f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1559136555-9303baea8ebd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1578662996442-48f60103fc96?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
        'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ];

    // Preload images
    eventImages.forEach(src => {
        const img = new Image();
        img.src = src;
    });

    // Auto-focus search input
    const searchInput = document.getElementById('search');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }

    // Add search suggestions (placeholder for future enhancement)
    const searchForm = document.querySelector('form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Searching...';
            submitBtn.disabled = true;

            // Re-enable after form submission
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        });
    }

    // Add hover effects to event cards
    const eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-10px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
