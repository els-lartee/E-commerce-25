<?php

require_once dirname(__DIR__) . '/settings/db_class.php';

/**
 * Category class for managing category operations
 */
class Category extends db_connection
{
    private $cat_id;
    private $cat_name;

    public function __construct($cat_id = null)
    {
        if (!parent::db_connect()) {
            throw new Exception("Database connection failed");
        }
        if ($cat_id) {
            $this->cat_id = $cat_id;
            $this->loadCategory();
        }
    }

    private function loadCategory()
    {
        if (!$this->cat_id || !$this->user_id) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $this->cat_id, $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->cat_name = $result['cat_name'];
        }
        return $result ? true : false;
    }

    public function addCategory($name, $user_id)
    {
        // Check if name already exists for this user
        $check_stmt = $this->db->prepare("SELECT cat_id FROM categories WHERE cat_name = ? AND user_id = ?");
        $check_stmt->bind_param("si", $name, $user_id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            return false; // Name exists for this user
        }

        $stmt = $this->db->prepare("INSERT INTO categories (cat_name, user_id) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $user_id);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function getCategories($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE user_id = ? ORDER BY cat_name ASC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCategoryById($id, $user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateCategory($id, $name, $user_id)
    {
        // Check if category belongs to user
        $cat = $this->getCategoryById($id, $user_id);
        if (!$cat || $cat['user_id'] != $user_id) {
            return false;
        }

        // Check if new name already exists for this user (excluding current ID)
        $check_stmt = $this->db->prepare("SELECT cat_id FROM categories WHERE cat_name = ? AND user_id = ? AND cat_id != ?");
        $check_stmt->bind_param("sii", $name, $user_id, $id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            return false; // Name exists for another category of this user
        }

        $stmt = $this->db->prepare("UPDATE categories SET cat_name = ? WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("sii", $name, $id, $user_id);
        return $stmt->execute();
    }

    public function deleteCategory($id, $user_id)
    {
        // Check if category belongs to user
        $cat = $this->getCategoryById($id, $user_id);
        if (!$cat || $cat['user_id'] != $user_id) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM categories WHERE cat_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        return $stmt->execute();
    }
}
?>
