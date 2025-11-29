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
    <title>Payment Successful - Jewellery Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .success-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 550px;
            width: 90%;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
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
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
            color: #38ef7d;
            animation: checkmark 0.5s ease-in-out 0.3s both;
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
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }
        .success-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .success-body {
            padding: 30px;
        }
        .order-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        .order-details h4 {
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #38ef7d;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            color: #666;
            font-weight: 500;
        }
        .detail-value {
            color: #333;
            font-weight: 600;
        }
        .detail-value.amount {
            color: #38ef7d;
            font-size: 18px;
        }
        .payment-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #d4edda;
            color: #155724;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-continue:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-orders {
            flex: 1;
            padding: 15px;
            background: #f8f9fa;
            color: #333;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
        }
        .btn-orders:hover {
            background: #e9ecef;
            color: #333;
        }
        .email-notice {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        .email-notice i {
            color: #667eea;
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
                📧 A confirmation email has been sent to your registered email address.
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
