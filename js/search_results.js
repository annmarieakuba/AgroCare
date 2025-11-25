// Search Results Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    loadCategories();
    performSearch();
});

// Load categories for filter dropdown
function loadCategories() {
    const basePath = window.APP_BASE_PATH || '';
    fetch(basePath + 'actions/fetch_category_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                populateCategoryFilter(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        });
}

// Populate category filter dropdown
function populateCategoryFilter(categories) {
    const categoryFilter = document.getElementById('categoryFilter');
    if (!categoryFilter) return;

    // Keep the "All Categories" option
    const allOption = categoryFilter.querySelector('option[value=""]');
    categoryFilter.innerHTML = '';
    if (allOption) {
        categoryFilter.appendChild(allOption);
    }

    categories.forEach(category => {
        const option = document.createElement('option');
        option.value = category.cat_id;
        option.textContent = category.cat_name;
        if (searchCategory && category.cat_id == searchCategory) {
            option.selected = true;
        }
        categoryFilter.appendChild(option);
    });
}

// Perform search
function performSearch() {
    const container = document.getElementById('searchResultsContainer');
    if (!container) return;

    const basePath = window.APP_BASE_PATH || '';
    let searchUrl = basePath + 'actions/product_actions.php?action=search&query=' + encodeURIComponent(searchQuery);

    // Add category filter if selected
    if (searchCategory && searchCategory > 0) {
        // If category is selected, we need to filter by category first, then search within results
        // For now, let's do a combined search
        fetch(basePath + 'actions/product_actions.php?action=filter_category&cat_id=' + searchCategory)
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.success && data.data) {
                        // Filter the category results by search query
                        const filtered = data.data.filter(product => {
                            const queryLower = searchQuery.toLowerCase();
                            return (
                                (product.product_title && product.product_title.toLowerCase().includes(queryLower)) ||
                                (product.product_keywords && product.product_keywords.toLowerCase().includes(queryLower)) ||
                                (product.product_desc && product.product_desc.toLowerCase().includes(queryLower))
                            );
                        });
                        displaySearchResults(filtered);
                    } else {
                        displayNoResults();
                    }
                } catch (e) {
                    console.error('Error parsing category filter response:', e);
                    performSimpleSearch();
                }
            })
            .catch(error => {
                console.error('Error filtering by category:', error);
                performSimpleSearch();
            });
    } else {
        performSimpleSearch();
    }
}

// Perform simple search (no category filter)
function performSimpleSearch() {
    const basePath = window.APP_BASE_PATH || '';
    const searchUrl = basePath + 'actions/product_actions.php?action=search&query=' + encodeURIComponent(searchQuery);

    fetch(searchUrl)
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success && data.data) {
                    displaySearchResults(data.data);
                } else {
                    displayNoResults();
                }
            } catch (e) {
                console.error('Error parsing search response:', e);
                displayError('Error processing search results. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error performing search:', error);
            displayError('An error occurred while searching. Please try again.');
        });
}

// Display search results
function displaySearchResults(products) {
    const container = document.getElementById('searchResultsContainer');
    if (!container) return;

    if (!products || products.length === 0) {
        displayNoResults();
        return;
    }

    const currencySymbol = '₵'; // GHS symbol

    container.innerHTML = `
        <div class="mb-4">
            <h3 class="fw-bold" style="color: #2d5016;">
                <i class="fas fa-search me-2"></i>Search Results
            </h3>
            <p class="text-muted">Found ${products.length} product(s) matching "${searchQuery}"</p>
        </div>
        <div class="row g-4" id="productsGrid">
            ${products.map(product => `
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card-search">
                        <div class="product-image-search">
                            ${product.product_image ? 
                                `<img src="${window.APP_BASE_PATH || ''}${product.product_image}" alt="${product.product_title}" loading="lazy">` : 
                                `<i class="fas fa-apple-alt"></i>`
                            }
                        </div>
                        <div class="card-body p-3">
                            <h5 class="fw-bold mb-2" style="color: #2d5016;">${escapeHtml(product.product_title)}</h5>
                            <p class="text-muted small mb-2">
                                <i class="fas fa-leaf me-1"></i>${escapeHtml(product.cat_name || 'N/A')}
                                ${product.brand_name ? ` | <i class="fas fa-tags me-1"></i>${escapeHtml(product.brand_name)}` : ''}
                            </p>
                            <div class="product-price-search">${currencySymbol}${parseFloat(product.product_price || 0).toFixed(2)}</div>
                            ${product.product_desc ? `<p class="text-muted small mb-3">${escapeHtml(product.product_desc.substring(0, 100))}${product.product_desc.length > 100 ? '...' : ''}</p>` : ''}
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm flex-fill" 
                                        style="background: linear-gradient(135deg, #2d5016, #4a7c59); color: white; border: none;"
                                        onclick="viewProductDetails(${product.product_id})">
                                    <i class="fas fa-eye me-1"></i>View
                                </button>
                                <button class="btn btn-sm flex-fill" 
                                        style="background: linear-gradient(135deg, #4a7c59, #2d5016); color: white; border: none;"
                                        onclick="addToCartFromSearch(${product.product_id})">
                                    <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('')}
        </div>
    `;
}

// Display no results message
function displayNoResults() {
    const container = document.getElementById('searchResultsContainer');
    if (!container) return;

    container.innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-search" style="font-size: 4rem; color: #ccc; margin-bottom: 20px;"></i>
            <h3 class="fw-bold mb-3" style="color: #2d5016;">No Products Found</h3>
            <p class="text-muted mb-4">We couldn't find any products matching "${searchQuery}"</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="all_product.php" class="btn" style="background: linear-gradient(135deg, #2d5016, #4a7c59); color: white;">
                    <i class="fas fa-apple-alt me-2"></i>Browse All Products
                </a>
                <a href="product_search_result.php" class="btn btn-outline-secondary">
                    <i class="fas fa-search me-2"></i>Try Another Search
                </a>
            </div>
        </div>
    `;
}

// Display error message
function displayError(message) {
    const container = document.getElementById('searchResultsContainer');
    if (!container) return;

    container.innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: #dc3545; margin-bottom: 20px;"></i>
            <h3 class="fw-bold mb-3" style="color: #dc3545;">Error</h3>
            <p class="text-muted mb-4">${escapeHtml(message)}</p>
            <button class="btn btn-primary" onclick="location.reload()">
                <i class="fas fa-redo me-2"></i>Retry
            </button>
        </div>
    `;
}

// View product details
function viewProductDetails(productId) {
    window.location.href = `all_product.php?product_id=${productId}`;
}

// Add to cart from search results
function addToCartFromSearch(productId) {
    const basePath = window.APP_BASE_PATH || '';
    fetch(basePath + 'actions/add_to_cart_action.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const btn = event.target.closest('button');
            const originalHTML = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check me-1"></i>Added!';
            btn.style.background = '#28a745';
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.style.background = 'linear-gradient(135deg, #4a7c59, #2d5016)';
            }, 2000);
            
            // Update cart count
            if (typeof updateCartCount === 'function') {
                updateCartCount();
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to add product to cart. Please try again.',
                    confirmButtonColor: '#2d5016'
                });
            } else {
                alert('Failed to add product to cart. Please try again.');
            }
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'An error occurred. Please try again.',
                confirmButtonColor: '#2d5016'
            });
        } else {
            alert('An error occurred. Please try again.');
        }
    });
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

