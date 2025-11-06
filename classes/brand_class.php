<?php
// require_once(__DIR__ . "../db_connection.php");
require_once '../settings/db_class.php';

class brand_class extends db_connection
{
    /**
     * Add a new brand
     */
    public function add_brand($brand_name, $cat_id, $user_id)
    {
        $conn = $this->db_conn();
        if (!$conn) {
            error_log("Database connection failed in add_brand");
            return false;
        }
        
        $brand_name = mysqli_real_escape_string($conn, $brand_name);
        $cat_id = (int)$cat_id;
        $user_id = (int)$user_id;
        
        $sql = "INSERT INTO brands (brand_name, cat_id, user_id) 
                VALUES (?, ?, ?)";
                
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            error_log("Prepare failed: " . mysqli_error($conn));
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "sii", $brand_name, $cat_id, $user_id);
        $result = mysqli_stmt_execute($stmt);
        
        if (!$result) {
            error_log("Execute failed: " . mysqli_stmt_error($stmt));
        }
        
        mysqli_stmt_close($stmt);
        return $result;
    }

    /**
     * Get all brands created by a specific user
     */
    public function get_brands_by_user($user_id)
    {
        $sql = "SELECT b.brand_id, b.brand_name, j.name as cat_name 
                FROM brands b 
                JOIN jewellery j ON b.cat_id = j.id 
                WHERE b.user_id = ?";
        
        $conn = $this->db_conn();
        if (!$conn) {
            error_log("Database connection failed in get_brands_by_user");
            return false;
        }
        
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            error_log("Prepare failed: " . mysqli_error($conn));
            return false;
        }
        
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        
        if (!mysqli_stmt_execute($stmt)) {
            error_log("Execute failed: " . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return false;
        }
        
        $result = mysqli_stmt_get_result($stmt);
        $brands = mysqli_fetch_all($result, MYSQLI_ASSOC);
        
        mysqli_stmt_close($stmt);
        return $brands;
    }

    /**
     * Update brand name
     */
    public function update_brand($brand_id, $brand_name)
    {
        $brand_name = mysqli_real_escape_string($this->db_conn(), $brand_name);
        $sql = "UPDATE brands 
                SET brand_name = '$brand_name' 
                WHERE brand_id = '$brand_id'";
        return $this->db_write_query($sql);
    }

    /**
     * Delete a brand
     */
    public function delete_brand($brand_id)
    {
        $sql = "DELETE FROM brands WHERE brand_id = '$brand_id'";
        return $this->db_write_query($sql);
    }

    /**
     * Fetch all jewellery categories (for dropdown)
     */
    public function get_categories()
    {
        $sql = "SELECT id, name FROM jewellery ORDER BY name ASC";
        return $this->db_fetch_all($sql);
    }
}
?>
