<?php

require_once '../classes/customer_class.php';

// Register customer
function register_customer_ctr($name, $email, $password, $country, $city, $phone_number, $role, $image = null)
{
    $customer = new Customer();
    $customer_id = $customer->createCustomer($name, $email, $password, $country, $city, $phone_number, $role, $image);
    return $customer_id ? $customer_id : false;
}

// Edit customer
function edit_customer_ctr($customer_id, $name, $email, $country, $city, $phone_number, $image = null)
{
    $customer = new Customer();
    return $customer->editCustomer($customer_id, $name, $email, $country, $city, $phone_number, $image);
}

// Delete customer
function delete_customer_ctr($customer_id)
{
    $customer = new Customer();
    return $customer->deleteCustomer($customer_id);
}

// Check if email exists
function check_email_ctr($email)
{
    $customer = new Customer();
    return $customer->checkEmailExists($email);
}

// Get customer by email
function get_customer_by_email_ctr($email)
{
    $customer = new Customer();
    return $customer->getCustomerByEmail($email);
}

// Login customer
function login_customer_ctr($email, $password) {
    $customer = new Customer();
    $data = $customer->getCustomerByEmail($email);

    if ($data) {
        $db_pass = $data['customer_pass'];

        // Case 1: Hashed password in DB
        if (password_verify($password, $db_pass)) {
            return $data;
        }

        // Case 2: MD5 hashed password in DB (legacy)
        if (md5($password) === $db_pass) {
            return $data;
        }

        // Case 3: Plain text password in DB (legacy)
        if ($password === $db_pass) {
            return $data;
        }

        // Wrong password
        return false;
    }

    // No user found
    return false;
}
