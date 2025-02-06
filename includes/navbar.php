<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!-- includes/navbar.php -->
<nav class="navbar">
<button class="hamburger" aria-label="Menu">
    <span class="bar"></span>
    <span class="bar"></span>
    <span class="bar"></span>
  </button>
  <div class="logo">
    <a href="index.php">
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
      <span>iBooks</span>
    </a>
  </div>
  
  <ul class="nav-links">
    <li><a href="index.php">Home</a></li>
    <li><a href="browse-books.php">Browse Books</a></li>
    <li><a href="about.php">About</a></li>
    <li><a href="contact.php">Contact</a></li>
    <?php if (isset($_SESSION['user']) && $_SESSION['user']['is_admin']): ?>
      <li><a href="admin/dashboard.php">Admin Dashboard</a></li>
    <?php endif; ?>
  </ul>
  
  <div class="nav-controls">
    <?php if (isset($_SESSION['user'])): ?>
      <div class="user-menu">
        <span class="welcome-msg">Hi, <?= htmlspecialchars($_SESSION['user']['name']) ?>!</span>
        <div class="dropdown">
          <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
          <form class="logout-form" method="POST" action="logout.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <button type="submit" class="logout-button">
              <i class="fas fa-sign-out-alt"></i> Logout
            </button>
          </form>
        </div>
      </div>
    <?php else: ?>
      <a href="login.php" class="login-btn"><i class="fas fa-sign-in-alt"></i> Login</a>
    <?php endif; ?>
  </div>
</nav>

<!-- Cart Button -->
<a href="cart.php" class="cart-button">
      <i class="fas fa-shopping-cart"></i>
      <span id="cart-count">
        <?php
        $cartCount = 0;
        if (isset($_SESSION['user'])) {
            // Get database cart total for logged-in users
            try {
                $stmt = $pdo->prepare("SELECT SUM(quantity) AS total FROM cart_items WHERE user_id = ?");
                $stmt->execute([$_SESSION['user']['user_id']]);
                $result = $stmt->fetch();
                $cartCount = $result['total'] ?? 0;
            } catch (PDOException $e) {
                error_log("Cart count error: " . $e->getMessage());
            }
        } else {
            // Get session cart total for guests
            $cartCount = array_sum($_SESSION['cart'] ?? []);
        }
        echo $cartCount;
        ?>
      </span>
    </a>

<style>
  /* General Navbar Styles */
.navbar {
  background: rgba(255, 255, 255, 0.98);
  padding: 1rem 5%;
  box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: fixed;
  width: 100%;
  top: 0;
  z-index: 1000;
  backdrop-filter: blur(12px);
  animation: slideDown 0.5s cubic-bezier(0.4, 0, 0.2, 1);
  transform-origin: top;
}

/* Navbar Slide Down Animation */
@keyframes slideDown {
  from { transform: translateY(-100%); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

/* Logo and Styling */
.navbar .logo a {
  display: flex;
  align-items: center;
  text-decoration: none;
  transition: transform 0.3s ease;
}

.navbar .logo a:hover {
  transform: scale(1.05);
}

.navbar .logo svg {
  margin-right: 15px;
  width: 50px;
  height: 75px;
  filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.1));
}

.navbar .logo span {
  font-size: 1.8rem;
  font-weight: 700;
  color: #2c3e50;
  letter-spacing: -0.5px;
}

/* Navigation Links */
.nav-links {
  display: flex;
  gap: 2.5rem;
  list-style: none;
  margin: 0;
  padding: 0;
}

.nav-links a {
  color: #2c3e50;
  text-decoration: none;
  font-weight: 600;
  position: relative;
  padding: 8px 0;
  transition: color 0.3s ease;
}

.nav-links a::after {
  content: '';
  position: absolute;
  width: 0;
  height: 2px;
  bottom: 0;
  left: 50%;
  transform: translateX(-50%);
  background: #c0392b;
  transition: width 0.3s ease;
}

.nav-links a:hover::after {
  width: 80%;
}

.nav-links a:hover {
  color: #c0392b;
}

/* Hamburger Menu */
.hamburger {
  display: none;
  cursor: pointer;
  background: none;
  border: none;
  padding: 10px;
  z-index: 1001;
}

.hamburger .bar {
  display: block;
  width: 25px;
  height: 3px;
  margin: 5px 0;
  background-color: #2c3e50;
  transition: all 0.3s ease;
  transform-origin: center;
}

.hamburger.active .bar:nth-child(1) {
  transform: translateY(8px) rotate(45deg);
}

.hamburger.active .bar:nth-child(2) {
  opacity: 0;
}

.hamburger.active .bar:nth-child(3) {
  transform: translateY(-8px) rotate(-45deg);
}

/* Responsive Design - Small Screens */
@media (max-width: 768px) {
  /* Move Hamburger Left */
  .hamburger {
    display: block;
    order: 0;
    margin-right: 10px;
  }

  /* Adjust Navbar Layout */
  .navbar {
    display: flex;
    flex-direction: row;
    align-items: center;
  }

  .logo {
    display: flex;
    align-items: center;
    order: 1;
  }

  .logo svg {
    width: 40px;
    height: 60px;
  }

  .logo span {
    font-size: 1.4rem;
  }

  .nav-controls {
    order: 2;
    display: flex;
    align-items: center;
    gap: 1rem;
  }

  /* Mobile Navigation Links */
  .nav-links {
    position: fixed;
    top: 70px;
    left: 0;
    right: 0;
    width: 100%;
    height: calc(100vh - 70px);
    background-color: #fff;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
    gap: 2rem;
    padding: 2rem 0;
    transform: translateX(-100%);
    transition: transform 0.3s ease-in-out;
    z-index: 999;
    text-align: center;
  }

  .nav-links.active {
    transform: translateX(0);
  }

  .nav-links li {
    width: 100%;
  }

  .nav-links a {
    font-size: 1.2rem;
    padding: 1rem;
    display: block;
    width: 100%;
  }
}

/* Centered User Message */
.welcome-msg {
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: bold;
}


/* Nav Controls */
.nav-controls {
  display: flex;
  align-items: center;
  gap: 1.5rem;
}

.user-menu {
  position: relative;
  cursor: pointer;
}

.welcome-msg {
  padding: 0.5rem 1rem;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.user-menu:hover .dropdown {
  display: block;
}

/* Dropdown */
.dropdown {
  display: none;
  position: absolute;
  right: 0;
  top: 100%;
  background: white;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  border-radius: 4px;
  min-width: 160px;
  z-index: 1000;
  padding: 0.5rem 0;
}

.dropdown a {
  display: block;
  padding: 0.75rem 1rem;
  color: #333;
  text-decoration: none;
}

.dropdown a:hover {
  background: #f8f9fa;
}

/* Login Button */
.login-btn {
  background: #c0392b;
  color: white;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  text-decoration: none;
}

/* Logout Button */
.logout-button {
  background: none;
  border: none;
  width: 100%;
  text-align: left;
  padding: 0.75rem 1rem;
  color: #333;
  cursor: pointer;
}

.logout-button:hover {
  background: #f8f9fa;
}

/* Cart Button - Positioned at the bottom right of the screen */
.cart-button {
  position: fixed;
  bottom: 20px;  /* Adjust this value to move the button higher or lower */
  right: 20px;   /* Adjust this value to move the button left or right */
  background-color: #c0392b;
  color: white;
  padding: 1rem;
  border-radius: 50%;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 1.5rem;
  z-index: 1000;
  transition: background-color 0.3s ease;
}

.cart-button:hover {
  background-color: #e74c3c;
}

.cart-button i {
  font-size: 1.2rem;
}

.cart-button span {
  position: absolute;
  top: -8px;
  right: -8px;
  background: #fff;
  color: #c0392b;
  border-radius: 50%;
  padding: 5px 8px;
  font-size: 0.9rem;
  font-weight: bold;
  display: flex;
  justify-content: center;
  align-items: center;
}

</style>