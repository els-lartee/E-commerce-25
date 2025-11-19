<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <ul class="breadcrumb">
            <li><a href="../index.php">Home</a></li>
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
                        <a href="../index.php" class="btn btn-primary">View All Products</a>
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
                            <a href="../index.php">View All Products</a>
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
                    <div class="product-container" data-product-card="${p.product_id}">
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
                                <div class="product-actions">
                                    <button class="btn btn-success btn-add-to-cart" data-product-id="${p.product_id}">
                                        Add to Cart
                                    </button>
                                </div>
                                <a href="../index.php" class="btn btn-secondary" style="margin-top: 10px; display: inline-block;">
                                    Back to Products
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
                        <a href="../index.php">View All Products</a>
                    </div>
                `);
            });
        }
    </script>
</body>
</html>
