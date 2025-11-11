// Cart management JavaScript
$(document).ready(function() {
    // Add to cart functionality
    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.preventDefault();
        const productId = $(this).data('product-id');
        const qty = $(this).data('qty') || 1;

        addToCart(productId, qty, $(this));
    });

    // Update cart count on page load
    updateCartCount();
});

function addToCart(productId, qty, buttonElement) {
    // Disable button during request
    if (buttonElement) {
        buttonElement.prop('disabled', true).text('Adding...');
    }

    $.ajax({
        url: '../actions/add_to_cart_action.php',
        type: 'POST',
        data: {
            product_id: productId,
            qty: qty
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                showMessage('Product added to cart!', 'success');
                updateCartCount();
            } else {
                showMessage(response.message || 'Failed to add product to cart', 'error');
            }
        },
        error: function() {
            showMessage('Error adding product to cart', 'error');
        },
        complete: function() {
            // Re-enable button
            if (buttonElement) {
                buttonElement.prop('disabled', false).text('Add to Cart');
            }
        }
    });
}

function updateCartCount() {
    // This would typically fetch cart count from server
    // For now, we'll just update any cart count displays
    $('.cart-count').each(function() {
        // Could fetch actual count here
        // $(this).text(actualCount);
    });
}

function showMessage(message, type) {
    // Remove existing messages
    $('.cart-message').remove();

    // Create message element
    const messageClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const messageHtml = `<div class="cart-message alert ${messageClass}" style="position: fixed; top: 20px; right: 20px; z-index: 9999; padding: 15px; border-radius: 4px; max-width: 300px;">${message}</div>`;

    // Add to body
    $('body').append(messageHtml);

    // Auto-remove after 3 seconds
    setTimeout(function() {
        $('.cart-message').fadeOut(function() {
            $(this).remove();
        });
    }, 3000);
}

// Export functions for use in other scripts
window.CartManager = {
    addToCart: addToCart,
    updateCartCount: updateCartCount,
    showMessage: showMessage
};
