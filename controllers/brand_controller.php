<?php

require_once '../classes/brand_class.php';

function add_brand_ctr($brand_name, $cat_id, $user_id)
{
    $brand = new Brand();
    return $brand->add_brand($brand_name, $cat_id, $user_id);
}

function update_brand_ctr($brand_id, $brand_name)
{
    $brand = new Brand();
    return $brand->update_brand($brand_id, $brand_name);
}

function delete_brand_ctr($brand_id)
{
    $brand = new Brand();
    return $brand->delete_brand($brand_id);
}

function get_brands_by_user_ctr($user_id)
{
    $brand = new Brand();
    return $brand->get_brands_by_user($user_id);
}

function get_brands_by_category_ctr($cat_id, $user_id)
{
    $brand = new Brand();
    return $brand->get_brands_by_category($cat_id, $user_id);
}

