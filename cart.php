<?php
session_start();
require_once 'includes/db.php'; // Database connection

$pageTitle = "iBooks - Cart";
include 'includes/navbar.php';

// Initialize cart if not set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $bookId = filter_input(INPUT_POST, 'book_id', FILTER_VALIDATE_INT);
  $action = $_POST['action'] ?? '';
  $allowed_actions = ['increment', 'decrement'];
  $action = in_array($action, $allowed_actions) ? $action : '';

  if ($bookId && $action) {
      try {
          $stmt = $pdo->prepare("SELECT stock FROM books WHERE book_id = ?");
          $stmt->execute([$bookId]);
          $book = $stmt->fetch();

          if ($book) {
              if (isset($_SESSION['user'])) {
                  // Handle database cart for logged-in users
                  $userId = $_SESSION['user']['user_id'];
                  
                  // Get current quantity from database
                  $stmt = $pdo->prepare("SELECT quantity FROM cart_items WHERE user_id = ? AND book_id = ?");
                  $stmt->execute([$userId, $bookId]);
                  $currentQty = $stmt->fetchColumn() ?? 0;
              } else {
                  // Handle session cart for guests
                  $currentQty = $_SESSION['cart'][$bookId] ?? 0;
              }

              if ($action === 'increment') {
                  $newQty = $currentQty + 1;
                  if ($newQty > $book['stock']) {
                      $error = "Insufficient stock for this item";
                  } else {
                      if (isset($_SESSION['user'])) {
                          // Update database
                          $stmt = $pdo->prepare("
                              INSERT INTO cart_items (user_id, book_id, quantity) 
                              VALUES (:user_id, :book_id, 1)
                              ON DUPLICATE KEY UPDATE quantity = quantity + 1
                          ");
                          $stmt->execute([':user_id' => $userId, ':book_id' => $bookId]);
                      } else {
                          // Update session
                          $_SESSION['cart'][$bookId] = $newQty;
                      }
                  }
              } elseif ($action === 'decrement') {
                  $newQty = max(0, $currentQty - 1);
                  if (isset($_SESSION['user'])) {
                      if ($newQty > 0) {
                          // Update database
                          $stmt = $pdo->prepare("
                              UPDATE cart_items SET quantity = ? 
                              WHERE user_id = ? AND book_id = ?
                          ");
                          $stmt->execute([$newQty, $userId, $bookId]);
                      } else {
                          // Remove from database
                          $stmt = $pdo->prepare("DELETE FROM cart_items WHERE user_id = ? AND book_id = ?");
                          $stmt->execute([$userId, $bookId]);
                      }
                  } else {
                      // Update session
                      $_SESSION['cart'][$bookId] = $newQty;
                      if ($newQty === 0) {
                          unset($_SESSION['cart'][$bookId]);
                      }
                  }
              }
          }
      } catch (PDOException $e) {
          error_log("Database error: " . $e->getMessage());
          $error = "Error processing your request";
      }
  }
}

// Get cart items (merged for logged-in users)
$cartItems = [];
$totalPrice = 0;

if (isset($_SESSION['user'])) {
    // Fetch from database for logged-in users
    try {
        $stmt = $pdo->prepare("
            SELECT b.book_id, b.title, b.price, b.image_url, b.stock, ci.quantity 
            FROM cart_items ci
            JOIN books b ON ci.book_id = b.book_id
            WHERE ci.user_id = ?
        ");
        $stmt->execute([$_SESSION['user']['user_id']]);
        $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $error = "Error loading cart items";
    }
} else {
    // Fetch from session for guests
    if (!empty($_SESSION['cart'])) {
        try {
            $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
            $stmt = $pdo->prepare("
                SELECT b.book_id, b.title, b.price, b.image_url, b.stock 
                FROM books b 
                WHERE b.book_id IN ($placeholders)
            ");
            $stmt->execute(array_keys($_SESSION['cart']));
            
            while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $book['quantity'] = $_SESSION['cart'][$book['book_id']];
                $cartItems[] = $book;
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = "Error loading cart items";
        }
    }
}

// Calculate total price
foreach ($cartItems as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}
// Get recommendations (exclude items already in cart)
// Get recommendations (exclude cart items)
$recommendations = [];
try {
    $exclude = array_column($cartItems, 'book_id');
    $exclude = empty($exclude) ? [0] : $exclude;
    
    $placeholders = str_repeat('?,', count($exclude) - 1) . '?';
    $stmt = $pdo->prepare("
        SELECT book_id, title, price, image_url 
        FROM books 
        WHERE book_id NOT IN ($placeholders) 
        ORDER BY RAND() 
        LIMIT 4
    ");
    $stmt->execute($exclude);
    $recommendations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
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
    /* Cart Page Styles */
.cart-page {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}
.cart-items {
  margin-bottom: 2rem;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
}
.cart-item {
  display: flex;
  align-items: center;
  gap: 1.5rem;
  padding: 1.5rem;
  border-bottom: 1px solid #eee;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.cart-item:last-child {
  border-bottom: none;
}
.cart-item:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.cart-item img {
  width: 100px;
  height: 150px;
  object-fit: cover;
  border-radius: 8px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.item-details {
  flex-grow: 1;
}
.item-details h3 {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  color: var(--primary-color);
}
.item-details .price {
  font-size: 1.2rem;
  color: var(--secondary-color);
  margin-bottom: 1rem;
  font-weight: 600;
}
/* Improved Quantity Buttons */
.quantity-control {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  background: #f8f9fa;
  border-radius: 8px;
  padding: 0.25rem;
}

.quantity-button {
  background: none;
  color: var(--secondary-color);
  border: 2px solid var(--secondary-color);
  padding: 0.5rem 1rem;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1.2rem;
  line-height: 1;
}

.quantity-button:hover {
  background: rgba(231, 76, 60, 0.1);
  color: #c0392b;
  border-color: #c0392b;
}

.quantity {
  font-size: 1.2rem;
  font-weight: 600;
  min-width: 36px;
  text-align: center;
}
.total-price {
  text-align: right;
  margin: 2rem 0;
  font-size: 1.75rem;
  color: var(--primary-color);
  font-weight: 700;
}
.recommendations {
  margin-top: 3rem;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  padding: 2rem;
  margin-bottom: 2rem; /* Added bottom margin */
}
.recommendations h2 {
  font-size: 2rem;
  margin-bottom: 1.5rem;
  color: var(--primary-color);
}
.book-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
  gap: 1.5rem;
}


.checkout-section {
  text-align: center;
  margin: 4rem 0 3rem; /* Added top margin */
  padding-top: 2rem;
  border-top: 1px solid #eee;
}
.checkout-button {
  background: var(--secondary-color);
  color: white;
  padding: 1.25rem 2.5rem;
  font-size: 1.25rem;
  border-radius: 8px;
  text-decoration: none;
  transition: background 0.3s ease, transform 0.2s ease;
  display: inline-block;
  box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
}
.checkout-button:hover {
  background: #c0392b;
  transform: translateY(-2px);
}
.empty-cart {
  text-align: center;
  font-size: 1.25rem;
  color: #666;
  padding: 2rem;
}
.empty-cart a {
  color: var(--secondary-color);
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}
.empty-cart a:hover {
  color: #c0392b;
  text-decoration: underline;
}
.stock-info {
      font-size: 0.9rem;
      color: #666;
      margin-top: 0.5rem;
    }
    .stock-info.low {
      color: #e74c3c;
    }
    .error-message {
      color: #e74c3c;
      padding: 1rem;
      border-radius: 8px;
      background: #fee;
      margin-bottom: 1rem;
    }
  </style>
</head>
<body>
    <main class="container cart-page">
        <h1>Your Cart</h1>

        <!-- Cart Items -->
        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <section class="cart-items">
            <?php if (empty($cartItems)): ?>
                <p class="empty-cart">Your cart is empty. <a href="browse-books.php">Browse books</a> to add some!</p>
            <?php else: ?>
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">
                        <img src="images/<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                        <div class="item-details">
                            <h3><?= htmlspecialchars($item['title']) ?></h3>
                            <p class="price">R<?= number_format($item['price'], 2) ?></p>
                            <div class="stock-info <?= $item['stock'] < 5 ? 'low' : '' ?>">
                                Stock: <?= $item['stock'] ?>
                            </div>
                            <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                                <input type="hidden" name="book_id" value="<?= $item['book_id'] ?>">
                                <div class="quantity-control">
                                    <button type="submit" name="action" value="decrement" class="quantity-button">−</button>
                                    <span class="quantity"><?= $item['quantity'] ?></span>
                                    <button type="submit" name="action" value="increment" class="quantity-button">+</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <!-- Total Price -->
        <div class="total-price">
            <h3>Total: R<?= number_format($totalPrice, 2) ?></h3>
        </div>

        <!-- Recommendations -->
        <section class="recommendations">
            <h2>You Might Also Like</h2>
            <div class="book-grid">
                <?php foreach ($recommendations as $book): ?>
                    <div class="book-card">
                    <a href="book-details.php?id=<?= $book['book_id'] ?>" class="book-card-link">
                        <img src="images/<?= htmlspecialchars($book['image_url']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                        <h3><?= htmlspecialchars($book['title']) ?></h3>
                        <p class="price">R<?= number_format($book['price'], 2) ?></p>
                        <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                            <input type="hidden" name="book_id" value="<?= $book['book_id'] ?>">
                            <button type="submit" name="action" value="increment" class="btn">Add to Cart</button>
                        </form>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if (!empty($cartItems)): ?>
            <div class="checkout-section">
                <?php if (isset($_SESSION['user']['user_id'])): ?>
                    <a href="checkout.php" class="btn checkout-button">Proceed to Checkout</a>
                <?php else: ?>
                    <p class="empty-cart">Please <a href="login.php">login</a> to proceed to checkout</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
    <script src="scripts/main.js"></script>
</body>
</html>