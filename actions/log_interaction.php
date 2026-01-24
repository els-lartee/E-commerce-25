<?php
session_start();
require_once "../db/connection.php"; // adjust if your DB file name is different

if (!isset($_SESSION['session_id'])) {
    $_SESSION['session_id'] = uniqid("sess_", true);
}

include "db.php";
session_start();

$session_id = $_SESSION['session_id'];
$product_id = $_POST['product_id'];
$action = $_POST['action'];
$duration = $_POST['duration'] ?? 0;

$stmt = $conn->prepare(
  "INSERT INTO user_interactions (session_id, product_id, action, duration)
   VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("sisi", $session_id, $product_id, $action, $duration);
$stmt->execute();
