<?php 
require_once 'settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Home</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
.menu-tray {
position: fixed;
top: 16px;
right: 16px;
background: rgba(255,255,255,0.95);
border: 1px solid #e6e6e6;
border-radius: 8px;
padding: 6px 10px;
box-shadow: 0 4px 10px rgba(0,0,0,0.06);
z-index: 1000;
}
.menu-tray a { margin-left: 8px; }
.cart-badge {
position: relative;
display: inline-block;
}
.cart-badge .badge {
position: absolute;
top: -8px;
right: -8px;
background: #dc3545;
color: white;
border-radius: 50%;
padding: 2px 6px;
font-size: 10px;
}
.quantity-controls {
display: none;
align-items: center;
gap: 10px;
}
.quantity-controls.active {
display: flex;
}
.quantity-controls button {
width: 32px;
height: 32px;
padding: 0;
font-weight: bold;
}
.quantity-controls .qty-display {
min-width: 40px;
text-align: center;
font-weight: bold;
}
</style>
</head>
<body>

<div class="menu-tray">
<span class="me-2">Menu:</span>
<?php if (isset($_SESSION['user_id'])): ?>
<span class="me-2">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
<?php if (is_admin()): ?>
<a href="admin/jewellery.php" class="btn btn-sm btn-outline-info">Jewellery</a>
<a href="admin/brand.php" class="btn btn-sm btn-outline-info">Brand</a>
<a href="admin/products.php" class="btn btn-sm btn-outline-info">Products</a>
<?php else: ?>
<a href="view/cart.php" class="btn btn-sm btn-outline-primary cart-badge">
Cart
<span class="badge" id="cartCount">0</span>
</a>
<?php endif; ?>
<a href="login/logout.php" class="btn btn-sm btn-outline-danger">Logout</a>
<?php else: ?>
<a href="login/register.php" class="btn btn-sm btn-outline-primary">Register</a>
<a href="login/login.php" class="btn btn-sm btn-outline-secondary">Login</a>
<?php endif; ?>
</div>

<div class="container" style="padding-top:120px;">
<?php if (!isset($_SESSION['user_id'])): ?>
<!-- Welcome message for guests -->
<div class="text-center">
<h1>Welcome to Our Store</h1>
<p class="text-muted">Please register or login using the menu in the top-right to browse our products.</p>
</div>
<?php elseif (is_admin()): ?>
<!-- Welcome message for admin -->
<div class="text-center">
<h1>Welcome, Admin!</h1>
<p class="text-muted">Use the menu in the top-right to manage Jewellery, Brands, and Products.</p>
</div>
<?php else: ?>
<!-- Products section for logged-in customers -->
<div class="mt-3">
<h2>Available Products</h2>
<div id="productsContainer" class="row"></div>
</div>
<?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/cart.js" defer></script>
<script>
$(document).ready(function(){
<?php if (isset($_SESSION['user_id']) && !is_admin()): ?>
loadProducts();
updateCartCount();
<?php endif; ?>
});

function updateCartCount() {
$.getJSON('actions/get_cart_summary_action.php', function(data) {
if (data.status === 'success') {
$('#cartCount').text(data.total_items || 0);
}
});
}

function loadProducts() {
$.getJSON('actions/fetch_product_action.php', function(resp){
if (resp.status !== 'success') {
$('#productsContainer').html('<div class="alert alert-danger">Failed to load products</div>');
return;
}
const products = resp.products || [];
if (products.length === 0) {
$('#productsContainer').html('<div class="alert alert-info">No products available.</div>');
return;
}
let html = '';
products.forEach(p => {
const img = p.product_image ? `<img src="${p.product_image}" class="card-img-top" style="height:200px; object-fit:cover;" alt="${p.product_title}">` : '<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:200px;"><span class="text-muted">No Image</span></div>';
html += `
<div class="col-md-4 mb-4">
<div class="card h-100" data-product-card="${p.product_id}">
${img}
<div class="card-body">
<h5 class="card-title">${p.product_title}</h5>
<p class="card-text"><strong>Price:</strong> $${p.product_price}</p>
<p class="card-text">${p.product_desc || 'No description available.'}</p>
<p class="card-text"><small class="text-muted">Category: ${p.cat_name} | Brand: ${p.brand_name}</small></p>
<div class="mt-3 product-actions">
<button class="btn btn-success btn-add-to-cart" data-product-id="${p.product_id}">
Add to Cart
</button>
</div>
</div>
</div>
</div>
`;
});
$('#productsContainer').html(html);
}).fail(function(){
$('#productsContainer').html('<div class="alert alert-danger">Error loading products</div>');
});
}
</script>
</body>
</html>
