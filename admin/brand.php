<?php
session_start();
require_once '../settings/core.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: ../login/login.php');
    exit;
}

require_once(__DIR__ . "/../controllers/brand_controller.php");
require_once(__DIR__ . "/../controllers/jewellery_controller.php");
$categories = get_jewellery_ctr($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Management</title>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        h2 {
            margin-bottom: 30px;
            color: #333;
        }
        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }
        .card-header {
            background: #007bff;
            color: white;
            padding: 15px 20px;
        }
        .card-header h5 {
            margin: 0;
            font-size: 18px;
        }
        .card-body {
            padding: 20px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 2fr 2fr 1fr;
            gap: 15px;
            align-items: end;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        input:focus,
        select:focus {
            outline: none;
            border-color: #007bff;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        .btn-warning:hover {
            background: #e0a800;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        tbody tr:hover {
            background: #f8f9fa;
        }
        [contenteditable="true"] {
            cursor: text;
            padding: 5px;
            border-radius: 3px;
        }
        [contenteditable="true"]:hover {
            background: #f8f9fa;
        }
        [contenteditable="true"]:focus {
            background: #e7f3ff;
            outline: 1px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Brand Management</h2>

        <!-- Add Brand Form -->
        <div class="card">
            <div class="card-header">
                <h5>Add New Brand</h5>
            </div>
            <div class="card-body">
                <form id="brandForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="brand_name">Brand Name</label>
                            <input type="text" id="brand_name" placeholder="Enter brand name" required>
                        </div>
                        <div class="form-group">
                            <label for="cat_id">Jewellery Category</label>
                            <select id="cat_id" required>
                                <option value="">Select Jewellery Category</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Brand</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Brands Table -->
        <div class="card">
            <div class="card-header">
                <h5>Existing Brands</h5>
            </div>
            <div class="card-body">
                <table id="brandTable">
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
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="../js/brand.js"></script>
</body>
</html>
