<?php
session_start();
header('Content-Type: application/json');

require_once '../settings/core.php';
require_once '../controllers/jewellery_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied. Admin only.']);
    exit;
}

$response = ['status' => 'error', 'message' => 'Invalid request.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid jewellery ID.']);
        exit;
    }

    try {
        $result = delete_jewellery_ctr($id, get_user_id());
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Jewellery deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete jewellery or access denied.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'POST method required.']);
}
?>
