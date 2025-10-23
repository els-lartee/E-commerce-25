<?php
session_start();
require_once '../settings/core.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: ../login/login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Brand Management</h1>

        <!-- Add Brand Form -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Add New Brand</h5>
            </div>
            <div class="card-body">
                <form id="addBrandForm">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="brandName" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="brandName" name="name" required maxlength="100">
                        </div>
                        <div class="col-md-6">
                            <label for="brandCategory" class="form-label">Category</label>
                            <select class="form-select" id="brandCategory" name="cat_id" required>
                                <option value="">Select Category</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Add Brand</button>
                </form>
            </div>
        </div>

        <!-- Brands Table -->
        <div class="card">
            <div class="card-header">
                <h5>Brands</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="brandsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Brands will be loaded here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Brand Modal -->
    <div class="modal fade" id="editBrandModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Brand</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editBrandForm">
                        <input type="hidden" id="editBrandId" name="id">
                        <div class="mb-3">
                            <label for="editBrandName" class="form-label">Brand Name</label>
                            <input type="text" class="form-control" id="editBrandName" name="name" required maxlength="100">
                        </div>
                        <div class="mb-3">
                            <label for="editBrandCategory" class="form-label">Category</label>
                            <input type="text" class="form-control" id="editBrandCategory" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Brand</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/brand.js"></script>
</body>
</html>
