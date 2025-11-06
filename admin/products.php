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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2>Products</h2>

        <div class="card mb-3">
            <div class="card-body">
                <form id="addProductForm" enctype="multipart/form-data">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <select id="product_cat" name="product_cat" class="form-control" required>
                                <option value="">Select category</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="product_brand" name="product_brand" class="form-control" required>
                                <option value="">Select brand</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="product_title" name="product_title" class="form-control" placeholder="Title" required>
                        </div>
                        <div class="col-md-3 mt-2">
                            <input type="number" step="0.01" id="product_price" name="product_price" class="form-control" placeholder="Price" required>
                        </div>
                        <div class="col-md-5 mt-2">
                            <input type="file" id="product_image" name="product_image" class="form-control">
                        </div>
                        <div class="col-md-12 mt-2">
                            <textarea id="product_desc" name="product_desc" class="form-control" placeholder="Description"></textarea>
                        </div>
                        <div class="col-md-8 mt-2">
                            <input type="text" id="product_keywords" name="product_keywords" class="form-control" placeholder="Keywords (comma or space separated)">
                        </div>
                        <div class="col-md-4 mt-2">
                            <button class="btn btn-primary w-100" type="submit">Add Product</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="productsContainer"></div>
            </div>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Edit Product</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="editProductForm" enctype="multipart/form-data">
                <input type="hidden" id="edit_product_id" name="product_id">
                <div class="row g-2">
                    <div class="col-md-4">
                        <select id="edit_product_cat" name="product_cat" class="form-control" required>
                            <option value="">Select category</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="edit_product_brand" name="product_brand" class="form-control" required>
                            <option value="">Select brand</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="edit_product_title" name="product_title" class="form-control" required>
                    </div>
                    <div class="col-md-3 mt-2">
                        <input type="number" step="0.01" id="edit_product_price" name="product_price" class="form-control" required>
                    </div>
                    <div class="col-md-5 mt-2">
                        <input type="file" id="edit_product_image" name="product_image" class="form-control">
                    </div>
                    <div class="col-md-12 mt-2">
                        <textarea id="edit_product_desc" name="product_desc" class="form-control"></textarea>
                    </div>
                    <div class="col-md-8 mt-2">
                        <input type="text" id="edit_product_keywords" name="product_keywords" class="form-control">
                    </div>
                    <div class="col-md-4 mt-2">
                        <button class="btn btn-primary w-100" type="submit">Save</button>
                    </div>
                </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/products.js"></script>
</body>
</html>
