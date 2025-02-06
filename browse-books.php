<?php
session_start();
require_once 'includes/db.php'; // Database connection

$pageTitle = "iBooks - Browse Books";
include 'includes/navbar.php';


// Get filter and sort parameters
// Get category ID from URL
$categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

// Fetch category name if filtering by category
$categoryName = '';
if ($categoryId > 0) {
    try {
        $stmt = $pdo->prepare("SELECT name FROM categories WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        $category = $stmt->fetch();
        $categoryName = $category ? $category['name'] : '';
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $categoryName = '';
    }
}
$searchQuery = isset($_GET['query']) ? trim($_GET['query']) : '';
$minPrice = isset($_GET['minPrice']) ? (float)$_GET['minPrice'] : 0;
$maxPrice = isset($_GET['maxPrice']) ? (float)$_GET['maxPrice'] : 1000;
$selectedCategories = isset($_GET['categories']) ? $_GET['categories'] : [];
$selectedRating = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;
$sortBy = isset($_GET['sortBy']) ? $_GET['sortBy'] : 'popularity';

// Build SQL query
$query = "SELECT b.*, c.name AS category 
          FROM books b 
          JOIN categories c ON b.category_id = c.category_id 
          WHERE 1=1";

$params = [];

if ($categoryId > 0) {
  $query .= " AND b.category_id = ?";
  $params[] = $categoryId;
}

// Apply search filter
if (!empty($searchQuery)) {
    $query .= " AND (LOWER(b.title) LIKE ? OR LOWER(b.author) LIKE ?)";
    $params[] = '%' . strtolower($searchQuery) . '%';
    $params[] = '%' . strtolower($searchQuery) . '%';
}

// Apply other filters (price, category, rating, etc.)
if (!empty($selectedCategories)) {
    $placeholders = str_repeat('?,', count($selectedCategories) - 1) . '?';
    $query .= " AND c.name IN ($placeholders)";
    $params = array_merge($params, $selectedCategories);
}

if ($selectedRating > 0) {
    $query .= " AND b.rating >= ?";
    $params[] = $selectedRating;
}

// Apply sorting
switch ($sortBy) {
    case 'price-low':
        $query .= " ORDER BY b.price ASC";
        break;
    case 'price-high':
        $query .= " ORDER BY b.price DESC";
        break;
    case 'newest':
        $query .= " ORDER BY b.created_at DESC";
        break;
    case 'rating':
        $query .= " ORDER BY b.rating DESC";
        break;
    default:
        $query .= " ORDER BY b.stock DESC"; // Default: Popularity
}

// Fetch books from the database
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $books = [];
    $error = "Error loading books. Please try again later.";
}

// Fetch all categories for the filter sidebar
try {
    $stmt = $pdo->query("SELECT name FROM categories");
    $allCategories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $allCategories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo $pageTitle; ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="styles/styles.css">
  <style>
    .search-results-header {
    margin-bottom: 2rem;
    text-align: center;
    }

    .search-results-header h2 {
        font-size: 1.5rem;
        margin-bottom: 0.5rem;
    }

    .search-results-header p {
        color: #666;
        font-size: 1rem;
    }
    .category-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
}

.btn-clear {
    background: #e74c3c;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
    transition: background 0.3s ease;
}

.btn-clear:hover {
    background: #c0392b;
}
  </style>
</head>
<body>
  <main class="container browse-container">
    <!-- Filters Sidebar -->
    <aside class="filters-sidebar">
      <h2>Filter By</h2>
      <form id="filterForm" method="GET" action="browse-books.php">
        <!-- Search Bar -->
        <div class="filter-group">
          <h3>Search</h3>
          <input type="text" name="query" placeholder="Search books..." value="<?php echo htmlspecialchars($searchQuery); ?>">
        </div>

        <!-- Price Filter -->
        <div class="filter-group">
          <h3>Price Range</h3>
          <div class="price-range">
            <input type="range" id="priceRange" name="maxPrice" min="0" max="1000" value="<?php echo $maxPrice; ?>">
            <div class="price-values">
              <span>R0</span>
              <span>R<?php echo $maxPrice; ?></span>
            </div>
          </div>
        </div>

        <!-- Category Filter -->
        <div class="filter-group">
          <h3>Categories</h3>
          <ul class="category-list">
            <?php foreach ($allCategories as $category): ?>
              <li>
                <input type="checkbox" id="<?php echo strtolower($category); ?>" name="categories[]" value="<?php echo $category; ?>" 
                       <?php echo in_array($category, $selectedCategories) ? 'checked' : ''; ?>>
                <label for="<?php echo strtolower($category); ?>"><?php echo $category; ?></label>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <!-- Rating Filter -->
        <div class="filter-group">
          <h3>Rating</h3>
          <div class="rating-filter">
            <?php for($i = 5; $i >= 1; $i--): ?>
              <div class="star-rating">
                <input type="radio" id="rating<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" 
                       <?php echo $selectedRating == $i ? 'checked' : ''; ?>>
                <label for="rating<?php echo $i; ?>"><?php echo str_repeat('★', $i); ?></label>
              </div>
            <?php endfor; ?>
          </div>
        </div>

        <input type="hidden" name="sortBy" value="<?php echo $sortBy; ?>">
        <button type="submit" class="btn apply-filters">Apply Filters</button>
      </form>
    </aside>

    <!-- Main Content -->
    <section class="book-listings">
    <!-- Search Results Header -->
    <?php if (!empty($searchQuery)): ?>
        <div class="search-results-header">
            <h2>Search Results for "<?= htmlspecialchars($searchQuery) ?>"</h2>
            <p><?= count($books) ?> result(s) found.</p>
        </div>
    <?php endif; ?>

    <!-- Category Filter Header -->
    <?php if ($categoryId > 0): ?>
        <div class="category-header">
            <h2>Books in <?= htmlspecialchars($categoryName) ?></h2>
            <a href="browse-books.php" class="btn btn-clear">Clear Filter</a>
        </div>
    <?php endif; ?>

    <!-- Sorting Options -->
    <div class="sort-options">
        <span>Sort By:</span>
        <select id="sortSelect" onchange="updateSort(this.value)">
            <option value="popularity" <?= $sortBy === 'popularity' ? 'selected' : ''; ?>>Popularity</option>
            <option value="price-low" <?= $sortBy === 'price-low' ? 'selected' : ''; ?>>Price: Low to High</option>
            <option value="price-high" <?= $sortBy === 'price-high' ? 'selected' : ''; ?>>Price: High to Low</option>
            <option value="newest" <?= $sortBy === 'newest' ? 'selected' : ''; ?>>Newest Arrivals</option>
            <option value="rating" <?= $sortBy === 'rating' ? 'selected' : ''; ?>>Rating</option>
        </select>
    </div>

    <!-- Book Grid -->
    <div class="book-grid">
        <?php if (empty($books)): ?>
            <p class="no-results">No books found matching your search or filters.</p>
        <?php else: ?>
            <?php foreach ($books as $book): ?>
                <div class="book-card">
                    <a href="book-details.php?id=<?= $book['book_id'] ?>" class="book-card-link">
                        <div class="book-image">
                            <img src="images/<?= htmlspecialchars($book['image_url']) ?>" 
                                 alt="<?= htmlspecialchars($book['title']) ?>">
                            <?php if ($book['is_bestseller']): ?>
                                <div class="book-badge">Bestseller</div>
                            <?php endif; ?>
                        </div>
                        <div class="book-details">
                            <h3><?= htmlspecialchars($book['title']) ?></h3>
                            <p class="author"><?= htmlspecialchars($book['author']) ?></p>
                            <div class="rating">
                                <?= str_repeat('★', floor($book['rating'])) ?>
                                <?= (round($book['rating'] * 2) / 2) - floor($book['rating']) >= 0.5 ? '½' : '' ?>
                            </div>
                            <p class="price">R<?= number_format($book['price'], 2) ?></p>
                        </div>
                    </a>
                    <button class="btn add-to-cart" data-book-id="<?= $book['book_id'] ?>">Add to Cart</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
  </main>

  <?php include 'includes/footer.php'; ?>

  <script>
    // Update sort parameter
    function updateSort(sortBy) {
      const url = new URL(window.location.href);
      url.searchParams.set('sortBy', sortBy);
      window.location.href = url.toString();
    }

    // Price Range Display
    const priceRange = document.getElementById('priceRange');
    priceRange.addEventListener('input', (e) => {
      document.querySelector('.price-values span:last-child').textContent = `$${e.target.value}`;
    });

    // Auto-submit filters
    document.querySelectorAll('.filters-sidebar input').forEach(input => {
      input.addEventListener('change', () => {
        document.getElementById('filterForm').submit();
      });
    });

    // Toggle mobile filters

    
    

    // Add to Cart functionality
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
  
</body>
</html>