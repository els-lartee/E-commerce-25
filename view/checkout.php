<?php
require_once '../settings/core.php';
require_once '../controllers/paystack_controller.php';

// Get Paystack public key through controller
$paystack_public_key = get_paystack_public_key_ctr();

// Get currency from config
require_once '../settings/paystack_config.php';
$currency = PAYSTACK_CURRENCY;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Golden Aura Jewellery</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #FFFFFF 0%, #F9F5EC 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .checkout-wrapper {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        .checkout-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #F9F5EC;
        }
        .checkout-header {
            background: linear-gradient(135deg, #D4AF37 0%, #7A5C3E 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .checkout-header h2 {
            font-family: 'Playfair Display', serif;
            margin: 0;
            font-size: 28px;
        }
        .checkout-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-weight: 300;
        }
        .checkout-body {
            padding: 30px;
        }
        .order-summary {
            background: #F9F5EC;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .order-summary h4 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 20px;
            color: #2B2B2B;
            border-bottom: 2px solid #D4AF37;
            padding-bottom: 10px;
        }
        .order-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .order-item .item-name {
            flex: 1;
            color: #2B2B2B;
        }
        .order-item .item-qty {
            color: #666666;
            margin: 0 15px;
        }
        .order-item .item-price {
            font-weight: 600;
            min-width: 80px;
            text-align: right;
            color: #7A5C3E;
        }
        .order-totals {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px dashed rgba(212, 175, 55, 0.3);
        }
        .order-totals .row {
            margin-bottom: 10px;
            color: #666666;
        }
        .order-totals .total-row {
            font-size: 20px;
            font-weight: 700;
            color: #D4AF37;
            border-top: 2px solid #D4AF37;
            padding-top: 15px;
            margin-top: 15px;
        }
        .payment-methods {
            margin-bottom: 25px;
        }
        .payment-methods h4 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 15px;
            color: #2B2B2B;
        }
        .payment-option {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 2px solid #F9F5EC;
            border-radius: 12px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: white;
        }
        .payment-option:hover {
            border-color: #D4AF37;
            background: #F9F5EC;
        }
        .payment-option.selected {
            border-color: #D4AF37;
            background: #F9F5EC;
        }
        .payment-option img {
            height: 30px;
            margin-right: 15px;
        }
        .payment-option .payment-name {
            font-weight: 600;
            color: #2B2B2B;
        }
        .payment-option .payment-desc {
            font-size: 13px;
            color: #666666;
        }
        .checkout-actions {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        .btn-pay {
            flex: 1;
            padding: 15px 30px;
            font-size: 18px;
            font-weight: 600;
            font-family: 'Poppins', sans-serif;
            background: #D4AF37;
            border: none;
            border-radius: 12px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn-pay:hover {
            background: #B8941F;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
        }
        .btn-pay:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        .btn-back {
            padding: 15px 25px;
            background: #F9F5EC;
            color: #7A5C3E;
            border: 1px solid #D4AF37;
            border-radius: 12px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-back:hover {
            background: #D4AF37;
            color: white;
        }
        .secure-badge {
            text-align: center;
            margin-top: 20px;
            color: #666666;
            font-size: 14px;
        }
        .secure-badge span {
            color: #D4AF37;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.95);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            flex-direction: column;
        }
        .loading-overlay.show {
            display: flex;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid #F9F5EC;
            border-top: 4px solid #D4AF37;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loading-text {
            margin-top: 20px;
            color: #7A5C3E;
            font-size: 16px;
        }
        .alert {
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: #FDF2F2;
            color: #9B2C2C;
            border: 1px solid #FEB2B2;
        }
        .alert-danger h4 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 10px;
        }
        .alert-info {
            background: #F9F5EC;
            color: #7A5C3E;
            border: 1px solid #D4AF37;
        }
        .alert-info h4 {
            font-family: 'Playfair Display', serif;
            margin-bottom: 10px;
        }
        .btn-primary {
            background: #D4AF37;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: #B8941F;
            color: white;
        }
        .btn-outline-primary {
            background: transparent;
            border: 1px solid #D4AF37;
            color: #7A5C3E;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-outline-primary:hover {
            background: #D4AF37;
            color: white;
        }
    </style>
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <div class="loading-text">Processing your payment...</div>
    </div>

    <div class="checkout-wrapper">
        <div class="checkout-card">
            <div class="checkout-header">
                <h2>💎 Secure Checkout</h2>
                <p>Complete your jewellery purchase</p>
            </div>
            
            <div class="checkout-body">
                <div id="checkoutContent">
                    <div class="text-center py-5">
                        <div class="spinner mx-auto"></div>
                        <p class="mt-3">Loading checkout...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script>
        const PAYSTACK_PUBLIC_KEY = '<?php echo $paystack_public_key; ?>';
        const CURRENCY = '<?php echo $currency; ?>';
        const TAX_RATE = 0.125; // 12.5% VAT

        $(document).ready(function() {
            checkLoginAndLoadCheckout();
        });

        function checkLoginAndLoadCheckout() {
            $.ajax({
                url: '../actions/check_login_status_action.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status !== 'success' || !response.logged_in) {
                        showLoginRequired();
                    } else {
                        loadCheckoutData(response);
                    }
                },
                error: function() {
                    showError('Failed to check login status. Please try again.');
                }
            });
        }

        function showLoginRequired() {
            $('#checkoutContent').html(`
                <div class="alert alert-danger text-center">
                    <h4>Login Required</h4>
                    <p>You must be logged in to complete checkout.</p>
                    <div class="mt-3">
                        <a href="../login/login.php" class="btn btn-primary me-2">Login</a>
                        <a href="../login/register.php" class="btn btn-outline-primary">Register</a>
                    </div>
                </div>
            `);
        }

        function loadCheckoutData(loginData) {
            $.ajax({
                url: '../actions/get_cart_summary_action.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        displayCheckout(response, loginData);
                    } else {
                        showError(response.message || 'Failed to load cart');
                    }
                },
                error: function() {
                    showError('Error loading cart data');
                }
            });
        }

        function displayCheckout(cartData, loginData) {
            const items = cartData.items || [];
            const subtotal = cartData.total || 0;
            
            if (items.length === 0) {
                $('#checkoutContent').html(`
                    <div class="alert alert-info text-center">
                        <h4>Your Cart is Empty</h4>
                        <p>Add some beautiful jewellery to your cart!</p>
                        <a href="../index.php" class="btn btn-primary mt-2">Browse Products</a>
                    </div>
                `);
                return;
            }

            const tax = subtotal * TAX_RATE;
            const total = subtotal + tax;

            let html = `
                <div class="order-summary">
                    <h4>📦 Order Summary</h4>
                    <div class="order-items">
            `;

            items.forEach(item => {
                const itemTotal = item.product_price * item.qty;
                html += `
                    <div class="order-item">
                        <span class="item-name">${item.product_title}</span>
                        <span class="item-qty">x${item.qty}</span>
                        <span class="item-price">${CURRENCY} ${itemTotal.toFixed(2)}</span>
                    </div>
                `;
            });

            html += `
                    </div>
                    <div class="order-totals">
                        <div class="row">
                            <div class="col-6">Subtotal:</div>
                            <div class="col-6 text-end">${CURRENCY} ${subtotal.toFixed(2)}</div>
                        </div>
                        <div class="row">
                            <div class="col-6">VAT (12.5%):</div>
                            <div class="col-6 text-end">${CURRENCY} ${tax.toFixed(2)}</div>
                        </div>
                        <div class="row total-row">
                            <div class="col-6">Total:</div>
                            <div class="col-6 text-end">${CURRENCY} ${total.toFixed(2)}</div>
                        </div>
                    </div>
                </div>

                <div class="payment-methods">
                    <h4>💳 Payment Method</h4>
                    <div class="payment-option selected" data-method="all">
                        <div>
                            <div class="payment-name">Pay with Paystack</div>
                            <div class="payment-desc">Card, Mobile Money (MTN MoMo, Vodafone Cash), Bank Transfer</div>
                        </div>
                    </div>
                </div>

                <div class="checkout-actions">
                    <a href="cart.php" class="btn-back">← Back to Cart</a>
                    <button class="btn-pay" id="payButton" data-amount="${total}" data-email="${loginData.email || ''}">
                        Pay ${CURRENCY} ${total.toFixed(2)}
                    </button>
                </div>

                <div class="secure-badge">
                    🔒 Secured by Paystack | 256-bit SSL Encryption
                </div>
            `;

            $('#checkoutContent').html(html);

            // Attach payment handler
            $('#payButton').on('click', function() {
                initializePayment();
            });
        }

        function initializePayment() {
            $('#loadingOverlay').addClass('show');
            $('#payButton').prop('disabled', true);

            $.ajax({
                url: '../actions/initialize_payment_action.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    $('#loadingOverlay').removeClass('show');
                    
                    if (response.status === 'success') {
                        // Redirect to Paystack checkout page
                        window.location.href = response.authorization_url;
                    } else {
                        $('#payButton').prop('disabled', false);
                        alert(response.message || 'Failed to initialize payment');
                    }
                },
                error: function() {
                    $('#loadingOverlay').removeClass('show');
                    $('#payButton').prop('disabled', false);
                    alert('Network error. Please try again.');
                }
            });
        }

        function showError(message) {
            $('#checkoutContent').html(`
                <div class="alert alert-danger text-center">
                    <h4>Error</h4>
                    <p>${message}</p>
                    <a href="cart.php" class="btn btn-primary mt-2">Back to Cart</a>
                </div>
            `);
        }
    </script>
</body>
</html>
