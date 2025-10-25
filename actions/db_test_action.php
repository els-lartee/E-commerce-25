<?php
header('Content-Type: application/json');
require_once '../settings/db_class.php';

$db = new db_connection();
$ok = $db->db_connect();
if ($ok) {
    echo json_encode(['status' => 'success', 'message' => 'Connected to DB', 'server' => SERVER, 'database' => DATABASE, 'port' => (defined('PORT') ? PORT : 'default')]);
} else {
    $err = mysqli_connect_error();
    echo json_encode(['status' => 'error', 'message' => 'Connection failed', 'error' => $err, 'server' => SERVER, 'database' => DATABASE, 'port' => (defined('PORT') ? PORT : 'default')]);
}
