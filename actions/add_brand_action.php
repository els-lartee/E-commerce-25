<?php
session_start();
header('Content-Type: application/json');

require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Admin only.']);
    exit;
}

$response = ['status' => 'error', 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $cat_id = intval($_POST['cat_id'] ?? 0);

    if (empty($name) || strlen($name) > 100) {
        echo json_encode(['status' => 'error', 'message' => 'Brand name is required and must be less than 100 characters.']);
        exit;
    }

    if ($cat_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Valid category is required.']);
        exit;
    }

    $user_id = get_user_id();
    $result = add_brand_ctr($name, $cat_id, $user_id);
    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Brand added successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Brand name already exists for this category.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'POST method required.']);
}
?>
