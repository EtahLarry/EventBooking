<?php
require_once 'includes/admin-functions.php';

$pageTitle = 'Admin Login';
$error = '';
$success = '';

// Check if already logged in as admin
if (isAdminLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        if (adminLogin($username, $password)) {
            logAdminActivity('Admin Login', 'Successful login');
            $_SESSION['admin_success_message'] = 'Welcome back! You have been logged in successfully.';
            header('Location: dashboard.php');
            exit();
        } else {
            $error = 'Invalid username or password.';
            logAdminActivity('Failed Login Attempt', "Username: $username");
        }
    }
}

// Check for success messages
if (isset($_SESSION['admin_success_message'])) {
    $success = $_SESSION['admin_success_message'];
    unset($_SESSION['admin_success_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Event Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #dc3545 0%, #6f42c1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        .login-header {
            background: linear-gradient(135deg, #dc3545, #6f42c1);
            color: white;
            padding: 2.5rem;
            text-align: center;
            position: relative;
        }
        .login-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        }
        .login-header > * {
            position: relative;
            z-index: 1;
        }
        .login-body {
            padding: 2.5rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .btn-admin {
            background: linear-gradient(135deg, #dc3545, #6f42c1);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
        }
        .login-type-selector {
            margin-bottom: 2rem;
        }
        .login-type-selector .btn {
            border-radius: 25px;
            padding: 8px 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .security-badge {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            border-radius: 10px;
            padding: 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-card">
                    <div class="login-header">
                        <i class="fas fa-shield-halved fa-4x mb-3"></i>
                        <h2>Administrator Access</h2>
                        <p class="mb-0">Secure Admin Panel - Event Booking System</p>
                    </div>
                    <div class="login-body">
                        <!-- Login Type Selector -->
                        <div class="text-center login-type-selector">
                            <div class="btn-group" role="group" aria-label="Login type">
                                <a href="../login.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-user me-1"></i>User Login
                                </a>
                                <a href="index.php" class="btn btn-danger active">
                                    <i class="fas fa-shield-alt me-1"></i>Admin Login
                                </a>
                            </div>
                        </div>
                        <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-2"></i>Username
                                </label>
                                <input type="text" class="form-control" id="username" name="username" 
                                       value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                                       required>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Admin Password
                                </label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-admin w-100 mb-3">
                                <i class="fas fa-shield-alt me-2"></i>Access Admin Panel
                            </button>
                        </form>

                        <div class="text-center">
                            <div class="mb-3">
                                <a href="create-admin.php" class="text-decoration-none text-success fw-bold">
                                    <i class="fas fa-user-plus me-1"></i>Create Admin Account
                                </a>
                            </div>
                            <a href="../index.php" class="text-decoration-none text-muted">
                                <i class="fas fa-arrow-left me-1"></i>Back to Main Website
                            </a>
                        </div>

                        <!-- Security Notice -->
                        <div class="security-badge">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-shield-check text-danger me-2"></i>
                                <strong class="text-danger">Secure Admin Access</strong>
                            </div>
                            <small class="text-muted">
                                This area is restricted to authorized administrators only.
                                All login attempts are monitored and logged.
                            </small>
                        </div>

                        <!-- Demo Credentials -->
                        <div class="mt-3 p-3 bg-light rounded">
                            <h6 class="mb-2 text-success">
                                <i class="fas fa-key me-1"></i>Demo Credentials:
                            </h6>
                            <small class="text-muted">
                                <strong>Username:</strong> admin<br>
                                <strong>Password:</strong> admin123
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle password visibility
            const togglePassword = document.getElementById('toggle-password');
            const passwordField = document.getElementById('password');

            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordField.setAttribute('type', type);

                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye');
                    icon.classList.toggle('fa-eye-slash');
                });
            }

            // Form validation and loading state
            const loginForm = document.querySelector('form');
            if (loginForm) {
                loginForm.addEventListener('submit', function(e) {
                    const username = document.getElementById('username').value.trim();
                    const password = document.getElementById('password').value;

                    if (!username || !password) {
                        e.preventDefault();
                        alert('Please fill in all fields.');
                        return false;
                    }

                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Authenticating...';
                    submitBtn.disabled = true;

                    // Re-enable button after 5 seconds (in case of server error)
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 5000);
                });
            }

            // Auto-focus on username field
            const usernameField = document.getElementById('username');
            if (usernameField) {
                usernameField.focus();
            }

            // Add security warning for demo
            setTimeout(() => {
                console.log('%c⚠️ SECURITY NOTICE', 'color: red; font-size: 16px; font-weight: bold;');
                console.log('%cThis is a demo admin panel. In production, ensure proper security measures are in place.', 'color: orange; font-size: 12px;');
            }, 1000);
        });
    </script>
</body>
</html>
