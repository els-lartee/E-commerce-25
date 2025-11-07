<?php 
session_start(); 
require_once 'settings/core.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Home - E-Commerce Store</title>
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
		.navbar {
			background: white;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			position: fixed;
			top: 0;
			left: 0;
			right: 0;
			z-index: 1000;
		}
		.nav-container {
			max-width: 1200px;
			margin: 0 auto;
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 15px 20px;
			flex-wrap: wrap;
			gap: 15px;
		}
		.navbar-brand {
			font-size: 24px;
			font-weight: bold;
			color: #007bff;
			text-decoration: none;
		}
		.nav-links {
			display: flex;
			align-items: center;
			gap: 20px;
			flex-wrap: wrap;
		}
		.nav-links a {
			text-decoration: none;
			color: #333;
			padding: 8px 15px;
			border-radius: 4px;
		}
		.nav-links a:hover {
			background: #f8f9fa;
		}
		.search-form {
			display: flex;
			gap: 5px;
		}
		.search-form input {
			padding: 8px 12px;
			border: 1px solid #ddd;
			border-radius: 4px;
			width: 200px;
		}
		.btn {
			padding: 8px 15px;
			border: none;
			border-radius: 4px;
			cursor: pointer;
			text-decoration: none;
			display: inline-block;
			font-size: 14px;
		}
		.btn-primary {
			background: #007bff;
			color: white;
		}
		.btn-primary:hover {
			background: #0056b3;
		}
		.btn-secondary {
			background: #6c757d;
			color: white;
		}
		.btn-secondary:hover {
			background: #5a6268;
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
		.btn-info {
			background: #17a2b8;
			color: white;
		}
		.btn-info:hover {
			background: #138496;
		}
		.btn-lg {
			padding: 12px 24px;
			font-size: 18px;
		}
		.container {
			max-width: 1200px;
			margin: 0 auto;
			padding: 20px;
			padding-top: 100px;
		}
		.welcome-section {
			text-align: center;
			margin-bottom: 50px;
		}
		.welcome-section h1 {
			font-size: 36px;
			margin-bottom: 15px;
		}
		.welcome-section p {
			color: #6c757d;
			font-size: 18px;
			margin-bottom: 20px;
		}
		.section-title {
			text-align: center;
			margin-bottom: 30px;
			font-size: 28px;
		}
		.products-grid {
			display: grid;
			grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
			gap: 20px;
		}
		.product-card {
			background: white;
			border-radius: 8px;
			overflow: hidden;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			transition: transform 0.2s, box-shadow 0.2s;
		}
		.product-card:hover {
			transform: translateY(-5px);
			box-shadow: 0 4px 15px rgba(0,0,0,0.15);
		}
		.product-image {
			width: 100%;
			height: 200px;
			object-fit: cover;
		}
		.product-image-placeholder {
			width: 100%;
			height: 200px;
			background: #f8f9fa;
			display: flex;
			align-items: center;
			justify-content: center;
			color: #6c757d;
		}
		.product-body {
			padding: 15px;
		}
		.product-title {
			font-size: 18px;
			font-weight: bold;
			margin-bottom: 10px;
		}
		.product-price {
			font-size: 20px;
			color: #28a745;
			font-weight: bold;
			margin-bottom: 8px;
		}
		.product-meta {
			font-size: 14px;
			color: #6c757d;
			margin-bottom: 15px;
		}
		.alert {
			padding: 15px;
			border-radius: 4px;
			margin-bottom: 20px;
		}
		.alert-danger {
			background: #f8d7da;
			color: #721c24;
			border: 1px solid #f5c6cb;
		}
		.alert-info {
			background: #d1ecf1;
			color: #0c5460;
			border: 1px solid #bee5eb;
		}
		@media (max-width: 768px) {
			.nav-container {
				flex-direction: column;
				align-items: stretch;
			}
			.nav-links {
				flex-direction: column;
				gap: 10px;
			}
			.search-form {
				flex-direction: column;
			}
			.search-form input {
				width: 100%;
			}
			.products-grid {
				grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
			}
		}
	</style>
</head>
<body>

	<nav class="navbar">
		<div class="nav-container">
			<a class="navbar-brand" href="index.php">E-Commerce Store</a>
			<div class="nav-links">
				<!-- <a href="index.php">Home</a>
				<a href="view/all_product.php">All Products</a> -->
				<form class="search-form" action="view/product_search_result.php" method="GET">
					<input type="search" name="q" placeholder="Search products..." required>
					<button class="btn btn-primary" type="submit">Search</button>
				</form>
				<?php if (isset($_SESSION['user_id'])): ?>
					<span style="color: #6c757d;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</span>
					<?php if (is_admin()): ?>
						<a href="admin/jewellery.php" class="btn btn-info">Jewellery</a>
						<a href="admin/brand.php" class="btn btn-info">Brand</a>
						<a href="admin/products.php" class="btn btn-info">Products</a>
					<?php endif; ?>
					<a href="login/logout.php" class="btn btn-danger">Logout</a>
				<?php else: ?>
					<a href="login/register.php" class="btn btn-primary">Register</a>
					<a href="login/login.php" class="btn btn-secondary">Login</a>
				<?php endif; ?>
			</div>
		</div>
	</nav>

	<div class="container">
		<div class="welcome-section">
			<h1>Welcome to Our Store</h1>
			<p>Browse our collection of quality products</p>
			<a href="view/all_product.php" class="btn btn-primary btn-lg">Shop Now</a>
		</div>

		<div>
			<h2 class="section-title">Featured Products</h2>
			<div id="productsContainer" class="products-grid"></div>
		</div>
	</div>
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script>
		$(document).ready(function(){
			loadProducts();
		});

		function loadProducts() {
			$.getJSON('actions/view_all_products_action.php', function(resp){
				if (resp.status !== 'success') {
					$('#productsContainer').html('<div class="alert alert-danger" style="grid-column: 1 / -1;">Failed to load products</div>');
					return;
				}
				const products = resp.products || [];
				if (products.length === 0) {
					$('#productsContainer').html('<div class="alert alert-info" style="grid-column: 1 / -1;">No products available.</div>');
					return;
				}
				const featuredProducts = products.slice(0, 6);
				let html = '';
				featuredProducts.forEach(p => {
					const img = p.product_image ? `<img src="${p.product_image}" class="product-image" alt="${p.product_title}">` : '<div class="product-image-placeholder">No Image</div>';
					html += `
						<div class="product-card">
							${img}
							<div class="product-body">
								<div class="product-title">${p.product_title}</div>
								<div class="product-price">$${parseFloat(p.product_price).toFixed(2)}</div>
								<div class="product-meta">Category: ${p.cat_name || 'N/A'} | Brand: ${p.brand_name || 'N/A'}</div>
								<a href="view/single_product.php?id=${p.product_id}" class="btn btn-primary">View Details</a>
							</div>
						</div>
					`;
				});
				$('#productsContainer').html(html);
			}).fail(function(){
				$('#productsContainer').html('<div class="alert alert-danger" style="grid-column: 1 / -1;">Error loading products</div>');
			});
		}
	</script>
</body>
</html>
