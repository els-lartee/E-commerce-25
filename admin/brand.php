<?php
session_start();
require_once '../settings/core.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: ../login/login.php');
    exit;
}

require_once(__DIR__ . "../controllers/brand_controller.php");
$categories = get_categories_ctr();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Brand Management</title>
    <script src="../js/brand.js"></script>
</head>
<body>
    <h2>Manage Brands</h2>

    <form id="brandForm">
        <input type="text" id="brand_name" placeholder="Enter brand name" required>
        <select id="cat_id" required>
            <option value="">Select Jewellery Category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['cat_id'] ?>"><?= $cat['cat_name'] ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Add Brand</button>
    </form>

    <h3>Existing Brands</h3>
    <table border="1" id="brandTable" cellpadding="8">
        <thead>
            <tr>
                <th>ID</th>
                <th>Brand Name</th>
                <th>Jewellery Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</body>
</html>
