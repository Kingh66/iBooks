<?php
session_start();
require_once 'includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$pageTitle = "iBooks - Edit Profile";
include 'includes/navbar.php';

// Initialize variables
$user = [];
$errors = [];
$success = false;

// Get current user data
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user']['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $errors[] = "User not found!";
    }
} catch (PDOException $e) {
    $errors[] = "Database error: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate inputs
    if (empty($name)) $errors[] = 'Name is required';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }

    // Check if password is being changed
    $passwordChanged = !empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword);
    
    if ($passwordChanged) {
        if (empty($currentPassword)) {
            $errors[] = 'Current password is required to change password';
        } elseif (!password_verify($currentPassword, $user['password_hash'])) {
            $errors[] = 'Current password is incorrect';
        }

        if (empty($newPassword)) {
            $errors[] = 'New password is required';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters';
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = 'New passwords do not match';
        }
    }

    // Update profile if no errors
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Prepare update query
            $query = "UPDATE users SET name = :name, email = :email";
            $params = [
                ':name' => $name,
                ':email' => $email,
                ':user_id' => $_SESSION['user']['user_id']
            ];

            // Add password update if changed
            if ($passwordChanged) {
                $query .= ", password_hash = :password_hash";
                $params[':password_hash'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            $query .= " WHERE user_id = :user_id";

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);

            // Update session data
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;

            $pdo->commit();
            $success = true;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors[] = "Error updating profile: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/styles.css">
    <style>
        .edit-profile-container {
            max-width: 800px;
            margin: 10rem auto 6rem;
            padding: 2rem;
            background: white;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
            font-weight: 600;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #eee;
            border-radius: 8px;
            font-size: 1rem;
            transition: var(--transition);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .password-toggle {
            position: relative;
        }

        .password-toggle i {
            position: absolute;
            right: 1rem;
            top: 70%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #95a5a6;
        }

        .btn-primary {
            background: var(--secondary-color);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-primary:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .edit-profile-container {
                margin: 4rem 1rem 2rem;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <main class="edit-profile-container">
        <h1>Edit Profile</h1>

        <?php if ($success): ?>
            <div class="alert alert-success">
                Profile updated successfully!
            </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p><?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label" for="name">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?= htmlspecialchars($_POST['name'] ?? $user['name']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($_POST['email'] ?? $user['email']) ?>" required>
            </div>

            <div class="form-group password-toggle">
                <label class="form-label" for="current_password">Current Password</label>
                <input type="password" class="form-control" id="current_password" name="current_password">
                <i class="fas fa-eye" onclick="togglePassword('current_password')"></i>
            </div>

            <div class="form-group password-toggle">
                <label class="form-label" for="new_password">New Password</label>
                <input type="password" class="form-control" id="new_password" name="new_password">
                <i class="fas fa-eye" onclick="togglePassword('new_password')"></i>
            </div>

            <div class="form-group password-toggle">
                <label class="form-label" for="confirm_password">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                <i class="fas fa-eye" onclick="togglePassword('confirm_password')"></i>
            </div>

            <button type="submit" class="btn-primary">Update Profile</button>
        </form>
    </main>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;
            if (field.type === "password") {
                field.type = "text";
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = "password";
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>

    <?php include 'includes/footer.php'; ?>
    <script src="scripts/main.js"></script>
</body>
</html>