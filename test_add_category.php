<?php
require_once 'settings/core.php'; // For session if needed, but skip checks
require_once 'controllers/category_controller.php';

try {
    $name = 'Test Category New'; // Unique name
    $result = add_category_ctr($name);
    if ($result) {
        echo 'Added successfully, ID: ' . $result;
    } else {
        echo 'Add failed: Name exists or other error';
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

// Fetch to verify
$categories = get_categories_ctr();
echo "\nCategories: " . json_encode($categories);
?>
