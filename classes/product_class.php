<?php
require_once '../settings/db_class.php';

class Product extends db_connection {
    public function __construct() {
        parent::db_connect();
    }

    public function add_product($data, $imageFile = null, $user_id = 0) {
        try {
            error_log("Starting add_product method with data: " . print_r($data, true));
            if ($imageFile) {
                error_log("Image file info: " . print_r($imageFile, true));
            }
            
            $conn = $this->db_conn();
            if (!$conn) {
                error_log("Database connection failed");
                return false;
            }

            // Handle image first if provided
            $image_path = null;
            if ($imageFile && isset($imageFile['tmp_name']) && is_uploaded_file($imageFile['tmp_name'])) {
                error_log("Processing uploaded image");
                $base_path = dirname(dirname(__FILE__));
                $upload_dir = $base_path . '/uploads/products/';
                error_log("Upload directory: " . $upload_dir);
                
                if (!is_dir($upload_dir)) {
                    error_log("Creating upload directory");
                    if (!mkdir($upload_dir, 0777, true)) {
                        error_log("Failed to create upload directory: " . error_get_last()['message']);
                        return false;
                    }
                }

                $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
                $filename = 'product_' . time() . '.' . $ext;
                
                if (strlen($filename) > 100) {
                    $filename = substr($filename, 0, 96) . '.' . $ext;
                }

                $filepath = $upload_dir . $filename;
                error_log("Attempting to move uploaded file from {$imageFile['tmp_name']} to: " . $filepath);
                error_log("File permissions before move: " . substr(sprintf('%o', fileperms($upload_dir)), -4));
                
                if (@move_uploaded_file($imageFile['tmp_name'], $filepath)) {
                    $image_path = 'uploads/products/' . $filename; // Store relative path in database
                    error_log("Image uploaded successfully to: " . $image_path);
                    // Ensure correct permissions after upload
                    chmod($filepath, 0666);
                } else {
                    $error = error_get_last();
                    error_log("Failed to move uploaded file. Upload error: " . $imageFile['error']);
                    error_log("Move error: " . ($error ? $error['message'] : 'Unknown error'));
                    error_log("Target path: " . $filepath);
                    error_log("Source exists: " . (file_exists($imageFile['tmp_name']) ? 'yes' : 'no'));
                    error_log("Target dir writable: " . (is_writable($upload_dir) ? 'yes' : 'no'));
                    return false;
                }
            }

            // Prepare product data
            $product_cat = (int)($data['product_cat'] ?? 0);
            $product_brand = (int)($data['product_brand'] ?? 0);
            $title = trim($data['product_title'] ?? '');
            $price = (float)($data['product_price'] ?? 0);
            $desc = trim($data['product_desc'] ?? '');
            $keywords = trim($data['product_keywords'] ?? '');

            // Validate required fields
            if ($product_cat <= 0) {
                error_log("Invalid category ID: " . $product_cat);
                return false;
            }
            if ($product_brand <= 0) {
                error_log("Invalid brand ID: " . $product_brand);
                return false;
            }
            if (empty($title)) {
                error_log("Title is required");
                return false;
            }
            if ($price <= 0) {
                error_log("Invalid price: " . $price);
                return false;
            }

            // Check if category exists
            $cat_check = mysqli_query($conn, "SELECT id FROM jewellery WHERE id = " . (int)$product_cat);
            if (!$cat_check || mysqli_num_rows($cat_check) === 0) {
                error_log("Category does not exist: " . $product_cat);
                return false;
            }
            mysqli_free_result($cat_check);

            // Check if brand exists
            $brand_check = mysqli_query($conn, "SELECT brand_id FROM brands WHERE brand_id = " . (int)$product_brand);
            if (!$brand_check || mysqli_num_rows($brand_check) === 0) {
                error_log("Brand does not exist: " . $product_brand);
                return false;
            }
            mysqli_free_result($brand_check);

            error_log("Validated data - Category: $product_cat, Brand: $product_brand, Title: $title, Price: $price");

            // Insert product with or without image
            $sql = "INSERT INTO product (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            error_log("Preparing SQL: " . $sql);
            error_log("Image path: " . ($image_path ?? 'null'));
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                error_log("Prepare failed: " . mysqli_error($conn));
                return false;
            }

            error_log("Statement prepared successfully");

            mysqli_stmt_bind_param($stmt, "iisdsss", 
                $product_cat, 
                $product_brand, 
                $title, 
                $price, 
                $desc, 
                $image_path,
                $keywords
            );

            error_log("Executing statement...");
            $success = mysqli_stmt_execute($stmt);
            
            if (!$success) {
                $error = mysqli_stmt_error($stmt);
                error_log("Execute failed with error: " . $error);
                error_log("SQL State: " . mysqli_stmt_sqlstate($stmt));
                error_log("Errno: " . mysqli_stmt_errno($stmt));
                mysqli_stmt_close($stmt);
                return false;
            }

            $product_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

            if ($product_id <= 0) {
                error_log("Failed to get inserted product ID");
                return false;
            }

            error_log("Product added successfully with ID: " . $product_id);
            return $product_id;

        } catch (Exception $e) {
            error_log("Exception in add_product: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function get_all_products() {
        $conn = $this->db_conn();
        if (!$conn) return [];

        $sql = "SELECT p.*, j.name as cat_name, b.brand_name 
                FROM product p 
                LEFT JOIN jewellery j ON p.product_cat = j.id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                ORDER BY p.product_id DESC";

        $result = mysqli_query($conn, $sql);
        if (!$result) {
            error_log("Query failed: " . mysqli_error($conn));
            return [];
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    public function delete_product($product_id) {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $stmt = mysqli_prepare($conn, "DELETE FROM product WHERE product_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }
}