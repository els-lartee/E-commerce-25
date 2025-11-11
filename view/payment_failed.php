<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
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
        .failed-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            padding: 40px 30px;
        }
        .failed-icon {
            width: 80px;
            height: 80px;
            background: #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
        }
        .failed-title {
            font-size: 28px;
            color: #dc3545;
            margin-bottom: 15px;
        }
        .failed-message {
            font-size: 16px;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .error-details {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 30px;
            text-align: left;
            border: 1px solid #f5c6cb;
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
            .failed-container {
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
        <div class="failed-container">
            <div class="failed-icon">✕</div>
            <h1 class="failed-title">Payment Failed</h1>
            <p class="failed-message">
                We're sorry, but your payment could not be processed.
            </p>

            <div id="errorDetails">
                <p>Loading error details...</p>
            </div>

            <div class="actions">
                <a href="cart.php" class="btn btn-primary">Try Again</a>
                <a href="all_product.php" class="btn btn-secondary">Continue Shopping</a>
                <a href="../index.php" class="btn btn-secondary">Back to Home</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message') || 'An unknown error occurred during payment processing.';

            displayErrorDetails(message);
        });

        function displayErrorDetails(message) {
            const html = `
                <div class="error-details">
                    <strong>Error Details:</strong><br>
                    ${decodeURIComponent(message)}
                </div>
                <p style="color: #6c757d; font-size: 14px;">
                    Please check your payment information and try again.
                    If the problem persists, contact our customer support.
                </p>
            `;

            $('#errorDetails').html(html);
        }
    </script>
</body>
</html>
