<?php
require_once 'settings/db_cred.php';
require_once 'settings/db_class.php';

$db = new db_connection();
$db->db_connect();

if (!$db->db) {
    die("Database connection failed");
}

// Insert customer user if not exists
$email = 'customer@example.com';
$check = $db->db->prepare("SELECT customer_id FROM customer WHERE customer_email = ?");
$check->bind_param("s", $email);
$check->execute();
if ($check->get_result()->num_rows == 0) {
    $name = 'Customer';
    $pass = 'password';
    $country = 'Country';
    $city = 'City';
    $contact = '0987654321';
    $role = 1; // Customer role
    $insert = $db->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) VALUES (?, ?, MD5(?), ?, ?, ?, ?)");
    $insert->bind_param("ssssssi", $name, $email, $pass, $country, $city, $contact, $role);
    $insert->execute();
    echo "Customer user inserted.<br>";
} else {
    echo "Customer user already exists.<br>";
}

echo "Setup customer complete.";
?>
