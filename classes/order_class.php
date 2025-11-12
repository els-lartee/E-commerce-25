<?php
require_once __DIR__ . '/../settings/db_class.php';

class Order extends db_connection {
    public function __construct() {
        parent::db_connect();
    }

    /**
     * Create new order
     * Returns order_id on success
     */
    public function create_order($customer_id, $order_ref, $total_amount) {
        $conn = $this->db_conn();
        if (!$conn) {
            error_log("Order creation failed: No database connection");
            return false;
        }

        $order_date = date('Y-m-d');
        $order_status = 'pending'; // Will be updated to 'completed' after payment
        
        // Generate numeric invoice number (invoice_no is INT in database)
        $invoice_no = time(); // Use timestamp as invoice number

        $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status)
                VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        
        if (!$stmt) {
            error_log("Order creation failed: SQL prepare error - " . mysqli_error($conn));
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "iiss", $customer_id, $invoice_no, $order_date, $order_status);
        $success = mysqli_stmt_execute($stmt);

        if ($success) {
            $order_id = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            error_log("Order created successfully: order_id=$order_id, invoice_no=$invoice_no, customer_id=$customer_id");
            return $order_id;
        }

        $error = mysqli_stmt_error($stmt);
        error_log("Order creation failed: Execute error - $error");
        mysqli_stmt_close($stmt);
        return false;
    }

    /**
     * Add order details
     */
    public function add_order_details($order_id, $product_id, $quantity, $price) {
        $conn = $this->db_conn();
        if (!$conn) return false;

        $sql = "INSERT INTO orderdetails (order_id, product_id, qty) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $order_id, $product_id, $quantity);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $success;
    }

    /**
     * Record payment
     */
    public function record_payment($order_id, $amount, $payment_method, $status) {
        $conn = $this->db_conn();
        if (!$conn) return false;

        // Get customer_id from order
        $order_sql = "SELECT customer_id FROM orders WHERE order_id = ?";
        $order_stmt = mysqli_prepare($conn, $order_sql);
        mysqli_stmt_bind_param($order_stmt, "i", $order_id);
        mysqli_stmt_execute($order_stmt);
        $order_result = mysqli_stmt_get_result($order_stmt);
        $order = mysqli_fetch_assoc($order_result);
        mysqli_stmt_close($order_stmt);

        if (!$order) return false;

        $customer_id = $order['customer_id'];
        $currency = 'USD';
        $payment_date = date('Y-m-d');

        $sql = "INSERT INTO payment (amt, customer_id, order_id, currency, payment_date)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "diiss", $amount, $customer_id, $order_id, $currency, $payment_date);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Update order status to completed if payment successful
        if ($success && $status === 'completed') {
            $update_sql = "UPDATE orders SET order_status = 'completed' WHERE order_id = ?";
            $update_stmt = mysqli_prepare($conn, $update_sql);
            mysqli_stmt_bind_param($update_stmt, "i", $order_id);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
        }

        return $success;
    }

    /**
     * Get user orders
     */
    public function get_user_orders($customer_id) {
        $conn = $this->db_conn();
        if (!$conn) return [];

        $sql = "SELECT o.*, p.amt as payment_amount, p.currency, p.payment_date
                FROM orders o
                LEFT JOIN payment p ON o.order_id = p.order_id
                WHERE o.customer_id = ?
                ORDER BY o.order_date DESC";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $customer_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $orders = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        return $orders;
    }

    /**
     * Get order details with products
     */
    public function get_order_details($order_id) {
        $conn = $this->db_conn();
        if (!$conn) return [];

        $sql = "SELECT od.*, p.product_title, p.product_price, p.product_image
                FROM orderdetails od
                LEFT JOIN product p ON od.product_id = p.product_id
                WHERE od.order_id = ?";

        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $order_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $details = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_stmt_close($stmt);
        return $details;
    }
}
