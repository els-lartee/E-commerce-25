<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="success-container">
            <div class="success-icon">✓</div>
            <h1 class="success-title">Payment Successful!</h1>
            <p class="success-message">
                Thank you for your purchase. Your order has been successfully processed.
            </p>

            <div id="orderDetails">
                <p>Loading order details...</p>
            </div>

            <div class="actions">
                <a href="../index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const orderRef = urlParams.get('order_ref');

            if (orderRef) {
                displayOrderDetails(orderRef);
            } else {
                $('#orderDetails').html('<p style="color: red;">Order reference not found.</p>');
            }
        });

        function displayOrderDetails(orderRef) {
            const html = `
                <div class="order-details">
                    <h4>Order Information</h4>
                    <div class="order-info">
                        <span class="order-label">Order Reference:</span>
                        <span class="order-value">${orderRef}</span>
                    </div>
                    <div class="order-info">
                        <span class="order-label">Status:</span>
                        <span class="order-value">Confirmed</span>
                    </div>
                    <div class="order-info">
                        <span class="order-label">Payment:</span>
                        <span class="order-value">Completed</span>
                    </div>
                </div>
                <p style="color: #6c757d; font-size: 14px;">
                    A confirmation email has been sent to your registered email address.
                    You can track your order status from your account dashboard.
                </p>
            `;

            $('#orderDetails').html(html);
        }
    </script>
</body>
</html>
