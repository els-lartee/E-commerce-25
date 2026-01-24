<?php
session_start();

// Generate session ID if not exists
if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid("sess_", true);
}

require_once '../settings/db_class.php';

$session_id = $_SESSION['session_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';
$duration = isset($_POST['duration']) ? (int)$_POST['duration'] : 0;

if ($product_id > 0 && !empty($action)) {
    $conn = new db_connection();
    $db = $conn->db_conn();
    
    if ($db) {
        $stmt = $db->prepare(
            "INSERT INTO user_interactions (session_id, product_id, action, duration, created_at) 
             VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->bind_param("sisi", $session_id, $product_id, $action, $duration);
        $stmt->execute();
        $stmt->close();
        
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid parameters']);
}

