<?php
require_once '../classes/cart_class.php';
require_once '../settings/core.php';

function add_to_cart_ctr($product_id, $qty = 1) {
    $customer_id = get_user_id(); // null for guests
    $cart = new Cart();
    return $cart->add_to_cart($customer_id, $product_id, $qty);
}

function update_cart_item_ctr($cart_id, $qty) {
    $customer_id = get_user_id(); // null for guests
    $cart = new Cart();
    return $cart->update_cart_quantity($customer_id, $cart_id, $qty);
}

function remove_from_cart_ctr($cart_id) {
    $customer_id = get_user_id(); // null for guests
    $cart = new Cart();
    return $cart->remove_from_cart($customer_id, $cart_id);
}

function get_user_cart_ctr() {
    $customer_id = get_user_id(); // null for guests
    $cart = new Cart();
    return $cart->get_cart_items($customer_id);
}

function empty_cart_ctr() {
    $customer_id = get_user_id(); // null for guests
    $cart = new Cart();
    return $cart->empty_cart($customer_id);
}

function get_cart_count_ctr() {
    $customer_id = get_user_id(); // null for guests
    $cart = new Cart();
    return $cart->get_cart_count($customer_id);
}
