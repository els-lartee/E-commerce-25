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
define('PAYSTACK_TEST_SECRET_KEY', 'sk_test_e8d5eef29c47b0e87880e5f8faa20a5c4999d160');
define('PAYSTACK_TEST_PUBLIC_KEY', 'pk_test_f7f851907f26ea0cec49b13286eb4dd6da13ef14');

// Live Keys (Replace with your actual live keys from Paystack dashboard)
define('PAYSTACK_LIVE_SECRET_KEY', 'sk_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('PAYSTACK_LIVE_PUBLIC_KEY', 'pk_live_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');

// Get active keys based on mode
define('PAYSTACK_SECRET_KEY', PAYSTACK_TEST_MODE ? PAYSTACK_TEST_SECRET_KEY : PAYSTACK_LIVE_SECRET_KEY);
define('PAYSTACK_PUBLIC_KEY', PAYSTACK_TEST_MODE ? PAYSTACK_TEST_PUBLIC_KEY : PAYSTACK_LIVE_PUBLIC_KEY);

// Paystack API Base URL
define('PAYSTACK_API_URL', 'https://api.paystack.co');

// Currency (NGN for Nigeria, GHS for Ghana, USD, etc.)
define('PAYSTACK_CURRENCY', 'GHS');

// Callback URL after payment
define('PAYSTACK_CALLBACK_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/E-commerce-25/actions/verify_payment_action.php');

// Webhook URL for payment notifications
define('PAYSTACK_WEBHOOK_URL', 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/E-commerce-25/actions/paystack_webhook_action.php');

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
