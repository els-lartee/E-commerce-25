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
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="css/styles.css" rel="stylesheet">
<style>
/* Recommendations Section Styling */
.recommendations-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 40px;
}

.recommendations-section h3 {
    color: #333;
    font-weight: 600;
    margin-bottom: 5px;
}

.rec-product-card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.rec-product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.rec-product-card .card-img-top {
    height: 120px !important;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.rec-product-card:hover .card-img-top {
    transform: scale(1.05);
}

.rec-product-card .card-body {
    padding: 12px;
}

.rec-product-card .card-title {
    font-size: 0.85rem !important;
    font-weight: 500;
    color: #333;
    margin-bottom: 8px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.rec-product-card .card-text {
    margin-bottom: 10px;
}

.rec-product-card .card-text strong {
    color: #7A5C3E;
    font-size: 0.95rem;
}

.rec-product-card .btn {
    font-size: 0.75rem;
    padding: 6px 12px;
    border-radius: 20px;
    transition: all 0.3s ease;
}

.rec-product-card .btn-info {
    background: #7A5C3E;
    border-color: #7A5C3E;
    color: white;
}

.rec-product-card .btn-info:hover {
    background: #5D4429;
    border-color: #5D4429;
    color: white;
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

<?php if (is_admin()): ?>
<!-- Welcome message for admin -->
<div class="text-center">
<h1>Welcome, Admin!</h1>
<p class="text-muted">Use the menu in the top-right to manage Jewellery, Brands, and Products.</p>
</div>
<?php elseif (is_logged_in()): ?>
<!-- Products section for logged-in customers only -->
<div class="mt-3">
	<!-- Search and Filters -->
	<div class="row mb-4">
		<div class="col-md-4">
			<form action="view/product_search_result.php" method="GET">
				<div class="input-group">
					<input name="q" type="search" class="form-control" placeholder="Search products..." required>
					<button class="btn btn-primary" type="submit">Search</button>
				</div>
			</form>
		</div>
		<div class="col-md-3">
			<select id="categoryFilter" class="form-select">
				<option value="">All Categories</option>
			</select>
		</div>
		<div class="col-md-3">
			<select id="brandFilter" class="form-select">
				<option value="">All Brands</option>
			</select>
		</div>
		<div class="col-md-2">
			<button id="clearFilters" class="btn btn-secondary w-100">Clear Filters</button>
		</div>
	</div>

<!-- Recommended Products Section -->
	<div class="recommendations-section" id="recommendationsSection" style="display:none;">
		<h3 class="mb-3"><i class="fas fa-star text-warning"></i> Recommended For You</h3>
		<p class="text-muted small mb-3">Based on your browsing history</p>
		<div id="recommendationsContainer" class="row"></div>
	</div>

	<!-- All Products Section -->
	<h2>Available Products</h2>
	<div id="productsContainer" class="row"></div>
</div>
<?php else: ?>
<!-- Guest user - show login/register prompt instead of products -->
<div class="mt-5">
	<div class="text-center">
		<div class="mb-4">
			<i class="fas fa-lock fa-4x text-muted"></i>
		</div>
		<h2>Welcome to Our Store!</h2>
		<p class="lead text-muted">Please login or create an account to browse our products.</p>
		
		<div class="d-flex justify-content-center gap-3 mt-4">
			<a href="login/login.php" class="btn btn-primary btn-lg">
				<i class="fas fa-sign-in-alt me-2"></i>Login
			</a>
			<a href="login/register.php" class="btn btn-outline-primary btn-lg">
				<i class="fas fa-user-plus me-2"></i>Register
			</a>
		</div>
		
		<div class="mt-5">
			<h5>Why create an account?</h5>
			<ul class="text-muted text-start d-inline-block">
				<li>Browse and purchase our exclusive products</li>
				<li>Track your orders easily</li>
				<li>Save your favorite items for later</li>
				<li>Get personalized recommendations</li>
			</ul>
		</div>
	</div>
</div>
<?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="js/cart.js" defer></script>

<?php 
// Include AI Chatbot for logged-in customers only
if (isset($_SESSION['user_id']) && !is_admin()): 
    $chat_context = 'home';
    include 'components/chat_widget.php';
endif; 
?>

<script>
let allProducts = [];
let filteredProducts = [];

$(document).ready(function(){
<?php if (is_logged_in() && !is_admin()): ?>
loadProducts();
updateCartCount();
loadFilters();
loadRecommendations();

$('#categoryFilter, #brandFilter').on('change', filterProducts);
$('#clearFilters').on('click', function() {
	$('#categoryFilter').val('');
	$('#brandFilter').val('');
	filterProducts();
});
<?php endif; ?>
});

function updateCartCount() {
$.getJSON('actions/get_cart_summary_action.php', function(data) {
if (data.status === 'success') {
$('#cartCount').text(data.total_items || 0);
}
});
}

function loadFilters() {
	// Load categories
	$.getJSON('actions/fetch_categories_public_action.php', function(resp) {
		if (resp.status === 'success' && resp.categories) {
			resp.categories.forEach(cat => {
				$('#categoryFilter').append(`<option value="${cat.cat_id}">${cat.cat_name}</option>`);
			});
		}
	});

	// Load brands
	$.getJSON('actions/fetch_brands_public_action.php', function(resp) {
		if (resp.status === 'success' && resp.brands) {
			resp.brands.forEach(brand => {
				$('#brandFilter').append(`<option value="${brand.brand_id}">${brand.brand_name}</option>`);
			});
		}
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

	displayProducts(filteredProducts);
}

function loadProducts() {
$.getJSON('actions/fetch_product_action.php', function(resp){
if (resp.status !== 'success') {
$('#productsContainer').html('<div class="alert alert-danger">Failed to load products</div>');
return;
}
allProducts = resp.products || [];
filteredProducts = allProducts;
if (allProducts.length === 0) {
$('#productsContainer').html('<div class="alert alert-info">No products available.</div>');
return;
}
displayProducts(filteredProducts);
}).fail(function(){
$('#productsContainer').html('<div class="alert alert-danger">Error loading products</div>');
});
}

function displayProducts(products) {
if (products.length === 0) {
$('#productsContainer').html('<div class="alert alert-info">No products found matching your filters.</div>');
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
<a href="view/single_product.php?id=${p.product_id}" class="btn btn-info btn-sm mb-2 d-block">View Details</a>
<div class="product-actions">
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
}

/**
 * Load personalized recommendations
 */
function loadRecommendations() {
    $.getJSON('actions/get_recommendations_action.php?limit=4', function(data) {
        if (data.status === 'success' && data.recommendations && data.recommendations.length > 0) {
            displayRecommendations(data.recommendations);
        }
    }).fail(function() {
        // Silently fail - recommendations are not critical
        console.log('Could not load recommendations');
    });
}

/**
 * Display recommendations in the container
 */
function displayRecommendations(products) {
    const container = $('#recommendationsContainer');
    const section = $('#recommendationsSection');
    
    if (!products || products.length === 0) {
        return;
    }
    
    let html = '';
    products.forEach(p => {
        const img = p.product_image ? 
            `<img src="${p.product_image}" class="card-img-top" alt="${p.product_title}">` : 
            '<div class="card-img-top bg-light d-flex align-items-center justify-content-center"><span class="text-muted">No Image</span></div>';
        
        html += `
        <div class="col-6 col-md-3 mb-3">
            <div class="card rec-product-card h-100">
                ${img}
                <div class="card-body">
                    <h6 class="card-title" title="${p.product_title}">${p.product_title}</h6>
                    <p class="card-text"><strong>GHS ${parseFloat(p.product_price).toFixed(2)}</strong></p>
                    <a href="view/single_product.php?id=${p.product_id}" class="btn btn-info btn-sm w-100">View Details</a>
                </div>
            </div>
        </div>
        `;
    });
    
    container.html(html);
    section.show();
}
</script>

<script src="js/interactions.js"></script>
</body>
</html>
