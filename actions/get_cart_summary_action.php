<?php
require_once '../controllers/cart_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

$cart_items = get_user_cart_ctr();

if (empty($cart_items)) {
    echo json_encode([
        'status' => 'success',
        'items' => [],
        'total' => 0
    ]);
    exit;
}

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['product_price'] * $item['qty'];
}

echo json_encode([
    'status' => 'success',
    'items' => $cart_items,
    'total' => $total
]);
