<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../settings/db_class.php';
$db = new db_connection();

// Try to connect
if ($db->db_connect()) {
    $response = [
        'status' => 'success',
        'message' => 'Connected to database successfully!',
        'server' => defined('SERVER') ? SERVER : 'not defined',
        'database' => defined('DATABASE') ? DATABASE : 'not defined',
        'port' => defined('PORT') ? constant('PORT') : 'default'
    ];
} else {
    $response = [
        'status' => 'error',
        'message' => 'Database connection failed.',
        'error' => mysqli_connect_error(),
        'server' => defined('SERVER') ? SERVER : 'not defined',
        'database' => defined('DATABASE') ? DATABASE : 'not defined',
        'port' => defined('PORT') ? constant('PORT') : 'default'
    ];
}

echo json_encode($response, JSON_PRETTY_PRINT);
exit;
?>