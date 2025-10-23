<?php

require_once dirname(__DIR__) . '/classes/brand_class.php';

/**
 * Brand controller functions
 */

function add_brand_ctr($name, $cat_id, $user_id)
{
    $brand = new Brand();
    return $brand->addBrand($name, $cat_id, $user_id);
}

function get_brands_ctr($user_id)
{
    $brand = new Brand();
    return $brand->getBrands($user_id);
}

function get_brand_by_id_ctr($id, $user_id)
{
    $brand = new Brand();
    return $brand->getBrandById($id, $user_id);
}

function update_brand_ctr($id, $name, $user_id)
{
    $brand = new Brand();
    return $brand->updateBrand($id, $name, $user_id);
}

function delete_brand_ctr($id, $user_id)
{
    $brand = new Brand();
    return $brand->deleteBrand($id, $user_id);
}

function get_categories_ctr()
{
    $brand = new Brand();
    return $brand->getCategories();
}
?>
