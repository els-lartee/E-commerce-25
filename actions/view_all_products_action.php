<?php
header('Content-Type: application/json');
require_once '../controllers/product_controller.php';

try {
    $products = view_all_products_ctr();
    
    if ($products !== false && is_array($products)) {
        echo json_encode([
            'status' => 'success',
            'count' => count($products),
            'products' => $products
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'count' => 0,
            'products' => []
        ]);
    }
} catch (Exception $e) {
    error_log("Error in view_all_products_action: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch products'
    ]);
}
