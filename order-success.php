<?php
session_start();
require_once 'includes/db.php';
$pageTitle = "iBooks - Order Success";
include 'includes/navbar.php';

// Check if order ID is set
if (!isset($_SESSION['order_id'])) {
    header("Location: index.php");
    exit;
}

// Get order details
$orderId = $_SESSION['order_id'];
$orderDetails = [];
$orderItems = [];

try {
    // Fetch order details
    $stmt = $pdo->prepare("
        SELECT o.order_id, o.total_amount, o.created_at, u.name AS customer_name
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        WHERE o.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $orderDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch order items
    $stmt = $pdo->prepare("
        SELECT b.title, oi.quantity, oi.price
        FROM order_items oi
        JOIN books b ON oi.book_id = b.book_id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error fetching order details: " . $e->getMessage();
}

// Clear order ID from session
unset($_SESSION['order_id']);
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
        .success-container {
            max-width: 800px;
            margin: 8rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            text-align: center;
        }

        .success-icon {
            font-size: 4rem;
            color: #2ecc71;
            margin-bottom: 1rem;
        }

        .order-details {
            margin-top: 2rem;
            text-align: left;
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #eee;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .total-amount {
            font-size: 1.25rem;
            font-weight: bold;
            text-align: right;
            margin-top: 1rem;
        }

        .btn-continue {
            background: #3498db;
            color: white;
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-top: 2rem;
            transition: background 0.3s ease;
        }

        .btn-continue:hover {
            background: #2980b9;
        }
    </style>
</head>
<body>
    <main class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h1>Thank You!</h1>
        <p>Your order has been successfully placed.</p>
        <p>Order ID: <strong>#<?= $orderId ?></strong></p>

        <?php if ($orderDetails): ?>
            <div class="order-details">
                <h2>Order Summary</h2>
                <p><strong>Customer:</strong> <?= htmlspecialchars($orderDetails['customer_name']) ?></p>
                <p><strong>Order Date:</strong> <?= date('M j, Y H:i', strtotime($orderDetails['created_at'])) ?></p>

                <h3>Items Purchased</h3>
                <?php foreach ($orderItems as $item): ?>
                    <div class="order-item">
                        <span><?= htmlspecialchars($item['title']) ?> (x<?= $item['quantity'] ?>)</span>
                        <span>R<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                    </div>
                <?php endforeach; ?>

                <div class="total-amount">
                    Total: R<?= number_format($orderDetails['total_amount'], 2) ?>
                </div>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn-continue">
            <i class="fas fa-arrow-left"></i> Continue Shopping
        </a>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="scripts/main.js"></script>
</body>
</html>