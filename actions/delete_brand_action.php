<?php
header('Content-Type: application/json');
session_start();
require_once '../settings/core.php';
require_once '../controllers/brand_controller.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'POST required']);
    exit;
}

$brand_id = intval($_POST['brand_id'] ?? 0);
if ($brand_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid brand id']);
    exit;
}

$res = delete_brand_ctr($brand_id);
if ($res) {
    echo json_encode(['status' => 'success', 'message' => 'Brand deleted']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to delete brand']);
}

