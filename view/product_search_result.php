<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Search Results</h2>
            <a href="../index.php" class="btn btn-secondary">Back to Home</a>
        </div>

        <div class="search-header">
            <h5 id="searchQuery"></h5>
        </div>

        <div class="filter-section">
            <h6>Refine Your Search</h6>
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
            <p style="text-align: center; grid-column: 1 / -1;">Loading search results...</p>
        </div>

        <ul id="pagination" class="pagination"></ul>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let allProducts = [];
        let filteredProducts = [];
        let currentPage = 1;
        const productsPerPage = 10;
        let searchQuery = '';

        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            searchQuery = urlParams.get('q') || '';

            if (!searchQuery) {
                $('#searchQuery').text('Please enter a search term');
                $('#productsContainer').html('<div class="col-12"><div class="alert alert-warning">No search query provided. <a href="../index.php">Go back to home</a></div></div>');
                return;
            }

            $('#searchQuery').html(`Search results for: <strong>"${searchQuery}"</strong>`);

            loadCategories();
            loadBrands();
            loadSearchResults();

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

        function loadSearchResults() {
            $.getJSON(`../actions/search_products_customer_action.php?q=${encodeURIComponent(searchQuery)}`, function(resp) {
                if (resp.status !== 'success') {
                    $('#productsContainer').html('<div class="alert alert-danger" style="grid-column: 1 / -1;">Failed to load search results</div>');
                    return;
                }
                allProducts = resp.products || [];
                filteredProducts = allProducts;
                displayProducts();
            }).fail(function() {
                $('#productsContainer').html('<div class="alert alert-danger" style="grid-column: 1 / -1;">Error loading search results</div>');
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

            $('#productCount').html(`Found ${filteredProducts.length} product(s)`);

            if (productsToDisplay.length === 0) {
                $('#productsContainer').html(`
                    <div class="alert alert-info" style="grid-column: 1 / -1;">
                        <h5>No products found</h5>
                        <p>No products match your search for "${searchQuery}". Try a different search term or browse <a href="../index.php">all products</a>.</p>
                    </div>
                `);
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
