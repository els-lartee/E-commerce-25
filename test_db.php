<?php
require_once 'settings/db_class.php';
$db = new db_connection();
if ($db->db_connect()) {
    echo 'DB connected successfully';
} else {
    echo 'DB connection failed: ' . mysqli_connect_error();
}
?>
