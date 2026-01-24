# TODO: Show products only to logged-in users

## Overview
Implement login requirement to view products across the e-commerce site.

## Tasks

### 1. index.php - Main Page
- [x] Modify to check if user is logged in
- [x] Show products only for logged-in users
- [x] Show login/register prompt for guests

### 2. view/all_product.php - All Products Page  
- [x] Add login check at the beginning
- [x] Show products only for logged-in users
- [x] Show login prompt for guests

### 3. view/product_search_result.php - Search Results Page
- [x] Add login check
- [x] Only show search results to logged-in users
- [x] Show login prompt for guests

### 4. view/single_product.php - Product Details Page
- [x] Add login check
- [x] Only show product details to logged-in users
- [x] Show login prompt for guests

### 5. js/login.js - Handle redirects
- [x] Get redirect URL from URL parameters
- [x] Redirect user back to original page after login

### 6. js/register.js - Handle redirects
- [x] Get redirect URL from URL parameters
- [x] Pass redirect URL through registration flow

## Implementation Steps - COMPLETED
1. [x] Modified index.php
2. [x] Modified view/all_product.php  
3. [x] Modified view/product_search_result.php
4. [x] Modified view/single_product.php
5. [x] Modified js/login.js
6. [x] Modified js/register.js

## Testing
- [ ] Test as guest - products should be hidden
- [ ] Test as logged-in user - products should be visible
- [ ] Test login/register redirects

