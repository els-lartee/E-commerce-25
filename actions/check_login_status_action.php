<?php
require_once __DIR__ . '/../settings/core.php';

header('Content-Type: application/json');

echo json_encode([
    'status' => 'success',
    'logged_in' => is_logged_in(),
    'user_id' => get_user_id()
]);
