<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f4f4f4;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        .checkout-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .checkout-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
            text-align: center;
        }
        .checkout-header h2 {
            margin-bottom: 10px;
        }
        .checkout-content {
            padding: 30px;
        }
        .order-summary {
            margin-bottom: 30px;
        }
        .summary-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .item-info {
            flex: 1;
        }
        .item-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .item-meta {
            color: #6c757d;
            font-size: 14px;
        }
        .item-price {
            font-weight: bold;
        }
        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            font-size: 20px;
            font-weight: bold;
        }
        .payment-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        .payment-section h3 {
            margin-bottom: 15px;
            color: #495057;
        }
        .payment-info {
            background: white;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }
        .checkout-actions {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-size: 16px;
            margin: 0 10px;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            .checkout-content {
                padding: 20px;
            }
            .btn {
                display: block;
                width: 100%;
                margin: 5px 0;
            }
        }
    </style>
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
                            <a href="all_product.php" class="btn btn-primary">Continue Shopping</a>
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
