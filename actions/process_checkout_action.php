<?php
require_once '../controllers/cart_controller.php';
require_once '../controllers/order_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

// Check if user is logged in (required for checkout)
if (!is_logged_in()) {
    echo json_encode([
        'status' => 'error',
        'message' => 'You must be logged in to checkout'
    ]);
    exit;
}

$customer_id = get_user_id();

// Get cart items
$cart_items = get_user_cart_ctr();

if (empty($cart_items)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Your cart is empty'
    ]);
    exit;
}

// Calculate total
$total = 0;
foreach ($cart_items as $item) {
    $total += $item['product_price'] * $item['qty'];
}

// Generate unique order reference
$order_ref = 'ORD-' . time() . '-' . $customer_id;

// Create order
$order_id = create_order_ctr($customer_id, $order_ref, $total);

if (!$order_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to create order'
    ]);
    exit;
}

// Add order details
foreach ($cart_items as $item) {
    $result = add_order_details_ctr($order_id, $item['p_id'], $item['qty'], $item['product_price']);
    if (!$result) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to add order details'
        ]);
        exit;
    }
}

// Simulate payment processing (always success for demo)
$payment_method = 'card'; // Could be extended for different methods
$payment_result = record_payment_ctr($order_id, $total, $payment_method, 'completed');

if (!$payment_result) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Payment processing failed'
    ]);
    exit;
}

// Empty cart after successful checkout
$empty_result = empty_cart_ctr();

if (!$empty_result) {
    // Log warning but don't fail the checkout
    error_log("Warning: Failed to empty cart after successful checkout for customer $customer_id");
}

echo json_encode([
    'status' => 'success',
    'order_reference' => $order_ref,
    'message' => 'Order placed successfully!'
]);
