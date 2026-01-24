<?php
// Start session for cart storage
session_start();

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid("sess_", true);
}

// Debugging: Log the request
error_log("Add to cart request: " . json_encode($_POST));

// Get POST data
$product_id = $_POST['product_id'] ?? null;
$qty = $_POST['qty'] ?? 1;

// Validate input
if (!$product_id || !is_numeric($product_id) || $product_id <= 0) {
    error_log("Invalid product ID: $product_id");
    echo json_encode(['status' => 'error', 'message' => 'Invalid product ID']);
    exit;
}

if (!is_numeric($qty) || $qty <= 0) {
    error_log("Invalid quantity: $qty");
    echo json_encode(['status' => 'error', 'message' => 'Invalid quantity']);
    exit;
}

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Check if product already in cart
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['product_id'] == $product_id) {
        $item['qty'] += $qty;
        $found = true;
        break;
    }
}

// If not found, add new item
if (!$found) {
    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'qty' => $qty
    ];
}

// Calculate cart count
$cart_count = 0;
foreach ($_SESSION['cart'] as $item) {
    $cart_count += $item['qty'];
}

error_log("Product $product_id added to cart. Session cart: " . json_encode($_SESSION['cart']));
echo json_encode([
    'status' => 'success',
    'message' => 'Product added to cart successfully',
    'cart_count' => $cart_count
]);
?>
