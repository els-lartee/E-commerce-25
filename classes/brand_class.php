<?php

require_once dirname(__DIR__) . '/settings/db_class.php';

/**
 * Brand class for managing brand operations
 */
class Brand extends db_connection
{
    private $id;
    private $name;
    private $cat_id;
    private $user_id;

    public function __construct($id = null)
    {
        if (!parent::db_connect()) {
            throw new Exception("Database connection failed");
        }
        if ($id) {
            $this->id = $id;
            $this->loadBrand();
        }
    }

    private function loadBrand()
    {
        if (!$this->id) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM brands WHERE brand_id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->name = $result['brand_name'];
            $this->cat_id = $result['cat_id'];
            $this->user_id = $result['user_id'];
        }
        return $result ? true : false;
    }

    public function addBrand($name, $cat_id, $user_id)
    {
        // Check if name already exists for this user and category
        $check_stmt = $this->db->prepare("SELECT brand_id FROM brands WHERE brand_name = ? AND cat_id = ? AND user_id = ?");
        $check_stmt->bind_param("sii", $name, $cat_id, $user_id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            return false; // Name exists for this user and category
        }

        $stmt = $this->db->prepare("INSERT INTO brands (brand_name, cat_id, user_id) VALUES (?, ?, ?)");
        $stmt->bind_param("sii", $name, $cat_id, $user_id);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function getBrands($user_id)
    {
        $stmt = $this->db->prepare("SELECT b.brand_id, b.brand_name, b.cat_id, c.cat_name FROM brands b JOIN categories c ON b.cat_id = c.cat_id WHERE b.user_id = ? ORDER BY c.cat_name ASC, b.brand_name ASC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getBrandById($id, $user_id)
    {
        $stmt = $this->db->prepare("SELECT b.brand_id, b.brand_name, b.cat_id, c.cat_name FROM brands b JOIN categories c ON b.cat_id = c.cat_id WHERE b.brand_id = ? AND b.user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateBrand($id, $name, $user_id)
    {
        // Check if brand belongs to user
        $brand = $this->getBrandById($id, $user_id);
        if (!$brand || $brand['user_id'] != $user_id) {
            return false;
        }

        // Check if new name already exists for this user and category (excluding current ID)
        $check_stmt = $this->db->prepare("SELECT brand_id FROM brands WHERE brand_name = ? AND cat_id = ? AND user_id = ? AND brand_id != ?");
        $check_stmt->bind_param("siii", $name, $brand['cat_id'], $user_id, $id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            return false; // Name exists for another brand of this user in the same category
        }

        $stmt = $this->db->prepare("UPDATE brands SET brand_name = ? WHERE brand_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $name, $id, $user_id);
        return $stmt->execute();
    }

    public function deleteBrand($id, $user_id)
    {
        // Check if brand belongs to user
        $brand = $this->getBrandById($id, $user_id);
        if (!$brand || $brand['user_id'] != $user_id) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM brands WHERE brand_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        return $stmt->execute();
    }

    public function getCategories()
    {
        $stmt = $this->db->prepare("SELECT cat_id, cat_name FROM categories ORDER BY cat_name ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
