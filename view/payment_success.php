<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful</title>
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
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
        }
        .success-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            padding: 40px 30px;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
        }
        .success-title {
            font-size: 28px;
            color: #28a745;
            margin-bottom: 15px;
        }
        .success-message {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .order-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            text-align: left;
        }
        .order-details h4 {
            margin-bottom: 15px;
            color: #495057;
        }
        .order-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .order-label {
            font-weight: bold;
            color: #495057;
        }
        .order-value {
            color: #007bff;
            font-weight: bold;
        }
        .btn {
            display: inline-block;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-size: 16px;
            margin: 0 10px 10px 0;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .actions {
            margin-top: 30px;
        }
        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
                padding: 10px;
            }
            .success-container {
                padding: 30px 20px;
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
                <a href="all_product.php" class="btn btn-primary">Continue Shopping</a>
                <a href="../index.php" class="btn btn-secondary">Back to Home</a>
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
