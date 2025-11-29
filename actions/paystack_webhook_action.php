<?php
/**
 * Paystack Webhook Handler
 * 
 * This endpoint receives payment notifications from Paystack
 * Configure this URL in your Paystack dashboard under Settings > Webhooks
 * 
 * Uses MVC pattern: Action -> Controller -> Class (Model)
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/paystack_controller.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

// Log all webhook requests for debugging
error_log("Paystack Webhook received at " . date('Y-m-d H:i:s'));

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

// Get the raw POST data
$input = @file_get_contents('php://input');

if (empty($input)) {
    http_response_code(400);
    error_log("Paystack Webhook: Empty request body");
    exit('Empty request body');
}

// Get the Paystack signature from headers
$signature = $_SERVER['HTTP_X_PAYSTACK_SIGNATURE'] ?? '';

if (empty($signature)) {
    http_response_code(401);
    error_log("Paystack Webhook: Missing signature");
    exit('Missing signature');
}

// Validate the webhook signature through controller
if (!validate_webhook_ctr($input, $signature)) {
    http_response_code(401);
    error_log("Paystack Webhook: Invalid signature");
    exit('Invalid signature');
}

// Parse the webhook payload
$event = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    error_log("Paystack Webhook: Invalid JSON payload");
    exit('Invalid JSON');
}

// Log the event
error_log("Paystack Webhook Event: " . $event['event'] . " | Reference: " . ($event['data']['reference'] ?? 'N/A'));

// Handle different event types
$event_type = $event['event'] ?? '';
$data = $event['data'] ?? [];

switch ($event_type) {
    case 'charge.success':
        handleSuccessfulPayment($data);
        break;
        
    case 'charge.failed':
        handleFailedPayment($data);
        break;
        
    case 'transfer.success':
        handleTransferSuccess($data);
        break;
        
    case 'transfer.failed':
        handleTransferFailed($data);
        break;
        
    default:
        error_log("Paystack Webhook: Unhandled event type - " . $event_type);
        break;
}

// Always return 200 to acknowledge receipt
http_response_code(200);
echo json_encode(['status' => 'success']);

/**
 * Handle successful payment
 */
function handleSuccessfulPayment($data) {
    $reference = $data['reference'] ?? '';
    $amount = ($data['amount'] ?? 0) / 100;
    $channel = $data['channel'] ?? 'unknown';
    $metadata = $data['metadata'] ?? [];
    
    $order_id = $metadata['order_id'] ?? null;
    
    error_log("Webhook: Processing successful payment - Order: $order_id, Amount: $amount, Channel: $channel");
    
    if (!$order_id) {
        error_log("Webhook: No order_id in metadata for reference $reference");
        return;
    }
    
    // Complete payment through controller
    $result = complete_payment_ctr($order_id, $amount, $channel, $reference);
    
    if ($result['status'] === 'success') {
        error_log("Webhook: Payment recorded successfully for order $order_id");
    } else {
        error_log("Webhook: Failed to record payment for order $order_id - " . $result['message']);
    }
}

/**
 * Handle failed payment
 */
function handleFailedPayment($data) {
    $reference = $data['reference'] ?? '';
    $metadata = $data['metadata'] ?? [];
    $order_id = $metadata['order_id'] ?? null;
    $gateway_response = $data['gateway_response'] ?? 'Unknown error';
    
    error_log("Webhook: Payment failed - Order: $order_id, Reference: $reference, Reason: $gateway_response");
}

/**
 * Handle successful transfer (for refunds)
 */
function handleTransferSuccess($data) {
    $reference = $data['reference'] ?? '';
    $amount = ($data['amount'] ?? 0) / 100;
    
    error_log("Webhook: Transfer successful - Reference: $reference, Amount: $amount");
}

/**
 * Handle failed transfer
 */
function handleTransferFailed($data) {
    $reference = $data['reference'] ?? '';
    $reason = $data['reason'] ?? 'Unknown';
    
    error_log("Webhook: Transfer failed - Reference: $reference, Reason: $reason");
}

?>
