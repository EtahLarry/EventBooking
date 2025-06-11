#!/bin/bash

# EventBooking Cameroon - GitHub Upload Script
# This script will upload all project files to GitHub repository

echo "🚀 EventBooking Cameroon - GitHub Upload Script"
echo "================================================"

# Check if git is installed
if ! command -v git &> /dev/null; then
    echo "❌ Git is not installed. Please install Git first."
    echo "   Download from: https://git-scm.com/downloads"
    exit 1
fi

# Repository URL
REPO_URL="https://github.com/EtahLarry/EventBooking.git"

echo "📁 Preparing project files for upload..."

# Initialize git repository if not already initialized
if [ ! -d ".git" ]; then
    echo "🔧 Initializing Git repository..."
    git init
fi

# Create .gitignore file
echo "📝 Creating .gitignore file..."
cat > .gitignore << 'EOF'
# Logs
logs/*.log
*.log

# Runtime data
pids
*.pid
*.seed
*.pid.lock

# Dependency directories
node_modules/
vendor/

# Environment variables
.env
.env.local
.env.development.local
.env.test.local
.env.production.local

# Database configuration (sensitive)
config/database.php

# Uploaded files (can be large)
uploads/*
!uploads/.gitkeep

# Cache files
cache/
tmp/

# IDE files
.vscode/
.idea/
*.swp
*.swo
*~

# OS generated files
.DS_Store
.DS_Store?
._*
.Spotlight-V100
.Trashes
ehthumbs.db
Thumbs.db

# Backup files
*.bak
*.backup
*.old

# Sensitive files
*.key
*.pem
*.p12
*.pfx

# Local development files
docker-compose.override.yml
.docker/

# PHP specific
composer.phar
/vendor/

# Test files
test-*.php
debug-*.php
EOF

# Create uploads directory structure with .gitkeep files
echo "📂 Creating directory structure..."
mkdir -p uploads/events
mkdir -p uploads/users
mkdir -p uploads/temp
mkdir -p logs

# Create .gitkeep files to preserve directory structure
touch uploads/.gitkeep
touch uploads/events/.gitkeep
touch uploads/users/.gitkeep
touch uploads/temp/.gitkeep
touch logs/.gitkeep

# Create sample database configuration file
echo "🔧 Creating sample configuration file..."
cat > config/database.sample.php << 'EOF'
<?php
/**
 * Database Configuration - Sample File
 * Copy this file to database.php and update with your actual database credentials
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventbooking_cameroon');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
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

// Email Settings
define('MAIL_FROM', 'noreply@eventbooking-cameroon.com');
define('MAIL_FROM_NAME', 'EventBooking Cameroon');

// Environment
define('ENVIRONMENT', 'development'); // development or production
define('DEBUG_MODE', true);
?>
EOF

# Add all files to git
echo "📦 Adding files to Git..."
git add .

# Check if there are any changes to commit
if git diff --staged --quiet; then
    echo "⚠️  No changes to commit. Repository might already be up to date."
else
    # Commit changes
    echo "💾 Committing changes..."
    git commit -m "Initial commit: EventBooking Cameroon - Complete Event Management System

Features:
- User registration and authentication
- Event browsing and booking system
- Admin panel for event management
- Messaging system between users and admins
- Interactive maps with OpenStreetMap
- Mobile-responsive design
- Cameroon localization
- Email notifications
- Secure payment processing
- Comprehensive documentation

Tech Stack:
- PHP 8+ with PDO
- MySQL database
- Bootstrap 5 frontend
- JavaScript/jQuery
- Leaflet.js for maps
- FontAwesome icons

Documentation:
- Complete installation guide
- User and admin manuals
- API documentation
- Troubleshooting guide
- Quick reference guide"
fi

# Add remote origin if not already added
if ! git remote get-url origin &> /dev/null; then
    echo "🔗 Adding remote repository..."
    git remote add origin $REPO_URL
fi

# Push to GitHub
echo "🚀 Uploading to GitHub..."
echo "Repository: $REPO_URL"

# Check if we need to set upstream
if ! git rev-parse --abbrev-ref --symbolic-full-name @{u} &> /dev/null; then
    echo "📤 Pushing to main branch..."
    git branch -M main
    git push -u origin main
else
    echo "📤 Pushing updates..."
    git push
fi

echo ""
echo "✅ Upload completed successfully!"
echo ""
echo "🌐 Your repository is now available at:"
echo "   $REPO_URL"
echo ""
echo "📋 Next steps:"
echo "   1. Visit your GitHub repository"
echo "   2. Update the README.md file if needed"
echo "   3. Set up GitHub Pages (optional)"
echo "   4. Configure repository settings"
echo "   5. Add collaborators if needed"
echo ""
echo "🔧 Local development:"
echo "   1. Copy config/database.sample.php to config/database.php"
echo "   2. Update database credentials"
echo "   3. Run database setup script"
echo "   4. Start local development server"
echo ""
echo "📚 Documentation files included:"
echo "   - PROJECT_DOCUMENTATION.md (Complete documentation)"
echo "   - INSTALLATION_GUIDE.md (Setup instructions)"
echo "   - QUICK_REFERENCE_GUIDE.md (Quick reference)"
echo "   - README.md (Project overview)"
echo ""
echo "🎉 EventBooking Cameroon is now on GitHub!"
EOF
