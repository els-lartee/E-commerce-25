<?php
header('Content-Type: application/json');
require_once '../classes/jewellery_class.php';

try {
    $jewellery = new Jewellery();
    $categories = $jewellery->getAllJewelleryPublic();
    
    if ($categories !== false && is_array($categories)) {
        echo json_encode([
            'status' => 'success',
            'count' => count($categories),
            'categories' => $categories
        ]);
    } else {
        echo json_encode([
            'status' => 'success',
            'count' => 0,
            'categories' => []
        ]);
    }
} catch (Exception $e) {
    error_log("Error in fetch_categories_public_action: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch categories'
    ]);
}
