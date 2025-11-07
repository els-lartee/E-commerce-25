<?php
session_start();
require_once '../settings/core.php';

// Check if user is logged in and is admin
if (!is_logged_in() || !is_admin()) {
    header("Location: ../login/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jewellery Management</title>
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
        h1 {
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
        input[type="email"],
        input[type="password"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
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
        }
        .modal.show {
            display: flex;
        }
        .modal-dialog {
            background: white;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
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
        <h1>Jewellery Management</h1>

        <!-- Add Jewellery Form -->
        <div class="card">
            <div class="card-header">
                <h5>Add New Jewellery</h5>
            </div>
            <div class="card-body">
                <form id="addJewelleryForm">
                    <div class="form-group">
                        <label for="jewelleryName">Jewellery Name</label>
                        <input type="text" id="jewelleryName" name="name" required maxlength="100">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Jewellery</button>
                </form>
            </div>
        </div>

        <!-- Jewellery Table -->
        <div class="card">
            <div class="card-header">
                <h5>Existing Jewellery</h5>
            </div>
            <div class="card-body">
                <table id="jewelleryTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Jewellery will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Jewellery Modal -->
    <div class="modal" id="editJewelleryModal">
        <div class="modal-dialog">
            <div class="modal-header">
                <h5 class="modal-title">Edit Jewellery</h5>
                <button type="button" class="btn-close" onclick="closeModal('editJewelleryModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editJewelleryForm">
                    <input type="hidden" id="editJewelleryId" name="id">
                    <div class="form-group">
                        <label for="editJewelleryName">Jewellery Name</label>
                        <input type="text" id="editJewelleryName" name="name" required maxlength="100">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Jewellery</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }
        function showModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }
        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                }
            });
        });
    </script>
    <script src="../js/jewellery.js"></script>
</body>
</html>
