<?php 
require_once '../settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link href="../css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Shopping Cart</h2>
            <a href="../index.php" class="btn btn-secondary">Continue Shopping</a>
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
                            <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
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

    <?php 
    // Include AI Chatbot for logged-in customers
    if (isset($_SESSION['user_id']) && !is_admin()): 
        $chat_context = 'cart';
        include '../components/chat_widget.php';
    endif; 
    ?>
</body>
</html>
