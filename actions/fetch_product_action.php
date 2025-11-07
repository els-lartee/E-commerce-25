<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
session_start();
require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

if (!is_logged_in()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$products = get_all_products_ctr();
echo json_encode(['status' => 'success', 'products' => $products]);
