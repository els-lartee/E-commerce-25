<?php
header('Content-Type: application/json');
session_start();
require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$q = trim($_GET['q'] ?? '');
if ($q === '') {
    echo json_encode(['status' => 'error', 'message' => 'Query required']);
    exit;
}

$results = search_products_by_keyword_ctr($q);
echo json_encode(['status' => 'success', 'products' => $results]);
