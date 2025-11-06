<?php
header('Content-Type: application/json');
session_start();
require_once '../settings/core.php';
require_once '../settings/db_class.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

require_once '../controllers/jewellery_controller.php';
$cats = get_jewellery_ctr($_SESSION['user_id']);

// Debug log to check what we're getting
error_log('Categories fetched: ' . print_r($cats, true));

if ($cats === false || empty($cats)) {
    echo json_encode(['status' => 'error', 'message' => 'No categories found']);
    exit;
}

// Ensure all categories have non-empty values
$validCats = array_filter($cats, function($cat) {
    return !empty($cat['id']) && !empty($cat['name']);
});

echo json_encode(['status' => 'success', 'categories' => array_values($validCats)]);

?>
