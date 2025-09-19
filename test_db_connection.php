<?php
require_once 'settings/db_cred.php';

$mysqli = new mysqli(SERVER, USERNAME, PASSWD, DATABASE);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
} else {
    echo "Connected successfully to database " . DATABASE;
}
?>
