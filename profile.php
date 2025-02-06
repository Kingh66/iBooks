<?php
session_start();
require_once 'includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$pageTitle = "iBooks - My Profile";
include 'includes/navbar.php';

// Initialize variables
$user = [];
$orders = [];
$cartItems = [];
$error = '';

try {
    // Get user data using session user_id
    $userId = $_SESSION['user']['user_id'];
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "User not found!";
    } else {
        // Get user orders
        $orderStmt = $pdo->prepare("
            SELECT o.*, SUM(oi.quantity * oi.price) AS total 
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            WHERE o.user_id = ?
            GROUP BY o.order_id
            ORDER BY o.created_at DESC
        ");
        $orderStmt->execute([$userId]);
        $orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

        // Get cart items
        $cartStmt = $pdo->prepare("
            SELECT ci.*, b.title, b.price, b.image_url 
            FROM cart_items ci
            JOIN books b ON ci.book_id = b.book_id
            WHERE ci.user_id = ?
        ");
        $cartStmt->execute([$userId]);
        $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
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
    

    .profile-container {
        max-width: 1200px;
        margin: 10rem auto 2rem;
        padding: 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #eee;
    }

    .profile-header h1 {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin: 0;
    }

    .account-info {
        text-align: right;
        color: #7f8c8d;
    }

    .user-info {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 3rem;
        position: relative;
        overflow: hidden;
    }

    .user-info h3 {
        color: var(--primary-color);
        margin-top: 0;
        margin-bottom: 1.5rem;
    }

    .btn-primary {
        background: var(--secondary-color);
        color: white;
        padding: 0.8rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: var(--transition);
    }

    .btn-primary:hover {
        background: #c0392b;
        transform: translateY(-2px);
    }

    section {
        margin-bottom: 3rem;
    }

    section h2 {
        color: var(--primary-color);
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #eee;
    }

    .cart-item, .order-card {
        display: flex;
        align-items: center;
        gap: 2rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        transition: var(--transition);
    }

    .cart-item:hover, .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .book-image {
        width: 100px;
        height: 150px;
        object-fit: cover;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .status {
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status.pending { background: #f1c40f20; color: #f39c12; }
    .status.completed { background: #2ecc7020; color: #27ae60; }
    .status.cancelled { background: #e74c3c20; color: #c0392b; }

    .text-muted {
        color: #95a5a6 !important;
        font-size: 0.9rem;
    }

    .text-end {
        text-align: right;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        border: 1px solid #f5c6cb;
    }

    .d-flex {
        display: flex;
    }

    .align-items-center {
        align-items: center;
    }

    .justify-content-between {
        justify-content: space-between;
    }

    .gap-3 {
        gap: 1rem;
    }

    .mb-2 {
        margin-bottom: 0.5rem;
    }

    .mt-3 {
        margin-top: 1rem;
    }

    @media (max-width: 768px) {
        .profile-container {
            margin: 4rem 1rem 2rem;
            padding: 1.5rem;
        }

        .profile-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .account-info {
            text-align: left;
        }

        .cart-item, .order-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .book-image {
            width: 100%;
            height: 200px;
        }

        section h2 {
            font-size: 1.5rem;
        }
    }
</style>
</head>
<body>
    <main class="profile-container">
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php else: ?>
            <div class="profile-header">
                <h1>Welcome, <?= htmlspecialchars($user['name']) ?></h1>
                <div class="account-info">
                    <p>Member since: <?= date('M Y', strtotime($user['created_at'])) ?></p>
                    <?php if ($user['last_login']): ?>
                        <p>Last login: <?= date('M j, Y g:i a', strtotime($user['last_login'])) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="user-info">
                <h3>Account Details</h3>
                <p>Email: <?= htmlspecialchars($user['email']) ?></p>
                <a href="edit_profile.php" class="btn btn-primary">Edit Profile</a>
            </div>

            <section class="cart-section">
                <h2>Shopping Cart (<?= count($cartItems) ?>)</h2>
                <?php if ($cartItems): ?>
                    <?php foreach ($cartItems as $item): ?>
                        <div class="cart-item d-flex align-items-center gap-3">
                            <img src="images/<?= htmlspecialchars($item['image_url']) ?>" 
                                 class="book-image" 
                                 alt="<?= htmlspecialchars($item['title']) ?>">
                            <div>
                                <h4><?= htmlspecialchars($item['title']) ?></h4>
                                <p>Quantity: <?= $item['quantity'] ?></p>
                                <p>Price: R<?= number_format($item['price'], 2) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Your cart is empty</p>
                <?php endif; ?>
            </section>

            <section class="order-history">
                <h2>Order History</h2>
                <?php if ($orders): ?>
                    <?php foreach ($orders as $order): ?>
                        <div class="order-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <h3>Order #<?= $order['order_id'] ?></h3>
                                    <small class="text-muted">
                                        <?= date('M j, Y g:i a', strtotime($order['created_at'])) ?>
                                    </small>
                                </div>
                                <div class="status <?= $order['status'] ?>">
                                    <?= ucfirst($order['status']) ?>
                                </div>
                            </div>
                            
                            <?php
                            // Get order items
                            $itemsStmt = $pdo->prepare("
                                SELECT oi.*, b.title, b.image_url 
                                FROM order_items oi
                                JOIN books b ON oi.book_id = b.book_id
                                WHERE oi.order_id = ?
                            ");
                            $itemsStmt->execute([$order['order_id']]);
                            $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                            ?>
                            
                            <?php foreach ($items as $item): ?>
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <img src="images/<?= htmlspecialchars($item['image_url']) ?>" 
                                         class="book-image" 
                                         alt="<?= htmlspecialchars($item['title']) ?>">
                                    <div>
                                        <h4><?= htmlspecialchars($item['title']) ?></h4>
                                        <p>Quantity: <?= $item['quantity'] ?></p>
                                        <p>Price: R<?= number_format($item['price'], 2) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                            <div class="text-end mt-3">
                                <strong>Total: R<?= number_format($order['total_amount'], 2) ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No orders found</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="scripts/main.js"></script>
</body>
</html>