<?php

require_once dirname(__DIR__) . '/classes/jewellery_class.php';

/**
 * Jewellery controller for handling jewellery operations
 */

// Add jewellery
function add_jewellery_ctr($name, $user_id) {
    $jewellery = new Jewellery();
    return $jewellery->addJewellery($name, $user_id);
}

// Get all jewellery for user
function get_jewellery_ctr($user_id) {
    $jewellery = new Jewellery();
    return $jewellery->getJewellery($user_id);
}

// Get jewellery by ID
function get_jewellery_by_id_ctr($id, $user_id) {
    $jewellery = new Jewellery();
    return $jewellery->getJewelleryById($id, $user_id);
}

// Update jewellery
function update_jewellery_ctr($id, $name, $user_id) {
    $jewellery = new Jewellery();
    return $jewellery->updateJewellery($id, $name, $user_id);
}

// Delete jewellery
function delete_jewellery_ctr($id, $user_id) {
    $jewellery = new Jewellery();
    return $jewellery->deleteJewellery($id, $user_id);
}
?>
