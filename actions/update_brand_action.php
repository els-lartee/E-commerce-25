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
$brand_name = trim($_POST['brand_name'] ?? '');

if ($brand_id <= 0 || $brand_name === '') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$res = update_brand_ctr($brand_id, $brand_name);
if ($res) {
    echo json_encode(['status' => 'success', 'message' => 'Brand updated']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update brand']);
}

