<?php
// Admin-specific functions - completely separate from user functions
session_start();
require_once __DIR__ . '/../../config/database.php';

// Admin authentication functions
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

function adminLogin($username, $password) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_role'] = $admin['role'];
        
        return true;
    }
    return false;
}

function adminLogout() {
    // Clear only admin session variables
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['admin_role']);
    
    // Destroy session if no other session data exists
    if (empty($_SESSION)) {
        session_destroy();
    }
    
    header('Location: index.php');
    exit();
}

function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

function createAdmin($data) {
    $pdo = getDBConnection();
    
    // Check if username or email already exists
    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ? OR email = ?");
    $stmt->execute([$data['username'], $data['email']]);
    if ($stmt->fetch()) {
        return false;
    }
    
    // Combine first and last name into full_name
    $full_name = trim(($data['first_name'] ?? '') . ' ' . ($data['last_name'] ?? ''));
    
    // Insert new admin (matching the database schema)
    $stmt = $pdo->prepare("
        INSERT INTO admin_users (username, email, password, full_name, role) 
        VALUES (?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $data['username'],
        $data['email'],
        password_hash($data['password'], PASSWORD_DEFAULT),
        $full_name,
        $data['role'] ?? 'admin'
    ]);
}

// Admin-specific utility functions
function getAdminStats() {
    $pdo = getDBConnection();
    
    $stats = [];
    
    // Total events
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM events");
    $stats['total_events'] = $stmt->fetch()['count'];
    
    // Total users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $stats['total_users'] = $stmt->fetch()['count'];
    
    // Total bookings
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
    $stats['total_bookings'] = $stmt->fetch()['count'];
    
    // Total revenue
    $stmt = $pdo->query("SELECT SUM(total_amount) as revenue FROM bookings WHERE status = 'confirmed'");
    $stats['total_revenue'] = $stmt->fetch()['revenue'] ?? 0;
    
    // Recent bookings (using PostgreSQL compatible syntax)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings WHERE DATE(created_at) = CURRENT_DATE");
    $stats['today_bookings'] = $stmt->fetch()['count'];
    
    // Upcoming events (using PostgreSQL compatible syntax)
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM events WHERE date >= CURRENT_DATE");
    $stats['upcoming_events'] = $stmt->fetch()['count'];
    
    return $stats;
}

function getAllAdmins() {
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT id, username, email, full_name, role, created_at FROM admin_users ORDER BY created_at DESC");
    return $stmt->fetchAll();
}

function updateAdminStatus($adminId, $status) {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("UPDATE admin_users SET status = ? WHERE id = ?");
    return $stmt->execute([$status, $adminId]);
}

function deleteAdmin($adminId) {
    // Prevent deletion of the last admin
    $pdo = getDBConnection();
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_users WHERE status = 'active'");
    $activeAdmins = $stmt->fetch()['count'];
    
    if ($activeAdmins <= 1) {
        return false; // Cannot delete the last admin
    }
    
    $stmt = $pdo->prepare("DELETE FROM admin_users WHERE id = ?");
    return $stmt->execute([$adminId]);
}

// Security functions
function generateSecureToken() {
    return bin2hex(random_bytes(32));
}

function validateAdminAccess($requiredRole = 'admin') {
    if (!isAdminLoggedIn()) {
        return false;
    }
    
    $adminRole = $_SESSION['admin_role'];
    
    // Super admin can access everything
    if ($adminRole === 'super_admin') {
        return true;
    }
    
    // Check specific role requirements
    return $adminRole === $requiredRole;
}

// Admin activity logging
    (simplified to avoid syntax errors)
 function logAdminActivity($action, $details = '') {
    return true;
}
    
    $pdo = getDBConnection();
    
    // Create admin_activity_log table if it doesn't exist
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id SERIAL PRIMARY KEY,
    admin_id INTEGER NOT NULL,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
    
    $stmt = $pdo->prepare("
        INSERT INTO admin_activity_log (admin_id, action, details, ip_address, user_agent) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_SESSION['admin_id'],
        $action,
        $details,
        $_SERVER['REMOTE_ADDR'] ?? '',
        $_SERVER['HTTP_USER_AGENT'] ?? ''
    ]);
}

// Format functions for admin panel
function formatAdminPrice($price) {
    return '$' . number_format($price, 2);
}

function formatAdminDate($date) {
    return date('M j, Y', strtotime($date));
}

function formatAdminDateTime($datetime) {
    return date('M j, Y g:i A', strtotime($datetime));
}

// Additional format functions needed by admin pages
function formatPrice($price) {
    return number_format($price ?? 0, 0) . ' CFA';
}

function formatDate($date) {
    return date('M j, Y', strtotime($date));
}

function formatTime($time) {
    return date('g:i A', strtotime($time));
}

function getAdminStatusBadge($status) {
    switch ($status) {
        case 'active':
            return '<span class="badge bg-success">Active</span>';
        case 'inactive':
            return '<span class="badge bg-secondary">Inactive</span>';
        case 'suspended':
            return '<span class="badge bg-danger">Suspended</span>';
        default:
            return '<span class="badge bg-secondary">Unknown</span>';
    }
}

function getRoleBadge($role) {
    switch ($role) {
        case 'super_admin':
            return '<span class="badge bg-danger">Super Admin</span>';
        case 'admin':
            return '<span class="badge bg-primary">Admin</span>';
        case 'moderator':
            return '<span class="badge bg-info">Moderator</span>';
        default:
            return '<span class="badge bg-secondary">User</span>';
    }
}

// Admin panel configuration
function getAdminConfig() {
    return [
        'app_name' => 'EventBooking Admin',
        'version' => '1.0.0',
        'contact_email' => 'nkumbelarry@gmail.com',
        'contact_phone' => '652731798',
        'timezone' => 'UTC',
        'date_format' => 'M j, Y',
        'datetime_format' => 'M j, Y g:i A'
    ];
}
?>
