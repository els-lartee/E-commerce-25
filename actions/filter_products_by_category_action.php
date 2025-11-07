<?php
header('Content-Type: application/json');
require_once '../controllers/product_controller.php';

try {
    $cat_id = intval($_GET['cat_id'] ?? $_GET['category'] ?? 0);
    
    if ($cat_id <= 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid category ID'
        ]);
        exit;
    }
    
    $products = filter_products_by_category_ctr($cat_id);
    
    echo json_encode([
        'status' => 'success',
        'category_id' => $cat_id,
        'count' => count($products),
        'products' => $products
    ]);
} catch (Exception $e) {
    error_log("Error in filter_products_by_category_action: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to filter products by category'
    ]);
}
