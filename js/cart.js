// Cart management JavaScript
document.addEventListener('DOMContentLoaded', () => {
    // Add to cart functionality using event delegation
    document.body.addEventListener('click', async e => {
        const btn = e.target.closest('.btn-success');
        if (!btn || !btn.hasAttribute('data-product-id')) return;

        const productId = btn.getAttribute('data-product-id');
        const qty = 1; // Default quantity

        // Prevent multiple clicks
        if (btn.disabled) return;

        btn.disabled = true;
        btn.textContent = 'Adding...';

        try {
            const response = await fetch('../actions/add_to_cart_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    product_id: productId,
                    qty: qty
                })
            });

            const data = await response.json();

            if (data.status === 'success') {
                showMessage(data.message, 'success');
                updateCartCount(data.cart_count);
            } else {
                showMessage(data.message || 'Failed to add product to cart', 'error');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            showMessage('Error adding product to cart', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Add to Cart';
        }
    });

    // Update cart count on page load
    updateCartCount();
});

function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('#cart-count');
    cartCountElements.forEach(element => {
        element.textContent = count || 0;
    });
}

function showMessage(message, type) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.cart-message');
    existingMessages.forEach(msg => msg.remove());

    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `cart-message alert ${type === 'success' ? 'alert-success' : 'alert-danger'}`;
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 15px;
        border-radius: 4px;
        max-width: 300px;
        background: ${type === 'success' ? '#d4edda' : '#f8d7da'};
        color: ${type === 'success' ? '#155724' : '#721c24'};
        border: 1px solid ${type === 'success' ? '#c3e6cb' : '#f5c6cb'};
    `;
    messageDiv.textContent = message;

    // Add to body
    document.body.appendChild(messageDiv);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 3000);
}
