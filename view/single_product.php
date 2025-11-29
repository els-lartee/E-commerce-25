<?php 
require_once '../settings/core.php';
?>
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
        <div id="productDetail">
            <p style="text-align: center;">Loading product details...</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/cart.js" defer></script>
    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            const productId = urlParams.get('id');

            if (!productId) {
                $('#productDetail').html('<div class="alert alert-danger">Product ID not provided</div>');
                return;
            }

            loadProductDetail(productId);
        });

        function loadProductDetail(productId) {
            $.getJSON(`../actions/view_single_product_action.php?id=${productId}`, function(resp) {
                if (resp.status !== 'success' || !resp.product) {
                    $('#productDetail').html('<div class="alert alert-danger">' + (resp.message || 'Failed to load product details') + '</div>');
                    return;
                }

                const product = resp.product;
                displayProductDetail(product);
            }).fail(function(jqXHR, textStatus, errorThrown) {
                console.error('Error loading product:', textStatus, errorThrown);
                $('#productDetail').html('<div class="alert alert-danger">Error loading product details</div>');
            });
        }

        function displayProductDetail(product) {
            const imageUrl = product.product_image ? `../${product.product_image}` : '';
            const imageHtml = imageUrl
                ? `<img src="${imageUrl}" class="product-image" alt="${product.product_title}">`
                : `<div class="product-image-placeholder"><span>No Image</span></div>`;

            const html = `
                <div class="product-detail">
                    <div class="product-image-container">
                        ${imageHtml}
                    </div>
                    <div class="product-info">
                        <h1>${product.product_title}</h1>
                        <div class="product-price">$${parseFloat(product.product_price).toFixed(2)}</div>
                        <div class="product-meta">
                            Category: ${product.cat_name || 'N/A'}<br>
                            Brand: ${product.brand_name || 'N/A'}
                        </div>
                        <div class="product-description">
                            ${product.product_desc || 'No description available.'}
                        </div>
                        <div class="quantity-section">
                            <div class="quantity-controls">
                                <button class="qty-btn" data-action="decrease">-</button>
                                <input type="number" class="qty-input" value="1" min="1" max="99">
                                <button class="qty-btn" data-action="increase">+</button>
                            </div>
                            <button class="btn btn-success add-to-cart-btn" data-product-id="${product.product_id}">Add to Cart</button>
                        </div>
                        <div class="product-actions">
                            <a href="../view/all_product.php" class="btn btn-secondary">Back to Products</a>
                            <a href="../view/cart.php" class="btn btn-primary">View Cart</a>
                        </div>
                    </div>
                </div>
            `;

            $('#productDetail').html(html);

            // Quantity controls
            $('.qty-btn').on('click', function() {
                const action = $(this).data('action');
                const input = $(this).siblings('.qty-input');
                let value = parseInt(input.val());

                if (action === 'increase' && value < 99) {
                    value++;
                } else if (action === 'decrease' && value > 1) {
                    value--;
                }

                input.val(value);
            });

            $('.qty-input').on('change', function() {
                let value = parseInt($(this).val());
                if (isNaN(value) || value < 1) {
                    value = 1;
                } else if (value > 99) {
                    value = 99;
                }
                $(this).val(value);
            });

            // Store product info for chatbot context
            window.currentProduct = product;
            
            // Initialize chatbot with product context
            if (window.chatbot) {
                window.chatbot.setContext('product', {
                    id: product.product_id,
                    name: product.product_title,
                    price: product.product_price,
                    category: product.cat_name,
                    brand: product.brand_name,
                    description: product.product_desc
                });
            }
        }
    </script>

    <?php 
    // Include AI Chatbot for logged-in customers
    if (isset($_SESSION['user_id']) && !is_admin()): 
        $chat_context = 'product';
        include '../components/chat_widget.php';
    endif; 
    ?>
</body>
</html>
