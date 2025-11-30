<?php
require_once '../settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Failed - Golden Aura Jewellery</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #FFFFFF 0%, #F9F5EC 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .failed-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 30px rgba(0,0,0,0.1);
            max-width: 550px;
            width: 100%;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
            border: 1px solid #F9F5EC;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .failed-header {
            background: linear-gradient(135deg, #9B2C2C 0%, #C53030 100%);
            padding: 40px;
            text-align: center;
            color: white;
        }
        .failed-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: #C53030;
            animation: shake 0.5s ease-in-out;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        .failed-header h1 {
            font-family: 'Playfair Display', serif;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .failed-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-weight: 300;
        }
        .failed-body {
            padding: 30px;
        }
        .error-details {
            background: #FDF2F2;
            border: 1px solid #FEB2B2;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .error-details h4 {
            font-family: 'Playfair Display', serif;
            color: #9B2C2C;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #FEB2B2;
        }
        .error-message {
            color: #742A2A;
            font-size: 15px;
            line-height: 1.6;
        }
        .reference-info {
            background: #F9F5EC;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            font-size: 13px;
            color: #666666;
            border-left: 3px solid #D4AF37;
        }
        .reference-info strong {
            color: #2B2B2B;
        }
        .help-section {
            background: #F9F5EC;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            border-left: 3px solid #D4AF37;
        }
        .help-section h5 {
            font-family: 'Playfair Display', serif;
            color: #7A5C3E;
            margin-bottom: 15px;
        }
        .help-section ul {
            margin: 0;
            padding-left: 20px;
            color: #666666;
        }
        .help-section li {
            margin-bottom: 8px;
        }
        .failed-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .btn-retry {
            flex: 1;
            padding: 15px;
            background: #D4AF37;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
        }
        .btn-retry:hover {
            background: #B8941F;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
            color: white;
        }
        .btn-home {
            flex: 1;
            padding: 15px;
            background: #F9F5EC;
            color: #7A5C3E;
            border: 1px solid #D4AF37;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            background: #D4AF37;
            color: white;
        }
        .support-notice {
            text-align: center;
            color: #666666;
            font-size: 14px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #F9F5EC;
        }
        .support-notice span {
            color: #D4AF37;
        }
    </style>
</head>
<body>
    <div class="failed-card">
        <div class="failed-header">
            <div class="failed-icon">✕</div>
            <h1>Payment Failed</h1>
            <p>We couldn't process your payment</p>
        </div>
        
        <div class="failed-body">
            <div class="error-details">
                <h4>⚠️ Error Details</h4>
                <div id="errorContent">
                    <p class="text-center">Loading error details...</p>
                </div>
            </div>

            <div class="help-section">
                <h5>💡 What you can try:</h5>
                <ul>
                    <li>Check that your card details are correct</li>
                    <li>Ensure you have sufficient funds</li>
                    <li>Try a different payment method (Card, Mobile Money, Bank Transfer)</li>
                    <li>Check your mobile money wallet balance</li>
                    <li>Contact your bank if the problem persists</li>
                </ul>
            </div>

            <div class="failed-actions">
                <a href="cart.php" class="btn-retry">Try Again</a>
                <a href="../index.php" class="btn-home">Continue Shopping</a>
            </div>

            <div class="support-notice">
                <span>📞</span> Need help? Contact us at support@goldenaura.com
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message') || 'An unknown error occurred during payment processing.';
            const reference = urlParams.get('reference');

            displayErrorDetails(message, reference);
        });

        function displayErrorDetails(message, reference) {
            let html = `<p class="error-message">${decodeURIComponent(message)}</p>`;
            
            if (reference) {
                html += `
                    <div class="reference-info">
                        <strong>Transaction Reference:</strong> ${reference}<br>
                        <small>Please quote this reference if you contact support.</small>
                    </div>
                `;
            }

            html += `
                <p style="margin-top: 15px; font-size: 13px; color: #666666;">
                    Your cart items are still saved. No payment has been deducted from your account.
                </p>
            `;

            $('#errorContent').html(html);
        }
    </script>
</body>
</html>
