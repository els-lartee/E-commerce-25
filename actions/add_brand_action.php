<?php
require_once(__DIR__ . "/../controllers/brand_controller.php");
session_start();

if (isset($_POST['brand_name'], $_POST['cat_id']) && isset($_SESSION['user_id'])) {
    $brand_name = trim($_POST['brand_name']);
    $cat_id = intval($_POST['cat_id']);
    $user_id = $_SESSION['user_id'];

    if (empty($brand_name)) {
        echo "Brand name cannot be empty.";
        exit;
    }

    $result = add_brand_ctr($brand_name, $cat_id, $user_id);
    echo $result ? "Brand added successfully!" : "Failed to add brand.";
} else {
    echo "Missing data.";
}
?>
