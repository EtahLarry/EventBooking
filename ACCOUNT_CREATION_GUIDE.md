# 🔐 Account Creation & Login Guide

## 🎯 **Problem Solved!**

You can now create your own custom accounts with your preferred email and password! I've created multiple tools to help you manage accounts.

---

## 🛠️ **Account Creation Tools**

### **1. User Registration Page** 
- **URL:** http://localhost:8080/register.php
- **Purpose:** Create regular user accounts for booking events
- **Features:** Full registration form with validation

### **2. Admin Account Creator**
- **URL:** http://localhost:8080/admin/create-admin.php  
- **Purpose:** Create administrator accounts
- **Features:** Admin-specific fields and role selection

### **3. Universal Account Creator** ⭐ **RECOMMENDED**
- **URL:** http://localhost:8080/create-account.php
- **Purpose:** Create both user and admin accounts in one tool
- **Features:** Switch between user/admin creation modes

---

## 🚀 **Quick Start - Create Your Accounts**

### **Step 1: Create Your User Account**
1. **Go to:** http://localhost:8080/create-account.php?type=user
2. **Fill in your details:**
   - Username: `your_username`
   - Email: `your_email@example.com`
   - Password: `your_secure_password`
   - First Name: `Your First Name`
   - Last Name: `Your Last Name`
   - Phone: `your_phone_number` (optional)
3. **Click:** "Create User Account"
4. **Success!** You can now login at http://localhost:8080/login.php

### **Step 2: Create Your Admin Account**
1. **Go to:** http://localhost:8080/create-account.php?type=admin
2. **Fill in your details:**
   - Username: `your_admin_username`
   - Email: `your_admin_email@example.com`
   - Password: `your_admin_password`
   - Full Name: `Your Full Name`
   - Role: `Admin` or `Super Admin`
3. **Click:** "Create Admin Account"
4. **Success!** You can now login at http://localhost:8080/admin/index.php

---

## 📋 **All Available Login Methods**

### **User Login:**
- **URL:** http://localhost:8080/login.php
- **Demo Account:** `john_doe` / `password123`
- **Your Account:** Use credentials you created above

### **Admin Login:**
- **URL:** http://localhost:8080/admin/index.php
- **Demo Account:** `admin` / `admin123`
- **Your Account:** Use admin credentials you created above

---

## 🔍 **Account Creation Features**

### **User Accounts Include:**
- ✅ Username and email validation
- ✅ Password strength requirements (min 6 characters)
- ✅ First name, last name, phone number
- ✅ Automatic password hashing for security
- ✅ Duplicate username/email checking

### **Admin Accounts Include:**
- ✅ Username and email validation
- ✅ Password strength requirements
- ✅ Full name and role selection
- ✅ Admin or Super Admin roles
- ✅ Secure password hashing
- ✅ Separate admin user table

### **Security Features:**
- ✅ Password hashing with PHP's `password_hash()`
- ✅ SQL injection protection with prepared statements
- ✅ Input sanitization and validation
- ✅ Duplicate account prevention
- ✅ Password confirmation matching

---

## 🗄️ **Database Storage**

### **User Accounts Stored In:**
- **Table:** `users`
- **Fields:** id, username, email, password, first_name, last_name, phone, address, created_at, updated_at

### **Admin Accounts Stored In:**
- **Table:** `admin_users`  
- **Fields:** id, username, email, password, full_name, role, created_at

### **Check Your Accounts:**
You can verify your accounts were created by accessing phpMyAdmin:
- **URL:** http://localhost:8081
- **Login:** root / rootpassword
- **Database:** event_booking_system
- **Tables:** users, admin_users

---

## 🎨 **Account Creator Features**

### **Universal Account Creator** (`create-account.php`)
- **Smart Interface:** Toggle between user/admin creation
- **Real-time Validation:** Password matching, email format
- **Visual Feedback:** Success/error messages
- **Responsive Design:** Works on all devices
- **Password Visibility:** Toggle to show/hide passwords

### **Registration Page** (`register.php`)
- **Full User Registration:** Complete user profile creation
- **Password Strength Meter:** Visual password strength indicator
- **Terms Acceptance:** Terms of service checkbox
- **Auto-focus:** Smooth form navigation

### **Admin Creator** (`admin/create-admin.php`)
- **Admin-focused Design:** Security-themed interface
- **Role Selection:** Admin vs Super Admin
- **Security Notices:** Admin-specific warnings
- **Professional Styling:** Matches admin panel design

---

## 🔧 **Troubleshooting**

### **"Username or email already exists"**
- **Solution:** Choose a different username or email
- **Check:** Verify in phpMyAdmin if account already exists

### **"Database error"**
- **Solution:** Ensure Docker containers are running
- **Check:** `docker-compose ps` to verify all services are up

### **"Invalid username/email or password" during login**
- **Solution:** Double-check your credentials
- **Verify:** Account was created successfully
- **Try:** Use the exact username/email you registered with

### **Can't access account creation pages**
- **Check:** Docker containers are running
- **Verify:** Web server is accessible at http://localhost:8080
- **Restart:** `docker-compose restart` if needed

---

## 📱 **Mobile-Friendly**

All account creation tools are fully responsive and work perfectly on:
- ✅ Desktop computers
- ✅ Tablets
- ✅ Mobile phones
- ✅ All modern browsers

---

## 🎊 **You're All Set!**

### **What You Can Do Now:**
1. **Create your personal user account** for booking events
2. **Create your admin account** for managing the system
3. **Login with your custom credentials** instead of demo accounts
4. **Manage your Event Booking System** with your own accounts

### **Next Steps:**
1. **Create your accounts** using the tools above
2. **Test login** with your new credentials
3. **Explore the system** with your personal accounts
4. **Start booking events** or managing the admin panel

**Your Event Booking System now supports custom account creation! 🚀**
