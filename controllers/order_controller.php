<?php
require_once '../classes/order_class.php';
require_once '../settings/core.php';

function create_order_ctr($customer_id, $order_ref, $total_amount) {
    $order = new Order();
    return $order->create_order($customer_id, $order_ref, $total_amount);
}

function add_order_details_ctr($order_id, $product_id, $quantity, $price) {
    $order = new Order();
    return $order->add_order_details($order_id, $product_id, $quantity, $price);
}

function record_payment_ctr($order_id, $amount, $payment_method = 'card', $status = 'completed') {
    $order = new Order();
    return $order->record_payment($order_id, $amount, $payment_method, $status);
}

function get_user_orders_ctr($customer_id) {
    $order = new Order();
    return $order->get_user_orders($customer_id);
}

function get_order_details_ctr($order_id) {
    $order = new Order();
    return $order->get_order_details($order_id);
}
