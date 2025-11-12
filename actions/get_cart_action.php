<?php
require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../settings/core.php';

header('Content-Type: application/json');

$cart_items = get_user_cart_ctr();

echo json_encode([
    'status' => 'success',
    'items' => $cart_items
]);
