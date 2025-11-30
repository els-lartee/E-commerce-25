<?php
/**
 * Paystack Configuration
 * 
 * Get your API keys from: https://dashboard.paystack.com/#/settings/developers
 * 
 * For testing, use the test keys (they start with sk_test_ and pk_test_)
 * For production, use the live keys (they start with sk_live_ and pk_live_)
 */

// Set to false for production
define('PAYSTACK_TEST_MODE', true);

// Test Keys (Replace with your actual test keys from Paystack dashboard)
define('PAYSTACK_SECRET_KEY', 'sk_test_007adcc0e8ab8a525a217d5233c5ca40e945057c');
define('PAYSTACK_PUBLIC_KEY', 'pk_test_5995cf1420d788d99c1831479efe3f77ab4fdefa');

// Paystack API Base URL
define('PAYSTACK_API_URL', 'https://api.paystack.co');

// Currency (NGN for Nigeria, GHS for Ghana, USD, etc.)
define('PAYSTACK_CURRENCY', 'GHS');

// Callback URL after payment (includes ~username for user directory on school server)
define('PAYSTACK_CALLBACK_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/~elsie.lartey/E-commerce-25/actions/verify_payment_action.php');

// Webhook URL for payment notifications
define('PAYSTACK_WEBHOOK_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/~elsie.lartey/E-commerce-25/actions/paystack_webhook_action.php');

/**
 * Supported Payment Channels in Ghana:
 * - card: Debit/Credit cards
 * - mobile_money: MTN MoMo, Vodafone Cash, AirtelTigo Money
 * - bank: Bank transfers
 * - ussd: USSD payments
 * - qr: QR code payments
 */
define('PAYSTACK_CHANNELS', json_encode(['card', 'mobile_money', 'bank']));

?>
