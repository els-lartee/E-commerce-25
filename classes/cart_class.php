<?php
require_once __DIR__ . '/../settings/db_class.php';

class Cart extends db_connection {
    public function __construct() {
        parent::db_connect();
    }

    /**
     * Add product to cart
     * For logged-in users: use c_id
     * For guests: use session_id
     * If product exists, update quantity
     */
    public function add_to_cart($customer_id, $product_id, $qty) {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $ip_add = session_id();

        // Check if product already exists in cart
        $existing = $this->check_existing_product($customer_id, $product_id);
        if ($existing) {
            // Update quantity
            return $this->update_cart_quantity($customer_id, $existing['p_id'], $existing['qty'] + $qty);
        }

        // Add new item - both ip_add and c_id are always set
        $sql = "INSERT INTO cart (p_id, ip_add, c_id, qty) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isii", $product_id, $ip_add, $customer_id, $qty);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    /**
     * Update cart item quantity
     * Note: This method now requires customer_id to identify the user's cart
     */
    public function update_cart_quantity($customer_id, $cart_id, $qty) {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $ip_add = session_id();

        if ($customer_id) {
            $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND c_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iii", $qty, $cart_id, $customer_id);
        } else {
            $sql = "UPDATE cart SET qty = ? WHERE p_id = ? AND ip_add = ? AND c_id IS NULL";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iis", $qty, $cart_id, $ip_add);
        }
        
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    /**
     * Get cart count for user
     */
    public function get_cart_count($customer_id) {
        $conn = $this->db_conn();
        if (!$conn) return 0;

        $ip_add = session_id();

        if ($customer_id) {
            $sql = "SELECT SUM(qty) as total FROM cart WHERE c_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $customer_id);
        } else {
            $sql = "SELECT SUM(qty) as total FROM cart WHERE ip_add = ? AND c_id IS NULL";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $ip_add);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row['total'] ?? 0;
    }

    /**
     * Remove item from cart
     * Note: This method now requires customer_id to identify the user's cart
     */
    public function remove_from_cart($customer_id, $cart_id) {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $ip_add = session_id();

        if ($customer_id) {
            $sql = "DELETE FROM cart WHERE p_id = ? AND c_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $cart_id, $customer_id);
        } else {
            $sql = "DELETE FROM cart WHERE p_id = ? AND ip_add = ? AND c_id IS NULL";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "is", $cart_id, $ip_add);
        }
        
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    /**
     * Get cart items for user
     */
    public function get_cart_items($customer_id) {
        $conn = $this->db_conn();
        if (!$conn) return [];

        $ip_add = session_id();

        $sql = "SELECT c.*, p.product_title, p.product_price, p.product_image,
                       j.name as cat_name, b.brand_name
                FROM cart c
                LEFT JOIN product p ON c.p_id = p.product_id
                LEFT JOIN jewellery j ON p.product_cat = j.id
                LEFT JOIN brands b ON p.product_brand = b.brand_id";

        if ($customer_id) {
            $sql .= " WHERE c.c_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $customer_id);
        } else {
            $sql .= " WHERE c.ip_add = ? AND c.c_id IS NULL";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $ip_add);
        }

        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $items = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        return $items;
    }

    /**
     * Empty cart for user
     */
    public function empty_cart($customer_id) {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $ip_add = session_id();

        if ($customer_id) {
            $sql = "DELETE FROM cart WHERE c_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $customer_id);
        } else {
            $sql = "DELETE FROM cart WHERE ip_add = ? AND c_id IS NULL";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $ip_add);
        }
        
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    /**
     * Check if product exists in cart
     * Returns cart item if exists, false otherwise
     */
    public function check_existing_product($customer_id, $product_id) {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $ip_add = session_id();

        if ($customer_id) {
            $sql = "SELECT * FROM cart WHERE p_id = ? AND c_id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ii", $product_id, $customer_id);
        } else {
            $sql = "SELECT * FROM cart WHERE p_id = ? AND ip_add = ? AND c_id IS NULL";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "is", $product_id, $ip_add);
        }
        
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $item = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $item ?: false;
    }
}
