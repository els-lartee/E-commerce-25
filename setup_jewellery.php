<?php
require_once 'settings/db_class.php';
require_once 'settings/db_cred.php';

$conn = new db_connection();
if (!$conn->db_connect()) {
    die("Connection failed: " . mysqli_connect_error());
}

try {
    // Drop foreign key constraint if exists
    $conn->db_write_query("ALTER TABLE products DROP FOREIGN KEY IF EXISTS products_ibfk_1");
    
    // Drop tables if exist
    $conn->db_write_query("DROP TABLE IF EXISTS jewellery");
    $conn->db_write_query("DROP TABLE IF EXISTS categories");
    
    // Create new jewellery table
    if (!$conn->db_write_query("CREATE TABLE jewellery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        user_id INT NOT NULL,
        UNIQUE KEY unique_jewellery (name, user_id)
    )")) {
        die("Failed to create jewellery table: " . mysqli_error($conn->db) . "\n");
    }
    
    echo "Database updated successfully: Created 'jewellery' table with fields id (AUTO_INCREMENT), name, user_id (unique on name+user_id).\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
