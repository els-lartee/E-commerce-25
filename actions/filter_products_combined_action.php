<?php
header('Content-Type: application/json');
require_once '../controllers/product_controller.php';

try {
    $cat_id = intval($_GET['cat_id'] ?? $_GET['category'] ?? 0);
    $brand_id = intval($_GET['brand_id'] ?? $_GET['brand'] ?? 0);
    $query = trim($_GET['q'] ?? $_GET['query'] ?? '');
    $products = [];
    
    if ($cat_id > 0 && $brand_id > 0) {
        $products = view_all_products_ctr();
        $products = array_filter($products, function($product) use ($cat_id, $brand_id) {
            return ($product['product_cat'] == $cat_id && $product['product_brand'] == $brand_id);
        });
        $products = array_values($products);
    } elseif ($cat_id > 0) {
        $products = filter_products_by_category_ctr($cat_id);
    } elseif ($brand_id > 0) {
        $products = filter_products_by_brand_ctr($brand_id);
    } else {
        $products = view_all_products_ctr();
    }
    
    if (!empty($query) && !empty($products)) {
        $search_term = strtolower($query);
        $products = array_filter($products, function($product) use ($search_term) {
            return (
                stripos($product['product_title'], $search_term) !== false ||
                stripos($product['product_desc'], $search_term) !== false ||
                stripos($product['product_keywords'], $search_term) !== false ||
                stripos($product['brand_name'], $search_term) !== false ||
                stripos($product['cat_name'], $search_term) !== false
            );
        });
        $products = array_values($products); // Re-index array
    }
    
    echo json_encode([
        'status' => 'success',
        'filters' => [
            'category_id' => $cat_id ?: null,
            'brand_id' => $brand_id ?: null,
            'search_query' => $query ?: null
        ],
        'count' => count($products),
        'products' => $products
    ]);
} catch (Exception $e) {
    error_log("Error in filter_products_combined_action: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to filter products'
    ]);
}
