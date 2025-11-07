<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json');

$response = array();

if (isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/customer_controller.php';

try {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (empty($email) || empty($password)) {
        throw new Exception('Email and password are required');
    }

    $customer_data = login_customer_ctr($email, $password);

    if (!$customer_data) {
        throw new Exception('Invalid email or password');
    }

    $_SESSION['user_id'] = $customer_data['customer_id'] ?? null;
    $_SESSION['user_role'] = $customer_data['user_role'] ?? 'customer';
    $_SESSION['user_name'] = $customer_data['customer_name'] ?? '';
    $_SESSION['user_email'] = $customer_data['customer_email'] ?? $email;

    $response['status'] = 'success';
    $response['message'] = 'Login successful';
} catch (Exception $e) {
    error_log('Login error: ' . $e->getMessage());
    $response['status'] = 'error';
    $response['message'] = 'Error: ' . $e->getMessage();
}

echo json_encode($response);
