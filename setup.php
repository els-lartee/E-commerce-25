<?php
require_once 'settings/db_class.php';

$db = new db_connection();
$db->db_connect();

if (!$db->db) {
    die("Database connection failed");
}

// Create categories table if not exists
$check_table = $db->db->query("SHOW TABLES LIKE 'categories'");
if ($check_table->num_rows == 0) {
    $create_table = "CREATE TABLE categories (
        cat_id INT AUTO_INCREMENT PRIMARY KEY,
        cat_name VARCHAR(100) UNIQUE NOT NULL,
        user_id INT(11) DEFAULT 0
    )";
    $db->db->query($create_table);
    echo "Categories table created.<br>";
} else {
    echo "Categories table already exists.<br>";
}

// Add user_id column if not exists (in case table existed without it)
$check_col = $db->db->query("SHOW COLUMNS FROM categories LIKE 'user_id'");
if ($check_col->num_rows == 0) {
    $query = "ALTER TABLE categories ADD COLUMN user_id INT(11) NOT NULL DEFAULT 0";
    $db->db->query($query);
    echo "Added user_id column.<br>";
} else {
    echo "user_id column already exists.<br>";
}

// Clean up duplicates in cat_name if any
$dup_check = $db->db->query("SELECT cat_name FROM categories GROUP BY cat_name HAVING COUNT(*) > 1");
if ($dup_check->num_rows > 0) {
    echo "Duplicates found in cat_name. Cleaning up...<br>";
    while ($dup = $dup_check->fetch_assoc()) {
        $name = $db->db->real_escape_string($dup['cat_name']);
        // Delete all but the first (lowest cat_id) occurrence
        $db->db->query("DELETE c1 FROM categories c1 INNER JOIN categories c2 WHERE c1.cat_id > c2.cat_id AND c1.cat_name = c2.cat_name AND c1.cat_name = '$name'");
    }
    echo "Duplicates removed.<br>";
} else {
    echo "No duplicates in cat_name.<br>";
}

// Remove global UNIQUE constraint on cat_name if exists
$global_unique_check = $db->db->query("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'categories' AND CONSTRAINT_TYPE = 'UNIQUE' AND CONSTRAINT_NAME = 'cat_name'");
if ($global_unique_check->fetch_assoc()['count'] > 0) {
    $drop_global = "ALTER TABLE categories DROP INDEX cat_name";
    $db->db->query($drop_global);
    echo "Dropped global UNIQUE constraint on cat_name.<br>";
} else {
    echo "No global UNIQUE constraint on cat_name.<br>";
}

// Add composite UNIQUE constraint on (cat_name, user_id) if not exists
$composite_unique_check = $db->db->query("SELECT COUNT(*) as count FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'categories' AND CONSTRAINT_TYPE = 'UNIQUE' AND CONSTRAINT_NAME = 'unique_cat_user'");
if ($composite_unique_check->fetch_assoc()['count'] == 0) {
    $add_composite = "ALTER TABLE categories ADD UNIQUE KEY unique_cat_user (cat_name, user_id)";
    $db->db->query($add_composite);
    echo "Added composite UNIQUE constraint on (cat_name, user_id).<br>";
} else {
    echo "Composite UNIQUE constraint on (cat_name, user_id) already exists.<br>";
}

// Insert admin user if not exists
$email = 'admin@example.com';
$check = $db->db->prepare("SELECT customer_id FROM customer WHERE customer_email = ?");
$check->bind_param("s", $email);
$check->execute();
if ($check->get_result()->num_rows == 0) {
    $name = 'Admin';
    $pass = 'password';
    $country = 'Country';
    $city = 'City';
    $contact = '1234567890';
    $role = 1;
    $insert = $db->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_country, customer_city, customer_contact, user_role) VALUES (?, ?, MD5(?), ?, ?, ?, ?)");
    $insert->bind_param("ssssssi", $name, $email, $pass, $country, $city, $contact, $role);
    $insert->execute();
    echo "Admin user inserted.<br>";
} else {
    echo "Admin user already exists.<br>";
}

echo "Setup complete.";
?>
