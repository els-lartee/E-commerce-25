<?php
require_once '../controllers/cart_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

$cart_items = get_user_cart_ctr();

echo json_encode([
    'status' => 'success',
    'items' => $cart_items
]);
