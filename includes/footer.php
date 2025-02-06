<!-- includes/footer.php -->
<footer class="footer">
  <div class="footer-content">
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
    </div>
    <div class="footer-links">
      <a href="about.php">About Us</a>
      <a href="contact.php">Contact</a>
      <a href="privacy.php">Privacy Policy</a>
    </div>
  </div>
  <p class="copyright">&copy; 2025 iBooks. All rights reserved.</p>
</footer>
<!-- Add to footer.php before closing </body> -->
<script>
// Scroll Progress Indicator
window.addEventListener('scroll', () => {
  const scrollable = document.documentElement.scrollHeight - window.innerHeight;
  const scrolled = window.scrollY;
  document.body.style.setProperty('--scroll', (scrolled / scrollable) * 100);
  document.body.style.background = `linear-gradient(to right, 
    var(--secondary-color) calc(var(--scroll) * 1%), 
    transparent 0) fixed`;
});

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
  const navLinks = document.querySelector('.nav-links');
  const hamburger = document.querySelector('.hamburger');
  
  if (!e.target.closest('.nav-links') && !e.target.closest('.hamburger')) {
    navLinks.classList.remove('active');
    hamburger.classList.remove('active');
  }
});
</script>


