<?php
require_once(__DIR__ . "/../controllers/brand_controller.php");

if (isset($_POST['brand_id'], $_POST['brand_name'])) {
    $brand_id = intval($_POST['brand_id']);
    $brand_name = trim($_POST['brand_name']);

    $result = update_brand_ctr($brand_id, $brand_name);
    echo $result ? "Brand updated successfully!" : "Failed to update brand.";
} else {
    echo "Missing data.";
}
?>
