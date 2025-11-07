<?php
header('Content-Type: application/json');
require_once '../controllers/product_controller.php';

try {
    $query = trim($_GET['q'] ?? $_GET['query'] ?? '');
    
    if (empty($query)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Search query is required'
        ]);
        exit;
    }
    
    $products = search_products_ctr($query);
    
    echo json_encode([
        'status' => 'success',
        'query' => $query,
        'count' => count($products),
        'products' => $products
    ]);
} catch (Exception $e) {
    error_log("Error in search_products_customer_action: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to search products'
    ]);
}
