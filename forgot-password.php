<?php
session_start();
require_once 'includes/db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            // Check if email exists
            $stmt = $pdo->prepare("SELECT user_id, name FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store token in database
                $stmt = $pdo->prepare("
                    INSERT INTO password_resets 
                    (user_id, token, expires_at) 
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                    token = VALUES(token),
                    expires_at = VALUES(expires_at)
                ");
                
                // Execute and verify insertion
                if($stmt->execute([$user['user_id'], $token, $expires])) {
                    $reset_link = "http://localhost/POEPART%202/reset-password.php?token=" . urlencode($token);
                    
                    // Return success response
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'email' => $email,
                        'to_name' => $user['name'],
                        'reset_link' => $reset_link
                    ]);
                    exit;
                } else {
                    throw new Exception("Failed to save reset token");
                }
            } else {
                $error = "No account found with that email address.";
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = "An error occurred. Please try again later.";
        } catch (Exception $e) {
            error_log("General error: " . $e->getMessage());
            $error = "Failed to process your request.";
        }
    }
    
    // Return error response
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $error]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iBooks - Reset Password</title>
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
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
        /* Additional success message styling */
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
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
            <h1>Reset Your Password</h1>
            <p>Enter your email to receive reset instructions</p>
        </div>

        <div id="message-container"></div>

        <form id="resetForm" method="POST" novalidate>
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

            <button type="submit" id="loginButton"><i class="fas fa-paper-plane"></i> Send Reset Link
            </button>
                
        </form>

        <div class="auth-links">
            <p>Remembered your password? <a href="login.php">Sign In</a></p>
        </div>
    </div>

    <script>
        // Initialize EmailJS with your public key
        emailjs.init('q69sk-XmcTj6GEzjl');

        document.getElementById('resetForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const messageContainer = document.getElementById('message-container');
            messageContainer.innerHTML = '';

            const formData = new FormData(e.target);
            
            try {
                const response = await fetch('forgot-password.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Send email using EmailJS
                    await emailjs.send('service_fcdwhbc', 'template_vjuw0k9', {
                        to_name: data.to_name,
                        email: data.email,
                        reset_link: data.reset_link
                    });

                    messageContainer.innerHTML = `
                        <div class="success-message">
                            Password reset instructions sent to ${data.email}
                        </div>
                    `;
                } else {
                    messageContainer.innerHTML = `
                        <div class="error-message">
                            ${data.error}
                        </div>
                    `;
                }
            } catch (err) {
                console.error('Error:', err);
                messageContainer.innerHTML = `
                    <div class="error-message">
                        An error occurred. Please try again.
                    </div>
                `;
            }
        });
    </script>
    <script src="scripts/main.js"></script>
</body>
</html>