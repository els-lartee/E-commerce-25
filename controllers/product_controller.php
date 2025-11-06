<?php

require_once '../classes/product_class.php';
require_once '../settings/core.php';

function add_product_ctr($data, $imageFile = null, $user_id = 0)
{
    try {
        if (!is_logged_in() || !is_admin()) {
            error_log("Unauthorized attempt to add product");
            return false;
        }
        $p = new Product();
        return $p->add_product($data, $imageFile, $user_id);
    } catch (Exception $e) {
        error_log("Error in add_product_ctr: " . $e->getMessage());
        return false;
    }
}

function update_product_ctr($product_id, $data, $imageFile = null, $user_id = 0)
{
    if (!is_logged_in() || !is_admin()) return false;
    $p = new Product();
    return $p->update_product($product_id, $data, $imageFile, $user_id);
}

function delete_product_ctr($product_id)
{
    if (!is_logged_in() || !is_admin()) return false;
    $p = new Product();
    return $p->delete_product($product_id);
}

function get_all_products_ctr()
{
    try {
        if (!is_logged_in() || !is_admin()) {
            error_log("Unauthorized attempt to get products");
            return [];
        }
        $p = new Product();
        $result = $p->get_all_products();
        error_log("Products fetched: " . print_r($result, true));
        return $result;
    } catch (Exception $e) {
        error_log("Error in get_all_products_ctr: " . $e->getMessage());
        return [];
    }
}

function get_product_ctr($product_id)
{
    if (!is_logged_in() || !is_admin()) return false;
    $p = new Product();
    return $p->get_product($product_id);
}

function search_products_by_keyword_ctr($keyword)
{
    if (!is_logged_in() || !is_admin()) return [];
    $p = new Product();
    return $p->search_by_keyword($keyword);
}
