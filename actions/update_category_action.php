<?php
session_start();
header('Content-Type: application/json');

require_once '../settings/core.php';
require_once '../controllers/category_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Admin only.']);
    exit;
}

$response = ['status' => 'error', 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid category ID.']);
        exit;
    }

    if (empty($name) || strlen($name) > 100) {
        echo json_encode(['status' => 'error', 'message' => 'Category name is required and must be less than 100 characters.']);
        exit;
    }

    try {
        $result = update_category_ctr($id, $name, get_user_id());
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Category updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Category name already exists or access denied.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'POST method required.']);
}
?>
