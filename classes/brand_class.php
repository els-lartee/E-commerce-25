<?php

require_once '../settings/db_class.php';

class Brand extends db_connection
{
    // Add a new brand - ensures (brand_name, cat_id, user_id) unique
    public function add_brand($brand_name, $cat_id, $user_id)
    {
        $brand_name = trim($brand_name);
        $cat_id = (int)$cat_id;
        $user_id = (int)$user_id;

        if (empty($brand_name) || $cat_id <= 0 || $user_id <= 0) {
            return false;
        }

        // Check uniqueness
        $stmt = $this->db->prepare("SELECT brand_id FROM brands WHERE brand_name = ? AND cat_id = ? AND user_id = ?");
        $stmt->bind_param('sii', $brand_name, $cat_id, $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            return false; // already exists
        }

        $stmt = $this->db->prepare("INSERT INTO brands (brand_name, cat_id, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param('sii', $brand_name, $cat_id, $user_id);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    // Update brand name
    public function update_brand($brand_id, $brand_name)
    {
        $brand_id = (int)$brand_id;
        $brand_name = trim($brand_name);

        if ($brand_id <= 0 || empty($brand_name)) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE brands SET brand_name = ? WHERE brand_id = ?");
        $stmt->bind_param('si', $brand_name, $brand_id);
        return $stmt->execute();
    }

    // Delete brand
    public function delete_brand($brand_id)
    {
        $brand_id = (int)$brand_id;
        if ($brand_id <= 0) {
            return false;
        }
        $stmt = $this->db->prepare("DELETE FROM brands WHERE brand_id = ?");
        $stmt->bind_param('i', $brand_id);
        return $stmt->execute();
    }

    // Get brands for a user (optional grouping by category left to controller/view)
    public function get_brands_by_user($user_id)
    {
        $user_id = (int)$user_id;
        if ($user_id <= 0) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT b.brand_id, b.brand_name, b.cat_id, c.cat_name FROM brands b LEFT JOIN categories c ON b.cat_id = c.cat_id WHERE b.user_id = ? ORDER BY c.cat_name, b.brand_name");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    // Get brands for a particular category and user
    public function get_brands_by_category($cat_id, $user_id)
    {
        $cat_id = (int)$cat_id;
        $user_id = (int)$user_id;
        if ($cat_id <= 0 || $user_id <= 0) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT brand_id, brand_name FROM brands WHERE cat_id = ? AND user_id = ? ORDER BY brand_name");
        $stmt->bind_param('ii', $cat_id, $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
}

