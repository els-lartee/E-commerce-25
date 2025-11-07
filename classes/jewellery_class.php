<?php

require_once dirname(__DIR__) . '/settings/db_class.php';

/**
 * Jewellery class for managing jewellery operations
 */
class Jewellery extends db_connection
{
    private $id;
    private $name;
    private $user_id;

    public function __construct($id = null)
    {
        if (!parent::db_connect()) {
            throw new Exception("Database connection failed");
        }
        if ($id) {
            $this->id = $id;
            $this->loadJewellery();
        }
    }

    private function loadJewellery()
    {
        if (!$this->id) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM jewellery WHERE id = ?");
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->name = $result['name'];
            $this->user_id = $result['user_id'];
        }
        return $result ? true : false;
    }

    public function addJewellery($name, $user_id)
    {
        // Check if name already exists for this user
        $check_stmt = $this->db->prepare("SELECT id FROM jewellery WHERE name = ? AND user_id = ?");
        $check_stmt->bind_param("si", $name, $user_id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            return false; // Name exists for this user
        }

        $stmt = $this->db->prepare("INSERT INTO jewellery (name, user_id) VALUES (?, ?)");
        $stmt->bind_param("si", $name, $user_id);
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function getJewellery($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM jewellery WHERE user_id = ? ORDER BY name ASC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getJewelleryById($id, $user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM jewellery WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateJewellery($id, $name, $user_id)
    {
        // Check if jewellery belongs to user
        $jew = $this->getJewelleryById($id, $user_id);
        if (!$jew || $jew['user_id'] != $user_id) {
            return false;
        }

        // Check if new name already exists for this user (excluding current ID)
        $check_stmt = $this->db->prepare("SELECT id FROM jewellery WHERE name = ? AND user_id = ? AND id != ?");
        $check_stmt->bind_param("sii", $name, $user_id, $id);
        $check_stmt->execute();
        if ($check_stmt->get_result()->num_rows > 0) {
            return false; // Name exists for another jewellery of this user
        }

        $stmt = $this->db->prepare("UPDATE jewellery SET name = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $name, $id, $user_id);
        return $stmt->execute();
    }

    public function deleteJewellery($id, $user_id)
    {
        // Check if jewellery belongs to user
        $jew = $this->getJewelleryById($id, $user_id);
        if (!$jew || $jew['user_id'] != $user_id) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM jewellery WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $id, $user_id);
        return $stmt->execute();
    }

    public function getAllJewelleryPublic()
    {
        $stmt = $this->db->prepare("SELECT id, name FROM jewellery ORDER BY name ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
