<?php
require_once(__DIR__ . "/../controllers/brand_controller.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$brands = get_brands_by_user_ctr($user_id);

error_log('Brands fetched: ' . print_r($brands, true));

if ($brands === false || empty($brands)) {
    echo json_encode(['status' => 'success', 'brands' => []]);
    exit;
}

$validBrands = array_filter($brands, function($brand) {
    return !empty($brand['brand_id']) && !empty($brand['brand_name']);
});

echo json_encode(['status' => 'success', 'brands' => array_values($validBrands)]);
?>
