<?php
require_once 'settings/db_class.php';

$db = new db_connection();
$db->db_connect();

if (!$db->db) {
    die("Database connection failed");
}

$email = 'admin@example.com';
$check = $db->db->prepare("SELECT customer_id, customer_name, user_role FROM customer WHERE customer_email = ?");
$check->bind_param("s", $email);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Admin user exists:<br>";
    echo "ID: " . $row['customer_id'] . "<br>";
    echo "Name: " . $row['customer_name'] . "<br>";
    echo "Role: " . $row['user_role'] . "<br>";
} else {
    echo "Admin user does not exist in the database.";
}
?>
