<?php
session_start();
header('Content-Type: application/json');

require_once '../settings/core.php';
require_once '../controllers/jewellery_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Access denied.']);
    exit;
}

try {
    $jewellery = get_jewellery_ctr(get_user_id());
    echo json_encode(['status' => 'success', 'data' => $jewellery]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
