# Add to Cart Implementation Plan

## Backend Updates
- [x] Update `settings/core.php` - Add `session_start()` for session management
- [x] Modify `classes/cart_class.php` - Change guest tracking from IP to session ID, fix update logic, add cart count method
- [x] Update `actions/add_to_cart_action.php` - Return `cart_count` in JSON response

## Frontend Updates
- [x] Rewrite `js/cart.js` - Convert to vanilla JavaScript with fetch API, fix event handling
- [x] Update `view/all_product.php` - Add cart count display and include cart.js script
- [x] Update `view/single_product.php` - Add cart count display and include cart.js script

## Testing
- [ ] Test add to cart functionality
- [ ] Test cart count updates
- [ ] Test guest vs logged-in user carts
- [ ] Test quantity updates for existing items
