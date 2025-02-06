<?php
session_start();
require_once 'includes/db.php';

$error = '';
$success = '';
$validToken = false;
$token = $_GET['token'] ?? '';

// Check if token exists and is valid
if (!empty($token)) {
    try {
        $stmt = $pdo->prepare("
            SELECT pr.*, u.email 
            FROM password_resets pr
            JOIN users u ON pr.user_id = u.user_id
            WHERE pr.token = ? AND pr.expires_at > NOW()
        ");
        $stmt->execute([$token]);
        $resetRequest = $stmt->fetch();

        if ($resetRequest) {
            $validToken = true;
            $email = $resetRequest['email'];
        } else {
            $error = "Invalid or expired reset link.";
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $error = "An error occurred. Please try again.";
    }
}

// Handle password reset submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    if (empty($password) || $password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        try {
            // Update password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
            $stmt->execute([$hashedPassword, $email]);
            
            // Delete used token
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
            $stmt->execute([$token]);
            
            $success = "Password updated successfully! You can now <a href='login.php'>login</a>.";
            $validToken = false; // Prevent further submissions

        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = "An error occurred. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBooks - Reset Password</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        .reset-container {
            max-width: 400px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .password-rules {
            font-size: 0.9rem;
            color: #666;
            margin: 0.5rem 0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .password-input {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
        }

        input[type="password"], input[type="text"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 1rem;
        }

        button[type="submit"] {
            width: 100%;
            padding: 0.75rem;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button[type="submit"]:hover {
            background: #219a52;
        }

        .alert {
            padding: 0.75rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h1>Reset Password</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($validToken): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="password">New Password</label>
                    <div class="password-input">
                        <input type="password" id="password" name="password" required>
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('password')"></i>
                    </div>
                    <p class="password-rules">Minimum 8 characters</p>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="password-input">
                        <input type="password" id="confirm_password" name="confirm_password" required>
                        <i class="fas fa-eye password-toggle" onclick="togglePassword('confirm_password')"></i>
                    </div>
                </div>

                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.parentElement.querySelector('.password-toggle');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // Client-side validation
        document.querySelector('form')?.addEventListener('submit', (e) => {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters');
                return;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
            }
        });
    </script>
    <script src="scripts/main.js"></script>
</body>
</html>