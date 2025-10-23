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
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid brand ID.']);
        exit;
    }

    if (empty($name) || strlen($name) > 100) {
        echo json_encode(['status' => 'error', 'message' => 'Brand name is required and must be less than 100 characters.']);
        exit;
    }

    $user_id = get_user_id();
    $result = update_brand_ctr($id, $name, $user_id);
    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Brand updated successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Brand name already exists for this category or access denied.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'POST method required.']);
}
?>
