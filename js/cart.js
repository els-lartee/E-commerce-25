const isRootLevel = window.location.pathname.endsWith('index.php') || window.location.pathname.endsWith('/');
const basePath = isRootLevel ? '' : '../';

const cartItems = new Map();

document.addEventListener('DOMContentLoaded', () => {
    updateCartCount();
    
    document.body.addEventListener('click', async e => {
        const addBtn = e.target.closest('.btn-add-to-cart');
        if (addBtn && addBtn.hasAttribute('data-product-id')) {
            await handleAddToCart(addBtn);
            return;
        }
        
        const increaseBtn = e.target.closest('.btn-qty-increase');
        if (increaseBtn && increaseBtn.hasAttribute('data-product-id')) {
            await handleQuantityChange(increaseBtn, 1);
            return;
        }
        
        const decreaseBtn = e.target.closest('.btn-qty-decrease');
        if (decreaseBtn && decreaseBtn.hasAttribute('data-product-id')) {
            await handleQuantityChange(decreaseBtn, -1);
            return;
        }
    });
});

async function handleAddToCart(btn) {
    const productId = btn.getAttribute('data-product-id');
    
    if (btn.disabled) return;
    
    btn.disabled = true;
    const originalText = btn.textContent;
    btn.textContent = 'Adding...';

    try {
        const response = await fetch(`${basePath}actions/add_to_cart_action.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                product_id: productId,
                qty: 1
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            showMessage(data.message, 'success');
            updateCartCount(data.cart_count);
            
            cartItems.set(productId, (cartItems.get(productId) || 0) + 1);
            updateProductCardUI(productId);
        } else {
            showMessage(data.message || 'Failed to add product to cart', 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showMessage('Error adding product to cart', 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = originalText;
    }
}

async function handleQuantityChange(btn, delta) {
    const productId = btn.getAttribute('data-product-id');
    const currentQty = cartItems.get(productId) || 1;
    const newQty = currentQty + delta;
    
    if (newQty < 1) {
        await removeFromCart(productId);
        return;
    }
    
    btn.disabled = true;

    try {
        const response = await fetch(`${basePath}actions/update_quantity_action.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                cart_id: productId,
                qty: newQty
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            cartItems.set(productId, newQty);
            updateProductCardUI(productId);
            updateCartCount();
        } else {
            showMessage(data.message || 'Failed to update quantity', 'error');
        }
    } catch (error) {
        console.error('Error updating quantity:', error);
        showMessage('Error updating quantity', 'error');
    } finally {
        btn.disabled = false;
    }
}

async function removeFromCart(productId) {
    try {
        const response = await fetch(`${basePath}actions/remove_from_cart_action.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                cart_id: productId
            })
        });

        const data = await response.json();

        if (data.status === 'success') {
            cartItems.delete(productId);
            updateProductCardUI(productId);
            updateCartCount();
            showMessage('Item removed from cart', 'success');
        } else {
            showMessage(data.message || 'Failed to remove item', 'error');
        }
    } catch (error) {
        console.error('Error removing from cart:', error);
        showMessage('Error removing item', 'error');
    }
}

function updateProductCardUI(productId) {
    const cards = document.querySelectorAll(`[data-product-card="${productId}"]`);
    cards.forEach(card => {
        const actionsDiv = card.querySelector('.product-actions');
        if (!actionsDiv) return;
        
        const qty = cartItems.get(productId);
        
        if (qty) {
            actionsDiv.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px; justify-content: center; background: #f8f9fa; padding: 10px; border-radius: 4px;">
                    <button class="btn btn-secondary btn-qty-decrease" data-product-id="${productId}" style="padding: 5px 12px;">−</button>
                    <span style="font-weight: bold; min-width: 30px; text-align: center;">${qty}</span>
                    <button class="btn btn-primary btn-qty-increase" data-product-id="${productId}" style="padding: 5px 12px;">+</button>
                </div>
            `;
        } else {
            actionsDiv.innerHTML = `
                <button class="btn btn-success btn-add-to-cart" data-product-id="${productId}">Add to Cart</button>
            `;
        }
    });
}

async function updateCartCount(count) {
    if (count === undefined) {
        try {
            const response = await fetch(`${basePath}actions/get_cart_action.php`);
            const data = await response.json();
            if (data.status === 'success') {
                count = data.items.reduce((sum, item) => sum + parseInt(item.qty), 0);
                
                data.items.forEach(item => {
                    cartItems.set(item.p_id.toString(), parseInt(item.qty));
                    updateProductCardUI(item.p_id.toString());
                });
            } else {
                count = 0;
            }
        } catch (error) {
            console.error('Error fetching cart count:', error);
            count = 0;
        }
    }
    
    const cartCountElements = document.querySelectorAll('#cart-count');
    cartCountElements.forEach(element => {
        element.textContent = count || 0;
    });
}

function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('#cart-count');
    cartCountElements.forEach(element => {
        element.textContent = count || 0;
    });
}

function showMessage(message, type) {
    // Remove existing messages
    const existingMessages = document.querySelectorAll('.cart-message');
    existingMessages.forEach(msg => msg.remove());

    // Create message element
    const messageDiv = document.createElement('div');
    messageDiv.className = `cart-message alert ${type === 'success' ? 'alert-success' : 'alert-danger'}`;
    messageDiv.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        padding: 15px;
        border-radius: 4px;
        max-width: 300px;
        background: ${type === 'success' ? '#d4edda' : '#f8d7da'};
        color: ${type === 'success' ? '#155724' : '#721c24'};
        border: 1px solid ${type === 'success' ? '#c3e6cb' : '#f5c6cb'};
    `;
    messageDiv.textContent = message;

    // Add to body
    document.body.appendChild(messageDiv);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (messageDiv.parentNode) {
            messageDiv.remove();
        }
    }, 3000);
}
