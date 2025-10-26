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

if ($brand_name === '') {
    $response['message'] = 'Brand name is required';
    echo json_encode($response);
    exit;
}

$res = add_brand_ctr($brand_name);
if ($res) {
    $response = ['status' => 'success', 'message' => 'Brand added', 'brand_id' => $res];
} else {
    $response = ['status' => 'error', 'message' => 'Failed to add brand (may already exist)'];
}

echo json_encode($response);

