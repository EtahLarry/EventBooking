# 🚀 EventBooking Cameroon - Quick Reference Guide

## 📋 **Quick Start Checklist**

### **For Developers**
- [ ] Clone repository
- [ ] Set up LAMP/WAMP stack
- [ ] Configure database connection
- [ ] Run database migrations
- [ ] Set file permissions
- [ ] Test installation
- [ ] Configure email settings
- [ ] Set up SSL certificate

### **For Administrators**
- [ ] Access admin panel (/admin/)
- [ ] Change default admin password
- [ ] Configure site settings
- [ ] Create first event
- [ ] Test booking process
- [ ] Set up payment gateway
- [ ] Configure email notifications
- [ ] Review security settings

### **For Users**
- [ ] Register account
- [ ] Verify email (if required)
- [ ] Browse available events
- [ ] Make first booking
- [ ] Download tickets
- [ ] Set notification preferences
- [ ] Update profile information

---

## 🔗 **Important URLs**

### **Public Pages**
- **Homepage:** `/index.php`
- **Events:** `/events.php`
- **Event Details:** `/event-details.php?id={ID}`
- **Contact:** `/contact.php`
- **Login:** `/login.php`
- **Register:** `/register.php`

### **User Dashboard**
- **Dashboard:** `/user/dashboard.php`
- **Profile:** `/user/profile.php`
- **Bookings:** `/user/bookings.php`
- **Messages:** `/user/messages.php`

### **Admin Panel**
- **Admin Login:** `/admin/`
- **Dashboard:** `/admin/dashboard.php`
- **Events:** `/admin/events.php`
- **Bookings:** `/admin/bookings.php`
- **Users:** `/admin/users.php`
- **Messages:** `/admin/messages.php`

---

## 🔑 **Default Credentials**

### **Admin Access**
```
Username: admin
Password: admin123
URL: /admin/
```
⚠️ **Change immediately after installation!**

### **Database**
```
Host: localhost
Database: eventbooking_cameroon
Username: eventbooking
Password: [set during installation]
```

### **Test User Account**
```
Username: john_doe
Password: password123
Email: john@example.com
```

---

## 📁 **File Structure Quick Reference**

```
eventbooking-cameroon/
├── 📁 admin/              # Admin panel
├── 📁 assets/             # CSS, JS, Images
├── 📁 includes/           # Core functions
├── 📁 user/               # User dashboard
├── 📁 api/                # API endpoints
├── 📁 uploads/            # User uploads
├── 📁 logs/               # Application logs
├── 📄 index.php           # Homepage
├── 📄 events.php          # Events listing
├── 📄 contact.php         # Contact form
├── 📄 login.php           # User login
└── 📄 .htaccess           # Apache config
```

---

## 🗄️ **Database Tables**

### **Core Tables**
- `users` - User accounts
- `events` - Event information
- `bookings` - Ticket bookings
- `admin_users` - Admin accounts

### **Communication Tables**
- `messages` - Contact messages
- `message_replies` - Message responses
- `user_notifications` - User notifications

### **System Tables**
- `admin_activity_log` - Admin actions
- `sessions` - User sessions

---

## ⚙️ **Configuration Files**

### **Database Configuration**
```php
// includes/config.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventbooking_cameroon');
define('DB_USER', 'eventbooking');
define('DB_PASS', 'your_password');
```

### **Email Configuration**
```php
// includes/email-config.php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-app-password');
```

### **Site Configuration**
```php
// includes/site-config.php
define('SITE_NAME', 'EventBooking Cameroon');
define('SITE_URL', 'https://yourdomain.com');
define('ADMIN_EMAIL', 'nkumbelarry@gmail.com');
define('CONTACT_PHONE', '+237 652 731 798');
```

---

## 🛠️ **Common Commands**

### **Database Operations**
```sql
-- Check database size
SELECT table_schema AS 'Database', 
       ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables 
WHERE table_schema = 'eventbooking_cameroon';

-- Reset admin password
UPDATE admin_users SET password = '$2y$10$hash_here' WHERE username = 'admin';

-- Check recent bookings
SELECT * FROM bookings ORDER BY created_at DESC LIMIT 10;

-- Get event statistics
SELECT status, COUNT(*) as count FROM events GROUP BY status;
```

### **File Operations**
```bash
# Set proper permissions
sudo chown -R www-data:www-data /var/www/html/eventbooking-cameroon/
sudo chmod -R 755 /var/www/html/eventbooking-cameroon/
sudo chmod -R 777 /var/www/html/eventbooking-cameroon/uploads/

# Check disk usage
du -sh /var/www/html/eventbooking-cameroon/

# View error logs
tail -f /var/log/apache2/error.log
tail -f logs/application.log
```

### **System Monitoring**
```bash
# Check Apache status
sudo systemctl status apache2

# Check MySQL status
sudo systemctl status mysql

# Monitor system resources
htop
df -h
free -m
```

---

## 🔧 **Troubleshooting Quick Fixes**

### **Common Issues**

**"Database connection failed"**
```bash
# Check MySQL service
sudo systemctl restart mysql
# Verify credentials in config.php
# Test connection: mysql -u username -p database_name
```

**"Permission denied" errors**
```bash
# Fix file permissions
sudo chown -R www-data:www-data /var/www/html/eventbooking-cameroon/
sudo chmod -R 755 /var/www/html/eventbooking-cameroon/
```

**"Page not found" errors**
```bash
# Enable mod_rewrite
sudo a2enmod rewrite
sudo systemctl restart apache2
# Check .htaccess file exists
```

**Email not sending**
```php
// Test SMTP settings in includes/email-config.php
// Check logs/email_log.txt for development mode
// Verify SMTP credentials and ports
```

---

## 📊 **Performance Optimization**

### **Database Optimization**
```sql
-- Add indexes for better performance
CREATE INDEX idx_events_date ON events(date);
CREATE INDEX idx_bookings_user ON bookings(user_id);
CREATE INDEX idx_messages_status ON messages(status);

-- Optimize tables
OPTIMIZE TABLE events, bookings, users, messages;
```

### **File Optimization**
```bash
# Compress images
find uploads/ -name "*.jpg" -exec jpegoptim --max=85 {} \;

# Enable Gzip compression in .htaccess
echo "
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/javascript
</IfModule>
" >> .htaccess
```

---

## 🔒 **Security Checklist**

### **Essential Security Steps**
- [ ] Change default admin password
- [ ] Update all default credentials
- [ ] Enable HTTPS/SSL
- [ ] Set proper file permissions
- [ ] Configure firewall rules
- [ ] Enable security headers
- [ ] Regular security updates
- [ ] Monitor access logs

### **Security Headers (.htaccess)**
```apache
# Security headers
Header always set X-Content-Type-Options nosniff
Header always set X-Frame-Options DENY
Header always set X-XSS-Protection "1; mode=block"
Header always set Strict-Transport-Security "max-age=31536000"
Header always set Content-Security-Policy "default-src 'self'"
```

---

## 📞 **Emergency Contacts**

### **Technical Support**
- **Email:** nkumbelarry@gmail.com
- **Phone:** +237 652 731 798
- **Hours:** Monday-Friday 8AM-6PM WAT

### **Hosting Support**
- Check with your hosting provider
- Keep hosting credentials accessible
- Document server specifications

---

## 📚 **Quick Links**

### **Documentation**
- [Full Documentation](PROJECT_DOCUMENTATION.md)
- [Installation Guide](INSTALLATION.md)
- [User Manual](USER_MANUAL.md)
- [Admin Manual](ADMIN_MANUAL.md)

### **External Resources**
- [PHP Documentation](https://php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Bootstrap Documentation](https://getbootstrap.com/docs/)

---

## 🎯 **Success Metrics**

### **Key Performance Indicators**
- **User Registration Rate:** Target 50+ new users/month
- **Event Booking Rate:** Target 80% of events booked
- **User Satisfaction:** Target 4.5+ star rating
- **System Uptime:** Target 99.9% availability
- **Page Load Speed:** Target <3 seconds
- **Mobile Usage:** Target 60%+ mobile traffic

### **Monitoring Tools**
- Google Analytics for traffic
- Server monitoring for uptime
- Database monitoring for performance
- User feedback for satisfaction

---

*This quick reference guide provides essential information for rapid deployment and troubleshooting. For detailed information, refer to the complete project documentation.*

**Last Updated:** December 2024  
**Version:** 1.0.0
