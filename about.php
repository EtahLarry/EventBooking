<?php
require_once 'includes/functions.php';
$pageTitle = 'About Us - Creating Unforgettable Experiences';
include 'includes/header.php';
?>

<style>
/* About Us Page Styles */
.about-hero {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.95), rgba(118, 75, 162, 0.95)),
                url('https://images.unsplash.com/photo-1511795409834-ef04bbd61622?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80') center/cover;
    color: white;
    padding: 100px 0;
    position: relative;
    overflow: hidden;
}

.about-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 20%, rgba(255,255,255,0.1) 0%, transparent 50%);
    animation: heroGlow 6s ease-in-out infinite;
}

@keyframes heroGlow {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.8; }
}

.about-hero > * {
    position: relative;
    z-index: 1;
}

.mission-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 80px 0;
    position: relative;
}

.mission-card {
    background: white;
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    transform: translateY(0);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.mission-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #667eea, #764ba2, #f093fb);
}

.mission-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
}

.values-section {
    padding: 80px 0;
    background: white;
}

.value-item {
    text-align: center;
    padding: 30px 20px;
    border-radius: 15px;
    transition: all 0.3s ease;
    height: 100%;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
    border: 2px solid transparent;
}

.value-item:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border-color: rgba(102, 126, 234, 0.3);
    transform: translateY(-5px);
}

.value-icon {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 2rem;
    color: white;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.team-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 80px 0;
    position: relative;
}

.team-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
    opacity: 0.3;
}

.team-section > * {
    position: relative;
    z-index: 1;
}

.stats-section {
    background: #f8f9fa;
    padding: 60px 0;
}

.stat-item {
    text-align: center;
    padding: 20px;
}

.stat-number {
    font-size: 3rem;
    font-weight: bold;
    background: linear-gradient(135deg, #667eea, #764ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 10px;
    display: block;
}

.cta-section {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    padding: 80px 0;
    text-align: center;
}

.cta-button {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 15px 40px;
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
    display: inline-block;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    border: none;
}

.cta-button:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.floating-elements {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
    pointer-events: none;
}

.floating-element {
    position: absolute;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
}

.floating-element:nth-child(1) {
    width: 60px;
    height: 60px;
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.floating-element:nth-child(2) {
    width: 40px;
    height: 40px;
    top: 60%;
    right: 15%;
    animation-delay: 2s;
}

.floating-element:nth-child(3) {
    width: 80px;
    height: 80px;
    bottom: 20%;
    left: 20%;
    animation-delay: 4s;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.quote-section {
    background: white;
    padding: 60px 0;
    text-align: center;
}

.quote-text {
    font-size: 1.5rem;
    font-style: italic;
    color: #667eea;
    max-width: 800px;
    margin: 0 auto;
    line-height: 1.8;
}

@media (max-width: 768px) {
    .about-hero {
        padding: 60px 0;
    }
    
    .mission-card {
        padding: 30px 20px;
    }
    
    .stat-number {
        font-size: 2.5rem;
    }
}
</style>

<!-- Hero Section -->
<div class="about-hero">
    <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">
                    Creating Unforgettable Experiences in Cameroon
                </h1>
                <p class="lead mb-4">
                    We believe that every moment matters, every connection counts, and every experience
                    has the power to transform lives. Welcome to Cameroon's premier platform where dreams meet reality.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <span class="badge bg-light text-dark px-3 py-2 fs-6">🎯 Passion-Driven</span>
                    <span class="badge bg-light text-dark px-3 py-2 fs-6">🌟 Innovation-Focused</span>
                    <span class="badge bg-light text-dark px-3 py-2 fs-6">❤️ People-Centered</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mission Section -->
<div class="mission-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="mission-card">
                    <div class="row align-items-center">
                        <div class="col-lg-6">
                            <h2 class="display-6 fw-bold text-primary mb-4">Our Mission</h2>
                            <p class="lead mb-4">
                                To democratize access to extraordinary experiences across Cameroon and Central Africa
                                by connecting passionate event creators with enthusiastic participants through
                                innovative technology and seamless user experiences.
                            </p>
                            <p class="mb-4">
                                We're not just a booking platform – we're dream facilitators, connection
                                builders, and memory makers. Every event booked through our platform is
                                a step toward a more connected, inspired, and joyful Cameroon.
                            </p>
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <i class="fas fa-heart text-danger fs-3"></i>
                                </div>
                                <div>
                                    <strong>Powered by Passion:</strong> Every feature we build, every 
                                    improvement we make, is driven by our love for bringing people together.
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center">
                            <img src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                                 alt="Our Mission" class="img-fluid rounded-3 shadow-lg">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Values Section -->
<div class="values-section">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-primary mb-3">Our Core Values</h2>
                <p class="lead text-muted">
                    These principles guide everything we do and shape every decision we make
                </p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Community First</h4>
                    <p>
                        We believe in the power of community. Every feature we develop strengthens 
                        the bonds between event creators and attendees, fostering meaningful connections 
                        that last beyond the event itself.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Innovation & Excellence</h4>
                    <p>
                        We're constantly pushing boundaries, embracing new technologies, and 
                        refining our platform to deliver exceptional experiences that exceed 
                        expectations at every touchpoint.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Trust & Transparency</h4>
                    <p>
                        Your trust is our foundation. We maintain the highest standards of security, 
                        privacy, and transparency in all our operations, ensuring your data and 
                        experiences are always protected.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Empowerment</h4>
                    <p>
                        We empower event creators to bring their visions to life and enable 
                        attendees to discover experiences that inspire, educate, and transform 
                        their perspectives.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Accessibility</h4>
                    <p>
                        Great experiences should be accessible to everyone. We're committed to 
                        breaking down barriers and making our platform inclusive, user-friendly, 
                        and available to all.
                    </p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="value-item">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Quality Experiences</h4>
                    <p>
                        We curate and support only the highest quality events, ensuring that 
                        every booking leads to an exceptional experience that creates lasting 
                        memories and genuine value.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-primary mb-3">Our Impact</h2>
                <p class="lead text-muted">Numbers that reflect our commitment to excellence</p>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <span class="stat-number">10K+</span>
                    <h5 class="fw-bold">Happy Customers</h5>
                    <p class="text-muted">Satisfied event attendees who found their perfect experience</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <span class="stat-number">500+</span>
                    <h5 class="fw-bold">Successful Events</h5>
                    <p class="text-muted">Memorable events hosted through our platform</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <span class="stat-number">50+</span>
                    <h5 class="fw-bold">Event Partners</h5>
                    <p class="text-muted">Trusted organizers creating amazing experiences</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stat-item">
                    <span class="stat-number">99%</span>
                    <h5 class="fw-bold">Satisfaction Rate</h5>
                    <p class="text-muted">Customer satisfaction score across all events</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quote Section -->
<div class="quote-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="quote-text">
                    "Life is not measured by the number of breaths we take, but by the moments 
                    that take our breath away. We're here to help you find those moments."
                </div>
                <div class="mt-4">
                    <strong class="text-primary">— The EventBooking Team</strong>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Team Section -->
<div class="team-section">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Meet Our Team</h2>
                <p class="lead">
                    Passionate individuals united by a common vision of creating extraordinary experiences
                </p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center">
                    <div class="mb-4">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" 
                             alt="Our Team" class="img-fluid rounded-circle shadow-lg" style="width: 200px; height: 200px; object-fit: cover;">
                    </div>
                    <h4 class="fw-bold mb-3">Dedicated to Your Success</h4>
                    <p class="lead mb-4">
                        Our diverse team of developers, designers, and experience specialists work 
                        tirelessly to ensure every interaction with our platform is seamless, 
                        enjoyable, and meaningful.
                    </p>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h6 class="fw-bold">🎨 Creative Minds</h6>
                            <p>Designing beautiful, intuitive experiences</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="fw-bold">⚡ Tech Innovators</h6>
                            <p>Building robust, scalable solutions</p>
                        </div>
                        <div class="col-md-4">
                            <h6 class="fw-bold">❤️ Customer Champions</h6>
                            <p>Ensuring your success every step of the way</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="cta-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold text-primary mb-4">Ready to Create Amazing Experiences?</h2>
                <p class="lead mb-4">
                    Join thousands of event creators and attendees who trust us to bring their 
                    visions to life. Your next unforgettable experience is just a click away.
                </p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="events.php" class="cta-button">
                        <i class="fas fa-calendar-alt me-2"></i>Explore Events
                    </a>
                    <a href="login.php" class="cta-button">
                        <i class="fas fa-user-plus me-2"></i>Join Our Community
                    </a>
                </div>
                <div class="mt-4">
                    <p class="text-muted">
                        <i class="fas fa-envelope me-2"></i>
                        Questions? Reach out to us at 
                        <a href="mailto:nkumbelarry@gmail.com" class="text-primary">nkumbelarry@gmail.com</a>
                        or call <a href="tel:652731798" class="text-primary">652731798</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
