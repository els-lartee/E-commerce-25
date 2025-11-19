<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed</title>
    <link href="../css/styles.css" rel="stylesheet">
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
                <a href="../index.php" class="btn btn-secondary">Continue Shopping</a>
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
