<?php

require_once '../classes/customer_class.php';

function register_customer_ctr($name, $email, $password, $country, $city, $phone_number, $role, $image = null)
{
    $customer = new Customer();
    $customer_id = $customer->createCustomer($name, $email, $password, $country, $city, $phone_number, $role, $image);
    if ($customer_id) {
        return $customer_id;
    }
    return false;
}

function edit_customer_ctr($customer_id, $name, $email, $country, $city, $phone_number, $image = null)
{
    $customer = new Customer();
    return $customer->editCustomer($customer_id, $name, $email, $country, $city, $phone_number, $image);
}

function delete_customer_ctr($customer_id)
{
    $customer = new Customer();
    return $customer->deleteCustomer($customer_id);
}

function check_email_ctr($email)
{
    $customer = new Customer();
    return $customer->checkEmailExists($email);
}

function get_customer_by_email_ctr($email)
{
    $customer = new Customer();
    return $customer->getCustomerByEmail($email);
}
