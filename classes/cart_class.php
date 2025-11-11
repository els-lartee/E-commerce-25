<?php
require_once '../settings/db_class.php';

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

        // Determine identifier: customer_id or session_id
        $identifier = $customer_id ? ['c_id', $customer_id] : ['ip_add', session_id()];
        list($id_field, $id_value) = $identifier;

        // Check if product already exists in cart
        $existing = $this->check_existing_product($customer_id, $product_id);
        if ($existing) {
            // Update quantity
            return $this->update_cart_quantity($existing['p_id'], $existing['qty'] + $qty);
        }

        // Add new item
        $sql = "INSERT INTO cart (p_id, $id_field, qty) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "isi", $product_id, $id_value, $qty);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    /**
     * Update cart item quantity
     */
    public function update_cart_quantity($cart_id, $qty) {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "UPDATE cart SET qty = ? WHERE p_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $qty, $cart_id);
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

        // Determine identifier
        $identifier = $customer_id ? ['c_id', $customer_id] : ['ip_add', session_id()];
        list($id_field, $id_value) = $identifier;

        $sql = "SELECT SUM(qty) as total FROM cart WHERE $id_field = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $id_value);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $row['total'] ?? 0;
    }

    /**
     * Remove item from cart
     */
    public function remove_from_cart($cart_id) {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "DELETE FROM cart WHERE p_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $cart_id);
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

        // Determine identifier
        $identifier = $customer_id ? ['c_id', $customer_id] : ['ip_add', session_id()];
        list($id_field, $id_value) = $identifier;

        $sql = "SELECT c.*, p.product_title, p.product_price, p.product_image,
                       j.name as cat_name, b.brand_name
                FROM cart c
                LEFT JOIN product p ON c.p_id = p.product_id
                LEFT JOIN jewellery j ON p.product_cat = j.id
                LEFT JOIN brands b ON p.product_brand = b.brand_id
                WHERE c.$id_field = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $id_value);
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

        // Determine identifier
        $identifier = $customer_id ? ['c_id', $customer_id] : ['ip_add', session_id()];
        list($id_field, $id_value) = $identifier;

        $sql = "DELETE FROM cart WHERE $id_field = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $id_value);
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

        // Determine identifier
        $identifier = $customer_id ? ['c_id', $customer_id] : ['ip_add', session_id()];
        list($id_field, $id_value) = $identifier;

        $sql = "SELECT * FROM cart WHERE p_id = ? AND $id_field = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "is", $product_id, $id_value);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $item = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);
        return $item ?: false;
    }
}
