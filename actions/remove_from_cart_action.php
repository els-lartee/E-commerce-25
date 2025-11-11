<?php
require_once '../controllers/cart_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

// Get POST data
$cart_id = $_POST['cart_id'] ?? null;

// Validate input
if (!$cart_id || !is_numeric($cart_id) || $cart_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid cart item ID'
    ]);
    exit;
}

// Remove from cart
$result = remove_from_cart_ctr($cart_id);

if ($result) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Item removed from cart successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to remove item from cart'
    ]);
}
