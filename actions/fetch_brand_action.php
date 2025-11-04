<?php
require_once(__DIR__ . "/../controllers/brand_controller.php");
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$user_id = $_SESSION['user_id'];
$brands = get_brands_by_user_ctr($user_id);

echo json_encode($brands ?? []);
?>
