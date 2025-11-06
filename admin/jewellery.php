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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Jewellery Management</h1>

        <!-- Add Jewellery Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Add New Jewellery</h5>
            </div>
            <div class="card-body">
                <form id="addJewelleryForm">
                    <div class="mb-3">
                        <label for="jewelleryName" class="form-label">Jewellery Name</label>
                        <input type="text" class="form-control" id="jewelleryName" name="name" required maxlength="100">
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
                <table class="table table-striped" id="jewelleryTable">
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
    <div class="modal fade" id="editJewelleryModal" tabindex="-1" aria-labelledby="editJewelleryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editJewelleryModalLabel">Edit Jewellery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editJewelleryForm">
                        <input type="hidden" id="editJewelleryId" name="id">
                        <div class="mb-3">
                            <label for="editJewelleryName" class="form-label">Jewellery Name</label>
                            <input type="text" class="form-control" id="editJewelleryName" name="name" required maxlength="100">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Jewellery</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="../js/jewellery.js"></script>
</body>
</html>
