<?php
require_once(__DIR__ . "/../classes/brand_class.php");

function add_brand_ctr($brand_name, $cat_id, $user_id)
{
    $brand = new brand_class();
    return $brand->add_brand($brand_name, $cat_id, $user_id);
}

function get_brands_by_user_ctr($user_id)
{
    $brand = new brand_class();
    return $brand->get_brands_by_user($user_id);
}

function update_brand_ctr($brand_id, $brand_name)
{
    $brand = new brand_class();
    return $brand->update_brand($brand_id, $brand_name);
}

function delete_brand_ctr($brand_id)
{
    $brand = new brand_class();
    return $brand->delete_brand($brand_id);
}

function get_categories_ctr()
{
    $brand = new brand_class();
    $method = 'get_categories';
    if (is_callable([$brand, $method])) {
        return call_user_func([$brand, $method]);
    }
    // Fallback: method not available in brand_class — return empty array to avoid fatal error
    return [];
}
?>
