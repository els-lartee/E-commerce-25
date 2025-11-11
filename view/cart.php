<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
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
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-secondary:hover {
            background: #5a6268;
        }
        .cart-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .cart-header {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        .cart-header h2 {
            margin-bottom: 10px;
        }
        .cart-empty {
            text-align: center;
            padding: 50px 20px;
        }
        .cart-empty h3 {
            margin-bottom: 20px;
            color: #6c757d;
        }
        .cart-item {
            display: flex;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 20px;
        }
        .item-image-placeholder {
            width: 80px;
            height: 80px;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            border-radius: 4px;
            margin-right: 20px;
        }
        .item-details {
            flex: 1;
        }
        .item-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .item-meta {
            color: #6c757d;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .quantity-controls {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .quantity-controls button {
            width: 30px;
            height: 30px;
            border: 1px solid #dee2e6;
            background: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .quantity-controls input {
            width: 60px;
            height: 30px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-left: none;
            border-right: none;
        }
        .item-price {
            font-size: 16px;
            font-weight: bold;
            color: #28a745;
            margin-right: 20px;
        }
        .item-subtotal {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        .item-actions {
            margin-left: 20px;
        }
        .cart-footer {
            background: #f8f9fa;
            padding: 20px;
            border-top: 1px solid #dee2e6;
        }
        .cart-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .total-label {
            font-size: 20px;
            font-weight: bold;
        }
        .total-amount {
            font-size: 24px;
            font-weight: bold;
            color: #28a745;
        }
        .cart-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        @media (max-width: 768px) {
            .cart-item {
                flex-direction: column;
                align-items: flex-start;
            }
            .item-image, .item-image-placeholder {
                margin-bottom: 15px;
                margin-right: 0;
            }
            .cart-actions {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Shopping Cart</h2>
            <a href="all_product.php" class="btn btn-secondary">Continue Shopping</a>
        </div>

        <div id="cartContent">
            <p style="text-align: center;">Loading cart...</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/cart.js"></script>
    <script>
        $(document).ready(function() {
            loadCart();
        });

        function loadCart() {
            $.ajax({
                url: '../actions/get_cart_action.php', // We'll create this action
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        displayCart(response.items || []);
                    } else {
                        $('#cartContent').html('<div class="alert alert-danger">' + (response.message || 'Failed to load cart') + '</div>');
                    }
                },
                error: function() {
                    $('#cartContent').html('<div class="alert alert-danger">Error loading cart</div>');
                }
            });
        }

        function displayCart(items) {
            if (items.length === 0) {
                $('#cartContent').html(`
                    <div class="cart-container">
                        <div class="cart-empty">
                            <h3>Your cart is empty</h3>
                            <a href="all_product.php" class="btn btn-primary">Start Shopping</a>
                        </div>
                    </div>
                `);
                return;
            }

            let total = 0;
            let html = '<div class="cart-container">';

            html += '<div class="cart-header"><h2>Cart Items (' + items.length + ')</h2></div>';

            items.forEach(item => {
                const subtotal = item.product_price * item.qty;
                total += subtotal;

                const imageUrl = item.product_image ? `../${item.product_image}` : '';
                const imageHtml = imageUrl
                    ? `<img src="${imageUrl}" class="item-image" alt="${item.product_title}">`
                    : `<div class="item-image-placeholder"><span>No Image</span></div>`;

                html += `
                    <div class="cart-item" data-cart-id="${item.p_id}">
                        ${imageHtml}
                        <div class="item-details">
                            <div class="item-title">${item.product_title}</div>
                            <div class="item-meta">
                                Category: ${item.cat_name || 'N/A'} | Brand: ${item.brand_name || 'N/A'}
                            </div>
                            <div class="quantity-controls">
                                <button class="qty-btn" data-action="decrease">-</button>
                                <input type="number" class="qty-input" value="${item.qty}" min="1" max="99">
                                <button class="qty-btn" data-action="increase">+</button>
                            </div>
                        </div>
                        <div class="item-price">$${parseFloat(item.product_price).toFixed(2)}</div>
                        <div class="item-subtotal">$${subtotal.toFixed(2)}</div>
                        <div class="item-actions">
                            <button class="btn btn-danger btn-sm remove-item-btn" data-cart-id="${item.p_id}">Remove</button>
                        </div>
                    </div>
                `;
            });

            html += `
                <div class="cart-footer">
                    <div class="cart-total">
                        <span class="total-label">Total:</span>
                        <span class="total-amount">$${total.toFixed(2)}</span>
                    </div>
                    <div class="cart-actions">
                        <button class="btn btn-danger" id="emptyCartBtn">Empty Cart</button>
                        <div>
                            <button class="btn btn-success proceed-to-checkout-btn">Proceed to Checkout</button>
                        </div>
                    </div>
                </div>
            `;

            html += '</div>';
            $('#cartContent').html(html);

            // Attach event handlers
            attachCartEventHandlers();
        }

        function attachCartEventHandlers() {
            // Quantity controls
            $('.qty-btn').on('click', function() {
                const action = $(this).data('action');
                const input = $(this).siblings('.qty-input');
                let qty = parseInt(input.val());

                if (action === 'increase') {
                    qty++;
                } else if (action === 'decrease' && qty > 1) {
                    qty--;
                }

                input.val(qty);
                updateQuantity($(this).closest('.cart-item').data('cart-id'), qty);
            });

            $('.qty-input').on('change', function() {
                const qty = parseInt($(this).val());
                if (qty < 1) $(this).val(1);
                updateQuantity($(this).closest('.cart-item').data('cart-id'), qty);
            });

            // Remove item
            $('.remove-item-btn').on('click', function() {
                const cartId = $(this).data('cart-id');
                removeFromCart(cartId);
            });

            // Empty cart
            $('#emptyCartBtn').on('click', function() {
                if (confirm('Are you sure you want to empty your cart?')) {
                    emptyCart();
                }
            });
        }

        function updateQuantity(cartId, qty) {
            $.ajax({
                url: '../actions/update_quantity_action.php',
                type: 'POST',
                data: {
                    cart_id: cartId,
                    qty: qty
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        loadCart(); // Reload cart to update totals
                    } else {
                        alert(response.message || 'Failed to update quantity');
                        loadCart(); // Reload to revert changes
                    }
                },
                error: function() {
                    alert('Error updating quantity');
                    loadCart();
                }
            });
        }

        function removeFromCart(cartId) {
            $.ajax({
                url: '../actions/remove_from_cart_action.php',
                type: 'POST',
                data: { cart_id: cartId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        loadCart();
                    } else {
                        alert(response.message || 'Failed to remove item');
                    }
                },
                error: function() {
                    alert('Error removing item');
                    loadCart();
                }
            });
        }

        function emptyCart() {
            $.ajax({
                url: '../actions/empty_cart_action.php',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        loadCart();
                    } else {
                        alert(response.message || 'Failed to empty cart');
                    }
                },
                error: function() {
                    alert('Error emptying cart');
                }
            });
        }
    </script>
</body>
</html>
