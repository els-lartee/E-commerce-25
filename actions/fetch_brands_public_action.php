<?php
header('Content-Type: application/json');
require_once '../classes/brand_class.php';

try {
    $brand = new brand_class();
    $brands = $brand->getAllBrandsPublic();
    
    if ($brands !== false && is_array($brands)) {
        echo json_encode([
            'status' => 'success',
            'count' => count($brands),
            'brands' => $brands
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'count' => 0,
            'brands' => []
        ]);
    }
} catch (Exception $e) {
    error_log("Error in fetch_brands_public_action: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch brands'
    ]);
}
