<?php
header('Content-Type: application/json');
session_start();
require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';

$response = ['status' => 'error', 'message' => 'Invalid request'];

if (!is_logged_in() || !is_admin()) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode($response);
    exit;
}

$brand_name = trim($_POST['brand_name'] ?? '');
$cat_id = intval($_POST['cat_id'] ?? 0);
$user_id = get_user_id();

if ($brand_name === '' || $cat_id <= 0) {
    $response['message'] = 'Brand name and category are required';
    echo json_encode($response);
    exit;
}

$res = add_brand_ctr($brand_name, $cat_id, $user_id);
if ($res) {
    $response = ['status' => 'success', 'message' => 'Brand added', 'brand_id' => $res];
} else {
    $response = ['status' => 'error', 'message' => 'Failed to add brand (may already exist)'];
}

echo json_encode($response);

