<?php
session_start();
header('Content-Type: application/json');

require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Admin only.']);
    exit;
}

$categories = get_categories_ctr();

if ($categories !== false) {
    echo json_encode(['status' => 'success', 'data' => $categories]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to fetch categories.']);
}
?>
