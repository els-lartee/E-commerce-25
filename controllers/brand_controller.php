<?php

require_once '../classes/brand_class.php';
require_once '../settings/core.php';

function add_brand_ctr($brand_name)
{
    // enforce admin at controller level as extra safety
    if (!is_logged_in() || !is_admin()) {
        return false;
    }
    $brand = new Brand();
    return $brand->add_brand($brand_name);
}

function update_brand_ctr($brand_id, $brand_name)
{
    if (!is_logged_in() || !is_admin()) {
        return false;
    }
    $brand = new Brand();
    return $brand->update_brand($brand_id, $brand_name);
}

function delete_brand_ctr($brand_id)
{
    if (!is_logged_in() || !is_admin()) {
        return false;
    }
    $brand = new Brand();
    return $brand->delete_brand($brand_id);
}

function get_all_brands_ctr()
{
    if (!is_logged_in() || !is_admin()) {
        return [];
    }
    $brand = new Brand();
    return $brand->get_all_brands();
}

