<?php
require_once '../controllers/cart_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

// Debugging: Log received POST data
error_log("Add to cart request: " . json_encode($_POST));

// Get POST data
$product_id = $_POST['product_id'] ?? null;
$qty = $_POST['qty'] ?? 1;

// Validate input
if (!$product_id || !is_numeric($product_id) || $product_id <= 0) {
    error_log("Invalid product ID: $product_id");
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid product ID'
    ]);
    exit;
}

if (!is_numeric($qty) || $qty <= 0) {
    error_log("Invalid quantity: $qty");
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
    error_log("Product $product_id added to cart successfully. Cart count: $cart_count");
    echo json_encode([
        'status' => 'success',
        'message' => 'Product added to cart successfully',
        'cart_count' => $cart_count
    ]);
} else {
    error_log("Failed to add product $product_id to cart");
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to add product to cart'
    ]);
}
