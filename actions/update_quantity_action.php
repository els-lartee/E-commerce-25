<?php
require_once '../controllers/cart_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

// Get POST data
$cart_id = $_POST['cart_id'] ?? null;
$qty = $_POST['qty'] ?? null;

// Validate input
if (!$cart_id || !is_numeric($cart_id) || $cart_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid cart item ID'
    ]);
    exit;
}

if (!$qty || !is_numeric($qty) || $qty <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid quantity'
    ]);
    exit;
}

// Update quantity
$result = update_cart_item_ctr($cart_id, $qty);

if ($result) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Quantity updated successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to update quantity'
    ]);
}
