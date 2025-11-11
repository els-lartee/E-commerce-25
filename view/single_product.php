<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
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
        .breadcrumb {
            list-style: none;
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            background: white;
            padding: 10px 15px;
            border-radius: 4px;
        }
        .breadcrumb li {
            display: flex;
            align-items: center;
        }
        .breadcrumb li:not(:last-child)::after {
            content: '/';
            margin-left: 10px;
            color: #6c757d;
        }
        .breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }
        .breadcrumb a:hover {
            text-decoration: underline;
        }
        .cart-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .cart-count {
            background: #007bff;
            color: white;
            padding: 2px 8px;
            border-radius: 50%;
            font-size: 12px;
            min-width: 20px;
            text-align: center;
        }
        .product-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .product-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }
        .product-image-placeholder {
            width: 100%;
            height: 400px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            border-radius: 8px;
        }
        .product-details h1 {
            font-size: 28px;
            margin-bottom: 15px;
        }
        .price-tag {
            font-size: 32px;
            color: #28a745;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .product-meta {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .product-meta p {
            margin-bottom: 8px;
        }
        .product-meta p:last-child {
            margin-bottom: 0;
        }
        .product-description {
            margin-top: 20px;
        }
        .product-description h4 {
            margin-bottom: 10px;
        }
        .product-keywords {
            margin-top: 15px;
        }
        .product-keywords h5 {
            margin-bottom: 8px;
        }
        .product-keywords p {
            color: #6c757d;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            font-size: 16px;
            text-align: center;
            margin-bottom: 10px;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .alert {
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert h4 {
            margin-bottom: 10px;
        }
        .alert a {
            color: #007bff;
            text-decoration: none;
        }
        .alert a:hover {
            text-decoration: underline;
        }
        @media (max-width: 768px) {
            .product-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="../index.php">Home</a></li>
            <li><a href="all_product.php">All Products</a></li>
            <li>Product Details</li>
        </ul>

        <div class="cart-info" style="text-align: right; margin-bottom: 20px;">
            <a href="../view/cart.php" class="btn btn-secondary">View Cart (<span id="cart-count">0</span>)</a>
        </div>

        <div id="productDetails">
            <p style="text-align: center;">Loading product details...</p>
        </div>
    </div>

    <script src="../js/cart.js" defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');

            if (!productId) {
                $('#productDetails').html(`
                    <div class="alert alert-danger">
                        <h4>Error</h4>
                        <p>No product ID specified.</p>
                        <a href="all_product.php" class="btn btn-primary">View All Products</a>
                    </div>
                `);
                return;
            }

            loadProduct(productId);
        });

        function loadProduct(productId) {
            $.getJSON(`../actions/view_single_product_action.php?id=${productId}`, function(resp) {
                if (resp.status !== 'success' || !resp.product) {
                    $('#productDetails').html(`
                        <div class="alert alert-danger">
                            <h4>Product Not Found</h4>
                            <p>The product you are looking for does not exist.</p>
                            <a href="all_product.php">View All Products</a>
                        </div>
                    `);
                    return;
                }

                const p = resp.product;
                document.title = p.product_title + ' - Product Details';

                const imageUrl = p.product_image ? `../${p.product_image}` : '';
                const imageHtml = imageUrl 
                    ? `<img src="${imageUrl}" class="product-image" alt="${p.product_title}">`
                    : `<div class="product-image-placeholder"><span>No Image Available</span></div>`;

                const html = `
                    <div class="product-container">
                        <div>
                            ${imageHtml}
                        </div>
                        <div class="product-details">
                            <h1>${p.product_title}</h1>
                            <div class="price-tag">$${parseFloat(p.product_price).toFixed(2)}</div>
                            
                            <div class="product-meta">
                                <p><strong>Category:</strong> ${p.cat_name || 'N/A'}</p>
                                <p><strong>Brand:</strong> ${p.brand_name || 'N/A'}</p>
                            </div>

                            <div class="product-description">
                                <h4>Description</h4>
                                <p>${p.product_desc || 'No description available.'}</p>
                            </div>

                            ${p.product_keywords ? `
                                <div class="product-keywords">
                                    <h5>Keywords</h5>
                                    <p>${p.product_keywords}</p>
                                </div>
                            ` : ''}

                            <div style="margin-top: 30px;">
                                <button class="btn btn-success" data-product-id="${p.product_id}">
                                    Add to Cart
                                </button>
                                <a href="all_product.php" class="btn btn-secondary">
                                    Back to All Products
                                </a>
                            </div>
                        </div>
                    </div>
                `;

                $('#productDetails').html(html);
            }).fail(function() {
                $('#productDetails').html(`
                    <div class="alert alert-danger">
                        <h4>Error</h4>
                        <p>Failed to load product details. Please try again later.</p>
                        <a href="all_product.php">View All Products</a>
                    </div>
                `);
            });
        }
    </script>
</body>
</html>
