<?php
session_start();
require_once '../settings/core.php';

if (!is_logged_in() || !is_admin()) {
    header('Location: ../login/login.php');
    exit;
}

// We'll load categories from DB for selection
// simple flat brands (brand_id, brand_name) - no category selection required

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2>Brand Management</h2>

        <div class="card mb-3">
            <div class="card-body">
                <form id="addBrandForm" class="row g-2">
                    <div class="col-md-8">
                        <input type="text" id="brand_name" name="brand_name" class="form-control" placeholder="Brand name" maxlength="100" required>
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary w-100" type="submit">Add Brand</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="brandsContainer">
                    <!-- Brands list will be loaded here via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editBrandModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Brand</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="editBrandForm">
                <input type="hidden" id="edit_brand_id" name="brand_id">
                <div class="mb-3">
                    <label for="edit_brand_name" class="form-label">Brand Name</label>
                    <input type="text" id="edit_brand_name" name="brand_name" class="form-control" required maxlength="100">
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="../js/brand.js"></script>
</body>
</html>
