# 🎪 EventBooking Cameroon - Complete Project Documentation

## 📋 **Table of Contents**

1. [Project Overview](#project-overview)
2. [System Architecture](#system-architecture)
3. [Design & User Interface](#design--user-interface)
4. [Implementation Details](#implementation-details)
5. [Database Design](#database-design)
6. [Security Features](#security-features)
7. [Deployment Guide](#deployment-guide)
8. [Code Explanation](#code-explanation)
9. [User Manual](#user-manual)
10. [Admin Manual](#admin-manual)
11. [API Documentation](#api-documentation)
12. [Troubleshooting](#troubleshooting)

---

## 🎯 **Project Overview**

### **Project Name:** EventBooking Cameroon
### **Version:** 1.0.0
### **Development Period:** 2024
### **Technology Stack:** PHP, MySQL, Bootstrap 5, JavaScript, Leaflet.js

### **Project Description**
EventBooking Cameroon is a comprehensive web-based event management and booking platform specifically designed for the Cameroon market. The system enables event organizers to create and manage events while providing users with an intuitive platform to discover, book, and manage their event tickets.

### **Key Features**
- ✅ **Event Management** - Create, edit, and manage events
- ✅ **User Registration & Authentication** - Secure user accounts
- ✅ **Ticket Booking System** - Online ticket purchasing
- ✅ **Payment Integration** - Secure payment processing
- ✅ **Admin Dashboard** - Comprehensive management interface
- ✅ **Messaging System** - Two-way communication between users and admins
- ✅ **Interactive Maps** - OpenStreetMap integration for event locations
- ✅ **Mobile Responsive** - Optimized for all devices
- ✅ **Cameroon Localization** - Tailored for local market

### **Target Audience**
- **Primary:** Event organizers and attendees in Cameroon
- **Secondary:** Event management companies and venues
- **Tertiary:** Corporate event planners

### **Business Objectives**
- Digitize event booking process in Cameroon
- Provide centralized platform for event discovery
- Streamline event management for organizers
- Enhance user experience with modern web technologies
- Support local economy through event promotion

---

## 🏗️ **System Architecture**

### **Architecture Pattern**
The system follows a **Model-View-Controller (MVC)** pattern with a three-tier architecture:

```
┌─────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                       │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────────────────┐ │
│  │   Web UI    │ │  Admin UI   │ │    Mobile Interface     │ │
│  │ (Bootstrap) │ │ (Dashboard) │ │   (Responsive Design)   │ │
│  └─────────────┘ └─────────────┘ └─────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                    APPLICATION LAYER                        │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────────────────┐ │
│  │   PHP Core  │ │  Functions  │ │    Business Logic       │ │
│  │ (Controllers)│ │ (Utilities) │ │   (Event Management)    │ │
│  └─────────────┘ └─────────────┘ └─────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
                              │
┌─────────────────────────────────────────────────────────────┐
│                      DATA LAYER                             │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────────────────┐ │
│  │   MySQL     │ │  File       │ │    External APIs        │ │
│  │  Database   │ │  Storage    │ │   (Maps, Payments)      │ │
│  └─────────────┘ └─────────────┘ └─────────────────────────┘ │
└─────────────────────────────────────────────────────────────┘
```

### **Technology Stack**

**Frontend Technologies:**
- **HTML5** - Semantic markup structure
- **CSS3** - Modern styling with animations
- **Bootstrap 5** - Responsive framework
- **JavaScript (ES6+)** - Interactive functionality
- **jQuery** - DOM manipulation and AJAX
- **FontAwesome** - Icon library
- **Leaflet.js** - Interactive maps

**Backend Technologies:**
- **PHP 8+** - Server-side programming
- **MySQL 8.0** - Relational database
- **Apache/Nginx** - Web server
- **PDO** - Database abstraction layer

**Development Tools:**
- **Git** - Version control
- **Composer** - Dependency management
- **NPM** - Package management
- **Docker** - Containerization (optional)

### **System Components**

**Core Modules:**
1. **Authentication Module** - User login/registration
2. **Event Management Module** - CRUD operations for events
3. **Booking Module** - Ticket booking and management
4. **Payment Module** - Payment processing
5. **Admin Module** - Administrative functions
6. **Messaging Module** - Communication system
7. **Notification Module** - Email and in-app notifications
8. **Maps Module** - Location services

---

## 🎨 **Design & User Interface**

### **Design Philosophy**
The design follows modern web standards with emphasis on:
- **User-Centric Design** - Intuitive navigation and workflows
- **Mobile-First Approach** - Responsive design for all devices
- **Accessibility** - WCAG 2.1 compliance
- **Performance** - Optimized loading and interactions
- **Cameroon Cultural Elements** - Local colors and imagery

### **Color Palette**
```css
Primary Colors:
- Primary Blue: #667eea (Main brand color)
- Secondary Purple: #764ba2 (Accent color)
- Success Green: #28a745 (Confirmations)
- Warning Orange: #fd7e14 (Alerts)
- Danger Red: #dc3545 (Errors)

Neutral Colors:
- Dark Gray: #343a40 (Text)
- Medium Gray: #6c757d (Secondary text)
- Light Gray: #f8f9fa (Backgrounds)
- White: #ffffff (Cards, modals)
```

### **Typography**
- **Primary Font:** 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif
- **Headings:** Bold weights (600-700)
- **Body Text:** Regular weight (400)
- **Small Text:** Light weight (300)

### **UI Components**

**Navigation:**
- Fixed top navigation with brand logo
- Responsive hamburger menu for mobile
- User dropdown with profile options
- Notification bell with badge counter

**Cards:**
- Rounded corners (border-radius: 15px)
- Subtle shadows for depth
- Hover effects with smooth transitions
- Gradient backgrounds for featured content

**Forms:**
- Floating labels for better UX
- Input validation with real-time feedback
- Progress indicators for multi-step forms
- Accessible error messaging

**Buttons:**
- Gradient backgrounds with hover effects
- Icon integration with FontAwesome
- Loading states with spinners
- Consistent sizing and spacing

### **Responsive Design**

**Breakpoints:**
```css
/* Mobile First Approach */
@media (min-width: 576px) { /* Small devices */ }
@media (min-width: 768px) { /* Medium devices */ }
@media (min-width: 992px) { /* Large devices */ }
@media (min-width: 1200px) { /* Extra large devices */ }
```

**Mobile Optimizations:**
- Touch-friendly button sizes (minimum 44px)
- Simplified navigation for small screens
- Optimized image loading
- Reduced animation complexity
- Swipe gestures for carousels

---

## 💻 **Implementation Details**

### **Development Methodology**
The project follows **Agile Development** principles with:
- Iterative development cycles
- Continuous integration and testing
- User feedback incorporation
- Regular code reviews
- Documentation-driven development

### **Code Standards**

**PHP Coding Standards:**
```php
<?php
// PSR-12 compliant code structure
namespace EventBooking\Core;

class EventManager
{
    private $database;
    
    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    
    public function createEvent(array $eventData): int
    {
        // Implementation
    }
}
```

**JavaScript Standards:**
```javascript
// ES6+ with consistent formatting
class EventBooking {
    constructor(options = {}) {
        this.options = { ...this.defaults, ...options };
        this.init();
    }
    
    async bookEvent(eventId, quantity) {
        try {
            const response = await fetch('/api/book-event', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ eventId, quantity })
            });
            return await response.json();
        } catch (error) {
            console.error('Booking error:', error);
            throw error;
        }
    }
}
```

**CSS Standards:**
```css
/* BEM methodology for class naming */
.event-card {
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.event-card__title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.event-card--featured {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}
```

### **Security Implementation**

**Authentication Security:**
- Password hashing with `password_hash()`
- Session management with secure cookies
- CSRF protection on all forms
- Rate limiting for login attempts
- Account lockout after failed attempts

**Data Security:**
- SQL injection prevention with PDO prepared statements
- XSS protection with `htmlspecialchars()`
- Input validation and sanitization
- File upload restrictions
- Secure file permissions

**Communication Security:**
- HTTPS enforcement
- Secure headers implementation
- Content Security Policy (CSP)
- HTTP Strict Transport Security (HSTS)

### **Performance Optimizations**

**Frontend Optimizations:**
- Image compression and lazy loading
- CSS and JavaScript minification
- CDN usage for external libraries
- Browser caching strategies
- Progressive Web App features

**Backend Optimizations:**
- Database query optimization
- Connection pooling
- Caching strategies (Redis/Memcached)
- Gzip compression
- Optimized autoloading

**Database Optimizations:**
- Proper indexing strategies
- Query optimization
- Connection management
- Regular maintenance procedures
- Backup and recovery plans

---

## 🗄️ **Database Design**

### **Entity Relationship Diagram**

```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│    USERS    │    │   EVENTS    │    │  BOOKINGS   │
├─────────────┤    ├─────────────┤    ├─────────────┤
│ id (PK)     │    │ id (PK)     │    │ id (PK)     │
│ username    │    │ name        │    │ user_id (FK)│
│ email       │    │ description │    │ event_id(FK)│
│ password    │    │ date        │    │ quantity    │
│ full_name   │    │ time        │    │ total_amount│
│ phone       │    │ venue       │    │ status      │
│ created_at  │    │ location    │    │ booking_ref │
│ updated_at  │    │ price       │    │ created_at  │
└─────────────┘    │ capacity    │    └─────────────┘
                   │ available   │
                   │ image       │
                   │ created_at  │
                   └─────────────┘
```

### **Database Schema**

**Users Table:**
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    profile_image VARCHAR(255),
    email_verified BOOLEAN DEFAULT FALSE,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_status (status)
);
```

**Events Table:**
```sql
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    date DATE NOT NULL,
    time TIME NOT NULL,
    venue VARCHAR(200) NOT NULL,
    location VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    capacity INT NOT NULL DEFAULT 100,
    available_tickets INT NOT NULL,
    image VARCHAR(255),
    category VARCHAR(50),
    status ENUM('active', 'inactive', 'cancelled') DEFAULT 'active',
    featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_date (date),
    INDEX idx_status (status),
    INDEX idx_featured (featured),
    INDEX idx_category (category),
    FULLTEXT idx_search (name, description, venue, location)
);
```

**Bookings Table:**
```sql
CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    total_amount DECIMAL(10,2) NOT NULL,
    booking_reference VARCHAR(20) UNIQUE NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50),
    payment_reference VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    
    INDEX idx_user_id (user_id),
    INDEX idx_event_id (event_id),
    INDEX idx_status (status),
    INDEX idx_booking_ref (booking_reference),
    INDEX idx_created_at (created_at)
);
```

### **Additional Tables**

**Messages Table:**
```sql
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    inquiry_type ENUM('general', 'support', 'booking', 'complaint') DEFAULT 'general',
    status ENUM('new', 'read', 'replied', 'closed') DEFAULT 'new',
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    last_read_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_created_at (created_at)
);
```

**Admin Users Table:**
```sql
CREATE TABLE admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'super_admin') DEFAULT 'admin',
    permissions JSON,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_status (status)
);
```

### **Database Relationships**

**One-to-Many Relationships:**
- Users → Bookings (One user can have many bookings)
- Events → Bookings (One event can have many bookings)
- Users → Messages (One user can send many messages)

**Many-to-Many Relationships:**
- Users ↔ Events (Through Bookings table)

**Referential Integrity:**
- Foreign key constraints ensure data consistency
- Cascade deletes for dependent records
- NULL handling for optional relationships

---

## 🚀 **Deployment Guide**

### **System Requirements**

**Minimum Requirements:**
- **Web Server:** Apache 2.4+ or Nginx 1.18+
- **PHP:** Version 8.0 or higher
- **Database:** MySQL 8.0 or MariaDB 10.5+
- **Memory:** 512MB RAM minimum
- **Storage:** 2GB available space
- **SSL Certificate:** Required for production

**Recommended Requirements:**
- **Web Server:** Apache 2.4+ with mod_rewrite
- **PHP:** Version 8.1+ with extensions:
  - PDO and PDO_MySQL
  - GD or ImageMagick
  - cURL
  - OpenSSL
  - Mbstring
  - JSON
- **Database:** MySQL 8.0+ with InnoDB engine
- **Memory:** 2GB RAM or higher
- **Storage:** 10GB+ SSD storage
- **CDN:** CloudFlare or similar for static assets

### **Installation Steps**

**Step 1: Server Preparation**
```bash
# Update system packages
sudo apt update && sudo apt upgrade -y

# Install LAMP stack
sudo apt install apache2 mysql-server php8.1 php8.1-mysql php8.1-gd php8.1-curl php8.1-mbstring -y

# Enable Apache modules
sudo a2enmod rewrite
sudo a2enmod ssl
sudo systemctl restart apache2
```

**Step 2: Database Setup**
```sql
-- Create database
CREATE DATABASE eventbooking_cameroon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create database user
CREATE USER 'eventbooking'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON eventbooking_cameroon.* TO 'eventbooking'@'localhost';
FLUSH PRIVILEGES;
```

**Step 3: Application Deployment**
```bash
# Clone or upload project files
cd /var/www/html
sudo git clone https://github.com/your-repo/eventbooking-cameroon.git
sudo chown -R www-data:www-data eventbooking-cameroon/
sudo chmod -R 755 eventbooking-cameroon/

# Set proper permissions for uploads
sudo chmod -R 777 eventbooking-cameroon/uploads/
sudo chmod -R 777 eventbooking-cameroon/logs/
```

**Step 4: Configuration**
```php
// config/database.php
<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventbooking_cameroon');
define('DB_USER', 'eventbooking');
define('DB_PASS', 'secure_password_here');
define('DB_CHARSET', 'utf8mb4');

// Security settings
define('SITE_URL', 'https://yourdomain.com');
define('SECURE_SSL', true);
define('SESSION_SECURE', true);
?>
```

**Step 5: Database Migration**
```bash
# Run database setup script
php setup/install.php

# Or manually import SQL files
mysql -u eventbooking -p eventbooking_cameroon < database/schema.sql
mysql -u eventbooking -p eventbooking_cameroon < database/sample_data.sql
```

### **Production Configuration**

**Apache Virtual Host:**
```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/eventbooking-cameroon

    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key

    <Directory /var/www/html/eventbooking-cameroon>
        AllowOverride All
        Require all granted
    </Directory>

    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
</VirtualHost>
```

**PHP Configuration (php.ini):**
```ini
; Security settings
expose_php = Off
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log

; Performance settings
memory_limit = 256M
max_execution_time = 30
max_input_time = 60
post_max_size = 50M
upload_max_filesize = 20M

; Session security
session.cookie_secure = 1
session.cookie_httponly = 1
session.use_strict_mode = 1
```

### **Environment-Specific Configurations**

**Development Environment:**
```php
// config/environment.php
<?php
define('ENVIRONMENT', 'development');
define('DEBUG_MODE', true);
define('ERROR_REPORTING', E_ALL);
define('DISPLAY_ERRORS', true);
define('LOG_LEVEL', 'debug');
?>
```

**Production Environment:**
```php
// config/environment.php
<?php
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);
define('ERROR_REPORTING', E_ERROR | E_WARNING);
define('DISPLAY_ERRORS', false);
define('LOG_LEVEL', 'error');
?>
```

### **Backup and Maintenance**

**Automated Backup Script:**
```bash
#!/bin/bash
# backup.sh - Daily backup script

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/eventbooking"
DB_NAME="eventbooking_cameroon"
DB_USER="eventbooking"
DB_PASS="secure_password_here"

# Create backup directory
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Files backup
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/html/eventbooking-cameroon

# Clean old backups (keep 30 days)
find $BACKUP_DIR -name "*.sql" -mtime +30 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
```

---

## 📝 **Code Explanation**

### **Project Structure**
```
eventbooking-cameroon/
├── admin/                  # Admin panel files
│   ├── dashboard.php      # Admin dashboard
│   ├── events.php         # Event management
│   ├── bookings.php       # Booking management
│   ├── users.php          # User management
│   ├── messages.php       # Message management
│   └── includes/          # Admin-specific includes
├── assets/                # Static assets
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   ├── images/           # Image assets
│   └── uploads/          # User uploaded files
├── includes/             # Core includes
│   ├── config.php        # Configuration settings
│   ├── functions.php     # Core functions
│   ├── database.php      # Database connection
│   ├── auth.php          # Authentication functions
│   ├── header.php        # Common header
│   └── footer.php        # Common footer
├── user/                 # User dashboard files
│   ├── dashboard.php     # User dashboard
│   ├── profile.php       # User profile
│   ├── bookings.php      # User bookings
│   └── messages.php      # User messages
├── api/                  # API endpoints
│   ├── book-event.php    # Booking API
│   ├── cancel-booking.php # Cancellation API
│   └── notifications.php # Notifications API
├── database/             # Database files
│   ├── schema.sql        # Database schema
│   └── migrations/       # Database migrations
├── logs/                 # Application logs
├── index.php             # Homepage
├── events.php            # Events listing
├── event-details.php     # Event details
├── contact.php           # Contact page
├── login.php             # User login
├── register.php          # User registration
└── .htaccess            # Apache configuration
```

### **Core Functions Explanation**

**Database Connection (includes/database.php):**
```php
<?php
/**
 * Database connection using PDO with error handling
 * Implements singleton pattern for efficient connection management
 */
function getDBConnection() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
            ];

            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    return $pdo;
}
?>
```

**Authentication System (includes/auth.php):**
```php
<?php
/**
 * User authentication and session management
 * Implements secure password hashing and session handling
 */

function authenticateUser($username, $password) {
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT id, username, password, status FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'active') {
                startUserSession($user);
                return true;
            } else {
                throw new Exception("Account is not active");
            }
        }

        return false;
    } catch (Exception $e) {
        error_log("Authentication error: " . $e->getMessage());
        return false;
    }
}

function startUserSession($user) {
    session_start();
    session_regenerate_id(true); // Prevent session fixation

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['logged_in'] = true;
    $_SESSION['login_time'] = time();
}

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}
?>
```

**Event Management (includes/event-functions.php):**
```php
<?php
/**
 * Event management functions
 * Handles CRUD operations for events with validation
 */

function createEvent($eventData) {
    try {
        $pdo = getDBConnection();

        // Validate input data
        $validatedData = validateEventData($eventData);

        $sql = "INSERT INTO events (name, description, date, time, venue, location, price, capacity, available_tickets, image, category)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $validatedData['name'],
            $validatedData['description'],
            $validatedData['date'],
            $validatedData['time'],
            $validatedData['venue'],
            $validatedData['location'],
            $validatedData['price'],
            $validatedData['capacity'],
            $validatedData['capacity'], // available_tickets = capacity initially
            $validatedData['image'],
            $validatedData['category']
        ]);

        return $pdo->lastInsertId();
    } catch (Exception $e) {
        error_log("Event creation error: " . $e->getMessage());
        throw new Exception("Failed to create event");
    }
}

function validateEventData($data) {
    $errors = [];

    // Required fields validation
    if (empty($data['name'])) $errors[] = "Event name is required";
    if (empty($data['date'])) $errors[] = "Event date is required";
    if (empty($data['time'])) $errors[] = "Event time is required";
    if (empty($data['venue'])) $errors[] = "Venue is required";
    if (empty($data['location'])) $errors[] = "Location is required";

    // Data type validation
    if (!is_numeric($data['price']) || $data['price'] < 0) {
        $errors[] = "Price must be a valid positive number";
    }

    if (!is_numeric($data['capacity']) || $data['capacity'] < 1) {
        $errors[] = "Capacity must be a positive integer";
    }

    // Date validation
    if (!validateDate($data['date'])) {
        $errors[] = "Invalid date format";
    }

    if (!empty($errors)) {
        throw new ValidationException(implode(", ", $errors));
    }

    return $data;
}

function getEvents($filters = []) {
    try {
        $pdo = getDBConnection();
        $sql = "SELECT * FROM events WHERE status = 'active'";
        $params = [];

        // Apply filters
        if (!empty($filters['category'])) {
            $sql .= " AND category = ?";
            $params[] = $filters['category'];
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND date >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (name LIKE ? OR description LIKE ? OR venue LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        $sql .= " ORDER BY date ASC, time ASC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Get events error: " . $e->getMessage());
        return [];
    }
}
?>
```

### **Frontend JavaScript Architecture**

**Main Application Script (js/main.js):**
```javascript
/**
 * EventBooking Cameroon - Main Application Script
 * Handles global functionality and initialization
 */

class EventBookingApp {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeComponents();
        this.loadNotifications();
    }

    setupEventListeners() {
        // Global form validation
        document.querySelectorAll('form[data-validate]').forEach(form => {
            form.addEventListener('submit', this.validateForm.bind(this));
        });

        // Image lazy loading
        this.setupLazyLoading();

        // Smooth scrolling for anchor links
        this.setupSmoothScrolling();
    }

    validateForm(event) {
        const form = event.target;
        const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateInput(input)) {
                isValid = false;
            }
        });

        if (!isValid) {
            event.preventDefault();
            this.showValidationErrors(form);
        }
    }

    validateInput(input) {
        const value = input.value.trim();
        const type = input.type;

        // Basic required field validation
        if (input.hasAttribute('required') && !value) {
            this.showInputError(input, 'This field is required');
            return false;
        }

        // Email validation
        if (type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                this.showInputError(input, 'Please enter a valid email address');
                return false;
            }
        }

        // Phone validation (Cameroon format)
        if (input.name === 'phone' && value) {
            const phoneRegex = /^(\+237|237)?[2368]\d{8}$/;
            if (!phoneRegex.test(value.replace(/\s/g, ''))) {
                this.showInputError(input, 'Please enter a valid Cameroon phone number');
                return false;
            }
        }

        this.clearInputError(input);
        return true;
    }

    showInputError(input, message) {
        input.classList.add('is-invalid');

        let errorElement = input.parentNode.querySelector('.invalid-feedback');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'invalid-feedback';
            input.parentNode.appendChild(errorElement);
        }

        errorElement.textContent = message;
    }

    clearInputError(input) {
        input.classList.remove('is-invalid');
        const errorElement = input.parentNode.querySelector('.invalid-feedback');
        if (errorElement) {
            errorElement.remove();
        }
    }

    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        observer.unobserve(img);
                    }
                });
            });

            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    }

    async loadNotifications() {
        if (!document.getElementById('notificationDropdown')) return;

        try {
            const response = await fetch('user/check-notifications.php');
            const data = await response.json();

            if (data.success) {
                this.updateNotificationBadge(data.unread_count);
                this.updateNotificationList(data.notifications);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    }
}

// Initialize application when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new EventBookingApp();
});
```

---

## 👤 **User Manual**

### **Getting Started**

**Creating an Account:**
1. Visit the EventBooking Cameroon website
2. Click "Register" in the top navigation
3. Fill out the registration form with:
   - Username (unique identifier)
   - Email address (for notifications)
   - Full name
   - Phone number (Cameroon format: +237XXXXXXXXX)
   - Secure password
4. Click "Create Account"
5. Check your email for verification (if enabled)
6. Log in with your credentials

**Logging In:**
1. Click "Login" in the top navigation
2. Enter your username/email and password
3. Click "Sign In"
4. You'll be redirected to your dashboard

### **Browsing Events**

**Finding Events:**
1. **Homepage:** Featured events are displayed prominently
2. **Events Page:** Complete list of all available events
3. **Search:** Use the search bar to find specific events
4. **Filter:** Filter by category, date, or location
5. **Sort:** Sort by date, price, or popularity

**Event Information:**
- **Event Details:** Name, description, date, time
- **Venue Information:** Location with interactive map
- **Pricing:** Ticket prices and availability
- **Capacity:** Total and remaining tickets
- **Images:** Event photos and venue images

### **Booking Events**

**Making a Booking:**
1. Navigate to the event details page
2. Select the number of tickets (up to maximum allowed)
3. Review the total price
4. Click "Book Now"
5. Confirm your booking details
6. Proceed to payment
7. Receive booking confirmation

**Booking Process Flow:**
```
Event Selection → Quantity Selection → Review Details →
Payment → Confirmation → Email Receipt → Dashboard Update
```

**Payment Options:**
- Credit/Debit Cards
- Mobile Money (MTN, Orange)
- Bank Transfer
- Cash on Delivery (selected events)

### **Managing Your Bookings**

**Viewing Bookings:**
1. Log in to your account
2. Go to "My Dashboard" or "My Bookings"
3. View all your past and upcoming bookings
4. Filter by status: Confirmed, Pending, Cancelled

**Booking Details:**
- **Booking Reference:** Unique identifier for your booking
- **Event Information:** Date, time, venue
- **Ticket Quantity:** Number of tickets booked
- **Total Amount:** Amount paid
- **Status:** Current booking status
- **QR Code:** For event entry (when available)

**Downloading Tickets:**
1. Go to your bookings page
2. Find the confirmed booking
3. Click "Download Ticket" or "View QR Code"
4. Save or print your ticket
5. Present at event entrance

**Cancelling Bookings:**
1. Go to your bookings page
2. Find the booking you want to cancel
3. Click "Cancel Booking"
4. Confirm cancellation
5. Refund will be processed according to policy

### **Communication Features**

**Sending Messages:**
1. Go to "Contact Us" page
2. Fill out the contact form:
   - Subject line
   - Message type (General, Support, Booking, Complaint)
   - Detailed message
3. Submit the form
4. Receive confirmation with message ID

**Viewing Messages:**
1. Go to "My Messages" in your dashboard
2. View all your conversations with support
3. See message status: New, Read, Replied, Closed
4. Click on a message to view full conversation

**Replying to Messages:**
1. Open a message conversation
2. Scroll to the reply section
3. Type your response
4. Click "Send Reply"
5. Receive notification when admin responds

### **Profile Management**

**Updating Profile:**
1. Go to "My Profile" in your dashboard
2. Edit your information:
   - Full name
   - Email address
   - Phone number
   - Profile picture
3. Click "Update Profile"
4. Confirm changes

**Changing Password:**
1. Go to "My Profile"
2. Click "Change Password"
3. Enter current password
4. Enter new password (twice for confirmation)
5. Click "Update Password"

**Notification Preferences:**
1. Go to "My Profile"
2. Navigate to "Notification Settings"
3. Choose your preferences:
   - Email notifications
   - SMS notifications
   - Event reminders
   - Promotional emails
4. Save settings

### **Mobile Usage**

**Mobile Features:**
- Responsive design works on all devices
- Touch-friendly interface
- Swipe gestures for image galleries
- Mobile-optimized forms
- GPS integration for directions

**Mobile Booking:**
- Same functionality as desktop
- Optimized checkout process
- Mobile payment options
- Digital ticket storage
- Offline ticket viewing

### **Troubleshooting**

**Common Issues:**

**Login Problems:**
- Check username/email spelling
- Verify password (case-sensitive)
- Clear browser cache and cookies
- Try password reset if needed

**Booking Issues:**
- Ensure sufficient ticket availability
- Check payment method validity
- Verify internet connection
- Contact support if payment fails

**Notification Issues:**
- Check email spam folder
- Verify email address in profile
- Check notification preferences
- Ensure phone number is correct

**Getting Help:**
1. Check FAQ section
2. Use contact form for specific issues
3. Call support: +237 652 731 798
4. Email: nkumbelarry@gmail.com
5. Live chat (when available)

---

## 🛡️ **Admin Manual**

### **Admin Access**

**Logging into Admin Panel:**
1. Navigate to `/admin/` directory
2. Enter admin credentials
3. Access admin dashboard
4. Default login: admin / admin123 (change immediately)

**Admin Dashboard Overview:**
- **Statistics Cards:** Key metrics at a glance
- **Recent Activity:** Latest bookings and messages
- **Quick Actions:** Common administrative tasks
- **Navigation Menu:** Access to all admin functions

### **Event Management**

**Creating Events:**
1. Go to "Events" in admin menu
2. Click "Add New Event"
3. Fill out event form:
   - **Basic Info:** Name, description, category
   - **Date & Time:** Event date and start time
   - **Location:** Venue name and address
   - **Pricing:** Ticket price and capacity
   - **Images:** Upload event photos
4. Set event status (Active/Inactive)
5. Mark as featured (optional)
6. Save event

**Event Form Fields:**
```
Required Fields:
- Event Name (max 200 characters)
- Description (detailed event information)
- Date (YYYY-MM-DD format)
- Time (HH:MM format)
- Venue (location name)
- Location (full address)
- Price (in CFA)
- Capacity (maximum attendees)

Optional Fields:
- Category (Music, Sports, Business, etc.)
- Featured status
- Event image
- Additional notes
```

**Managing Events:**
1. **View All Events:** Complete list with filters
2. **Edit Events:** Modify event details
3. **Delete Events:** Remove events (with confirmation)
4. **Duplicate Events:** Copy event for similar events
5. **Bulk Actions:** Manage multiple events

**Event Status Management:**
- **Active:** Event is visible and bookable
- **Inactive:** Event is hidden from public
- **Cancelled:** Event is cancelled (bookings refunded)

### **Booking Management**

**Viewing Bookings:**
1. Go to "Bookings" in admin menu
2. View booking statistics:
   - Total bookings
   - Confirmed bookings
   - Cancelled bookings
   - Total revenue
3. Filter bookings by:
   - Status (Pending, Confirmed, Cancelled)
   - Date range
   - Event
   - User

**Booking Details:**
- **User Information:** Name, email, phone
- **Event Details:** Event name, date, venue
- **Booking Info:** Reference, quantity, amount
- **Payment Status:** Pending, Completed, Failed
- **Timestamps:** Created and updated dates

**Managing Bookings:**
1. **Confirm Bookings:** Approve pending bookings
2. **Cancel Bookings:** Cancel confirmed bookings
3. **Refund Processing:** Handle refund requests
4. **Update Status:** Change booking status
5. **Send Notifications:** Email booking updates

**Booking Actions:**
```
Available Actions:
- View booking details
- Confirm pending booking
- Cancel confirmed booking
- Process refund
- Send confirmation email
- Download booking report
- Export booking data
```

### **User Management**

**User Overview:**
1. Go to "Users" in admin menu
2. View user statistics and list
3. Search users by name, email, or username
4. Filter by status or registration date

**User Information:**
- **Profile Data:** Name, email, phone, username
- **Account Status:** Active, Inactive, Suspended
- **Registration Date:** When user joined
- **Last Login:** Recent activity
- **Booking History:** User's event bookings
- **Message History:** Support conversations

**User Management Actions:**
1. **View Profile:** Complete user information
2. **Edit User:** Modify user details
3. **Suspend Account:** Temporarily disable user
4. **Delete User:** Permanently remove user
5. **Reset Password:** Generate new password
6. **Send Message:** Direct communication

### **Message Management**

**Message Center:**
1. Go to "Messages" in admin menu
2. View message statistics:
   - Total messages
   - New messages
   - Replied messages
   - Urgent messages
3. Filter messages by:
   - Status (New, Read, Replied, Closed)
   - Priority (Low, Normal, High, Urgent)
   - Type (General, Support, Booking, Complaint)

**Handling Messages:**
1. **View Message:** Read full message content
2. **Reply to Message:** Send response to user
3. **Set Priority:** Mark as urgent or high priority
4. **Change Status:** Update message status
5. **Close Conversation:** Mark as resolved
6. **Forward Message:** Send to other admins

**Message Workflow:**
```
New Message → Read → Reply → User Response →
Further Discussion → Resolution → Close
```

**Reply Features:**
- Rich text editor for formatting
- File attachments (when needed)
- Template responses for common issues
- Auto-notification to user
- Conversation threading

### **Reports and Analytics**

**Dashboard Analytics:**
- **Booking Trends:** Daily, weekly, monthly statistics
- **Revenue Reports:** Income tracking and projections
- **Event Performance:** Popular events and attendance
- **User Activity:** Registration and engagement metrics

**Generating Reports:**
1. Go to "Reports" section
2. Select report type:
   - Booking reports
   - Revenue reports
   - User reports
   - Event reports
3. Choose date range
4. Apply filters
5. Generate and download report

**Export Options:**
- PDF reports for printing
- Excel files for analysis
- CSV data for external tools
- Email reports to stakeholders

### **System Administration**

**Admin Settings:**
1. **Site Configuration:** Basic site settings
2. **Email Settings:** SMTP configuration
3. **Payment Settings:** Payment gateway setup
4. **Security Settings:** Password policies, session timeout
5. **Backup Settings:** Automated backup configuration

**User Role Management:**
- **Super Admin:** Full system access
- **Admin:** Standard administrative functions
- **Moderator:** Limited management access
- **Support:** Message and user management only

**Security Features:**
1. **Activity Logging:** Track all admin actions
2. **Login Monitoring:** Failed login attempts
3. **Session Management:** Secure session handling
4. **Access Control:** Role-based permissions
5. **Audit Trail:** Complete action history

**Maintenance Tasks:**
1. **Database Cleanup:** Remove old data
2. **Log Management:** Archive and clean logs
3. **Cache Clearing:** Clear system cache
4. **Backup Verification:** Test backup integrity
5. **Security Updates:** Apply system updates

### **Best Practices**

**Daily Tasks:**
- Check new messages and respond promptly
- Review pending bookings for approval
- Monitor system alerts and errors
- Verify payment processing status

**Weekly Tasks:**
- Generate booking and revenue reports
- Review user feedback and complaints
- Update event information as needed
- Check system performance metrics

**Monthly Tasks:**
- Analyze booking trends and patterns
- Review and update system settings
- Perform security audits
- Plan promotional campaigns

**Security Guidelines:**
- Use strong, unique passwords
- Enable two-factor authentication
- Regularly update admin credentials
- Monitor for suspicious activity
- Keep system software updated
- Regular security backups

---

## 🔌 **API Documentation**

### **API Overview**

The EventBooking Cameroon system provides RESTful API endpoints for integration with external systems and mobile applications.

**Base URL:** `https://yourdomain.com/api/`
**Authentication:** Session-based or API key
**Response Format:** JSON
**HTTP Methods:** GET, POST, PUT, DELETE

### **Authentication Endpoints**

**User Login**
```http
POST /api/auth/login
Content-Type: application/json

{
    "username": "user@example.com",
    "password": "userpassword"
}

Response:
{
    "success": true,
    "message": "Login successful",
    "user": {
        "id": 1,
        "username": "john_doe",
        "email": "user@example.com",
        "full_name": "John Doe"
    },
    "session_token": "abc123xyz789"
}
```

**User Registration**
```http
POST /api/auth/register
Content-Type: application/json

{
    "username": "newuser",
    "email": "newuser@example.com",
    "password": "securepassword",
    "full_name": "New User",
    "phone": "+237123456789"
}

Response:
{
    "success": true,
    "message": "Registration successful",
    "user_id": 123
}
```

### **Event Endpoints**

**Get All Events**
```http
GET /api/events?category=music&date_from=2024-01-01&limit=10

Response:
{
    "success": true,
    "events": [
        {
            "id": 1,
            "name": "Music Festival 2024",
            "description": "Annual music festival...",
            "date": "2024-06-15",
            "time": "18:00:00",
            "venue": "Palais des Sports",
            "location": "Yaoundé, Cameroon",
            "price": 15000,
            "capacity": 500,
            "available_tickets": 250,
            "image": "uploads/events/festival.jpg",
            "category": "music"
        }
    ],
    "total": 1,
    "page": 1,
    "per_page": 10
}
```

**Get Event Details**
```http
GET /api/events/{id}

Response:
{
    "success": true,
    "event": {
        "id": 1,
        "name": "Music Festival 2024",
        "description": "Detailed description...",
        "date": "2024-06-15",
        "time": "18:00:00",
        "venue": "Palais des Sports",
        "location": "Yaoundé, Cameroon",
        "price": 15000,
        "capacity": 500,
        "available_tickets": 250,
        "image": "uploads/events/festival.jpg",
        "category": "music",
        "created_at": "2024-01-15 10:30:00"
    }
}
```

### **Booking Endpoints**

**Create Booking**
```http
POST /api/bookings
Content-Type: application/json
Authorization: Bearer {session_token}

{
    "event_id": 1,
    "quantity": 2,
    "payment_method": "card"
}

Response:
{
    "success": true,
    "message": "Booking created successfully",
    "booking": {
        "id": 456,
        "booking_reference": "BK20240115001",
        "event_id": 1,
        "quantity": 2,
        "total_amount": 30000,
        "status": "pending",
        "payment_status": "pending"
    }
}
```

**Get User Bookings**
```http
GET /api/bookings/user/{user_id}
Authorization: Bearer {session_token}

Response:
{
    "success": true,
    "bookings": [
        {
            "id": 456,
            "booking_reference": "BK20240115001",
            "event": {
                "name": "Music Festival 2024",
                "date": "2024-06-15",
                "venue": "Palais des Sports"
            },
            "quantity": 2,
            "total_amount": 30000,
            "status": "confirmed",
            "created_at": "2024-01-15 14:30:00"
        }
    ]
}
```

### **Message Endpoints**

**Send Message**
```http
POST /api/messages
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "subject": "Booking Inquiry",
    "message": "I have a question about my booking...",
    "inquiry_type": "booking"
}

Response:
{
    "success": true,
    "message": "Message sent successfully",
    "message_id": 789
}
```

### **Error Handling**

**Error Response Format:**
```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "Invalid input data",
        "details": {
            "email": "Invalid email format",
            "phone": "Phone number is required"
        }
    }
}
```

**HTTP Status Codes:**
- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

### **Rate Limiting**

API requests are limited to prevent abuse:
- **Authenticated users:** 1000 requests per hour
- **Anonymous users:** 100 requests per hour
- **Admin users:** 5000 requests per hour

Rate limit headers:
```http
X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1640995200
```

---

## 🔧 **Troubleshooting**

### **Common Issues and Solutions**

**Database Connection Issues**

*Problem:* "Database connection failed" error
*Causes:*
- Incorrect database credentials
- Database server not running
- Network connectivity issues
- Database server overloaded

*Solutions:*
1. Verify database credentials in `config/database.php`
2. Check if MySQL service is running: `sudo systemctl status mysql`
3. Test database connection: `mysql -u username -p database_name`
4. Check database server logs: `/var/log/mysql/error.log`
5. Verify network connectivity to database server

**Login/Authentication Problems**

*Problem:* Users cannot log in
*Causes:*
- Incorrect credentials
- Account suspended or inactive
- Session configuration issues
- Password hash mismatch

*Solutions:*
1. Verify user credentials in database
2. Check user status: `SELECT status FROM users WHERE email = 'user@example.com'`
3. Reset user password through admin panel
4. Clear browser cookies and cache
5. Check session configuration in `php.ini`

**Event Booking Failures**

*Problem:* Booking process fails or hangs
*Causes:*
- Insufficient ticket availability
- Payment gateway issues
- Database transaction failures
- Network timeouts

*Solutions:*
1. Check event availability: `SELECT available_tickets FROM events WHERE id = X`
2. Verify payment gateway configuration
3. Check database transaction logs
4. Increase PHP execution time limit
5. Monitor network connectivity

**Email Notification Issues**

*Problem:* Users not receiving emails
*Causes:*
- SMTP configuration errors
- Email marked as spam
- Invalid email addresses
- Mail server issues

*Solutions:*
1. Test SMTP configuration with test email
2. Check email logs: `/var/log/mail.log`
3. Verify email addresses in user profiles
4. Configure SPF, DKIM, and DMARC records
5. Use reputable email service provider

**Performance Issues**

*Problem:* Slow page loading or timeouts
*Causes:*
- Database query optimization needed
- Large image files
- Insufficient server resources
- Unoptimized code

*Solutions:*
1. Optimize database queries and add indexes
2. Compress and optimize images
3. Increase server memory and CPU
4. Enable caching (Redis, Memcached)
5. Use CDN for static assets

### **Debugging Tools**

**PHP Debugging:**
```php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Log debugging information
error_log("Debug: Variable value = " . print_r($variable, true));
```

**Database Debugging:**
```sql
-- Check slow queries
SHOW PROCESSLIST;

-- Analyze query performance
EXPLAIN SELECT * FROM events WHERE date >= '2024-01-01';

-- Check database size
SELECT
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
GROUP BY table_schema;
```

**JavaScript Debugging:**
```javascript
// Console debugging
console.log('Debug info:', variable);
console.error('Error occurred:', error);

// Network debugging
fetch('/api/events')
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => console.log('Data:', data))
    .catch(error => console.error('Fetch error:', error));
```

### **Log File Locations**

**System Logs:**
- Apache Error Log: `/var/log/apache2/error.log`
- Apache Access Log: `/var/log/apache2/access.log`
- PHP Error Log: `/var/log/php/error.log`
- MySQL Error Log: `/var/log/mysql/error.log`

**Application Logs:**
- Application Log: `logs/application.log`
- Email Log: `logs/email_log.txt`
- Debug Log: `logs/debug.log`
- Security Log: `logs/security.log`

### **Monitoring and Maintenance**

**Regular Maintenance Tasks:**

**Daily:**
- Check error logs for issues
- Monitor disk space usage
- Verify backup completion
- Check system performance

**Weekly:**
- Update system packages
- Optimize database tables
- Clean temporary files
- Review security logs

**Monthly:**
- Full system backup
- Security audit
- Performance analysis
- Update documentation

**Monitoring Commands:**
```bash
# Check disk usage
df -h

# Monitor memory usage
free -m

# Check running processes
top

# Monitor network connections
netstat -tulpn

# Check system load
uptime

# Monitor database connections
mysql -e "SHOW PROCESSLIST;"
```

### **Emergency Procedures**

**Site Down Emergency:**
1. Check server status and restart if needed
2. Verify database connectivity
3. Check DNS resolution
4. Review recent changes
5. Restore from backup if necessary

**Security Breach Response:**
1. Immediately change all passwords
2. Review access logs for suspicious activity
3. Update all software and plugins
4. Scan for malware
5. Notify users if data compromised

**Data Loss Recovery:**
1. Stop all write operations
2. Assess extent of data loss
3. Restore from most recent backup
4. Verify data integrity
5. Resume normal operations

### **Support Contacts**

**Technical Support:**
- Email: nkumbelarry@gmail.com
- Phone: +237 652 731 798
- Emergency: Available 24/7

**Documentation Updates:**
- GitHub Repository: [Project Repository]
- Wiki: [Documentation Wiki]
- Issue Tracker: [Bug Reports]

---

## 📚 **Additional Resources**

### **External Documentation**
- [PHP Official Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)
- [Leaflet.js Documentation](https://leafletjs.com/reference.html)

### **Development Tools**
- [Visual Studio Code](https://code.visualstudio.com/)
- [PHPStorm](https://www.jetbrains.com/phpstorm/)
- [MySQL Workbench](https://www.mysql.com/products/workbench/)
- [Postman](https://www.postman.com/) (API Testing)

### **Hosting Recommendations**
- **Shared Hosting:** Hostinger, Namecheap
- **VPS Hosting:** DigitalOcean, Linode
- **Cloud Hosting:** AWS, Google Cloud
- **CDN Services:** CloudFlare, MaxCDN

---

## 📄 **License and Credits**

### **License**
This project is licensed under the MIT License. See LICENSE file for details.

### **Credits**
- **Development Team:** EventBooking Cameroon Development Team
- **Design:** Bootstrap Framework and Custom CSS
- **Maps:** OpenStreetMap and Leaflet.js
- **Icons:** FontAwesome
- **Fonts:** Google Fonts

### **Third-Party Libraries**
- Bootstrap 5.1.3
- jQuery 3.6.0
- FontAwesome 6.0.0
- Leaflet.js 1.9.4
- Chart.js (for analytics)

---

## 📞 **Contact Information**

**Project Maintainer:**
- **Name:** EventBooking Cameroon Team
- **Email:** nkumbelarry@gmail.com
- **Phone:** +237 652 731 798
- **Address:** Avenue Kennedy, Plateau District, Yaoundé, Cameroon

**Business Hours:**
- Monday - Friday: 8:00 AM - 6:00 PM (WAT)
- Saturday: 9:00 AM - 4:00 PM (WAT)
- Sunday: Emergency support only

**Social Media:**
- Website: https://eventbooking-cameroon.com
- Facebook: @EventBookingCameroon
- Twitter: @EventBookingCM
- Instagram: @eventbooking_cameroon

---

*This documentation is maintained and updated regularly. Last updated: December 2024*

**Version:** 1.0.0
**Document Version:** 1.0
**Total Pages:** 50+
**Last Review:** December 2024
