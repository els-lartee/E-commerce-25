<?php

require_once dirname(__DIR__) . '/classes/category_class.php';

/**
 * Category controller for handling category operations
 */

// Add category
function add_category_ctr($name, $user_id) {
    $category = new Category();
    return $category->addCategory($name, $user_id);
}

// Get all categories for user
function get_categories_ctr($user_id) {
    $category = new Category();
    return $category->getCategories($user_id);
}

// Get category by ID
function get_category_by_id_ctr($id, $user_id) {
    $category = new Category();
    return $category->getCategoryById($id, $user_id);
}

// Update category
function update_category_ctr($id, $name, $user_id) {
    $category = new Category();
    return $category->updateCategory($id, $name, $user_id);
}

// Delete category
function delete_category_ctr($id, $user_id) {
    $category = new Category();
    return $category->deleteCategory($id, $user_id);
}
?>
