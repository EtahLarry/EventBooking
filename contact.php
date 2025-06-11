<?php
require_once 'includes/functions.php';
require_once 'includes/email-functions.php';
$pageTitle = 'Contact Us - Get in Touch';

// Handle contact form submission
$success = '';
$error = '';

// Initialize form variables
$name = '';
$email = '';
$phone = '';
$subject = '';
$message = '';
$inquiry_type = '';

// Helper function to safely escape HTML
function safeHtml($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $inquiry_type = $_POST['inquiry_type'] ?? '';
    
    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            $pdo = getDBConnection();

            // Check if user is logged in to link message to user account
            $userId = null;
            if (isset($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
            }

            // Insert message into database
            $stmt = $pdo->prepare("
                INSERT INTO messages (user_id, name, email, phone, subject, message, inquiry_type, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'new')
            ");

            $stmt->execute([
                $userId,
                $name,
                $email,
                $phone,
                $subject,
                $message,
                $inquiry_type
            ]);

            $messageId = $pdo->lastInsertId();

            // If user is logged in, create a notification
            if ($userId) {
                $notificationStmt = $pdo->prepare("
                    INSERT INTO user_notifications (user_id, message_id, title, content, type)
                    VALUES (?, ?, ?, ?, 'message_reply')
                ");

                $notificationStmt->execute([
                    $userId,
                    $messageId,
                    'Message Received',
                    'We have received your message: "' . substr($subject, 0, 50) . '..." and will respond within 24 hours.'
                ]);
            }

            // Send email notification to admin
            try {
                sendNewMessageNotification($messageId, $name, $email, $subject, $message, $inquiry_type);
            } catch (Exception $emailError) {
                error_log("Admin email notification error: " . $emailError->getMessage());
                // Don't fail the form submission if email fails
            }

            $success = 'Thank you for your message! We\'ve received your inquiry and will respond within 24 hours. Message ID: #' . $messageId;

            // Clear form data on success
            $name = $email = $phone = $subject = $message = $inquiry_type = '';

        } catch (Exception $e) {
            $error = 'Sorry, there was an error sending your message. Please try again later.';
            error_log("Contact form error: " . $e->getMessage());
        }
    }
}

include 'includes/header.php';
?>

<style>
/* Contact Us Page Styles */
.contact-hero {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.95), rgba(118, 75, 162, 0.95)),
                url('https://images.unsplash.com/photo-1423666639041-f56000c27a9a?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
    color: white;
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

.contact-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 70% 30%, rgba(255,255,255,0.1) 0%, transparent 50%);
    animation: heroGlow 8s ease-in-out infinite;
}

@keyframes heroGlow {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.7; }
}

.contact-hero > * {
    position: relative;
    z-index: 1;
}

.contact-section {
    padding: 80px 0;
    background: #f8f9fa;
}

.contact-form-container {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.contact-form-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
}

.contact-info-card {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border-radius: 20px;
    padding: 40px;
    height: 100%;
    position: relative;
    overflow: hidden;
}

.contact-info-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: cardGlow 10s linear infinite;
}

@keyframes cardGlow {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.contact-info-card > * {
    position: relative;
    z-index: 1;
}

.contact-item {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
    padding: 20px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 15px;
    transition: all 0.3s ease;
}

.contact-item:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateX(10px);
}

.contact-icon {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.form-group {
    margin-bottom: 25px;
}

.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    display: block;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background: white;
}

.form-select {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 15px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    background: white;
}

.btn-submit {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    width: 100%;
}

.btn-submit:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
    color: white;
}

.map-section {
    padding: 60px 0;
    background: white;
}

.map-container {
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    height: 400px;
    position: relative;
}

.map-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    position: relative;
    overflow: hidden;
}

.map-placeholder::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
    opacity: 0.3;
}

.map-placeholder > * {
    position: relative;
    z-index: 1;
}

.office-hours {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border-radius: 15px;
    padding: 30px;
    margin-top: 30px;
}

.hours-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid rgba(102, 126, 234, 0.2);
}

.hours-item:last-child {
    border-bottom: none;
}

.hours-day {
    font-weight: 600;
    color: #333;
}

.hours-time {
    color: #667eea;
    font-weight: 500;
}

.faq-section {
    padding: 80px 0;
    background: #f8f9fa;
}

.faq-item {
    background: white;
    border-radius: 15px;
    margin-bottom: 20px;
    overflow: hidden;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.faq-question {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    padding: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
    width: 100%;
    text-align: left;
    font-weight: 600;
    color: #333;
}

.faq-question:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
}

.faq-answer {
    padding: 0 20px;
    max-height: 0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.faq-answer.active {
    padding: 20px;
    max-height: 200px;
}

.social-section {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 60px 0;
    text-align: center;
}

.social-links {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 30px;
}

.social-link {
    width: 60px;
    height: 60px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    text-decoration: none;
    font-size: 1.5rem;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-5px);
    color: white;
}

@media (max-width: 768px) {
    .contact-hero {
        padding: 60px 0;
    }
    
    .contact-form-container,
    .contact-info-card {
        padding: 30px 20px;
        margin-bottom: 30px;
    }
    
    .contact-item {
        flex-direction: column;
        text-align: center;
    }
    
    .contact-icon {
        margin-right: 0;
        margin-bottom: 15px;
    }
    
    .social-links {
        flex-wrap: wrap;
    }
}
</style>

<!-- Hero Section -->
<div class="contact-hero">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">
                    <i class="fas fa-envelope me-3"></i>
                    Get in Touch
                </h1>
                <p class="lead mb-4">
                    We're here to help you create amazing experiences. Whether you have questions, 
                    need support, or want to partner with us, we'd love to hear from you.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <span class="badge bg-light text-dark px-3 py-2 fs-6">📞 24/7 Support</span>
                    <span class="badge bg-light text-dark px-3 py-2 fs-6">⚡ Quick Response</span>
                    <span class="badge bg-light text-dark px-3 py-2 fs-6">🤝 Personal Service</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Section -->
<div class="contact-section">
    <div class="container">
        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-5">
                <div class="contact-form-container">
                    <h2 class="fw-bold text-primary mb-4">
                        <i class="fas fa-paper-plane me-2"></i>Send us a Message
                    </h2>
                    
                    <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-user me-1"></i>Full Name *
                                    </label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="<?php echo safeHtml($name); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i>Email Address *
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?php echo safeHtml($email); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-1"></i>Phone Number
                                    </label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                           value="<?php echo safeHtml($phone); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="inquiry_type" class="form-label">
                                        <i class="fas fa-tag me-1"></i>Inquiry Type
                                    </label>
                                    <select class="form-select" id="inquiry_type" name="inquiry_type">
                                        <option value="">Select inquiry type</option>
                                        <option value="general" <?php echo ($inquiry_type ?? '') === 'general' ? 'selected' : ''; ?>>General Question</option>
                                        <option value="support" <?php echo ($inquiry_type ?? '') === 'support' ? 'selected' : ''; ?>>Technical Support</option>
                                        <option value="booking" <?php echo ($inquiry_type ?? '') === 'booking' ? 'selected' : ''; ?>>Booking Assistance</option>
                                        <option value="partnership" <?php echo ($inquiry_type ?? '') === 'partnership' ? 'selected' : ''; ?>>Partnership Opportunity</option>
                                        <option value="feedback" <?php echo ($inquiry_type ?? '') === 'feedback' ? 'selected' : ''; ?>>Feedback</option>
                                        <option value="other" <?php echo ($inquiry_type ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject" class="form-label">
                                <i class="fas fa-heading me-1"></i>Subject *
                            </label>
                            <input type="text" class="form-control" id="subject" name="subject"
                                   value="<?php echo safeHtml($subject); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="message" class="form-label">
                                <i class="fas fa-comment me-1"></i>Message *
                            </label>
                            <textarea class="form-control" id="message" name="message" rows="6"
                                      required><?php echo safeHtml($message); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit">
                            <i class="fas fa-paper-plane me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="col-lg-4">
                <div class="contact-info-card">
                    <h3 class="fw-bold mb-4">
                        <i class="fas fa-info-circle me-2"></i>Contact Information
                    </h3>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Email Us</h6>
                            <p class="mb-0">nkumbelarry@gmail.com</p>
                            <small>We respond within 24 hours</small>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Call Us</h6>
                            <p class="mb-0">+237 652 731 798</p>
                            <small>Mon-Fri, 9AM-6PM (WAT)</small>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Visit Us</h6>
                            <p class="mb-0">Avenue Kennedy<br>Plateau District<br>Yaoundé, Cameroon</p>
                            <small>By appointment only</small>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1">Support Hours</h6>
                            <p class="mb-0">24/7 Online Support</p>
                            <small>Always here to help</small>
                        </div>
                    </div>
                    
                    <!-- Office Hours -->
                    <div class="office-hours">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-business-time me-2"></i>Office Hours
                        </h6>
                        <div class="hours-item">
                            <span class="hours-day">Monday - Friday</span>
                            <span class="hours-time">9:00 AM - 6:00 PM</span>
                        </div>
                        <div class="hours-item">
                            <span class="hours-day">Saturday</span>
                            <span class="hours-time">10:00 AM - 4:00 PM</span>
                        </div>
                        <div class="hours-item">
                            <span class="hours-day">Sunday</span>
                            <span class="hours-time">Closed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Map Section -->
<div class="map-section">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-map-marked-alt me-2"></i>Find Us
                </h2>
                <p class="lead text-muted">
                    Located in the heart of Yaoundé's Plateau district, we're easily accessible by public transport
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="map-container">
                    <!-- Interactive Map - Yaoundé, Cameroon using OpenStreetMap -->
                    <div id="map" style="height: 400px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);"></div>

                    <!-- Fallback for when maps don't load -->
                    <div id="map-fallback" style="display: none; height: 400px; border-radius: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px; text-align: center;">
                        <i class="fas fa-map-marked-alt fa-4x mb-4"></i>
                        <h4>EventBooking Cameroon</h4>
                        <p class="mb-3">Avenue Kennedy, Plateau District<br>Yaoundé, Centre Region, Cameroon</p>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <h6><i class="fas fa-phone me-2"></i>Phone</h6>
                                <p>+237 652 731 798</p>
                            </div>
                            <div class="col-md-6">
                                <h6><i class="fas fa-envelope me-2"></i>Email</h6>
                                <p>nkumbelarry@gmail.com</p>
                            </div>
                        </div>
                        <a href="https://www.openstreetmap.org/search?query=Avenue%20Kennedy%20Yaound%C3%A9%20Cameroon"
                           target="_blank" class="btn btn-light mt-3">
                            <i class="fas fa-external-link-alt me-2"></i>View on OpenStreetMap
                        </a>
                    </div>
                </div>

                <!-- Map Information -->
                <div class="row mt-4">
                    <div class="col-md-4 text-center">
                        <div class="p-3">
                            <i class="fas fa-bus text-primary fs-2 mb-2"></i>
                            <h6 class="fw-bold">Public Transport</h6>
                            <p class="text-muted small">5 min walk from Bus Station</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="p-3">
                            <i class="fas fa-parking text-primary fs-2 mb-2"></i>
                            <h6 class="fw-bold">Parking Available</h6>
                            <p class="text-muted small">Free parking for visitors</p>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="p-3">
                            <i class="fas fa-wheelchair text-primary fs-2 mb-2"></i>
                            <h6 class="fw-bold">Accessible</h6>
                            <p class="text-muted small">Wheelchair accessible entrance</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="faq-section">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-primary mb-3">
                    <i class="fas fa-question-circle me-2"></i>Frequently Asked Questions
                </h2>
                <p class="lead text-muted">
                    Quick answers to common questions about our platform and services
                </p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(this)">
                        <i class="fas fa-plus me-2"></i>
                        How do I book an event?
                    </button>
                    <div class="faq-answer">
                        <p>Booking an event is simple! Browse our events page, select the event you're interested in, choose your tickets, and complete the secure checkout process. You'll receive a confirmation email with your tickets and QR codes.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(this)">
                        <i class="fas fa-plus me-2"></i>
                        Can I cancel or refund my booking?
                    </button>
                    <div class="faq-answer">
                        <p>Yes, you can cancel your booking up to 24 hours before the event start time. Refunds are processed within 5-7 business days. Please check the specific event's cancellation policy for details.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(this)">
                        <i class="fas fa-plus me-2"></i>
                        How do I access my tickets?
                    </button>
                    <div class="faq-answer">
                        <p>After booking, you can access your tickets through your account dashboard or the email confirmation. You can download PDF tickets or show the QR code on your mobile device at the event entrance.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(this)">
                        <i class="fas fa-plus me-2"></i>
                        Do you offer group discounts?
                    </button>
                    <div class="faq-answer">
                        <p>Yes! We offer group discounts for bookings of 10 or more tickets. Contact us directly for special group pricing and arrangements. Corporate packages are also available.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(this)">
                        <i class="fas fa-plus me-2"></i>
                        How can I become an event organizer?
                    </button>
                    <div class="faq-answer">
                        <p>We welcome new event organizers! Contact us through this form or email us directly. We'll guide you through our partnership process and help you get started with hosting amazing events.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <button class="faq-question" onclick="toggleFAQ(this)">
                        <i class="fas fa-plus me-2"></i>
                        Is my payment information secure?
                    </button>
                    <div class="faq-answer">
                        <p>Absolutely! We use industry-standard SSL encryption and secure payment processors to protect your financial information. We never store your complete payment details on our servers.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Social Media Section -->
<div class="social-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="fw-bold mb-3">
                    <i class="fas fa-share-alt me-2"></i>Connect With Us
                </h2>
                <p class="lead mb-4">
                    Follow us on social media for the latest updates, event announcements, and exclusive offers
                </p>
                <div class="social-links">
                    <a href="#" class="social-link" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="social-link" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="social-link" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="mailto:nkumbelarry@gmail.com" class="social-link" title="Email">
                        <i class="fas fa-envelope"></i>
                    </a>
                </div>
                <div class="mt-4">
                    <p class="mb-0">
                        <i class="fas fa-bell me-2"></i>
                        Subscribe to our newsletter for exclusive event updates and special offers
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// FAQ Toggle Function
function toggleFAQ(button) {
    const answer = button.nextElementSibling;
    const icon = button.querySelector('i');

    // Close all other FAQs
    document.querySelectorAll('.faq-answer').forEach(item => {
        if (item !== answer) {
            item.classList.remove('active');
            item.previousElementSibling.querySelector('i').className = 'fas fa-plus me-2';
        }
    });

    // Toggle current FAQ
    answer.classList.toggle('active');

    if (answer.classList.contains('active')) {
        icon.className = 'fas fa-minus me-2';
    } else {
        icon.className = 'fas fa-plus me-2';
    }
}

// Form Enhancement
document.addEventListener('DOMContentLoaded', function() {
    // Add floating labels effect
    const formControls = document.querySelectorAll('.form-control, .form-select');

    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        control.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });

        // Check if field has value on load
        if (control.value) {
            control.parentElement.classList.add('focused');
        }
    });

    // Form validation enhancement
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Email validation
            const emailField = form.querySelector('input[type="email"]');
            if (emailField && emailField.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailField.value)) {
                    emailField.classList.add('is-invalid');
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();
                // Scroll to first invalid field
                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) {
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus();
                }
            }
        });
    }
});

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>

<!-- Leaflet.js for OpenStreetMap -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<!-- Map Initialization Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Initialize the map centered on Yaoundé, Cameroon
        var map = L.map('map', {
            center: [3.8480, 11.5174],
            zoom: 14,
            zoomControl: true,
            scrollWheelZoom: true
        });

        // Add OpenStreetMap tiles with better styling
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
            tileSize: 256,
            zoomOffset: 0
        }).addTo(map);

        // Custom marker HTML with EventBooking branding
        var customIcon = L.divIcon({
            html: `
                <div style="
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    color: white;
                    width: 50px;
                    height: 50px;
                    border-radius: 50% 50% 50% 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 20px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.4);
                    border: 3px solid white;
                    transform: rotate(-45deg);
                ">
                    <i class="fas fa-calendar-alt" style="transform: rotate(45deg);"></i>
                </div>
            `,
            className: 'custom-div-icon',
            iconSize: [50, 50],
            iconAnchor: [25, 50],
            popupAnchor: [0, -50]
        });

        // Add main marker for EventBooking Cameroon
        var marker = L.marker([3.8480, 11.5174], {icon: customIcon}).addTo(map);

        // Enhanced popup with professional styling
        marker.bindPopup(`
            <div style="
                text-align: center;
                padding: 15px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                min-width: 250px;
            ">
                <div style="
                    background: linear-gradient(135deg, #667eea, #764ba2);
                    color: white;
                    padding: 10px;
                    margin: -15px -15px 15px -15px;
                    border-radius: 8px 8px 0 0;
                ">
                    <h5 style="margin: 0; font-weight: bold;">
                        <i class="fas fa-calendar-alt me-2"></i>EventBooking Cameroon
                    </h5>
                </div>

                <div style="text-align: left; line-height: 1.6;">
                    <p style="margin-bottom: 8px; color: #333;">
                        <i class="fas fa-map-marker-alt me-2" style="color: #667eea;"></i>
                        <strong>Address:</strong><br>
                        Avenue Kennedy, Plateau District<br>
                        Yaoundé, Centre Region, Cameroon
                    </p>

                    <p style="margin-bottom: 8px; color: #333;">
                        <i class="fas fa-phone me-2" style="color: #667eea;"></i>
                        <strong>Phone:</strong> +237 652 731 798
                    </p>

                    <p style="margin-bottom: 15px; color: #333;">
                        <i class="fas fa-envelope me-2" style="color: #667eea;"></i>
                        <strong>Email:</strong> nkumbelarry@gmail.com
                    </p>

                    <div style="text-align: center;">
                        <a href="https://www.openstreetmap.org/search?query=Avenue%20Kennedy%20Yaound%C3%A9%20Cameroon"
                           target="_blank"
                           style="
                               background: linear-gradient(135deg, #667eea, #764ba2);
                               color: white;
                               padding: 8px 16px;
                               text-decoration: none;
                               border-radius: 20px;
                               font-size: 14px;
                               display: inline-block;
                           ">
                            <i class="fas fa-external-link-alt me-1"></i>View Larger Map
                        </a>
                    </div>
                </div>
            </div>
        `, {
            maxWidth: 300,
            className: 'custom-popup'
        }).openPopup();

        // Add nearby landmarks for better context
        var landmarks = [
            {
                name: "Palais des Congrès",
                lat: 3.8456,
                lng: 11.5189,
                description: "Conference Center",
                icon: "fas fa-building"
            },
            {
                name: "Central Post Office",
                lat: 3.8467,
                lng: 11.5156,
                description: "Main Post Office",
                icon: "fas fa-mail-bulk"
            },
            {
                name: "Yaoundé Cathedral",
                lat: 3.8501,
                lng: 11.5145,
                description: "Notre-Dame Cathedral",
                icon: "fas fa-church"
            }
        ];

        // Add landmark markers with different styling
        landmarks.forEach(function(landmark) {
            var landmarkIcon = L.divIcon({
                html: `
                    <div style="
                        background: rgba(102, 126, 234, 0.9);
                        color: white;
                        width: 35px;
                        height: 35px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 14px;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                        border: 2px solid white;
                    ">
                        <i class="${landmark.icon}"></i>
                    </div>
                `,
                className: 'landmark-icon',
                iconSize: [35, 35],
                iconAnchor: [17, 17],
                popupAnchor: [0, -17]
            });

            L.marker([landmark.lat, landmark.lng], {icon: landmarkIcon})
                .addTo(map)
                .bindPopup(`
                    <div style="text-align: center; padding: 10px;">
                        <h6 style="color: #667eea; margin-bottom: 5px;">
                            <i class="${landmark.icon} me-2"></i>${landmark.name}
                        </h6>
                        <p style="margin: 0; color: #666;">${landmark.description}</p>
                    </div>
                `);
        });

        // Add a subtle circle to show the service area
        L.circle([3.8480, 11.5174], {
            color: '#667eea',
            fillColor: '#667eea',
            fillOpacity: 0.1,
            radius: 800,
            weight: 2,
            opacity: 0.6
        }).addTo(map);

        // Add custom controls
        var info = L.control({position: 'topright'});
        info.onAdd = function (map) {
            this._div = L.DomUtil.create('div', 'info');
            this._div.innerHTML = `
                <div style="
                    background: white;
                    padding: 10px;
                    border-radius: 8px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                    font-size: 12px;
                    max-width: 200px;
                ">
                    <strong style="color: #667eea;">EventBooking Cameroon</strong><br>
                    <small>Your premier event booking platform in Yaoundé</small>
                </div>
            `;
            return this._div;
        };
        info.addTo(map);

        console.log('Map loaded successfully');

    } catch (error) {
        console.error('Error loading map:', error);
        // Show fallback if map fails to load
        document.getElementById('map').style.display = 'none';
        document.getElementById('map-fallback').style.display = 'block';
    }
});
</script>

<!-- Custom Map Styles -->
<style>
.leaflet-popup-content-wrapper {
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
}

.leaflet-popup-tip {
    background: white;
}

.custom-div-icon {
    background: transparent;
    border: none;
}

.landmark-icon {
    background: transparent;
    border: none;
}

.info {
    background: transparent;
    border: none;
}

#map {
    border: 3px solid #f8f9fa;
    transition: all 0.3s ease;
}

#map:hover {
    border-color: #667eea;
}

/* Responsive map */
@media (max-width: 768px) {
    #map {
        height: 300px !important;
    }

    .leaflet-popup-content-wrapper {
        max-width: 250px;
    }
}
</style>

<?php include 'includes/footer.php'; ?>
