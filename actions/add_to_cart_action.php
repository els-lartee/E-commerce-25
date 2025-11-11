<?php
require_once '../controllers/cart_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

// Get POST data
$product_id = $_POST['product_id'] ?? null;
$qty = $_POST['qty'] ?? 1;

// Validate input
if (!$product_id || !is_numeric($product_id) || $product_id <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid product ID'
    ]);
    exit;
}

if (!is_numeric($qty) || $qty <= 0) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid quantity'
    ]);
    exit;
}

// Add to cart
$result = add_to_cart_ctr($product_id, $qty);

if ($result) {
    $cart_count = get_cart_count_ctr();
    echo json_encode([
        'status' => 'success',
        'message' => 'Product added to cart successfully',
        'cart_count' => $cart_count
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to add product to cart'
    ]);
}
