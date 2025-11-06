<?php
require_once(__DIR__ . "/db_connection.php");

class brand_class extends db_connection
{
    /**
     * Add a new brand
     */
    public function add_brand($brand_name, $cat_id, $user_id)
    {
        $brand_name = mysqli_real_escape_string($this->db_conn(), $brand_name);
        $sql = "INSERT INTO brands (brand_name, cat_id, user_id) 
                VALUES ('$brand_name', '$cat_id', '$user_id')";
        return $this->db_write_query($sql);
    }

    /**
     * Get all brands created by a specific user
     */
    public function get_brands_by_user($user_id)
    {
        $sql = "SELECT b.brand_id, b.brand_name, j.cat_name 
                FROM brands b 
                JOIN jewellery j ON b.cat_id = j.cat_id 
                WHERE b.user_id = '$user_id'
                ORDER BY j.cat_name, b.brand_name ASC";
        return $this->db_fetch_all($sql);
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
        $sql = "SELECT * FROM jewellery ORDER BY cat_name ASC";
        return $this->db_fetch_all($sql);
    }
}
?>
