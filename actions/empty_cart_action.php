<?php
require_once '../controllers/cart_controller.php';
require_once '../settings/core.php';

header('Content-Type: application/json');

// Empty cart
$result = empty_cart_ctr();

if ($result) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Cart emptied successfully'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to empty cart'
    ]);
}
