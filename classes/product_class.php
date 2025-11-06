<?php

require_once '../settings/db_class.php';

class Product extends db_connection
{
    public function __construct()
    {
        $connected = parent::db_connect();
        if (!$connected) {
            throw new Exception('Database connection failed: ' . mysqli_connect_error());
        }
        // Ensure keyword tables exist
        $this->ensureKeywordTables();
    }

    private function ensureKeywordTables()
    {
        // keywords table
        $this->db->query("CREATE TABLE IF NOT EXISTS keywords (
            keyword_id INT AUTO_INCREMENT PRIMARY KEY,
            keyword VARCHAR(100) NOT NULL UNIQUE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // product_keywords mapping
        $this->db->query("CREATE TABLE IF NOT EXISTS product_keywords (
            product_id INT NOT NULL,
            keyword_id INT NOT NULL,
            PRIMARY KEY (product_id, keyword_id),
            KEY (keyword_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    // Normalize keywords string into array of lower-case trimmed unique keywords
    private function normalizeKeywords($raw)
    {
        $raw = trim((string)$raw);
        if ($raw === '') return [];
        // split on comma or whitespace
        $parts = preg_split('/[\,;\|]+|\s+/', $raw);
        $out = [];
        foreach ($parts as $p) {
            $k = mb_strtolower(trim($p));
            if ($k !== '') $out[$k] = true;
        }
        return array_keys($out);
    }

    // Add product. $imageFile is optional $_FILES['product_image'] entry
    public function add_product($data, $imageFile = null, $user_id = 0)
    {
        // required fields
        $product_cat = (int)($data['product_cat'] ?? 0);
        $product_brand = (int)($data['product_brand'] ?? 0);
        $title = trim($data['product_title'] ?? '');
        $price = (float)($data['product_price'] ?? 0);
        $desc = trim($data['product_desc'] ?? '');
        $keywords_raw = $data['product_keywords'] ?? '';

        if ($product_cat <= 0 || $product_brand <= 0 || $title === '') return false;

        $stmt = $this->db->prepare("INSERT INTO products (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords) VALUES (?, ?, ?, ?, ?, NULL, ?)");
        $keywords_for_field = implode(', ', $this->normalizeKeywords($keywords_raw));
        $stmt->bind_param('iisdss', $product_cat, $product_brand, $title, $price, $desc, $keywords_for_field);
        if (!$stmt->execute()) return false;

        $product_id = $this->db->insert_id;

        // handle image upload if provided
        $image_path = null;
        if ($imageFile && isset($imageFile['tmp_name']) && is_uploaded_file($imageFile['tmp_name'])) {
            $image_path = $this->saveImageFile($imageFile, $user_id, $product_id);
            if ($image_path) {
                $up = $this->db->prepare("UPDATE products SET product_image = ? WHERE product_id = ?");
                $up->bind_param('si', $image_path, $product_id);
                $up->execute();
            }
        }

        // store keywords normalized in mapping tables for fast search
        $this->storeKeywordsForProduct($product_id, $keywords_raw);

        return $product_id;
    }

    private function saveImageFile($file, $user_id, $product_id)
    {
        // build path: uploads/<user_id>/<product_id>/filename
        $uploadsBase = __DIR__ . '/../uploads';
        $userDir = rtrim($uploadsBase, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . intval($user_id);
        $prodDir = $userDir . DIRECTORY_SEPARATOR . intval($product_id);
        if (!is_dir($prodDir)) {
            @mkdir($prodDir, 0755, true);
        }
        $original = basename($file['name']);
        $ext = pathinfo($original, PATHINFO_EXTENSION);
        $safe = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($original, PATHINFO_FILENAME));
        $filename = $safe . '_' . time() . ($ext ? '.' . $ext : '');
        $dest = $prodDir . DIRECTORY_SEPARATOR . $filename;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            // return web-accessible path relative to project root
            $relative = 'uploads/' . intval($user_id) . '/' . intval($product_id) . '/' . $filename;
            return $relative;
        }
        return null;
    }

    private function storeKeywordsForProduct($product_id, $keywords_raw)
    {
        $keys = $this->normalizeKeywords($keywords_raw);
        if (empty($keys)) return;
        foreach ($keys as $kw) {
            // insert keyword if not exists
            $stmt = $this->db->prepare("INSERT INTO keywords (keyword) VALUES (?) ON DUPLICATE KEY UPDATE keyword=keyword");
            $stmt->bind_param('s', $kw);
            $stmt->execute();
            // get id
            $kIdRes = $this->db->query("SELECT keyword_id FROM keywords WHERE keyword = '" . $this->db->real_escape_string($kw) . "'");
            if ($kIdRes && $row = $kIdRes->fetch_assoc()) {
                $kid = (int)$row['keyword_id'];
                // insert mapping
                $map = $this->db->prepare("INSERT IGNORE INTO product_keywords (product_id, keyword_id) VALUES (?, ?)");
                $map->bind_param('ii', $product_id, $kid);
                $map->execute();
            }
        }
    }

    public function update_product($product_id, $data, $imageFile = null, $user_id = 0)
    {
        $product_id = (int)$product_id;
        if ($product_id <= 0) return false;

        $product_cat = (int)($data['product_cat'] ?? 0);
        $product_brand = (int)($data['product_brand'] ?? 0);
        $title = trim($data['product_title'] ?? '');
        $price = (float)($data['product_price'] ?? 0);
        $desc = trim($data['product_desc'] ?? '');
        $keywords_raw = $data['product_keywords'] ?? '';

        if ($product_cat <= 0 || $product_brand <= 0 || $title === '') return false;

        $stmt = $this->db->prepare("UPDATE products SET product_cat = ?, product_brand = ?, product_title = ?, product_price = ?, product_desc = ?, product_keywords = ? WHERE product_id = ?");
        $keywords_for_field = implode(', ', $this->normalizeKeywords($keywords_raw));
        $stmt->bind_param('iisdssi', $product_cat, $product_brand, $title, $price, $desc, $keywords_for_field, $product_id);
        if (!$stmt->execute()) return false;

        if ($imageFile && isset($imageFile['tmp_name']) && is_uploaded_file($imageFile['tmp_name'])) {
            $image_path = $this->saveImageFile($imageFile, $user_id, $product_id);
            if ($image_path) {
                $up = $this->db->prepare("UPDATE products SET product_image = ? WHERE product_id = ?");
                $up->bind_param('si', $image_path, $product_id);
                $up->execute();
            }
        }

        // refresh keyword mappings: delete existing, re-insert
        $this->db->query("DELETE FROM product_keywords WHERE product_id = " . intval($product_id));
        $this->storeKeywordsForProduct($product_id, $keywords_raw);

        return true;
    }

    public function delete_product($product_id)
    {
        $product_id = (int)$product_id;
        if ($product_id <= 0) return false;
        $stmt = $this->db->prepare("DELETE FROM products WHERE product_id = ?");
        $stmt->bind_param('i', $product_id);
        return $stmt->execute();
    }

    public function get_all_products()
    {
        $res = $this->db->query("SELECT p.*, c.cat_name, b.brand_name FROM products p LEFT JOIN categories c ON p.product_cat = c.cat_id LEFT JOIN brands b ON p.product_brand = b.brand_id ORDER BY p.product_id DESC");
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function get_product($product_id)
    {
        $product_id = (int)$product_id;
        if ($product_id <= 0) return false;
        $stmt = $this->db->prepare("SELECT * FROM products WHERE product_id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $res = $stmt->get_result();
        return $res ? $res->fetch_assoc() : false;
    }

    // Search products by keyword using normalized mapping (fast)
    public function search_by_keyword($keyword)
    {
        $k = mb_strtolower(trim($keyword));
        if ($k === '') return [];
        $k = $this->db->real_escape_string($k);
        $sql = "SELECT p.* FROM products p JOIN product_keywords pk ON p.product_id = pk.product_id JOIN keywords k ON pk.keyword_id = k.keyword_id WHERE k.keyword = '" . $k . "'";
        $res = $this->db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
}
