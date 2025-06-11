# 🚀 EventBooking Cameroon - Installation Guide

## 📋 **Prerequisites**

### **System Requirements**
- **Operating System:** Linux (Ubuntu 20.04+ recommended), Windows 10+, or macOS 10.15+
- **Web Server:** Apache 2.4+ or Nginx 1.18+
- **PHP:** Version 8.0 or higher with extensions:
  - PDO and PDO_MySQL
  - GD or ImageMagick
  - cURL
  - OpenSSL
  - Mbstring
  - JSON
  - Zip
- **Database:** MySQL 8.0+ or MariaDB 10.5+
- **Memory:** Minimum 512MB RAM (2GB+ recommended)
- **Storage:** Minimum 2GB free space (10GB+ recommended)

### **Development Tools (Optional)**
- Git for version control
- Composer for PHP dependencies
- Node.js and NPM for frontend tools
- Code editor (VS Code, PHPStorm, etc.)

---

## 🐧 **Linux Installation (Ubuntu/Debian)**

### **Step 1: Update System**
```bash
sudo apt update && sudo apt upgrade -y
```

### **Step 2: Install LAMP Stack**
```bash
# Install Apache
sudo apt install apache2 -y

# Install MySQL
sudo apt install mysql-server -y

# Install PHP and extensions
sudo apt install php8.1 php8.1-mysql php8.1-gd php8.1-curl php8.1-mbstring php8.1-zip php8.1-xml -y

# Install additional tools
sudo apt install git unzip curl -y
```

### **Step 3: Configure Apache**
```bash
# Enable mod_rewrite
sudo a2enmod rewrite
sudo a2enmod ssl
sudo a2enmod headers

# Restart Apache
sudo systemctl restart apache2

# Enable Apache to start on boot
sudo systemctl enable apache2
```

### **Step 4: Secure MySQL**
```bash
sudo mysql_secure_installation
```
Follow the prompts to:
- Set root password
- Remove anonymous users
- Disallow root login remotely
- Remove test database
- Reload privilege tables

### **Step 5: Create Database and User**
```bash
sudo mysql -u root -p
```

```sql
-- Create database
CREATE DATABASE eventbooking_cameroon CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user
CREATE USER 'eventbooking'@'localhost' IDENTIFIED BY 'secure_password_here';

-- Grant privileges
GRANT ALL PRIVILEGES ON eventbooking_cameroon.* TO 'eventbooking'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Exit MySQL
EXIT;
```

### **Step 6: Download and Install Application**
```bash
# Navigate to web directory
cd /var/www/html

# Clone repository (or upload files)
sudo git clone https://github.com/your-repo/eventbooking-cameroon.git

# Or download and extract ZIP
sudo wget https://github.com/your-repo/eventbooking-cameroon/archive/main.zip
sudo unzip main.zip
sudo mv eventbooking-cameroon-main eventbooking-cameroon

# Set ownership and permissions
sudo chown -R www-data:www-data eventbooking-cameroon/
sudo chmod -R 755 eventbooking-cameroon/
sudo chmod -R 777 eventbooking-cameroon/uploads/
sudo chmod -R 777 eventbooking-cameroon/logs/
```

---

## 🪟 **Windows Installation (WAMP)**

### **Step 1: Install WAMP Server**
1. Download WAMP from [wampserver.com](http://www.wampserver.com/)
2. Run installer as Administrator
3. Follow installation wizard
4. Start WAMP services

### **Step 2: Configure PHP**
1. Click WAMP icon in system tray
2. Go to PHP → PHP Extensions
3. Enable required extensions:
   - php_pdo_mysql
   - php_gd2
   - php_curl
   - php_mbstring
   - php_openssl

### **Step 3: Create Database**
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Create new database: `eventbooking_cameroon`
3. Set collation: `utf8mb4_unicode_ci`
4. Create user with full privileges

### **Step 4: Install Application**
1. Download project files
2. Extract to `C:\wamp64\www\eventbooking-cameroon\`
3. Set folder permissions (Full Control for IIS_IUSRS)

---

## 🍎 **macOS Installation (MAMP)**

### **Step 1: Install MAMP**
1. Download MAMP from [mamp.info](https://www.mamp.info/)
2. Install MAMP application
3. Start Apache and MySQL servers

### **Step 2: Configure Environment**
1. Open MAMP preferences
2. Set document root to `/Applications/MAMP/htdocs/`
3. Configure PHP version (8.0+)
4. Set MySQL port (default 8889)

### **Step 3: Setup Database**
1. Access phpMyAdmin via MAMP start page
2. Create database and user as described above

### **Step 4: Install Application**
1. Download and extract project files
2. Place in `/Applications/MAMP/htdocs/eventbooking-cameroon/`

---

## ⚙️ **Configuration**

### **Step 1: Database Configuration**
Create or edit `includes/config.php`:
```php
<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventbooking_cameroon');
define('DB_USER', 'eventbooking');
define('DB_PASS', 'your_secure_password');
define('DB_CHARSET', 'utf8mb4');

// Site Configuration
define('SITE_NAME', 'EventBooking Cameroon');
define('SITE_URL', 'http://localhost/eventbooking-cameroon');
define('ADMIN_EMAIL', 'nkumbelarry@gmail.com');
define('CONTACT_PHONE', '+237 652 731 798');

// Security Settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('PASSWORD_MIN_LENGTH', 8);
define('MAX_LOGIN_ATTEMPTS', 5);

// File Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('UPLOAD_PATH', 'uploads/');
define('ALLOWED_EXTENSIONS', 'jpg,jpeg,png,gif,pdf');

// Email Settings (for development)
define('MAIL_FROM', 'noreply@eventbooking-cameroon.com');
define('MAIL_FROM_NAME', 'EventBooking Cameroon');

// Environment
define('ENVIRONMENT', 'development'); // development or production
define('DEBUG_MODE', true);
?>
```

### **Step 2: Email Configuration (Optional)**
Create `includes/email-config.php`:
```php
<?php
// SMTP Configuration for production
define('SMTP_ENABLED', false); // Set to true for production
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');

// For development, emails are logged to logs/email_log.txt
?>
```

### **Step 3: Apache Configuration**
Create or edit `.htaccess` in project root:
```apache
RewriteEngine On

# Redirect to HTTPS (for production)
# RewriteCond %{HTTPS} off
# RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Pretty URLs
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^events/([0-9]+)/?$ event-details.php?id=$1 [L,QSA]

# Security headers
<IfModule mod_headers.c>
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>

# Prevent access to sensitive files
<Files "config.php">
    Order allow,deny
    Deny from all
</Files>

<Files "*.log">
    Order allow,deny
    Deny from all
</Files>

# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/json
</IfModule>

# Cache static files
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/x-javascript "access plus 1 month"
</IfModule>
```

---

## 🗄️ **Database Setup**

### **Step 1: Run Database Migration**
```bash
# Navigate to project directory
cd /var/www/html/eventbooking-cameroon

# Run database setup script
php setup/install.php
```

### **Step 2: Manual Database Setup (Alternative)**
If the install script doesn't work, manually import SQL files:

```bash
# Import database schema
mysql -u eventbooking -p eventbooking_cameroon < database/schema.sql

# Import sample data (optional)
mysql -u eventbooking -p eventbooking_cameroon < database/sample_data.sql
```

### **Step 3: Verify Database Setup**
```sql
-- Connect to database
mysql -u eventbooking -p eventbooking_cameroon

-- Check tables
SHOW TABLES;

-- Verify admin user
SELECT * FROM admin_users;

-- Check sample events
SELECT * FROM events LIMIT 5;
```

---

## 🧪 **Testing Installation**

### **Step 1: Access Application**
1. Open web browser
2. Navigate to: `http://localhost/eventbooking-cameroon/`
3. Verify homepage loads correctly

### **Step 2: Test User Registration**
1. Click "Register" link
2. Fill out registration form
3. Submit and verify account creation

### **Step 3: Test Admin Panel**
1. Navigate to: `http://localhost/eventbooking-cameroon/admin/`
2. Login with default credentials:
   - Username: `admin`
   - Password: `admin123`
3. **IMPORTANT:** Change password immediately!

### **Step 4: Test Event Booking**
1. Browse to events page
2. Select an event
3. Test booking process
4. Verify booking appears in admin panel

### **Step 5: Test Contact Form**
1. Go to contact page
2. Submit a message
3. Check if message appears in admin messages

---

## 🔒 **Security Setup**

### **Step 1: Change Default Passwords**
```sql
-- Change admin password
UPDATE admin_users 
SET password = '$2y$10$your_new_hashed_password' 
WHERE username = 'admin';
```

### **Step 2: Set Secure File Permissions**
```bash
# Set proper ownership
sudo chown -R www-data:www-data /var/www/html/eventbooking-cameroon/

# Set directory permissions
sudo find /var/www/html/eventbooking-cameroon/ -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/html/eventbooking-cameroon/ -type f -exec chmod 644 {} \;

# Set writable directories
sudo chmod -R 777 /var/www/html/eventbooking-cameroon/uploads/
sudo chmod -R 777 /var/www/html/eventbooking-cameroon/logs/
```

### **Step 3: Configure Firewall (Linux)**
```bash
# Enable UFW
sudo ufw enable

# Allow SSH
sudo ufw allow ssh

# Allow HTTP and HTTPS
sudo ufw allow 80
sudo ufw allow 443

# Check status
sudo ufw status
```

---

## 🚀 **Production Deployment**

### **Step 1: SSL Certificate**
```bash
# Install Certbot
sudo apt install certbot python3-certbot-apache

# Get SSL certificate
sudo certbot --apache -d yourdomain.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

### **Step 2: Production Configuration**
Update `includes/config.php`:
```php
// Environment
define('ENVIRONMENT', 'production');
define('DEBUG_MODE', false);
define('SITE_URL', 'https://yourdomain.com');

// Enable SMTP
define('SMTP_ENABLED', true);
```

### **Step 3: Performance Optimization**
```bash
# Enable OPcache
echo "opcache.enable=1" | sudo tee -a /etc/php/8.1/apache2/php.ini

# Restart Apache
sudo systemctl restart apache2
```

---

## 🔧 **Troubleshooting**

### **Common Issues**

**"Database connection failed"**
- Check database credentials in `config.php`
- Verify MySQL service is running
- Test database connection manually

**"Permission denied" errors**
- Check file ownership and permissions
- Ensure web server can write to uploads/ and logs/

**"Page not found" errors**
- Verify .htaccess file exists
- Check if mod_rewrite is enabled
- Verify Apache configuration

**PHP errors**
- Check PHP error logs
- Verify required extensions are installed
- Check PHP version compatibility

---

## 📞 **Support**

If you encounter issues during installation:

1. Check the troubleshooting section above
2. Review log files for error messages
3. Consult the full documentation
4. Contact support:
   - Email: nkumbelarry@gmail.com
   - Phone: +237 652 731 798

---

**Installation complete! Your EventBooking Cameroon system is ready to use.**

*Remember to change default passwords and configure security settings for production use.*
