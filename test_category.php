<?php
require_once 'classes/category_class.php';
try {
    $cat = new Category();
    echo 'Category class loaded successfully';
    $categories = $cat->getCategories();
    echo 'Categories fetched: ' . count($categories);
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
