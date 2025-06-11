<?php
require_once 'includes/functions.php';

// Set logout message
$_SESSION['success_message'] = 'You have been logged out successfully.';

// Destroy session and redirect to login
session_destroy();
header('Location: login.php');
exit();
?>
