<?php
require_once(__DIR__ . "/../controllers/brand_controller.php");

if (isset($_POST['brand_id'])) {
    $brand_id = intval($_POST['brand_id']);
    $result = delete_brand_ctr($brand_id);
    echo $result ? "Brand deleted successfully!" : "Failed to delete brand.";
} else {
    echo "Missing data.";
}
?>
