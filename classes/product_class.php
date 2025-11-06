<?php
require_once '../settings/db_class.php';

class Product extends db_connection {
    public function __construct() {
        parent::db_connect();
    }

    public function add_product($data, $imageFile = null, $user_id = 0) {
        try {
            error_log("Starting add_product method");
            
            $conn = $this->db_conn();
            if (!$conn) {
                error_log("Database connection failed");
                return false;
            }

            // Handle image first if provided
            $image_path = null;
            if ($imageFile && isset($imageFile['tmp_name']) && is_uploaded_file($imageFile['tmp_name'])) {
                error_log("Processing uploaded image");
                $upload_dir = '../uploads/products/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }

                $ext = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
                $filename = 'product_' . time() . '.' . $ext;
                
                if (strlen($filename) > 100) {
                    $filename = substr($filename, 0, 96) . '.' . $ext;
                }

                $filepath = $upload_dir . $filename;
                error_log("Attempting to move uploaded file to: " . $filepath);
                
                if (move_uploaded_file($imageFile['tmp_name'], $filepath)) {
                    $image_path = 'uploads/products/' . $filename;
                    error_log("Image uploaded successfully to: " . $image_path);
                } else {
                    error_log("Failed to move uploaded file. Upload error: " . $imageFile['error']);
                    error_log("Target path: " . $filepath);
                }
            }

            // Prepare product data
            $product_cat = (int)($data['product_cat'] ?? 0);
            $product_brand = (int)($data['product_brand'] ?? 0);
            $title = trim($data['product_title'] ?? '');
            $price = (float)($data['product_price'] ?? 0);
            $desc = trim($data['product_desc'] ?? '');
            $keywords = trim($data['product_keywords'] ?? '');

            // Insert product with or without image
            $sql = "INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            if (!$stmt) {
                error_log("Prepare failed: " . mysqli_error($conn));
                return false;
            }

            mysqli_stmt_bind_param($stmt, "iisdsss", 
                $product_cat, 
                $product_brand, 
                $title, 
                $price, 
                $desc, 
                $image_path,
                $keywords
            );

            $success = mysqli_stmt_execute($stmt);
            
            if (!$success) {
                error_log("Execute failed: " . mysqli_stmt_error($stmt));
                mysqli_stmt_close($stmt);
                return false;
            }

            $product_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);

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
                FROM products p 
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

        $stmt = mysqli_prepare($conn, "DELETE FROM products WHERE product_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }
}