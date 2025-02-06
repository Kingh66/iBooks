<?php
session_start(); // Ensure session is started
$pageTitle = "iBooks - Home";
include 'includes/navbar.php';
require_once 'includes/db.php'; // Database connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle; ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
  <main class="container">
    <!-- Hero Section -->
    <section class="hero">
      <div class="hero-content">
        <h1>Welcome to iBooks</h1>
        <p>Your digital library for book lovers. Explore, discover, and enjoy!</p>
        <form class="search-form" action="browse-books.php" method="GET">
            <input type="text" name="query" placeholder="Search for books..." aria-label="Search books">
            <button type="submit">Search</button>
        </form>
      </div>
    </section>

    <!-- Trending Books -->
    <section class="book-section">
      <h2>Trending Books</h2>
      <div class="book-grid">
        <?php
        try {
            $stmt = $pdo->query("
                SELECT * FROM books 
                WHERE is_bestseller = 1 
                ORDER BY created_at DESC 
                LIMIT 4
            ");
            while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) :
        ?>
            <div class="book-card">
                <a href="book-details.php?id=<?= $book['book_id'] ?>" class="book-card-link">
                    <div class="book-image">
                        <img src="images/<?= htmlspecialchars($book['image_url']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                        <?php if ($book['is_bestseller']): ?>
                            <div class="book-badge">Bestseller</div>
                        <?php endif; ?>
                    </div>
                    <div class="book-info">
                        <h3><?= htmlspecialchars($book['title']) ?></h3>
                        <p class="author"><?= htmlspecialchars($book['author']) ?></p>
                        <p class="price">R<?= number_format($book['price'], 2) ?></p>
                    </div>
                </a>
                <button class="btn add-to-cart" data-book-id="<?= $book['book_id'] ?>">Add to Cart</button>
            </div>
        <?php
            endwhile;
        } catch (PDOException $e) {
            echo "<p>Error loading trending books: " . $e->getMessage() . "</p>";
        }
        ?>
      </div>
    </section>

    <!-- Other sections (New Arrivals, Top Rated, Genre Categories, Seller Section) remain the same -->
    <!-- New Arrivals -->
<section class="book-section">
  <h2>New Arrivals</h2>
  <div class="book-grid">
    <?php
    try {
        $stmt = $pdo->query("
            SELECT * FROM books 
            ORDER BY created_at DESC 
            LIMIT 4
        ");
        while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) :
    ?>
        <div class="book-card">
            <a href="book-details.php?id=<?= $book['book_id'] ?>" class="book-card-link">
                <div class="book-image">
                    <img src="images/<?= htmlspecialchars($book['image_url']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                </div>
                <div class="book-info">
                    <h3><?= htmlspecialchars($book['title']) ?></h3>
                    <p class="author"><?= htmlspecialchars($book['author']) ?></p>
                    <p class="price">R<?= number_format($book['price'], 2) ?></p>
                </div>
            </a>
            <button class="btn add-to-cart" data-book-id="<?= $book['book_id'] ?>">Add to Cart</button>
        </div>
    <?php
        endwhile;
    } catch (PDOException $e) {
        echo "<p>Error loading new arrivals: " . $e->getMessage() . "</p>";
    }
    ?>
  </div>
</section>

    <!-- Top Rated Books -->
    <section class="book-section">
      <h2>Top Rated Books</h2>
      <div class="book-grid">
        <?php
        try {
            $stmt = $pdo->query("
                SELECT * FROM books 
                ORDER BY rating DESC 
                LIMIT 4
            ");
            while ($book = $stmt->fetch(PDO::FETCH_ASSOC)) :
        ?>
            <div class="book-card">
                <a href="book-details.php?id=<?= $book['book_id'] ?>" class="book-card-link">
                    <div class="book-image">
                        <img src="images/<?= htmlspecialchars($book['image_url']) ?>" alt="<?= htmlspecialchars($book['title']) ?>">
                    </div>
                    <div class="book-info">
                        <h3><?= htmlspecialchars($book['title']) ?></h3>
                        <p class="author"><?= htmlspecialchars($book['author']) ?></p>
                        <p class="price">R<?= number_format($book['price'], 2) ?></p>
                        <div class="rating">
                            <?= str_repeat('★', floor($book['rating'])) ?><?= (round($book['rating'] * 2) / 2) - floor($book['rating']) >= 0.5 ? '½' : '' ?>
                        </div>
                    </div>
                </a>
                <button class="btn add-to-cart" data-book-id="<?= $book['book_id'] ?>">Add to Cart</button>
            </div>
        <?php
            endwhile;
        } catch (PDOException $e) {
            echo "<p>Error loading top rated books: " . $e->getMessage() . "</p>";
        }
        ?>
      </div>
    </section>

    <!-- Genre Categories -->
    <section class="genre-section">
    <h2>Explore by Genre</h2>
    <div class="genre-grid">
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM categories LIMIT 4");
            while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) :
        ?>
            <a href="browse-books.php?category_id=<?= $category['category_id'] ?>" class="genre-card">
                <img src="images/category-<?= $category['category_id'] ?>.jpg" alt="<?= htmlspecialchars($category['name']) ?>">
                <h3><?= htmlspecialchars($category['name']) ?></h3>
            </a>
        <?php
            endwhile;
        } catch (PDOException $e) {
            echo "<p>Error loading categories: " . $e->getMessage() . "</p>";
        }
        ?>
    </div>
</section>

    <!-- Seller Section -->
    <?php if (isset($_SESSION['user']['is_admin']) && $_SESSION['user']['is_admin']): ?>
      <section class="seller-section">
        <h2>Seller Dashboard</h2>
        <p>Manage your books, track sales, and grow your business.</p>
        <a href="seller/dashboard.php" class="btn">Go to Dashboard</a>
      </section>
    <?php endif; ?>
  
  </main>
  <script>
document.querySelectorAll('.book-card .btn.add-to-cart').forEach(button => {
    button.addEventListener('click', async (e) => {
        e.stopPropagation();
        const bookId = button.dataset.bookId;

        try {
            const response = await fetch('add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ book_id: bookId })
            });

            const data = await response.json();

            if (data.success) {
                // Update cart count in navbar
                const cartCount = document.getElementById('cart-count');
                if (cartCount) {
                    cartCount.textContent = parseInt(cartCount.textContent) + 1;
                }
                
                // Show success feedback
                const successBadge = document.createElement('div');
                successBadge.className = 'cart-success-badge';
                successBadge.textContent = '✓ Added to Cart';
                button.parentElement.appendChild(successBadge);
                
                // Remove feedback after 2 seconds
                setTimeout(() => {
                    successBadge.remove();
                }, 2000);
                
            } else if (data.redirect) {
                // Redirect to login if not authenticated
                window.location.href = data.redirect;
            } else {
                // Show error message
                alert(data.message || 'Error adding to cart');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while adding to cart');
        }
    });
});
</script>
  <script src="scripts/main.js"></script>

  <?php include 'includes/footer.php'; ?>
</body>
</html>