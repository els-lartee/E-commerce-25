<?php
header('Content-Type: application/json');
require_once '../controllers/product_controller.php';

try {
    $product_id = intval($_GET['id'] ?? 0);
    
    if ($product_id <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid product ID'
        ]);
        exit;
    }
    
    $product = view_single_product_ctr($product_id);
    
    if ($product) {
        echo json_encode([
            'status' => 'success',
            'product' => $product
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Product not found'
        ]);
    }
} catch (Exception $e) {
    error_log("Error in view_single_product_action: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch product'
    ]);
}
