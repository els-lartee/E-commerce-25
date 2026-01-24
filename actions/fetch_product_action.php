<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
session_start();
require_once '../settings/core.php';

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid("sess_", true);
}

require_once '../controllers/product_controller.php';

// Allow both logged-in users and guests to view products

$products = get_all_products_ctr();
echo json_encode(['status' => 'success', 'products' => $products]);
