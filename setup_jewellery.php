<?php
require_once 'settings/db_class.php';
require_once 'settings/db_cred.php';

$conn = new db_connection();
$db = $conn->db_connect();

try {
    // Rename table
    $db->query("RENAME TABLE categories TO jewellery");
    
    // Rename fields if needed (assuming cat_id and cat_name exist)
    $db->query("ALTER TABLE jewellery CHANGE cat_id id INT AUTO_INCREMENT PRIMARY KEY");
    $db->query("ALTER TABLE jewellery CHANGE cat_name name VARCHAR(100) NOT NULL");
    
    // Ensure user_id exists (if not, add it)
    $result = $db->query("SHOW COLUMNS FROM jewellery LIKE 'user_id'");
    if ($result->num_rows == 0) {
        $db->query("ALTER TABLE jewellery ADD COLUMN user_id INT DEFAULT 0 AFTER name");
    }
    
    // Drop old unique if exists, add composite unique on (name, user_id)
    $db->query("ALTER TABLE jewellery DROP INDEX IF EXISTS cat_name");
    $db->query("ALTER TABLE jewellery ADD UNIQUE KEY unique_jewellery (name, user_id)");
    
    echo "Database updated successfully: Table renamed to 'jewellery' with fields id (AUTO_INCREMENT), name, user_id (unique on name+user_id).\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
