<?php
/**
 * Initialize Paystack Payment Action
 * Creates a payment session and returns the authorization URL
 * 
 * Uses MVC pattern: Action -> Controller -> Class (Model)
 */

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/paystack_controller.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to make a payment'
    ]);
    exit;
}

$customer_id = get_user_id();
$customer_email = $_SESSION['user_email'] ?? '';

if (empty($customer_email)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Customer email not found. Please update your profile.'
    ]);
    exit;
}

// Get cart items
$cart_items = get_user_cart_ctr();

if (empty($cart_items)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Your cart is empty'
    ]);
    exit;
}

// Process checkout through controller
$result = process_checkout_ctr($customer_id, $customer_email, $cart_items);

if ($result['status'] === 'success') {
    // Store reference in session for verification
    $_SESSION['payment_reference'] = $result['reference'];
    $_SESSION['payment_order_id'] = $result['order_id'];
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Payment initialized',
        'authorization_url' => $result['authorization_url'],
        'reference' => $result['reference'],
        'access_code' => $result['access_code'],
        'order_id' => $result['order_id'],
        'amount' => $result['totals']['total'],
        'subtotal' => $result['totals']['subtotal'],
        'tax' => $result['totals']['tax_amount']
    ]);
} else {
    error_log("Checkout failed for customer $customer_id: " . $result['message']);
    echo json_encode($result);
}

?>
