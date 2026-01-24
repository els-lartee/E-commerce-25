<?php
session_start();

require_once '../settings/db_class.php';

header('Content-Type: application/json');

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
$limit = min(max($limit, 1), 20); // Ensure between 1 and 20

// Get session ID
$session_id = $_SESSION['session_id'] ?? '';

// If logged in, also use user_id for more personalized recommendations
$user_id = $_SESSION['user_id'] ?? 0;

$conn = new db_connection();
$db = $conn->db_conn();

if (!$db) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit;
}

$recommendations = [];

// Strategy: Get products based on user's interaction history
// 1. Find categories and brands from viewed products
// 2. Find products from those categories/brands that user hasn't viewed
// 3. Sort by popularity and recency

// Get products the user has already interacted with (to exclude)
$viewedProducts = [];
$sql = "SELECT DISTINCT product_id FROM user_interactions WHERE session_id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $viewedProducts[] = $row['product_id'];
}
$stmt->close();

// If logged in, also get products viewed by this user
if ($user_id > 0) {
    $sql = "SELECT DISTINCT product_id FROM user_interactions WHERE user_id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $viewedProducts[] = $row['product_id'];
    }
    $stmt->close();
}

$viewedProducts = array_unique($viewedProducts);
$excludeClause = !empty($viewedProducts) ? "AND p.product_id NOT IN (" . implode(',', $viewedProducts) . ")" : "";

// Get categories and brands from user's most viewed/interacted products
$topCategories = [];
$topBrands = [];

$sql = "SELECT p.product_cat, p.product_brand, COUNT(ui.id) as interaction_count 
        FROM user_interactions ui 
        JOIN product p ON ui.product_id = p.product_id 
        WHERE ui.session_id = ? 
        GROUP BY p.product_cat, p.product_brand 
        ORDER BY interaction_count DESC 
        LIMIT 5";

$stmt = $db->prepare($sql);
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    if ($row['product_cat']) {
        $topCategories[$row['product_cat']] = $row['interaction_count'];
    }
    if ($row['product_brand']) {
        $topBrands[$row['product_brand']] = $row['interaction_count'];
    }
}
$stmt->close();

// If user is logged in, also consider their interactions
if ($user_id > 0) {
    $sql = "SELECT p.product_cat, p.product_brand, COUNT(ui.id) as interaction_count 
            FROM user_interactions ui 
            JOIN product p ON ui.product_id = p.product_id 
            WHERE ui.user_id = ? 
            GROUP BY p.product_cat, p.product_brand 
            ORDER BY interaction_count DESC 
            LIMIT 5";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        if ($row['product_cat']) {
            $topCategories[$row['product_cat']] = ($topCategories[$row['product_cat']] ?? 0) + $row['interaction_count'];
        }
        if ($row['product_brand']) {
            $topBrands[$row['product_brand']] = ($topBrands[$row['product_brand']] ?? 0) + $row['interaction_count'];
        }
    }
    $stmt->close();
}

// If we have interaction history, recommend from top categories/brands
if (!empty($topCategories) || !empty($topBrands)) {
    // Build weighted SQL based on user preferences
    $weights = [];
    $params = [];
    $types = '';
    
    foreach ($topCategories as $cat_id => $weight) {
        $weights[] = "WHEN p.product_cat = ? THEN ?";
        $params[] = $cat_id;
        $params[] = $weight;
        $types .= 'ii';
    }
    
    foreach ($topBrands as $brand_id => $weight) {
        $weights[] = "WHEN p.product_brand = ? THEN ?";
        $params[] = $brand_id;
        $params[] = $weight;
        $types .= 'ii';
    }
    
    if (!empty($weights)) {
        // Priority 1: Products from user's preferred categories/brands
        $sql = "SELECT p.*, j.name as cat_name, b.brand_name,
                (CASE " . implode(' ', $weights) . " ELSE 0 END) as preference_score
                FROM product p 
                LEFT JOIN jewellery j ON p.product_cat = j.id 
                LEFT JOIN brands b ON p.product_brand = b.brand_id 
                WHERE p.product_id > 0 $excludeClause
                ORDER BY preference_score DESC, p.product_id DESC 
                LIMIT ?";
        
        $params[] = $limit;
        $types .= 'i';
        
        $stmt = $db->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $recommendations[] = $row;
        }
        $stmt->close();
    }
}

// If we don't have enough recommendations, fill with popular products
if (count($recommendations) < $limit) {
    $needed = $limit - count($recommendations);
    $existingIds = array_column($recommendations, 'product_id');
    
    if (!empty($existingIds)) {
        $excludeClause = "AND p.product_id NOT IN (" . implode(',', $existingIds) . ")";
    }
    
    // Get most viewed products (popular items)
    $sql = "SELECT p.*, j.name as cat_name, b.brand_name, 
            COUNT(ui.id) as view_count
            FROM product p 
            LEFT JOIN jewellery j ON p.product_cat = j.id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            LEFT JOIN user_interactions ui ON p.product_id = ui.product_id 
            WHERE p.product_id > 0 $excludeClause
            GROUP BY p.product_id 
            ORDER BY view_count DESC, p.product_id DESC 
            LIMIT ?";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $needed);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recommendations[] = $row;
    }
    $stmt->close();
}

// If still empty (new user with no interactions), show newest products
if (empty($recommendations)) {
    $sql = "SELECT p.*, j.name as cat_name, b.brand_name 
            FROM product p 
            LEFT JOIN jewellery j ON p.product_cat = j.id 
            LEFT JOIN brands b ON p.product_brand = b.brand_id 
            ORDER BY p.product_id DESC 
            LIMIT ?";
    
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $recommendations[] = $row;
    }
    $stmt->close();
}

// Format image paths
foreach ($recommendations as &$product) {
    if (!empty($product['product_image']) && strpos($product['product_image'], 'uploads/') !== 0) {
        $product['product_image'] = 'uploads/products/' . $product['product_image'];
    }
}

echo json_encode([
    'status' => 'success',
    'recommendations' => array_slice($recommendations, 0, $limit),
    'count' => count($recommendations)
]);

