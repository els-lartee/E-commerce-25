<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
session_start();
require_once '../settings/core.php';
require_once '../controllers/product_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'POST required']);
    exit;
}

error_log("POST data received: " . print_r($_POST, true));
error_log("FILES data received: " . print_r($_FILES, true));

$data = [
    'product_cat' => intval($_POST['product_cat'] ?? 0),
    'product_brand' => intval($_POST['product_brand'] ?? 0),
    'product_title' => trim($_POST['product_title'] ?? ''),
    'product_price' => floatval($_POST['product_price'] ?? 0),
    'product_desc' => trim($_POST['product_desc'] ?? ''),
    'product_keywords' => trim($_POST['product_keywords'] ?? ''),
];

error_log("Processed data: " . print_r($data, true));

$user_id = $_SESSION['user_id'] ?? 0;
$imageFile = $_FILES['product_image'] ?? null;

error_log("Calling add_product_ctr with data");
$res = add_product_ctr($data, $imageFile, $user_id);
if ($res) {
    echo json_encode(['status' => 'success', 'message' => 'Product added', 'product_id' => $res]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to add product']);
}
