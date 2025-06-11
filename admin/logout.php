<?php
require_once 'includes/admin-functions.php';

// Log admin logout activity
if (isAdminLoggedIn()) {
    logAdminActivity('Admin Logout', 'User logged out');
}

// Perform admin logout
adminLogout();
?>
