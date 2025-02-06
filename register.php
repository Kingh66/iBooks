<?php
session_start();
require_once 'includes/db.php'; // Include database connection

// Enable error reporting (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';
$success = '';

// Handle registration attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    try {
        // Validate input
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (empty($password) || strlen($password) < 8) {
            $error = "Password must be at least 8 characters.";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match.";
        } else {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->rowCount() > 0) {
                $error = "This email is already registered.";
            } else {
                // Hash password
                $passwordHash = password_hash($password, PASSWORD_BCRYPT);
                $name = explode('@', $email)[0]; // Default name from email

                // Insert new user
                $stmt = $pdo->prepare("
                    INSERT INTO users (email, password_hash, name, is_admin, created_at) 
                    VALUES (?, ?, ?, 0, NOW())
                ");
                $stmt->execute([$email, $passwordHash, $name]);

                $success = "Registration successful! Redirecting to login...";
                header('Refresh: 2; URL=login.php');
                exit;
            }
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBooks - Join Our Library</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/login.css">
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        .error-message {
            background: #F8D7DA;
            color: #721C24;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: <?= isset($error) ? 'block' : 'none' ?>;
        }


        /* Additional registration-specific styles */
        .password-strength {
            height: 4px;
            background: #eee;
            margin: 0.5rem 0;
            border-radius: 2px;
        }

        .strength-meter {
            height: 100%;
            width: 0;
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        .terms {
            font-size: 0.9rem;
            margin: 1rem 0;
            color: #7F8C8D;
        }
    </style>
</head>
<body>
<div class="logo-container">
        <div class="logo">
                <svg width="50" height="75" viewBox="0 0 100 150" xmlns="http://www.w3.org/2000/svg">
                    <!-- Book Cover -->
                    <rect x="30" y="20" width="40" height="110" fill="#E6BE8A" rx="5" ry="5" />
                    <!-- Book Spine -->
                    <rect x="20" y="20" width="10" height="110" fill="#DAA520" />
                    <!-- Book Pages -->
                    <rect x="40" y="20" width="30" height="110" fill="#C2B280" />
                    <!-- Book Pages Highlight -->
                    <rect x="41" y="21" width="28" height="108" fill="none" stroke="#FFF" stroke-width="1" />
                    <!-- Book Top -->
                    <rect x="30" y="10" width="40" height="10" fill="#E6BE8A" />
                    <!-- Book Bottom -->
                    <rect x="30" y="130" width="40" height="10" fill="#E6BE8A" />
                </svg>
                <span class="logo-text">iBooks</span>
            </a>
        </div>
    </div>
    <div class="login-container">
        <div class="login-header">
            <h1>Join Our Library!</h1>
            <p>Create your free account to start reading</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message" role="alert">
                <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" autocomplete="off" novalidate>
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required
                    placeholder="reader@ibooks.com"
                    autofocus
                >
            </div>

            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password</label>
                <div class="password-field">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        placeholder="••••••••"
                        minlength="8"
                    >
                    <button type="button" class="password-toggle">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="strength-meter" id="strengthMeter"></div>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                <div class="password-field">
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required
                        placeholder="••••••••"
                    >
                    <button type="button" class="password-toggle">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <p class="terms">
                By creating an account, you agree to our 
                <a href="terms.php">Terms of Service</a>
            </p>

            <button type="submit" id="loginButton">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="auth-links">
            <p>Already have an account? <a href="login.php">Sign In</a></p>
        </div>
    </div>

    <script>
        // Password toggle functionality
        document.querySelectorAll('.password-toggle').forEach(button => {
            button.addEventListener('click', (e) => {
                const input = e.currentTarget.previousElementSibling;
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                e.currentTarget.innerHTML = isPassword 
                    ? '<i class="fas fa-eye-slash"></i>'
                    : '<i class="fas fa-eye"></i>';
            });
        });

        // Password strength calculator
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strengthMeter = document.getElementById('strengthMeter');
            let strength = 0;

            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^A-Za-z0-9]/)) strength++;
            if (password.length >= 12) strength++;

            const width = (strength/5) * 100;
            strengthMeter.style.width = `${width}%`;
            strengthMeter.style.backgroundColor = getStrengthColor(strength);
        });

        function getStrengthColor(strength) {
            const colors = ['#ff4444', '#ffbb33', '#00C851'];
            return colors[Math.min(strength, 3) - 1];
        }
    </script>
    <script src="scripts/main.js"></script>
</body>
</html>