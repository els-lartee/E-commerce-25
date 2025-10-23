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

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid brand ID.']);
        exit;
    }

    $user_id = get_user_id();
    $result = delete_brand_ctr($id, $user_id);
    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Brand deleted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete brand or access denied.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'POST method required.']);
}
?>
