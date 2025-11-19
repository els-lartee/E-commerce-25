<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="checkout-container">
            <div class="checkout-header">
                <h2>Checkout</h2>
                <p>Review your order and complete payment</p>
            </div>

            <div id="checkoutContent">
                <div class="checkout-content">
                    <div class="alert alert-info">
                        <strong>Note:</strong> You must be logged in to proceed with checkout.
                    </div>
                    <p style="text-align: center;">Loading checkout information...</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/checkout.js"></script>
    <script>
        $(document).ready(function() {
            loadCheckout();
        });

        function loadCheckout() {
            // First check if user is logged in
            $.ajax({
                url: '../actions/check_login_status_action.php', // We'll create this
                type: 'GET',
                dataType: 'json',
                success: function(loginResponse) {
                    if (loginResponse.status !== 'success' || !loginResponse.logged_in) {
                        $('#checkoutContent').html(`
                            <div class="checkout-content">
                                <div class="alert alert-danger">
                                    <strong>Login Required</strong><br>
                                    You must be logged in to checkout. Please <a href="../login/login.php">login</a> or <a href="../login/register.php">register</a> first.
                                </div>
                                <div class="checkout-actions">
                                    <a href="../login/login.php" class="btn btn-primary">Login</a>
                                    <a href="../login/register.php" class="btn btn-secondary">Register</a>
                                    <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                                </div>
                            </div>
                        `);
                        return;
                    }

                    // User is logged in, load cart summary
                    loadCartSummary();
                },
                error: function() {
                    $('#checkoutContent').html(`
                        <div class="checkout-content">
                            <div class="alert alert-danger">Error checking login status</div>
                        </div>
                    `);
                }
            });
        }

        function loadCartSummary() {
            $.ajax({
                url: '../actions/get_cart_summary_action.php', // We'll create this
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        displayCheckout(response);
                    } else {
                        $('#checkoutContent').html(`
                            <div class="checkout-content">
                                <div class="alert alert-danger">${response.message || 'Failed to load cart summary'}</div>
                                <div class="checkout-actions">
                                    <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                                </div>
                            </div>
                        `);
                    }
                },
                error: function() {
                    $('#checkoutContent').html(`
                        <div class="checkout-content">
                            <div class="alert alert-danger">Error loading cart summary</div>
                            <div class="checkout-actions">
                                <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                            </div>
                        `);
                }
            });
        }

        function displayCheckout(response) {
            const items = response.items || [];
            const total = response.total || 0;

            if (items.length === 0) {
                $('#checkoutContent').html(`
                    <div class="checkout-content">
                        <div class="alert alert-info">Your cart is empty.</div>
                        <div class="checkout-actions">
                            <a href="../index.php" class="btn btn-primary">Continue Shopping</a>
                        </div>
                    </div>
                `);
                return;
            }

            let html = '<div class="checkout-content">';

            // Order Summary
            html += '<div class="order-summary">';
            html += '<div class="summary-header"><h3>Order Summary</h3></div>';

            items.forEach(item => {
                const subtotal = item.product_price * item.qty;
                html += `
                    <div class="summary-item">
                        <div class="item-info">
                            <div class="item-title">${item.product_title}</div>
                            <div class="item-meta">Quantity: ${item.qty}</div>
                        </div>
                        <div class="item-price">$${subtotal.toFixed(2)}</div>
                    </div>
                `;
            });

            html += `
                <div class="summary-total">
                    <span>Total:</span>
                    <span>$${total.toFixed(2)}</span>
                </div>
            `;

            html += '</div>'; // End order summary

            // Payment Section
            html += `
                <div class="payment-section">
                    <h3>Payment Information</h3>
                    <div class="payment-info">
                        <p><strong>Payment Method:</strong> Credit Card (Simulated)</p>
                        <p><strong>Total Amount:</strong> $${total.toFixed(2)}</p>
                        <p style="color: #6c757d; font-size: 14px; margin-top: 10px;">
                            This is a simulated checkout for demonstration purposes.
                            No real payment will be processed.
                        </p>
                    </div>
                </div>
            `;

            // Checkout Actions
            html += `
                <div class="checkout-actions">
                    <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
                    <button class="btn btn-success proceed-to-checkout-btn">Complete Order</button>
                </div>
            `;

            html += '</div>'; // End checkout content

            $('#checkoutContent').html(html);
        }
    </script>
</body>
</html>
