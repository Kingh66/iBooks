<?php
session_start();
ob_start();
require_once 'includes/db.php';
$pageTitle = "iBooks - Book Details";
include 'includes/navbar.php';

// Get book ID from URL
$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch book details from database
try {
    $stmt = $pdo->prepare("
        SELECT b.*, c.name AS category 
        FROM books b
        JOIN categories c ON b.category_id = c.category_id
        WHERE b.book_id = ?
    ");
    $stmt->execute([$bookId]);
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$book) {
        header("Location: 404.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "Error loading book details.";
}

// Fetch reviews from database
$reviews = [];
try {
    $stmt = $pdo->prepare("
        SELECT r.*, u.name AS user 
        FROM reviews r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.book_id = ?
        ORDER BY r.created_at DESC
    ");
    $stmt->execute([$bookId]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
}

// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }

    $rating = (int)$_POST['rating'];
    $comment = trim($_POST['comment']);

    if ($rating < 1 || $rating > 5 || empty($comment)) {
        $reviewError = "Please provide valid rating (1-5) and comment.";
    } else {
        try {
            // Insert review
            $stmt = $pdo->prepare("
                INSERT INTO reviews (user_id, book_id, rating, comment)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $_SESSION['user']['user_id'],
                $bookId,
                $rating,
                htmlspecialchars($comment)
            ]);

            // Update book rating average
            $stmt = $pdo->prepare("
                UPDATE books 
                SET rating = (
                    SELECT AVG(rating) 
                    FROM reviews 
                    WHERE book_id = ?
                ) 
                WHERE book_id = ?
            ");
            $stmt->execute([$bookId, $bookId]);

            // Refresh page to show new review
            header("Location: book-details.php?id=$bookId");
            exit;
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $reviewError = "Error submitting review. Please try again.";
        }
    }
}

// Handle add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit;
    }

    $quantity = (int)$_POST['quantity'];
    $userId = $_SESSION['user']['user_id'];

    if ($quantity < 1 || $quantity > $book['stock']) {
        $cartError = "Invalid quantity selected.";
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO cart_items (user_id, book_id, quantity)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE quantity = quantity + ?
            ");
            $stmt->execute([$userId, $bookId, $quantity, $quantity]);
            
            $cartMessage = "Added $quantity copy(ies) of {$book['title']} to cart!";
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $cartError = "Error adding to cart. Please try again.";
        }
    }
}
ob_end_flush();
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
        .book-details {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
        }

        .book-main {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 3rem;
        margin-bottom: 4rem;
        }

        .book-image {
        position: relative;
        max-width: 400px;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }

        .book-image img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 0.3s ease;
        }

        .book-badge {
        position: absolute;
        top: 20px;
        left: -8px;
        background: var(--secondary-color);
        color: white;
        padding: 8px 20px;
        font-weight: 600;
        border-radius: 4px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        transform: rotate(-5deg);
        z-index: 1;
        }

        .book-info {
        padding: 1.5rem;
        }

        .book-info h1 {
        font-size: 2.75rem;
        margin-bottom: 0.75rem;
        color: #2d3436;
        }

        .author {
        font-size: 1.25rem;
        color: #636e72;
        margin-bottom: 1.5rem;
        }

        .price {
        font-size: 2rem;
        color: var(--secondary-color);
        font-weight: 700;
        margin: 1.5rem 0;
        }

        .stock {
        font-size: 1rem;
        color: #00b894;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        }

        .stock::before {
        content: '✔';
        color: currentColor;
        }

        /* Enhanced Quantity Selector */
        .quantity-selector {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin: 2rem 0;
        }

        .quantity-control {
        display: flex;
        align-items: center;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #dfe6e9;
        }

        .quantity-button {
        background: #f8f9fa;
        border: none;
        padding: 0.75rem 1rem;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 1.1rem;
        color: #2d3436;
        }

        .quantity-button:hover {
        background: #e3f2fd;
        color: var(--secondary-color);
        }

        .quantity-input {
        width: 60px;
        text-align: center;
        border: none;
        border-left: 1px solid #dfe6e9;
        border-right: 1px solid #dfe6e9;
        padding: 0.75rem;
        font-size: 1.1rem;
        -moz-appearance: textfield;
        }

        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        /* Enhanced Buttons */
        .btn {
        background: var(--secondary-color);
        color: white;
        border: none;
        padding: 1rem 2rem;
        border-radius: 8px;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        }

        .btn:hover {
        background: #c0392b;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(26, 115, 232, 0.25);
        }

        .btn:active {
        transform: translateY(0);
        }

        .btn::before {
        content: '🛒';
        font-size: 1.2rem;
        }

        /* Enhanced Review Form */
        .review-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        .review-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        .review-form input[type="text"],
        .review-form textarea,
        .review-form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .review-form button {
            padding: 10px 16px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .review-form button:hover {
            background-color: #0056b3;
        }

        .form-group select,
        .form-group textarea {
        border: 1px solid #b2bec3;
        border-radius: 8px;
        padding: 1rem;
        font-size: 1rem;
        transition: border-color 0.2s ease;
        }

        .form-group select:focus,
        .form-group textarea:focus {
        outline: none;
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
        }

        /* Enhanced Review Cards */
        .review-card {
        padding: 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        transition: transform 0.2s ease;
        }

        .review-card:hover {
        transform: translateY(-2px);
        }

        .review-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        }

        .user {
        font-weight: 600;
        color: #2d3436;
        }

        .date {
        color: #636e72;
        font-size: 0.9rem;
        }

        .rating {
        color: #fdcb6e;
        font-size: 1.1rem;
        }

        /* Enhanced Alert */
        .alert {
        padding: 1.25rem;
        border-radius: 8px;
        margin: 1.5rem 0;
        display: flex;
        align-items: center;
        gap: 1rem;
        }

        .alert.success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
        }

        .alert.success::before {
        content: '✓';
        font-size: 1.2rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
        .book-main {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        
        .book-info h1 {
            font-size: 2rem;
        }
        
        .btn {
            width: 100%;
            justify-content: center;
        }
        
        .review-form {
            padding: 1.5rem;
        }
        }

        h3.reviews-title {
        margin-top: 20px;
        margin-bottom: 15px;
        font-size: 1.5rem;
        color: #333;
        border-bottom: 2px solid #ddd;
        padding-bottom: 5px;
    }

    /* Style for Individual Review Blocks */
    .review-block {
        margin-bottom: 20px;
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: #f9f9f9;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .review-block p {
        margin: 5px 0;
    }

    .review-block strong {
        font-weight: bold;
        color: #555;
    }

    .review-block em {
        font-style: italic;
        color: #777;
        font-size: 0.9rem;
    }

    .review-block .rating-stars {
        color: #ff9900; /* Gold color for stars */
        font-size: 1.1rem;
    }

    </style>
</head>
<body>
    <main class="container book-details">
        <!-- Error Messages -->
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <!-- Book Main Info -->
        <?php if ($book): ?>
            <section class="book-main">
                <div class="book-image">
                    <img src="images/<?= htmlspecialchars($book['image_url']) ?>" 
                         alt="<?= htmlspecialchars($book['title']) ?>">
                    <?php if ($book['is_bestseller']): ?>
                        <div class="book-badge">Bestseller</div>
                    <?php endif; ?>
                </div>
                
                <div class="book-info">
                    <h1><?= htmlspecialchars($book['title']) ?></h1>
                    <p class="author">by <?= htmlspecialchars($book['author']) ?></p>
                    <div class="rating">
                        <?= str_repeat('★', floor($book['rating'])) ?>
                        <?= (round($book['rating'] * 2) / 2) - floor($book['rating']) >= 0.5 ? '½' : '' ?>
                        <span>(<?= count($reviews) ?> reviews)</span>
                    </div>
                    
                    <p class="price">R<?= number_format($book['price'], 2) ?></p>
                    <p class="stock">In Stock: <?= $book['stock'] ?></p>
                    
                    <form class="add-to-cart" method="POST">
                        <div class="quantity-selector">
                            <label>Quantity:</label>
                            <input type="number" name="quantity" 
                                   min="1" max="<?= $book['stock'] ?>" value="1">
                        </div>
                        <button type="submit" name="add_to_cart" class="btn">
                            Add to Cart
                        </button>
                    </form>
                    
                    <?php if (isset($cartMessage)): ?>
                        <div class="alert success"><?= $cartMessage ?></div>
                    <?php endif; ?>
                    <?php if (isset($cartError)): ?>
                        <div class="alert error"><?= $cartError ?></div>
                    <?php endif; ?>
                </div>
            </section>

            <!-- Book Description -->
            <section class="book-description">
                <h2>Description</h2>
                <p><?= htmlspecialchars($book['description']) ?></p>
            </section>

            <!-- Reviews Section -->
            <section class="reviews">
                <h2>Write a Review</h2>
                <?php if (isset($_SESSION['user'])): ?>
                    <form class="review-form" method="post">
                        <label for="rating">Rating:</label>
                        <select id="rating" name="rating" required>
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <option value="<?= $i ?>"><?= $i ?> ★</option>
                            <?php endfor; ?>
                        </select>

                        <label for="comment">Comment:</label>
                        <textarea id="comment" name="comment" rows="4" required></textarea>

                        <button type="submit" name="submit_review">Submit Review</button>
                    </form>
                <?php else: ?>
                    <p class="login-prompt">
                        Please <a href="login.php">login</a> to write a review.
                    </p>
                <?php endif; ?>

                <h3 class="reviews-title">Reviews (<?= count($reviews) ?>)</h3>
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="review-block">
                            <p><strong><?= htmlspecialchars($review['user']) ?>:</strong> 
                                <span class="rating-stars">
                                    <?= str_repeat('★', $review['rating']) ?>
                                </span>
                            </p>
                            <p><em>
                                <?= date('M j, Y', strtotime($review['created_at'])) ?>
                            </em></p>
                            <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="no-reviews">No reviews yet. Be the first to write one!</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
    <!-- Add this script at the bottom of your book-details.php file -->
<script>
document.querySelector('form.add-to-cart').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const form = e.target;
    const quantity = form.querySelector('input[name="quantity"]').value;
    const bookId = <?= $bookId ?>; // Use PHP to inject the book ID

    try {
        const response = await fetch('add-to-cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ 
                book_id: bookId,
                quantity: quantity 
            })
        });

        const data = await response.json();

        if (data.success) {
            // Update cart count in navbar
            const cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = data.new_total_items;
            }
            
            // Show success feedback
            const successBadge = document.createElement('div');
            successBadge.className = 'cart-success-badge';
            successBadge.textContent = `✓ Added ${quantity} item(s) to Cart`;
            form.appendChild(successBadge);
            
            // Remove feedback after 2 seconds
            setTimeout(() => {
                successBadge.remove();
            }, 2000);
            
        } else if (data.redirect) {
            window.location.href = data.redirect;
        } else {
            alert(data.message || 'Error adding to cart');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while adding to cart');
    }
});
</script>
    <script src="scripts/main.js"></script>
</body>
</html>