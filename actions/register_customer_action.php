<?php

header('Content-Type: application/json');

session_start();

$response = array();

// TODO: Check if the user is already logged in and redirect to the dashboard
if (isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/customer_controller.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$country = $_POST['country'];
$city = $_POST['city'];
$phone_number = $_POST['phone_number'];
$role = $_POST['role'];

$customer = check_email_ctr($email);
if ($customer) {
    $response['status'] = 'error';
    $response['message'] = 'Email already exists';
    echo json_encode($response);
    exit();
}

$customer_id = register_customer_ctr($name, $email, $password, $country, $city, $phone_number, $role);

if ($customer_id) {
    $response['status'] = 'success';
    $response['message'] = 'Registered successfully';
    $response['customer_id'] = $customer_id;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to register';
}

echo json_encode($response);
