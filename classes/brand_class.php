<?php

require_once '../settings/db_class.php';

class Brand extends db_connection
{
    public function __construct()
    {
        $connected = parent::db_connect();
        if (!$connected) {
            throw new Exception('Database connection failed: ' . mysqli_connect_error());
        }
    }

    /**
     * Helper: check if a column exists in brands table and whether it's nullable
     * @param string $col
     * @return array|null  ['exists' => bool, 'nullable' => bool]
     */
    private function columnInfo($col)
    {
        $col = $this->db->real_escape_string($col);
        $sql = "SELECT IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'brands' AND COLUMN_NAME = '{$col}'";
        $res = $this->db->query($sql);
        if ($res && $row = $res->fetch_assoc()) {
            return ['exists' => true, 'nullable' => strtoupper($row['IS_NULLABLE']) === 'YES'];
        }
        return ['exists' => false, 'nullable' => true];
    }
    // Add a new brand using simple schema (brand_name)
    public function add_brand($brand_name)
    {
        $brand_name = trim($brand_name);
        if (empty($brand_name)) {
            return false;
        }

        // Check uniqueness
        $stmt = $this->db->prepare("SELECT brand_id FROM brands WHERE brand_name = ?");
        $stmt->bind_param('s', $brand_name);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            return false; // already exists
        }

        // Detect additional columns (cat_id, user_id) and whether they accept NULL
        $catInfo = $this->columnInfo('cat_id');
        $userInfo = $this->columnInfo('user_id');

        if ($catInfo['exists'] || $userInfo['exists']) {
            // Build insert dynamically to include present columns
            $cols = ['brand_name'];
            $placeholders = ['?'];
            $types = 's';
            $values = [$brand_name];

            if ($catInfo['exists']) {
                $cols[] = 'cat_id';
                $placeholders[] = '?';
                $types .= 'i';
                // use NULL if allowed, otherwise 0
                $values[] = $catInfo['nullable'] ? null : 0;
            }
            if ($userInfo['exists']) {
                $cols[] = 'user_id';
                $placeholders[] = '?';
                $types .= 'i';
                $values[] = $userInfo['nullable'] ? null : 0;
            }

            $sql = "INSERT INTO brands (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $placeholders) . ")";
            $stmt = $this->db->prepare($sql);
            // bind params dynamically
            $bind_names[] = $types;
            for ($i = 0; $i < count($values); $i++) {
                $bind_name = 'bind' . $i;
                $$bind_name = $values[$i];
                $bind_names[] = &$$bind_name;
            }
            call_user_func_array([$stmt, 'bind_param'], $bind_names);
            if ($stmt->execute()) {
                return $this->db->insert_id;
            }
            return false;
        }

        // Simple insert (flat schema)
        $stmt = $this->db->prepare("INSERT INTO brands (brand_name) VALUES (?)");
        $stmt->bind_param('s', $brand_name);
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

    // Get all brands
    public function get_all_brands()
    {
        $res = $this->db->query("SELECT brand_id, brand_name FROM brands ORDER BY brand_name");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
}

