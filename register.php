<?php
require_once 'includes/functions.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$pageTitle = 'Register';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'username' => sanitizeInput($_POST['username']),
        'email' => sanitizeInput($_POST['email']),
        'password' => $_POST['password'],
        'confirm_password' => $_POST['confirm_password'],
        'first_name' => sanitizeInput($_POST['first_name']),
        'last_name' => sanitizeInput($_POST['last_name']),
        'phone' => sanitizeInput($_POST['phone']),
        'address' => sanitizeInput($_POST['address'])
    ];
    
    // Validation
    if (empty($data['username']) || empty($data['email']) || empty($data['password']) || 
        empty($data['first_name']) || empty($data['last_name'])) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($data['password']) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($data['password'] !== $data['confirm_password']) {
        $error = 'Passwords do not match.';
    } elseif (strlen($data['username']) < 3) {
        $error = 'Username must be at least 3 characters long.';
    } else {
        try {
            if (register($data)) {
                $_SESSION['success_message'] = 'Registration successful! You can now log in.';
                header('Location: login.php');
                exit();
            } else {
                $error = 'Username or email already exists. Please choose different ones.';
            }
        } catch (Exception $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="form-container">
                <div class="text-center mb-4">
                    <i class="fas fa-user-plus fa-3x text-primary mb-3"></i>
                    <h2>Create Account</h2>
                    <p class="text-muted">Join us to start booking amazing events</p>
                </div>

                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                </div>
                <?php endif; ?>

                <form method="POST" id="register-form">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name" class="form-label">
                                    <i class="fas fa-user me-2"></i>First Name *
                                </label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" 
                                       required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name" class="form-label">
                                    <i class="fas fa-user me-2"></i>Last Name *
                                </label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" 
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="fas fa-at me-2"></i>Username *
                        </label>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                               required minlength="3">
                        <small class="form-text text-muted">At least 3 characters, letters and numbers only</small>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-2"></i>Email Address *
                        </label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                               required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
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
                                <div class="password-strength mt-2">
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar" id="password-strength-bar" 
                                             role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="form-text text-muted" id="password-strength-text">
                                        At least 6 characters
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Confirm Password *
                                </label>
                                <input type="password" class="form-control" id="confirm_password" 
                                       name="confirm_password" required>
                                <small class="form-text" id="password-match"></small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone" class="form-label">
                            <i class="fas fa-phone me-2"></i>Phone Number
                        </label>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="address" class="form-label">
                            <i class="fas fa-map-marker-alt me-2"></i>Address
                        </label>
                        <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($_POST['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> 
                                and <a href="#" class="text-decoration-none">Privacy Policy</a> *
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </form>

                <div class="text-center">
                    <p class="mb-0">
                        Already have an account? 
                        <a href="login.php" class="text-decoration-none fw-bold">
                            <i class="fas fa-sign-in-alt me-1"></i>Sign in here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#toggle-password').on('click', function() {
        const passwordField = $('#password');
        const icon = $(this).find('i');
        
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            icon.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            icon.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Password strength checker
    $('#password').on('input', function() {
        const password = $(this).val();
        const strength = checkPasswordStrength(password);
        const strengthBar = $('#password-strength-bar');
        const strengthText = $('#password-strength-text');
        
        let width = (strength / 5) * 100;
        let className = 'bg-danger';
        let text = 'Weak';
        
        if (strength >= 4) {
            className = 'bg-success';
            text = 'Strong';
        } else if (strength >= 3) {
            className = 'bg-warning';
            text = 'Medium';
        }
        
        strengthBar.css('width', width + '%')
                  .removeClass('bg-danger bg-warning bg-success')
                  .addClass(className);
        strengthText.text(text);
    });

    // Password match checker
    $('#confirm_password').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        const matchText = $('#password-match');
        
        if (confirmPassword === '') {
            matchText.text('').removeClass('text-success text-danger');
        } else if (password === confirmPassword) {
            matchText.text('Passwords match').removeClass('text-danger').addClass('text-success');
        } else {
            matchText.text('Passwords do not match').removeClass('text-success').addClass('text-danger');
        }
    });

    // Username validation
    $('#username').on('input', function() {
        const username = $(this).val();
        const regex = /^[a-zA-Z0-9_]+$/;
        
        if (username && !regex.test(username)) {
            $(this).addClass('is-invalid');
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    // Form validation
    $('#register-form').on('submit', function(e) {
        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();
        const terms = $('#terms').is(':checked');
        
        if (password !== confirmPassword) {
            e.preventDefault();
            showAlert('danger', 'Passwords do not match.');
            return false;
        }
        
        if (!terms) {
            e.preventDefault();
            showAlert('danger', 'Please accept the terms and conditions.');
            return false;
        }
        
        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<span class="loading"></span> Creating account...').prop('disabled', true);
        
        // Re-enable button after 10 seconds (in case of server error)
        setTimeout(() => {
            submitBtn.html(originalText).prop('disabled', false);
        }, 10000);
    });

    // Auto-focus on first name field
    $('#first_name').focus();
});
</script>

<?php include 'includes/footer.php'; ?>
