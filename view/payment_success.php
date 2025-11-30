<?php
require_once '../settings/core.php';
require_once '../settings/paystack_config.php';

$currency = PAYSTACK_CURRENCY;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Golden Aura Jewellery</title>
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
        .success-card {
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
        .success-header {
            background: linear-gradient(135deg, #D4AF37 0%, #7A5C3E 100%);
            padding: 40px;
            text-align: center;
            color: white;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: #D4AF37;
            animation: checkmark 0.5s ease-in-out 0.3s both;
            box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        }
        @keyframes checkmark {
            0% {
                transform: scale(0);
            }
            50% {
                transform: scale(1.2);
            }
            100% {
                transform: scale(1);
            }
        }
        .success-header h1 {
            font-family: 'Playfair Display', serif;
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .success-header p {
            margin: 10px 0 0;
            opacity: 0.9;
            font-weight: 300;
        }
        .success-body {
            padding: 30px;
        }
        .order-details {
            background: #F9F5EC;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .order-details h4 {
            font-family: 'Playfair Display', serif;
            color: #2B2B2B;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #D4AF37;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid rgba(212, 175, 55, 0.2);
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #666666;
            font-weight: 500;
        }
        .detail-value {
            color: #2B2B2B;
            font-weight: 600;
        }
        .detail-value.amount {
            color: #D4AF37;
            font-size: 18px;
        }
        .payment-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #F9F5EC;
            color: #7A5C3E;
            border: 1px solid #D4AF37;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .success-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .btn-continue {
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
        .btn-continue:hover {
            background: #B8941F;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
            color: white;
        }
        .btn-orders {
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
        .btn-orders:hover {
            background: #D4AF37;
            color: white;
        }
        .email-notice {
            text-align: center;
            color: #666666;
            font-size: 14px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #F9F5EC;
        }
        .email-notice span {
            color: #D4AF37;
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="success-header">
            <div class="success-icon">✓</div>
            <h1>Payment Successful!</h1>
            <p>Thank you for your purchase</p>
        </div>
        
        <div class="success-body">
            <div class="order-details">
                <h4>💎 Order Details</h4>
                <div id="orderDetailsContent">
                    <p class="text-center">Loading order details...</p>
                </div>
            </div>

            <div class="success-actions">
                <a href="../index.php" class="btn-continue">Continue Shopping</a>
                <a href="../index.php" class="btn-orders">My Orders</a>
            </div>

            <div class="email-notice">
                <span>📧</span> A confirmation email has been sent to your registered email address.
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const CURRENCY = '<?php echo $currency; ?>';
        
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            displayOrderDetails({
                orderRef: urlParams.get('order_ref'),
                reference: urlParams.get('reference'),
                amount: urlParams.get('amount'),
                currency: urlParams.get('currency') || CURRENCY,
                channel: urlParams.get('channel')
            });
        });

        function displayOrderDetails(data) {
            const channelLabels = {
                'card': '💳 Card Payment',
                'mobile_money': '📱 Mobile Money',
                'bank': '🏦 Bank Transfer',
                'ussd': '📞 USSD',
                'qr': '📷 QR Code'
            };

            const channelDisplay = channelLabels[data.channel] || data.channel || 'Online Payment';
            const amount = data.amount ? parseFloat(data.amount).toFixed(2) : '0.00';

            const html = `
                <div class="detail-row">
                    <span class="detail-label">Order Reference</span>
                    <span class="detail-value">${data.orderRef || 'N/A'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Transaction Ref</span>
                    <span class="detail-value">${data.reference || 'N/A'}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Payment Method</span>
                    <span class="detail-value">${channelDisplay}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount Paid</span>
                    <span class="detail-value amount">${data.currency} ${amount}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status</span>
                    <span class="detail-value"><span class="payment-badge">✓ Confirmed</span></span>
                </div>
            `;

            $('#orderDetailsContent').html(html);
        }
    </script>
</body>
</html>
