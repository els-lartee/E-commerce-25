<?php
session_start();
header('Content-Type: application/json');

require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Admin only.']);
    exit;
}

$user_id = get_user_id();
$brands = get_brands_ctr($user_id);

if ($brands !== false) {
    echo json_encode(['status' => 'success', 'data' => $brands]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch brands.']);
}
?>
