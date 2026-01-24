<?php
session_start();

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid("sess_", true);
}

header('Content-Type: application/json');

require_once '../controllers/customer_controller.php';

$email = $_POST['email'];

$exists = check_email_ctr($email);

echo json_encode(array('exists' => $exists));
