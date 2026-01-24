<?php 
require_once '../settings/core.php';

// Redirect non-logged-in users to login page
if (!is_logged_in()) {
    header('Location: ../login/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products</title>
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>All Products</h2>
            <div class="cart-info">
                <a href="../view/cart.php" class="btn btn-secondary">View Cart (<span id="cart-count">0</span>)</a>
                <a href="../index.php" class="btn btn-secondary">Back to Home</a>
                <a href="../login/logout.php" class="btn btn-outline-danger">Logout</a>
            </div>
        </div>

        <div class="filter-section">
            <div class="filter-row">
                <div class="form-group">
                    <label for="categoryFilter">Filter by Category</label>
                    <select id="categoryFilter">
                        <option value="">All Categories</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="brandFilter">Filter by Brand</label>
                    <select id="brandFilter">
                        <option value="">All Brands</option>
                    </select>
                </div>
                <div class="form-group">
                    <button id="clearFilters" class="btn btn-secondary">Clear Filters</button>
                </div>
            </div>
        </div>

        <div id="productCount"></div>

        <div id="productsContainer" class="products-grid">
            <p style="text-align: center; grid-column: 1 / -1;">Loading products...</p>
        </div>

        <ul id="pagination" class="pagination"></ul>
    </div>

    <script src="../js/cart.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let allProducts = [];
        let filteredProducts = [];
        let currentPage = 1;
        const productsPerPage = 10;

        $(document).ready(function() {
            loadCategories();
            loadBrands();
            loadProducts();

            $('#categoryFilter, #brandFilter').on('change', function() {
                filterProducts();
            });

            $('#clearFilters').on('click', function() {
                $('#categoryFilter').val('');
                $('#brandFilter').val('');
                filterProducts();
            });
        });

        function loadCategories() {
            $.getJSON('../actions/fetch_categories_public_action.php', function(resp) {
                if (resp.status === 'success') {
                    const categories = resp.categories || [];
                    categories.forEach(cat => {
                        $('#categoryFilter').append(`<option value="${cat.id}">${cat.name}</option>`);
                    });
                }
            });
        }

        function loadBrands() {
            $.getJSON('../actions/fetch_brands_public_action.php', function(resp) {
                if (resp.status === 'success') {
                    const brands = resp.brands || [];
                    brands.forEach(brand => {
                        $('#brandFilter').append(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
                    });
                }
            });
        }

        function loadProducts() {
            $.getJSON('../actions/view_all_products_action.php', function(resp) {
                if (resp.status !== 'success') {
                    $('#productsContainer').html('<div class="alert alert-danger" style="grid-column: 1 / -1;">Failed to load products</div>');
                    return;
                }
                allProducts = resp.products || [];
                filteredProducts = allProducts;
                displayProducts();
            }).fail(function() {
                $('#productsContainer').html('<div class="alert alert-danger" style="grid-column: 1 / -1;">Error loading products</div>');
            });
        }

        function filterProducts() {
            const categoryId = $('#categoryFilter').val();
            const brandId = $('#brandFilter').val();

            filteredProducts = allProducts.filter(product => {
                let match = true;
                if (categoryId && product.product_cat != categoryId) {
                    match = false;
                }
                if (brandId && product.product_brand != brandId) {
                    match = false;
                }
                return match;
            });

            currentPage = 1;
            displayProducts();
        }

        function displayProducts() {
            const start = (currentPage - 1) * productsPerPage;
            const end = start + productsPerPage;
            const productsToDisplay = filteredProducts.slice(start, end);

            $('#productCount').html(`Showing ${filteredProducts.length} product(s)`);

            if (productsToDisplay.length === 0) {
                $('#productsContainer').html('<div class="alert alert-info" style="grid-column: 1 / -1;">No products found</div>');
                $('#pagination').html('');
                return;
            }

            let html = '';
            productsToDisplay.forEach(p => {
                const imageUrl = p.product_image ? `../${p.product_image}` : '';
                const imageHtml = imageUrl 
                    ? `<img src="${imageUrl}" class="product-image" alt="${p.product_title}">`
                    : `<div class="product-image-placeholder"><span>No Image</span></div>`;

                html += `
                    <div class="product-card">
                        ${imageHtml}
                        <div class="product-body">
                            <div class="product-title">${p.product_title}</div>
                            <div class="product-price">$${parseFloat(p.product_price).toFixed(2)}</div>
                            <div class="product-meta">
                                Category: ${p.cat_name || 'N/A'}<br>
                                Brand: ${p.brand_name || 'N/A'}
                            </div>
                            <div class="product-actions">
                                <a href="single_product.php?id=${p.product_id}" class="btn btn-primary">View Details</a>
                                <button class="btn btn-success" data-product-id="${p.product_id}">Add to Cart</button>
                            </div>
                        </div>
                    </div>
                `;
            });

            $('#productsContainer').html(html);
            renderPagination();
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredProducts.length / productsPerPage);
            
            if (totalPages <= 1) {
                $('#pagination').html('');
                return;
            }

            let html = '';
            
            if (currentPage > 1) {
                html += `<li><a href="#" data-page="${currentPage - 1}">Previous</a></li>`;
            }

            for (let i = 1; i <= totalPages; i++) {
                if (i === currentPage) {
                    html += `<li class="active"><a href="#">${i}</a></li>`;
                } else {
                    html += `<li><a href="#" data-page="${i}">${i}</a></li>`;
                }
            }

            if (currentPage < totalPages) {
                html += `<li><a href="#" data-page="${currentPage + 1}">Next</a></li>`;
            }

            $('#pagination').html(html);

            $('#pagination a').on('click', function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                if (page) {
                    currentPage = page;
                    displayProducts();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        }
    </script>
</body>
</html>
