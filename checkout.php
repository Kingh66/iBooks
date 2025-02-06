<?php
session_start();
ob_start(); // Start output buffering

require_once 'includes/db.php';
$pageTitle = "iBooks - Checkout";
include 'includes/navbar.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Get cart items
$userId = $_SESSION['user']['user_id'];
$cartItems = [];
$totalAmount = 0;

try {
    $stmt = $pdo->prepare("
        SELECT b.book_id, b.title, b.price, ci.quantity 
        FROM cart_items ci
        JOIN books b ON ci.book_id = b.book_id
        WHERE ci.user_id = ?
    ");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($cartItems as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }
    
} catch (PDOException $e) {
    $error = "Error loading cart items: " . $e->getMessage();
}

// Handle Paystack callback
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference = $_POST['reference'];
    
    // Verify transaction with Paystack
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer sk_test_827ff56f1e94d2faded5bd7051b03ed7e87dc050",
            "Cache-Control: no-cache",
        ],
        CURLOPT_SSL_VERIFYPEER => true, // Enable SSL verification
        CURLOPT_SSL_VERIFYHOST => 2, // Verify host
        CURLOPT_CAINFO => __DIR__ . '/cacert.pem', // Path to your CA certificate
    ]);
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);
    
    if ($err) {
        $error = "cURL Error: " . $err;
    } else {
        $result = json_decode($response);
        
        if ($result->data->status === 'success') {
            // Verify the amount matches
            $paidAmount = $result->data->amount / 100; // Convert from cents to ZAR
            if ($paidAmount != $totalAmount) {
                $error = "Paid amount does not match order total";
            } else {
                try {
                    // Start transaction
                    $pdo->beginTransaction();

                    // Create order
                    $stmt = $pdo->prepare("
                        INSERT INTO orders 
                        (user_id, total_amount, status, created_at)
                        VALUES (?, ?, 'completed', NOW())
                    ");
                    $stmt->execute([$userId, $totalAmount]);
                    $orderId = $pdo->lastInsertId();

                    // Create order items
                    $stmt = $pdo->prepare("
                        INSERT INTO order_items 
                        (order_id, book_id, quantity, price)
                        VALUES (?, ?, ?, ?)
                    ");
                    
                    foreach ($cartItems as $item) {
                        $stmt->execute([
                            $orderId,
                            $item['book_id'],
                            $item['quantity'],
                            $item['price']
                        ]);
                    }

                    // Clear cart
                    $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ?");
                    $stmt->execute([$userId]);

                    $pdo->commit();
                    
                    $_SESSION['order_id'] = $orderId;
                    header("Location: order-success.php");
                    exit;
                    
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $error = "Error processing order: " . $e->getMessage();
                }
            }
        } else {
            $error = "Payment failed: " . $result->data->gateway_response;
        }
    }
}

ob_end_flush(); // End output buffering
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styles/styles.css">
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 8rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .order-summary {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #eee;
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: right;
            margin-top: 1.5rem;
        }

        .payment-form {
            margin-top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .paystack-button {
            background: #00A859;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1.1rem;
            width: 100%;
            transition: background 0.3s ease;
        }

        .paystack-button:hover {
            background: #008F4A;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <main class="checkout-container">
        <h1>Checkout</h1>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="order-summary">
            <h2>Order Summary</h2>
            <?php foreach ($cartItems as $item): ?>
                <div class="order-item">
                    <span><?= htmlspecialchars($item['title']) ?> (x<?= $item['quantity'] ?>)</span>
                    <span>ZAR <?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                </div>
            <?php endforeach; ?>
            <div class="total-amount">
                Total: R <?= number_format($totalAmount, 2) ?>
            </div>
        </div>

        <form class="payment-form" id="paymentForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" class="form-control" 
                       value="<?= $_SESSION['user']['email'] ?>" required readonly>
            </div>

            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="text" id="amount" class="form-control" 
                       value="<?= $totalAmount ?>" required readonly>
            </div>

            <button type="button" class="paystack-button" onclick="payWithPaystack()">Pay with Paystack</button>
        </form>
    </main>

    <script>
        function payWithPaystack() {
            const handler = PaystackPop.setup({
                key: 'pk_test_87bff2d72b89d74e10f2de06aa716f88bce79e76',
                email: document.getElementById('email').value,
                amount: document.getElementById('amount').value * 100, // Convert to cents
                currency: 'ZAR', // Use ZAR for South African Rand
                ref: 'IBOOKS_' + Math.floor((Math.random() * 1000000000) + 1), // Unique reference
                onClose: function() {
                    alert('Payment cancelled');
                },
                callback: function(response) {
                    // Submit form with transaction reference
                    const form = document.createElement('form');
                    form.method = 'POST';
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'reference';
                    input.value = response.reference;
                    
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
            
            // Open the Paystack payment modal
            handler.openIframe();
        }
    </script>

    <?php include 'includes/footer.php'; ?>
    <script src="scripts/main.js"></script>
</body>
</html>