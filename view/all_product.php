<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header h2 {
            color: #333;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .btn-primary {
            background: #007bff;
            color: white;
        }
        .btn-primary:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .filter-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .filter-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            align-items: end;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            margin-bottom: 5px;
            font-weight: bold;
            font-size: 14px;
        }
        .form-group select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        #productCount {
            margin-bottom: 20px;
            font-weight: bold;
        }
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .product-image-placeholder {
            width: 100%;
            height: 200px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
        .product-body {
            padding: 15px;
        }
        .product-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .product-price {
            font-size: 18px;
            color: #28a745;
            font-weight: bold;
            margin-bottom: 8px;
        }
        .product-meta {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 15px;
        }
        .product-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .product-actions .btn {
            width: 100%;
            text-align: center;
            padding: 8px;
        }
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            list-style: none;
            margin-top: 30px;
        }
        .pagination a {
            padding: 8px 12px;
            text-decoration: none;
            background: white;
            color: #007bff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .pagination a:hover {
            background: #007bff;
            color: white;
        }
        .pagination .active a {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        @media (max-width: 768px) {
            .filter-row {
                grid-template-columns: 1fr;
            }
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
            .header {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>All Products</h2>
            <a href="../index.php" class="btn btn-secondary">Back to Home</a>
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
