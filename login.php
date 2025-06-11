<?php
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Login';
$error = '';
$success = '';
$mode = $_GET['mode'] ?? 'login'; // login or register

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'login';

    if ($action === 'login') {
        // Handle login
        $username = sanitizeInput($_POST['username']);
        $password = $_POST['password'];

        if (empty($username) || empty($password)) {
            $error = 'Please fill in all fields.';
        } else {
            if (login($username, $password)) {
                $_SESSION['success_message'] = 'Welcome back! You have been logged in successfully.';

                // Redirect to intended page or homepage
                $redirect = $_GET['redirect'] ?? 'index.php';
                header('Location: ' . $redirect);
                exit();
            } else {
                $error = 'Invalid username/email or password.';
            }
        }
    } elseif ($action === 'register') {
        // Handle registration
        $username = sanitizeInput($_POST['username']);
        $email = sanitizeInput($_POST['email']);
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];
        $firstName = sanitizeInput($_POST['first_name']);
        $lastName = sanitizeInput($_POST['last_name']);
        $phone = sanitizeInput($_POST['phone']);

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($firstName) || empty($lastName)) {
            $error = 'Please fill in all required fields.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Please enter a valid email address.';
        } elseif (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters long.';
        } elseif ($password !== $confirmPassword) {
            $error = 'Passwords do not match.';
        } else {
            try {
                $data = [
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'address' => ''
                ];

                if (register($data)) {
                    $success = 'Account created successfully! You can now login with your credentials.';
                    $mode = 'login'; // Switch to login mode
                    $_POST = []; // Clear form data
                } else {
                    $error = 'Username or email already exists. Please choose different ones.';
                }
            } catch (Exception $e) {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<style>
/* Professional Animated Login Page Styles */
body {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    overflow-x: hidden;
}

/* Beautiful CSS Background Patterns */
.login-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -3;
    background:
        linear-gradient(135deg, rgba(102, 126, 234, 0.9) 0%, rgba(118, 75, 162, 0.9) 100%),
        radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 70% 80%, rgba(102, 126, 234, 0.2) 0%, transparent 50%),
        repeating-linear-gradient(45deg, transparent, transparent 2px, rgba(255,255,255,0.03) 2px, rgba(255,255,255,0.03) 4px);
    animation: backgroundZoom 30s ease-in-out infinite;
}

.login-background::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background:
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
        repeating-conic-gradient(from 0deg at 50% 50%, transparent 0deg, rgba(255,255,255,0.05) 15deg, transparent 30deg),
        url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    animation: backgroundMove 20s ease-in-out infinite;
}

/* Background Image Animations */
@keyframes backgroundZoom {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Secondary Background Layer */
.login-background::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background:
        radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.15) 0%, transparent 50%),
        radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.15) 0%, transparent 50%);
    animation: backgroundFloat 25s ease-in-out infinite reverse;
}

@keyframes backgroundFloat {
    0%, 100% { transform: translateY(0px) scale(1); }
    50% { transform: translateY(-20px) scale(1.02); }
}

@keyframes backgroundMove {
    0%, 100% { transform: translateX(0) translateY(0); }
    25% { transform: translateX(-10px) translateY(-10px); }
    50% { transform: translateX(10px) translateY(-5px); }
    75% { transform: translateX(-5px) translateY(10px); }
}

/* Additional Background Layers */
.background-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -2;
    background:
        radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 70% 80%, rgba(102, 126, 234, 0.15) 0%, transparent 50%),
        linear-gradient(45deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
    animation: overlayShift 40s ease-in-out infinite;
}

@keyframes overlayShift {
    0%, 100% { transform: rotate(0deg) scale(1); }
    50% { transform: rotate(1deg) scale(1.02); }
}

.background-shapes {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: -1;
    background:
        conic-gradient(from 0deg at 50% 50%, rgba(102, 126, 234, 0.1) 0deg, transparent 60deg, rgba(118, 75, 162, 0.1) 120deg, transparent 180deg),
        radial-gradient(circle at 25% 75%, rgba(255, 255, 255, 0.05) 0%, transparent 50%),
        repeating-linear-gradient(90deg, transparent, transparent 20px, rgba(255,255,255,0.02) 20px, rgba(255,255,255,0.02) 40px),
        repeating-linear-gradient(0deg, transparent, transparent 20px, rgba(102,126,234,0.02) 20px, rgba(102,126,234,0.02) 40px);
    animation: shapesFloat 35s ease-in-out infinite;
}

@keyframes shapesFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-15px) rotate(1deg); }
    66% { transform: translateY(10px) rotate(-1deg); }
}

/* Enhanced Floating Particles */
.particles {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    pointer-events: none;
}

.particle {
    position: absolute;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.1) 50%, transparent 100%);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
}

.particle:nth-child(1) { width: 4px; height: 4px; left: 10%; animation-delay: 0s; }
.particle:nth-child(2) { width: 6px; height: 6px; left: 20%; animation-delay: 1s; }
.particle:nth-child(3) { width: 3px; height: 3px; left: 30%; animation-delay: 2s; }
.particle:nth-child(4) { width: 5px; height: 5px; left: 40%; animation-delay: 3s; }
.particle:nth-child(5) { width: 4px; height: 4px; left: 50%; animation-delay: 4s; }
.particle:nth-child(6) { width: 7px; height: 7px; left: 60%; animation-delay: 5s; }
.particle:nth-child(7) { width: 3px; height: 3px; left: 70%; animation-delay: 0.5s; }
.particle:nth-child(8) { width: 5px; height: 5px; left: 80%; animation-delay: 1.5s; }
.particle:nth-child(9) { width: 4px; height: 4px; left: 90%; animation-delay: 2.5s; }

@keyframes float {
    0%, 100% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
    10% { opacity: 1; }
    90% { opacity: 1; }
    100% { transform: translateY(-100px) rotate(360deg); opacity: 0; }
}

/* Main Container */
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    z-index: 1;
}

/* Professional Card Design */
.form-container {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(25px) saturate(180%);
    border-radius: 24px;
    box-shadow:
        0 32px 64px rgba(0, 0, 0, 0.2),
        0 0 0 1px rgba(255, 255, 255, 0.3),
        inset 0 1px 0 rgba(255, 255, 255, 0.4);
    padding: 0;
    overflow: hidden;
    max-width: 480px;
    width: 100%;
    transform: translateY(20px);
    animation: slideUp 0.8s ease-out forwards;
    position: relative;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.form-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background:
        radial-gradient(circle at 50% 0%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
        linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
    pointer-events: none;
    z-index: 1;
}

.form-container > * {
    position: relative;
    z-index: 2;
}

@keyframes slideUp {
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Header Section */
.form-header {
    background:
        linear-gradient(135deg, rgba(102, 126, 234, 0.95) 0%, rgba(118, 75, 162, 0.95) 100%),
        radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
    color: white;
    padding: 40px 30px 30px;
    text-align: center;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
}

.form-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background:
        radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%),
        conic-gradient(from 0deg at 50% 50%, rgba(255, 255, 255, 0.1) 0deg, transparent 60deg, rgba(255, 255, 255, 0.05) 120deg, transparent 180deg);
    opacity: 0.3;
    animation: headerGlow 4s ease-in-out infinite;
}

@keyframes headerGlow {
    0%, 100% { transform: rotate(0deg); }
    50% { transform: rotate(180deg); }
}

.form-header > * {
    position: relative;
    z-index: 1;
}

.header-icon {
    font-size: 3.5rem;
    margin-bottom: 20px;
    animation: iconPulse 2s ease-in-out infinite;
}

@keyframes iconPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.header-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 10px;
    letter-spacing: -0.5px;
}

.header-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    font-weight: 300;
}

/* Mode Selector */
.mode-selector {
    padding: 30px 30px 20px;
    background: rgba(255, 255, 255, 0.8);
}

.mode-selector .btn-group {
    width: 100%;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.mode-selector .btn {
    flex: 1;
    padding: 12px 20px;
    font-weight: 600;
    border: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
}

.mode-selector .btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.mode-selector .btn:hover::before {
    left: 100%;
}

.mode-selector .btn-primary {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    transform: scale(1.02);
}

.mode-selector .btn-success {
    background: linear-gradient(135deg, #56ab2f, #a8e6cf);
    color: white;
    transform: scale(1.02);
}

.mode-selector .btn-outline-primary,
.mode-selector .btn-outline-success,
.mode-selector .btn-outline-danger {
    background: rgba(255, 255, 255, 0.8);
    color: #666;
    border: 2px solid rgba(0, 0, 0, 0.1);
}

/* Form Body */
.form-body {
    padding: 30px;
    background: rgba(255, 255, 255, 0.9);
}

/* Form Groups */
.form-group {
    margin-bottom: 25px;
    position: relative;
}

.form-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
}

.form-label i {
    color: #667eea;
    margin-right: 8px;
}

/* Enhanced Form Controls */
.form-control {
    border: 2px solid rgba(0, 0, 0, 0.1);
    border-radius: 12px;
    padding: 14px 16px;
    font-size: 1rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    background: rgba(255, 255, 255, 0.9);
    position: relative;
}

.form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    background: white;
    transform: translateY(-2px);
}

.form-control:hover {
    border-color: rgba(102, 126, 234, 0.3);
}

/* Input Group Enhancements */
.input-group .btn {
    border: 2px solid rgba(0, 0, 0, 0.1);
    border-left: none;
    border-radius: 0 12px 12px 0;
    background: rgba(255, 255, 255, 0.9);
    transition: all 0.3s ease;
}

.input-group .btn:hover {
    background: #667eea;
    color: white;
    border-color: #667eea;
}

/* Animated Buttons */
.btn-animated {
    position: relative;
    overflow: hidden;
    border-radius: 12px;
    font-weight: 600;
    padding: 14px 24px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.95rem;
}

.btn-animated::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.btn-animated:hover::before {
    left: 100%;
}

.btn-animated:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.btn-primary.btn-animated {
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.btn-success.btn-animated {
    background: linear-gradient(135deg, #56ab2f, #a8e6cf);
}

/* Alert Enhancements */
.alert {
    border-radius: 12px;
    border: none;
    padding: 16px 20px;
    margin-bottom: 25px;
    animation: alertSlide 0.5s ease-out;
}

@keyframes alertSlide {
    from {
        transform: translateX(-20px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Form Check Enhancements */
.form-check {
    padding-left: 0;
    margin-bottom: 20px;
}

.form-check-input {
    width: 20px;
    height: 20px;
    margin-right: 10px;
    border: 2px solid #ddd;
    border-radius: 4px;
    transition: all 0.3s ease;
}

.form-check-input:checked {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: #667eea;
}

.form-check-label {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.4;
}

/* Footer Links */
.form-footer {
    text-align: center;
    padding: 20px 30px 30px;
    background: rgba(255, 255, 255, 0.8);
}

.form-footer a {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
}

.form-footer a::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: #667eea;
    transition: width 0.3s ease;
}

.form-footer a:hover::after {
    width: 100%;
}

.form-footer a:hover {
    color: #764ba2;
    transform: translateY(-1px);
}

/* Demo Credentials Box */
.demo-box {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 16px;
    padding: 20px;
    margin-top: 25px;
    animation: demoGlow 3s ease-in-out infinite;
}

@keyframes demoGlow {
    0%, 100% { box-shadow: 0 0 20px rgba(102, 126, 234, 0.1); }
    50% { box-shadow: 0 0 30px rgba(102, 126, 234, 0.2); }
}

/* Responsive Design */
@media (max-width: 768px) {
    .form-container {
        margin: 10px;
        border-radius: 20px;
    }

    .form-header {
        padding: 30px 20px 20px;
    }

    .header-title {
        font-size: 1.75rem;
    }

    .mode-selector,
    .form-body,
    .form-footer {
        padding: 20px;
    }
}

/* Loading Animation */
.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid rgba(255,255,255,0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}



/* Enhanced Visual Effects */
.form-container {
    animation: slideUp 0.8s ease-out forwards, glow 3s ease-in-out infinite;
}

@keyframes glow {
    0%, 100% {
        box-shadow:
            0 32px 64px rgba(0, 0, 0, 0.2),
            0 0 0 1px rgba(255, 255, 255, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.4);
    }
    50% {
        box-shadow:
            0 32px 64px rgba(0, 0, 0, 0.25),
            0 0 0 1px rgba(255, 255, 255, 0.4),
            inset 0 1px 0 rgba(255, 255, 255, 0.5),
            0 0 50px rgba(102, 126, 234, 0.1);
    }
}

/* Responsive Background Images */
@media (max-width: 768px) {
    .login-background,
    .background-overlay,
    .background-shapes {
        background-size: cover;
        background-position: center;
    }

    .form-header {
        background-size: cover;
        background-position: center;
    }
}
</style>

<!-- Beautiful Animated Background -->
<div class="login-background"></div>

<!-- Additional Background Layers -->
<div class="background-overlay"></div>
<div class="background-shapes"></div>

<!-- Floating Particles -->
<div class="particles">
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>

<div class="login-container">
    <div class="form-container">
                <!-- Professional Header -->
                <div class="form-header">
                    <div class="header-icon">
                        <i class="fas fa-<?php echo $mode === 'login' ? 'user-circle' : 'user-plus'; ?>"></i>
                    </div>
                    <h1 class="header-title"><?php echo $mode === 'login' ? 'Welcome Back' : 'Join Us Today'; ?></h1>
                    <p class="header-subtitle">
                        <?php echo $mode === 'login' ? 'Sign in to access your account and book amazing events' : 'Create your account to start booking incredible experiences'; ?>
                    </p>
                </div>



                <!-- Form Body -->
                <div class="form-body">

                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                </div>
                <?php endif; ?>

                <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                </div>
                <?php endif; ?>

                <?php if ($mode === 'login'): ?>
                <!-- Login Form -->
                <form method="POST" id="login-form">
                    <input type="hidden" name="action" value="login">

                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="fas fa-user me-2"></i>Username or Email
                        </label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                               required autocomplete="username">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock me-2"></i>Password
                        </label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password"
                                   required autocomplete="current-password">
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-animated w-100 mb-3">
                        <i class="fas fa-sign-in-alt me-2"></i>Sign In
                    </button>
                </form>

                <?php else: ?>
                <!-- Registration Form -->
                <form method="POST" id="register-form">
                    <input type="hidden" name="action" value="register">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name" class="form-label">
                                    <i class="fas fa-user me-2"></i>First Name *
                                </label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                       value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Last Name *
                                </label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                       value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="reg_username" class="form-label">
                            <i class="fas fa-at me-2"></i>Username *
                        </label>
                        <input type="text" class="form-control" id="reg_username" name="username"
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                        <small class="form-text text-muted">Choose a unique username</small>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email Address *
                        </label>
                        <input type="email" class="form-control" id="email" name="email"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone me-2"></i>Phone Number
                        </label>
                        <input type="tel" class="form-control" id="phone" name="phone"
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="reg_password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password *
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="reg_password" name="password"
                                           required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" id="toggle-reg-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">Minimum 6 characters</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Confirm Password *
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                           required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" id="toggle-confirm-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms of Service</a>
                                and <a href="#" class="text-decoration-none">Privacy Policy</a>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-animated w-100 mb-3">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </form>
                <?php endif; ?>

                </div>

                <!-- Professional Footer -->
                <div class="form-footer">
                    <?php if ($mode === 'login'): ?>
                    <p class="mb-3">
                        <a href="forgot-password.php">
                            <i class="fas fa-key me-2"></i>Forgot your password?
                        </a>
                    </p>
                    <p class="mb-0">
                        Don't have an account?
                        <a href="?mode=register" class="fw-bold text-success" style="font-size: 1.1rem;">
                            <i class="fas fa-user-plus me-1"></i>Create one here
                        </a>
                    </p>
                    <?php else: ?>
                    <p class="mb-0">
                        Already have an account?
                        <a href="?mode=login" class="fw-bold">
                            <i class="fas fa-sign-in-alt me-1"></i>Sign in here
                        </a>
                    </p>
                    <?php endif; ?>

                    <!-- Welcome Info -->
                    <div class="demo-box mt-4">
                        <h6 class="mb-2" style="color: <?php echo $mode === 'login' ? '#667eea' : '#56ab2f'; ?>;">
                            <i class="fas fa-<?php echo $mode === 'login' ? 'home' : 'star'; ?> me-2"></i><?php echo $mode === 'login' ? 'Welcome to EventBooking' : 'Join EventBooking'; ?>
                        </h6>
                        <div style="color: #666; font-size: 0.9rem;">
                            <?php if ($mode === 'login'): ?>
                                Please sign in to access your dashboard and start discovering amazing events.
                                Your gateway to unforgettable experiences starts here!
                            <?php else: ?>
                                Create your account to join thousands of event enthusiasts.
                                Once registered, you can explore and book incredible events!
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle password visibility for all password fields
    $('#toggle-password, #toggle-reg-password, #toggle-confirm-password').on('click', function() {
        const buttonId = $(this).attr('id');
        let targetField;

        if (buttonId === 'toggle-password') {
            targetField = $('#password');
        } else if (buttonId === 'toggle-reg-password') {
            targetField = $('#reg_password');
        } else {
            targetField = $('#confirm_password');
        }

        const icon = $(this).find('i');

        if (targetField.attr('type') === 'password') {
            targetField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            targetField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Login form validation
    $('#login-form').on('submit', function(e) {
        const username = $('#username').val().trim();
        const password = $('#password').val();

        if (!username || !password) {
            e.preventDefault();
            alert('Please fill in all fields.');
            return false;
        }

        // Show loading state with enhanced animation
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<span class="loading-spinner me-2"></span>Signing in...').prop('disabled', true).addClass('loading');

        // Re-enable button after 5 seconds (in case of server error)
        setTimeout(() => {
            submitBtn.html(originalText).prop('disabled', false);
        }, 5000);
    });

    // Registration form validation
    $('#register-form').on('submit', function(e) {
        const password = $('#reg_password').val();
        const confirmPassword = $('#confirm_password').val();
        const terms = $('#terms').is(':checked');

        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match.');
            return false;
        }

        if (!terms) {
            e.preventDefault();
            alert('Please accept the terms and conditions.');
            return false;
        }

        // Show loading state with enhanced animation
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<span class="loading-spinner me-2"></span>Creating Account...').prop('disabled', true).addClass('loading');

        // Re-enable button after 5 seconds (in case of server error)
        setTimeout(() => {
            submitBtn.html(originalText).prop('disabled', false);
        }, 5000);
    });

    // Password confirmation validation
    $('#confirm_password').on('input', function() {
        const password = $('#reg_password').val();
        const confirmPassword = $(this).val();

        if (confirmPassword && password !== confirmPassword) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Passwords do not match</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });

    // Auto-focus on appropriate field with delay for animation
    setTimeout(() => {
        <?php if ($mode === 'login'): ?>
        $('#username').focus();
        <?php else: ?>
        $('#first_name').focus();
        <?php endif; ?>
    }, 500);

    // Enhanced form interactions
    $('.form-control').on('focus', function() {
        $(this).parent().addClass('focused');
    }).on('blur', function() {
        $(this).parent().removeClass('focused');
    });

    // Smooth scroll to alerts
    if ($('.alert').length) {
        $('html, body').animate({
            scrollTop: $('.alert').offset().top - 100
        }, 500);
    }

    // Add ripple effect to buttons
    $('.btn-animated').on('click', function(e) {
        const button = $(this);
        const ripple = $('<span class="ripple"></span>');

        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.css({
            position: 'absolute',
            width: size + 'px',
            height: size + 'px',
            left: x + 'px',
            top: y + 'px',
            background: 'rgba(255,255,255,0.5)',
            borderRadius: '50%',
            transform: 'scale(0)',
            animation: 'ripple 0.6s linear',
            pointerEvents: 'none'
        });

        button.css('position', 'relative').css('overflow', 'hidden').append(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    });

    // Add CSS for ripple animation
    $('<style>').text(`
        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
        .loading {
            opacity: 0.8;
            pointer-events: none;
        }
    `).appendTo('head');

    // Parallax effect for background
    $(window).on('scroll', function() {
        const scrolled = $(this).scrollTop();
        $('.login-background').css('transform', 'translateY(' + (scrolled * 0.5) + 'px)');
    });

    // Form validation enhancements
    $('.form-control').on('input', function() {
        const value = $(this).val();
        const field = $(this);

        // Remove previous validation classes
        field.removeClass('is-valid is-invalid');

        // Basic validation
        if (value.length > 0) {
            if (field.attr('type') === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailRegex.test(value)) {
                    field.addClass('is-valid');
                } else {
                    field.addClass('is-invalid');
                }
            } else if (field.attr('name') === 'password' && value.length >= 6) {
                field.addClass('is-valid');
            } else if (field.attr('name') === 'password' && value.length < 6) {
                field.addClass('is-invalid');
            } else if (value.length >= 2) {
                field.addClass('is-valid');
            }
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
