// Checkout JavaScript
$(document).ready(function() {
    let checkoutModal = null;

    // Proceed to checkout button
    $(document).on('click', '.proceed-to-checkout-btn', function(e) {
        e.preventDefault();
        showCheckoutModal();
    });

    // Confirm payment button
    $(document).on('click', '.confirm-payment-btn', function(e) {
        e.preventDefault();
        processCheckout();
    });

    // Cancel checkout
    $(document).on('click', '.cancel-checkout-btn', function(e) {
        e.preventDefault();
        hideCheckoutModal();
    });
});

function showCheckoutModal() {
    // Create modal HTML
    const modalHtml = `
        <div id="checkout-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div style="background: white; padding: 30px; border-radius: 8px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto;">
                <h3 style="margin-bottom: 20px; text-align: center;">Checkout</h3>

                <div id="checkout-content">
                    <p style="text-align: center;">Processing...</p>
                </div>

                <div style="text-align: center; margin-top: 20px;">
                    <button class="confirm-payment-btn btn btn-success" style="margin-right: 10px;">Confirm Payment</button>
                    <button class="cancel-checkout-btn btn btn-secondary">Cancel</button>
                </div>
            </div>
        </div>
    `;

    $('body').append(modalHtml);
    checkoutModal = $('#checkout-modal');

    // Load checkout summary
    loadCheckoutSummary();
}

function hideCheckoutModal() {
    if (checkoutModal) {
        checkoutModal.remove();
        checkoutModal = null;
    }
}

function loadCheckoutSummary() {
    $.ajax({
        url: '../actions/get_cart_summary_action.php', // We'll need to create this
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                displayCheckoutSummary(response);
            } else {
                $('#checkout-content').html('<p style="color: red; text-align: center;">' + (response.message || 'Failed to load checkout summary') + '</p>');
            }
        },
        error: function() {
            $('#checkout-content').html('<p style="color: red; text-align: center;">Error loading checkout summary</p>');
        }
    });
}

function displayCheckoutSummary(response) {
    const items = response.items || [];
    const total = response.total || 0;

    let html = '<div style="margin-bottom: 20px;"><h4>Order Summary</h4></div>';

    if (items.length === 0) {
        html += '<p>Your cart is empty.</p>';
    } else {
        html += '<div style="max-height: 200px; overflow-y: auto; margin-bottom: 20px;">';
        items.forEach(item => {
            html += `
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                    <div>
                        <strong>${item.product_title}</strong><br>
                        <small>Qty: ${item.qty}</small>
                    </div>
                    <div>$${(item.product_price * item.qty).toFixed(2)}</div>
                </div>
            `;
        });
        html += '</div>';

        html += `
            <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 18px; margin-top: 20px; padding-top: 20px; border-top: 2px solid #eee;">
                <div>Total:</div>
                <div>$${total.toFixed(2)}</div>
            </div>
        `;
    }

    // Simulated payment form
    html += `
        <div style="margin-top: 30px;">
            <h4>Payment Information</h4>
            <p style="color: #666; font-size: 14px;">This is a simulated payment. In a real application, you would enter actual payment details.</p>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; margin-top: 10px;">
                <p><strong>Payment Method:</strong> Credit Card (Simulated)</p>
                <p><strong>Amount:</strong> $${total.toFixed(2)}</p>
            </div>
        </div>
    `;

    $('#checkout-content').html(html);
}

function processCheckout() {
    // Disable button
    $('.confirm-payment-btn').prop('disabled', true).text('Processing...');

    $.ajax({
        url: '../actions/process_checkout_action.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                // Redirect to success page
                window.location.href = 'payment_success.php?order_ref=' + encodeURIComponent(response.order_reference);
            } else {
                // Show error and redirect to failure page
                window.location.href = 'payment_failed.php?message=' + encodeURIComponent(response.message || 'Payment failed');
            }
        },
        error: function() {
            window.location.href = 'payment_failed.php?message=' + encodeURIComponent('Network error during checkout');
        }
    });
}

// Export functions
window.CheckoutManager = {
    showCheckoutModal: showCheckoutModal,
    hideCheckoutModal: hideCheckoutModal
};
