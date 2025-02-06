<?php
session_start();
require_once 'includes/db.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error = '';

// Handle login attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';

    try {
        // Validate input
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address.";
        } elseif (empty($password)) {
            $error = "Please enter your password.";
        } else {
            // Fetch user from database
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Successful login
                $_SESSION['user'] = [
                    'user_id' => $user['user_id'],
                    'email' => $user['email'],
                    'name' => $user['name'],
                    'is_admin' => $user['is_admin'],
                    'last_login' => time()
                ];

                // Regenerate session ID for security
                session_regenerate_id(true);

                // Merge session cart with database cart
                if (isset($_SESSION['cart'])) {
                    $userId = $_SESSION['user']['user_id'];
                    
                    // Load database cart
                    $stmt = $pdo->prepare("SELECT book_id, quantity FROM cart_items WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    $dbCart = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

                    // Merge with session cart
                    foreach ($_SESSION['cart'] as $bookId => $qty) {
                        $dbQty = $dbCart[$bookId] ?? 0;
                        $newQty = max($qty, $dbQty);
                        
                        $stmt = $pdo->prepare("
                            INSERT INTO cart_items (user_id, book_id, quantity) 
                            VALUES (?, ?, ?)
                            ON DUPLICATE KEY UPDATE quantity = ?
                        ");
                        $stmt->execute([$userId, $bookId, $newQty, $newQty]);
                    }

                    // Reload merged cart from database
                    $stmt = $pdo->prepare("SELECT book_id, quantity FROM cart_items WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    $_SESSION['cart'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                }

                // Redirect to stored URL or home
                $redirectUrl = $_SESSION['redirect_url'] ?? 'index.php';
                unset($_SESSION['redirect_url']);
                header("Location: $redirectUrl");
                exit;
            } else {
                $error = "Invalid email or password.";
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
    <title>iBooks - Digital Library Login</title>
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
        </div>
    </div>

    <div class="login-container">
        <div class="login-header">
            <h1>Welcome Back!</h1>
            <p>Dive into a world of stories. Login and explore!</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message" role="alert">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST" autocomplete="on" novalidate>
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
                    >
                    <button type="button" class="password-toggle" aria-label="Toggle password visibility">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" id="loginButton">
                <i class="fas fa-sign-in-alt"></i> Enter Library
            </button>
        </form>

        <div class="auth-links">
            <p>
                <a href="forgot-password.php">Forgot Password?</a> • 
                <a href="register.php">Join Our Community</a>
            </p>
        </div>
    </div>

    <script>
        // Password Visibility Toggle
        const toggleButton = document.querySelector('.password-toggle');
        const passwordInput = document.getElementById('password');

        toggleButton.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            toggleButton.innerHTML = isPassword 
                ? '<i class="fas fa-eye-slash"></i>'
                : '<i class="fas fa-eye"></i>';
            
            // Force redraw to prevent layout shift
            passwordInput.style.width = '100%'; 
        });

        // Form Validation
        document.querySelector('form').addEventListener('submit', (e) => {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            if (!email || !password) {
                e.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    </script>
    <script src="scripts/main.js"></script>
</body>
</html>