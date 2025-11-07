<?php
session_start();
require_once '../settings/core.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: ../login/login.php');
    exit;
}

// Note: categories and brands are populated client-side via their action scripts

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Products - Admin</title>
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
            padding: 20px;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .full-width {
            grid-column: 1 / -1;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="number"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        textarea {
            min-height: 80px;
            resize: vertical;
        }
        input:focus,
        select:focus,
        textarea:focus {
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
        .w-100 {
            width: 100%;
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
        .product-image-small {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }
        .modal.show {
            display: flex;
        }
        .modal-dialog {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 800px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            margin: 20px;
        }
        .modal-header {
            padding: 15px 20px;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .modal-title {
            margin: 0;
            font-size: 18px;
        }
        .btn-close {
            background: transparent;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }
        .btn-close:hover {
            color: #000;
        }
        .modal-body {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Products</h2>

        <div class="card">
            <form id="addProductForm" enctype="multipart/form-data">
                <div class="form-row">
                    <select id="product_cat" name="product_cat" required>
                        <option value="">Select category</option>
                    </select>
                    <select id="product_brand" name="product_brand" required>
                        <option value="">Select brand</option>
                    </select>
                    <input type="text" id="product_title" name="product_title" placeholder="Title" required>
                </div>
                <div class="form-row">
                    <input type="number" step="0.01" id="product_price" name="product_price" placeholder="Price" required>
                    <input type="file" id="product_image" name="product_image">
                </div>
                <div class="form-group">
                    <textarea id="product_desc" name="product_desc" placeholder="Description"></textarea>
                </div>
                <div class="form-row">
                    <input type="text" id="product_keywords" name="product_keywords" placeholder="Keywords (comma or space separated)">
                    <button class="btn btn-primary" type="submit">Add Product</button>
                </div>
            </form>
        </div>

        <div class="card">
            <div id="productsContainer"></div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal" id="editProductModal">
      <div class="modal-dialog">
        <div class="modal-header">
            <h5 class="modal-title">Edit Product</h5>
            <button type="button" class="btn-close" onclick="closeModal('editProductModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editProductForm" enctype="multipart/form-data">
                <input type="hidden" id="edit_product_id" name="product_id">
                <div class="form-row">
                    <select id="edit_product_cat" name="product_cat" required>
                        <option value="">Select category</option>
                    </select>
                    <select id="edit_product_brand" name="product_brand" required>
                        <option value="">Select brand</option>
                    </select>
                    <input type="text" id="edit_product_title" name="product_title" required>
                </div>
                <div class="form-row">
                    <input type="number" step="0.01" id="edit_product_price" name="product_price" required>
                    <input type="file" id="edit_product_image" name="product_image">
                </div>
                <div class="form-group">
                    <textarea id="edit_product_desc" name="product_desc"></textarea>
                </div>
                <div class="form-row">
                    <input type="text" id="edit_product_keywords" name="product_keywords">
                    <button class="btn btn-primary" type="submit">Save</button>
                </div>
            </form>
        </div>
      </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }
        function showModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });
        });
    </script>
    <script src="../js/products.js"></script>
</body>
</html>
