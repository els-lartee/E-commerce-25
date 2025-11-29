<?php
require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../settings/core.php';

header('Content-Type: application/json');

$cart_items = get_user_cart_ctr();
$total_items = 0;

if (empty($cart_items)) {
    echo json_encode([
        'status' => 'success',
        'items' => [],
        'total' => 0,
        'total_items' => 0,
        'customer_email' => $_SESSION['user_email'] ?? ''
    ]);
    exit;
}

$total = 0;
foreach ($cart_items as $item) {
    $total += $item['product_price'] * $item['qty'];
    $total_items += $item['qty'];
}

echo json_encode([
    'status' => 'success',
    'items' => $cart_items,
    'total' => $total,
    'total_items' => $total_items,
    'customer_email' => $_SESSION['user_email'] ?? ''
]);
