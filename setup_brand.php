<?php
require_once 'settings/db_class.php';
require_once 'settings/db_cred.php';

$conn = new db_connection();
if (!$conn->db_connect()) {
    die("Connection failed: " . mysqli_connect_error());
}

try {
    // Alter brands table to add cat_id and user_id
    $conn->db_write_query("ALTER TABLE brands ADD COLUMN cat_id INT NOT NULL AFTER brand_name");
    $conn->db_write_query("ALTER TABLE brands ADD COLUMN user_id INT NOT NULL AFTER cat_id");

    // Add unique constraint on (brand_name, cat_id, user_id)
    $conn->db_write_query("ALTER TABLE brands ADD UNIQUE KEY unique_brand (brand_name, cat_id, user_id)");

    // Add foreign key constraint for cat_id
    $conn->db_write_query("ALTER TABLE brands ADD CONSTRAINT brands_ibfk_1 FOREIGN KEY (cat_id) REFERENCES categories (cat_id) ON DELETE CASCADE");

    echo "Database updated successfully: Altered 'brands' table with cat_id, user_id, unique constraint, and foreign key.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
