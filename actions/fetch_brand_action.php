<?php
header('Content-Type: application/json');
session_start();
require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = get_user_id();
$brands = get_brands_by_user_ctr($user_id);
echo json_encode(['status' => 'success', 'brands' => $brands]);

