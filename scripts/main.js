
document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');

    // Add click event listener to the hamburger button
    hamburger.addEventListener('click', () => {
        navLinks.classList.toggle('active'); // Toggle the 'active' class
        hamburger.classList.toggle('active'); // Toggle the 'active' class for animation
    });

    // Close the menu when clicking outside of it
    document.addEventListener('click', (event) => {
        if (!hamburger.contains(event.target) && !navLinks.contains(event.target)) {
            navLinks.classList.remove('active');
            hamburger.classList.remove('active');
        }
    });
});

// Create the mobile filter toggle button
const mobileFilterToggle = document.createElement('button');
mobileFilterToggle.className = 'btn mobile-filter-toggle';
mobileFilterToggle.textContent = '☰ Filters';
const filterSidebar = document.querySelector('.filters-sidebar');
  const filterToggle = mobileFilterToggle;

  // If the click was outside the filter sidebar and the toggle button, close the sidebar
  if (!filterSidebar.contains(event.target) && !filterToggle.contains(event.target)) {
    filterSidebar.classList.remove('active');
  }

// Add the button to the page
document.querySelector('.book-listings').prepend(mobileFilterToggle);

// Toggle the filter sidebar when the button is clicked
mobileFilterToggle.addEventListener('click', () => {
  document.querySelector('.filters-sidebar').classList.toggle('active');
});

// Close the filter sidebar if clicking outside of it
document.addEventListener('click', (event) => {
  
});


// Function to handle "Add to Cart" button clicks
document.querySelectorAll('.add-to-cart').forEach(button => {
    button.addEventListener('click', async (e) => {
        e.preventDefault(); // Prevent default form submission
        const bookId = button.dataset.bookId; // Get book ID from data attribute

        try {
            // Send request to add-to-cart.php
            const response = await fetch('add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ book_id: bookId }) // Send book ID in request body
            });

            // Parse the JSON response
            const data = await response.json();

            // Handle the response
            if (data.success) {
                updateCartCount(); // Update the cart count in the navbar
                if (data.newQuantity) {
                    updateCartItemQuantity(bookId, data.newQuantity); // Update quantity in the cart UI
                }
                alert('Item added to cart!'); // Notify the user
            } else if (data.redirect) {
                // Redirect to login page if user is not logged in
                window.location.href = data.redirect;
            } else {
                // Show error message if something went wrong
                alert(data.message || 'Error adding to cart');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('An error occurred while adding to cart');
        }
    });
});

// Function to update the cart count in the navbar
function updateCartCount() {
    const cartCount = document.getElementById('cart-count');
    if (cartCount) {
        cartCount.textContent = parseInt(cartCount.textContent) + 1;
    }
}

// Function to update the quantity of a specific item in the cart UI
function updateCartItemQuantity(bookId, newQuantity) {
    const quantityElement = document.querySelector(`.cart-item[data-book-id="${bookId}"] .quantity`);
    if (quantityElement) {
        quantityElement.textContent = newQuantity;
    }
}