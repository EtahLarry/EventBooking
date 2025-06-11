<?php
require_once 'config/database.php';

$message = '';
$error = '';
$accountType = $_GET['type'] ?? 'user';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountType = $_POST['account_type'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $pdo = getDBConnection();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            if ($accountType === 'user') {
                // Create user account
                $firstName = trim($_POST['first_name']);
                $lastName = trim($_POST['last_name']);
                $phone = trim($_POST['phone']);
                
                if (empty($firstName) || empty($lastName)) {
                    $error = 'Please fill in first and last name.';
                } else {
                    // Check if username or email already exists
                    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    
                    if ($stmt->fetch()) {
                        $error = 'Username or email already exists in users table.';
                    } else {
                        $stmt = $pdo->prepare("
                            INSERT INTO users (username, email, password, first_name, last_name, phone) 
                            VALUES (?, ?, ?, ?, ?, ?)
                        ");
                        
                        if ($stmt->execute([$username, $email, $hashedPassword, $firstName, $lastName, $phone])) {
                            $message = 'User account created successfully!';
                            $_POST = [];
                        } else {
                            $error = 'Failed to create user account.';
                        }
                    }
                }
            } else {
                // Create admin account
                $fullName = trim($_POST['full_name']);
                $role = $_POST['role'] ?? 'admin';
                
                if (empty($fullName)) {
                    $error = 'Please fill in full name.';
                } else {
                    // Check if username or email already exists
                    $stmt = $pdo->prepare("SELECT id FROM admin_users WHERE username = ? OR email = ?");
                    $stmt->execute([$username, $email]);
                    
                    if ($stmt->fetch()) {
                        $error = 'Username or email already exists in admin table.';
                    } else {
                        $stmt = $pdo->prepare("
                            INSERT INTO admin_users (username, email, password, full_name, role) 
                            VALUES (?, ?, ?, ?, ?)
                        ");
                        
                        if ($stmt->execute([$username, $email, $hashedPassword, $fullName, $role])) {
                            $message = 'Admin account created successfully!';
                            $_POST = [];
                        } else {
                            $error = 'Failed to create admin account.';
                        }
                    }
                }
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Event Booking System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #6f42c1 0%, #007bff 100%);
            min-height: 100vh;
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .account-creator-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .account-creator-header {
            background: linear-gradient(135deg, #6f42c1, #007bff);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #6f42c1;
            box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
        }
        .btn-create {
            background: linear-gradient(135deg, #6f42c1, #007bff);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(111, 66, 193, 0.3);
        }
        .account-type-selector {
            margin-bottom: 2rem;
        }
        .account-type-selector .btn {
            border-radius: 25px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="account-creator-card">
                    <div class="account-creator-header">
                        <i class="fas fa-users-cog fa-4x mb-3"></i>
                        <h2>Account Creator Tool</h2>
                        <p class="mb-0">Create User or Admin Accounts - Event Booking System</p>
                    </div>
                    <div class="p-4">
                        <!-- Account Type Selector -->
                        <div class="text-center account-type-selector">
                            <div class="btn-group" role="group" aria-label="Account type">
                                <a href="?type=user" class="btn <?php echo $accountType === 'user' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                                    <i class="fas fa-user me-1"></i>Create User Account
                                </a>
                                <a href="?type=admin" class="btn <?php echo $accountType === 'admin' ? 'btn-danger' : 'btn-outline-danger'; ?>">
                                    <i class="fas fa-shield-alt me-1"></i>Create Admin Account
                                </a>
                            </div>
                        </div>

                        <?php if ($error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                        </div>
                        <?php endif; ?>

                        <?php if ($message): ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
                            <div class="mt-2">
                                <?php if ($accountType === 'user'): ?>
                                    <a href="login.php" class="btn btn-sm btn-success">
                                        <i class="fas fa-sign-in-alt me-1"></i>User Login
                                    </a>
                                <?php else: ?>
                                    <a href="admin/index.php" class="btn btn-sm btn-danger">
                                        <i class="fas fa-shield-alt me-1"></i>Admin Login
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <form method="POST" id="account-form">
                            <input type="hidden" name="account_type" value="<?php echo $accountType; ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">
                                            <i class="fas fa-at me-2"></i>Username *
                                        </label>
                                        <input type="text" class="form-control" id="username" name="username" 
                                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-2"></i>Email Address *
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                                               required>
                                    </div>
                                </div>
                            </div>

                            <?php if ($accountType === 'user'): ?>
                            <!-- User-specific fields -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">
                                            <i class="fas fa-user me-2"></i>First Name *
                                        </label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" 
                                               value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">
                                            <i class="fas fa-user me-2"></i>Last Name *
                                        </label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" 
                                               value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" 
                                               required>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-2"></i>Phone Number
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>
                            <?php else: ?>
                            <!-- Admin-specific fields -->
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="full_name" class="form-label">
                                            <i class="fas fa-user me-2"></i>Full Name *
                                        </label>
                                        <input type="text" class="form-control" id="full_name" name="full_name" 
                                               value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" 
                                               required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">
                                            <i class="fas fa-shield-alt me-2"></i>Role
                                        </label>
                                        <select class="form-control" id="role" name="role">
                                            <option value="admin">Admin</option>
                                            <option value="super_admin">Super Admin</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">
                                            <i class="fas fa-lock me-2"></i>Password *
                                        </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="password" name="password" 
                                                   required minlength="6">
                                            <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
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

                            <button type="submit" class="btn btn-create w-100 mb-3 text-white">
                                <i class="fas fa-<?php echo $accountType === 'user' ? 'user-plus' : 'user-shield'; ?> me-2"></i>
                                Create <?php echo ucfirst($accountType); ?> Account
                            </button>
                        </form>

                        <div class="text-center">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="login.php" class="text-decoration-none">
                                        <i class="fas fa-sign-in-alt me-1"></i>User Login
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="admin/index.php" class="text-decoration-none">
                                        <i class="fas fa-shield-alt me-1"></i>Admin Login
                                    </a>
                                </div>
                            </div>
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
            ['toggle-password', 'toggle-confirm-password'].forEach(id => {
                const toggle = document.getElementById(id);
                if (toggle) {
                    toggle.addEventListener('click', function() {
                        const targetId = id === 'toggle-password' ? 'password' : 'confirm_password';
                        const field = document.getElementById(targetId);
                        const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
                        field.setAttribute('type', type);
                        
                        const icon = this.querySelector('i');
                        icon.classList.toggle('fa-eye');
                        icon.classList.toggle('fa-eye-slash');
                    });
                }
            });

            // Form validation
            document.getElementById('account-form').addEventListener('submit', function(e) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Passwords do not match.');
                    return false;
                }
                
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';
                submitBtn.disabled = true;
                
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });
        });
    </script>
</body>
</html>
