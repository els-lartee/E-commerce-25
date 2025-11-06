<?php
header('Content-Type: application/json');
session_start();
require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'POST required']);
    exit;
}

$product_id = intval($_POST['product_id'] ?? 0);
if ($product_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid product id']);
    exit;
}

$res = delete_product_ctr($product_id);
if ($res) {
    echo json_encode(['status' => 'success', 'message' => 'Product deleted']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete product']);
}
