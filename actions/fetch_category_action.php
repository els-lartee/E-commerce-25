<?php
header('Content-Type: application/json');
session_start();
require_once '../settings/core.php';
require_once '../settings/db_class.php';

if (!is_logged_in() || !is_admin()) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$db = new db_connection();
$db->db_connect();
$cats = $db->db_fetch_all("SELECT cat_id, cat_name FROM categories ORDER BY cat_name");

echo json_encode(['status' => 'success', 'categories' => $cats]);

?>
