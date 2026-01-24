/**
 * User Interactions Tracker
 * Logs user interactions with products for the recommendation system
 */

document.addEventListener("DOMContentLoaded", function () {
    // Initialize interaction tracking for product cards
    initProductCardTracking();
    initAddToCartTracking();
    initSearchTracking();
});

/**
 * Track product card views
 */
function initProductCardTracking() {
    const productCards = document.querySelectorAll('.product-card, [data-product-id]');
    
    productCards.forEach(card => {
        const productId = card.dataset.productId || card.getAttribute('data-product-id');
        if (productId) {
            // Use Intersection Observer to track when product becomes visible
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        logInteraction(productId, 'view');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });
            
            observer.observe(card);
        }
    });
}

/**
 * Track add to cart button clicks
 */
function initAddToCartTracking() {
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-add-to-cart') || 
            e.target.closest('.btn-add-to-cart')) {
            const btn = e.target.classList.contains('btn-add-to-cart') ? 
                e.target : e.target.closest('.btn-add-to-cart');
            const productId = btn.dataset.productId || 
                            btn.getAttribute('data-product-id') ||
                            document.querySelector('.product-card[data-product-id]')?.dataset.productId;
            
            if (productId) {
                logInteraction(productId, 'add_to_cart');
            }
        }
    });
}

/**
 * Track search queries
 */
function initSearchTracking() {
    const searchForms = document.querySelectorAll('form[action*="search"]');
    searchForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const searchInput = form.querySelector('input[name="q"]');
            if (searchInput && searchInput.value) {
                // Store search query in session for recommendations
                sessionStorage.setItem('last_search_query', searchInput.value);
                logInteraction(0, 'search', 0, searchInput.value);
            }
        });
    });
}

/**
 * Log user interaction with a product
 * @param {number} productId - The product ID
 * @param {string} action - The type of interaction (view, add_to_cart, purchase, etc.)
 * @param {number} duration - Time spent viewing (in seconds)
 * @param {string} extraData - Additional data (like search query)
 */
function logInteraction(productId, action, duration = 0, extraData = '') {
    const data = {
        product_id: productId,
        action: action,
        duration: duration
    };
    
    if (extraData) {
        data.extra_data = extraData;
    }
    
    fetch('actions/log_interaction.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(data).toString()
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            console.log('Interaction logged:', action, 'for product', productId);
        } else {
            console.warn('Failed to log interaction:', data.message);
        }
    })
    .catch(error => {
        console.error('Error logging interaction:', error);
    });
}

/**
 * Track time spent on a product page
 * @param {number} productId - The product ID
 */
function trackProductTime(productId) {
    let startTime = Date.now();
    
    window.addEventListener('beforeunload', function() {
        const duration = Math.floor((Date.now() - startTime) / 1000);
        if (duration > 1) { // Only log if spent more than 1 second
            logInteraction(productId, 'view', duration);
        }
    });
}

/**
 * Track AR try-on feature usage
 * @param {number} productId - The product ID
 */
function startARTryOn(productId) {
    logInteraction(productId, 'ar_tryon');
    // Your AR logic continues here
}

/**
 * Get personalized recommendations
 * @param {number} limit - Number of recommendations to fetch
 * @returns {Promise} Promise resolving to recommended products
 */
function getRecommendations(limit = 6) {
    return fetch(`actions/get_recommendations_action.php?limit=${limit}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                return data.recommendations || [];
            }
            return [];
        })
        .catch(error => {
            console.error('Error fetching recommendations:', error);
            return [];
        });
}

/**
 * Display recommendations in a container
 * @param {string} containerId - ID of the container element
 * @param {number} limit - Number of recommendations to show
 */
function displayRecommendations(containerId, limit = 6) {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    getRecommendations(limit).then(products => {
        if (products.length === 0) {
            container.innerHTML = '<div class="alert alert-info">No recommendations available yet. Start browsing to get personalized suggestions!</div>';
            return;
        }
        
        let html = '';
        products.forEach(p => {
            const img = p.product_image ? 
                `<img src="${p.product_image}" class="card-img-top" style="height:150px; object-fit:cover;" alt="${p.product_title}">` : 
                '<div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:150px;"><span class="text-muted">No Image</span></div>';
            
            html += `
            <div class="col-md-2 mb-3">
                <div class="card h-100">
                    ${img}
                    <div class="card-body p-2">
                        <h6 class="card-title" style="font-size:0.9rem;">${p.product_title}</h6>
                        <p class="card-text"><strong>$${p.product_price}</strong></p>
                        <a href="view/single_product.php?id=${p.product_id}" class="btn btn-info btn-sm">View</a>
                    </div>
                </div>
            </div>
            `;
        });
        
        container.innerHTML = html;
    });
}

