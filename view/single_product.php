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
    <style>
        .btn-tryon {
            background: #7A5C3E;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-top: 15px;
            font-family: 'Poppins', sans-serif;
        }
        .btn-tryon:hover {
            background: #5D4429;
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(122, 92, 62, 0.4);
            color: white;
        }
        .btn-tryon svg {
            width: 20px;
            height: 20px;
        }
        .tryon-badge {
            background: #D4AF37;
            color: white;
            font-size: 0.7rem;
            padding: 3px 8px;
            border-radius: 12px;
            margin-left: 5px;
            vertical-align: middle;
        }
    </style>
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

            // Build Try-On URL with product info
            const tryOnUrl = `virtual_tryon.php?id=${product.product_id}&image=${encodeURIComponent(product.product_image || '')}&title=${encodeURIComponent(product.product_title)}&category=${encodeURIComponent(product.cat_name || '')}`;
            
            // Check if product has an image (required for AR try-on)
            const tryOnButton = product.product_image 
                ? `<a href="${tryOnUrl}" class="btn-tryon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Try It On <span class="tryon-badge">AR</span>
                   </a>`
                : '';

            const html = `
                <div class="product-detail">
                    <div class="product-image-container">
                        ${imageHtml}
                        ${tryOnButton}
                    </div>
                    <div class="product-info">
                        <h1>${product.product_title}</h1>
                        <div class="product-price">GHS ${parseFloat(product.product_price).toFixed(2)}</div>
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
