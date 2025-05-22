document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Add to cart form submission
    const addToCartForms = document.querySelectorAll('.add-to-cart-form');
    addToCartForms.forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const productId = formData.get('product_id');
            const quantity = formData.get('quantity');
            
            // AJAX request to add to cart
            fetch('ajax/add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    const cartBadge = document.querySelector('.fa-shopping-cart + .badge');
                    if (cartBadge) {
                        cartBadge.textContent = data.cart_count;
                    } else {
                        const cartIcon = document.querySelector('.fa-shopping-cart');
                        const badge = document.createElement('span');
                        badge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                        badge.textContent = data.cart_count;
                        cartIcon.parentNode.appendChild(badge);
                    }
                    
                    // Show success message
                    showAlert('success', 'Product added to cart!');
                } else {
                    // Show error message
                    showAlert('danger', data.message || 'Error adding product to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred. Please try again.');
            });
        });
    });
    
    // Update cart quantity
    const quantityInputs = document.querySelectorAll('.cart-quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.dataset.productId;
            const quantity = this.value;
            
            // Create and send form data
            const formData = new FormData();
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            
            fetch('ajax/update_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to show updated cart
                    location.reload();
                } else {
                    showAlert('danger', data.message || 'Error updating cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred. Please try again.');
            });
        });
    });
    
    // Remove from cart
    const removeButtons = document.querySelectorAll('.remove-from-cart');
    removeButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            
            const productId = this.dataset.productId;
            
            // Create and send form data
            const formData = new FormData();
            formData.append('product_id', productId);
            
            fetch('ajax/remove_from_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to show updated cart
                    location.reload();
                } else {
                    showAlert('danger', data.message || 'Error removing item from cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred. Please try again.');
            });
        });
    });
    
    // Function to show alert messages
    function showAlert(type, message) {
        const alertContainer = document.createElement('div');
        alertContainer.className = `alert alert-${type} alert-dismissible fade show`;
        alertContainer.setAttribute('role', 'alert');
        
        alertContainer.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        // Insert alert at the top of the main content
        const main = document.querySelector('main');
        main.insertBefore(alertContainer, main.firstChild);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            alertContainer.remove();
        }, 5000);
    }

    // Shop page filters
    const setupShopFilters = () => {
        // Skip if not on shop page
        if (!window.location.href.includes('page=shop')) {
            return;
        }
        
        // Auto-submit form when category changes
        const categorySelect = document.getElementById('category');
        if (categorySelect) {
            categorySelect.addEventListener('change', function() {
                const form = this.closest('form');
                if (form) form.submit();
            });
        }
        
        // Auto-submit form when sort changes
        const sortSelect = document.getElementById('sort');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                const form = this.closest('form');
                if (form) form.submit();
            });
        }
        
        // Reset button event to clear all filters
        const resetButton = document.querySelector('.btn-outline-secondary');
        if (resetButton) {
            resetButton.addEventListener('click', function(e) {
                e.preventDefault();
                window.location.href = this.getAttribute('href');
            });
        }
        
        // Close buttons on filter badges
        const filterBadges = document.querySelectorAll('.badge .fa-times');
        if (filterBadges) {
            filterBadges.forEach(badge => {
                badge.addEventListener('click', function(e) {
                    e.preventDefault();
                    const link = this.closest('a');
                    if (link) window.location.href = link.getAttribute('href');
                });
            });
        }
        
        // Price range validation
        const minPriceInput = document.querySelector('input[name="min_price"]');
        const maxPriceInput = document.querySelector('input[name="max_price"]');
        
        if (minPriceInput && maxPriceInput) {
            // Ensure min doesn't exceed max
            minPriceInput.addEventListener('change', function() {
                if (this.value && maxPriceInput.value && Number(this.value) > Number(maxPriceInput.value)) {
                    maxPriceInput.value = this.value;
                }
            });
            
            // Ensure max isn't less than min
            maxPriceInput.addEventListener('change', function() {
                if (this.value && minPriceInput.value && Number(this.value) < Number(minPriceInput.value)) {
                    minPriceInput.value = this.value;
                }
            });
        }
    };

    setupShopFilters();
});
