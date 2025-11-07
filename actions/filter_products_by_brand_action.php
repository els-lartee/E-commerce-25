<?php
header('Content-Type: application/json');
require_once '../controllers/product_controller.php';

try {
    $brand_id = intval($_GET['brand_id'] ?? $_GET['brand'] ?? 0);
    
    if ($brand_id <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid brand ID'
        ]);
        exit;
    }
    
    $products = filter_products_by_brand_ctr($brand_id);
    
    echo json_encode([
        'status' => 'success',
        'brand_id' => $brand_id,
        'count' => count($products),
        'products' => $products
    ]);
} catch (Exception $e) {
    error_log("Error in filter_products_by_brand_action: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to filter products by brand'
    ]);
}
