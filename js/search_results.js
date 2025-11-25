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

    // Store products globally for modal access
    window.searchResults = products;

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
    const products = window.searchResults || [];
    const product = products.find(p => p.product_id == productId);
    if (!product) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Product not found.',
            confirmButtonColor: '#2d5016'
        });
        return;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    const modalTitle = document.getElementById('productModalTitle');
    const modalBody = document.getElementById('productModalBody');
    const addToCartBtn = document.getElementById('addToCartBtn');
    
    modalTitle.innerHTML = `<i class="fas fa-apple-alt me-2"></i>${escapeHtml(product.product_title)}`;
    
    const basePath = window.APP_BASE_PATH || '';
    modalBody.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                ${product.product_image ? 
                    `<img src="${basePath}${product.product_image}" alt="${escapeHtml(product.product_title)}" class="img-fluid rounded">` : 
                    `<div class="bg-light text-center rounded py-5">
                        <i class="fas fa-apple-alt fa-4x text-muted"></i>
                    </div>`
                }
            </div>
            <div class="col-md-6">
                <h4>${escapeHtml(product.product_title)}</h4>
                <div class="h3 text-success fw-bold mb-3">₵${parseFloat(product.product_price || 0).toFixed(2)}</div>
                
                <div class="mb-3">
                    <div class="mb-2">
                        <span class="text-muted"><i class="fas fa-leaf me-2"></i>Category:</span>
                        <span class="fw-semibold">${escapeHtml(product.cat_name || 'N/A')}</span>
                    </div>
                    ${product.brand_name ? `
                    <div class="mb-2">
                        <span class="text-muted"><i class="fas fa-tags me-2"></i>Brand:</span>
                        <span class="fw-semibold">${escapeHtml(product.brand_name)}</span>
                    </div>
                    ` : ''}
                    ${product.product_keywords ? `
                    <div class="mb-2">
                        <span class="text-muted"><i class="fas fa-key me-2"></i>Keywords:</span>
                        <span class="fw-semibold">${escapeHtml(product.product_keywords)}</span>
                    </div>
                    ` : ''}
                </div>
                
                <div class="product-detail-description">
                    <h6 class="fw-bold mb-2">Description:</h6>
                    <p>${escapeHtml(product.product_desc || 'No description available for this product.')}</p>
                </div>
            </div>
        </div>
    `;
    
    addToCartBtn.onclick = () => addToCartFromSearch(productId);
    
    modal.show();
}

// Add to cart from search results
async function addToCartFromSearch(productId) {
    const products = window.searchResults || [];
    const product = products.find(p => p.product_id == productId);
    const productName = product ? product.product_title : 'Product';

    if (!window.CartAPI) {
        Swal.fire({
            icon: 'error',
            title: 'Cart Error',
            text: 'Cart system is not ready. Please refresh the page and try again.',
            confirmButtonColor: '#2d5016'
        });
        return;
    }

    try {
        const addBtn = document.getElementById('addToCartBtn');
        if (addBtn) {
            addBtn.disabled = true;
            addBtn.innerHTML = `<span class="spinner-border spinner-border-sm me-2"></span>Adding...`;
        }

        await CartAPI.addToCart(productId, 1);
        
        Swal.fire({
            icon: 'success',
            title: 'Added to Cart!',
            text: `"${productName}" has been added to your cart!`,
            confirmButtonColor: '#2d5016',
            timer: 2000,
            showConfirmButton: true
        });

        // Close modal if open
        const modal = bootstrap.Modal.getInstance(document.getElementById('productModal'));
        if (modal) {
            modal.hide();
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'Unable to add item to cart.',
            confirmButtonColor: '#2d5016'
        });
    } finally {
        const addBtn = document.getElementById('addToCartBtn');
        if (addBtn) {
            addBtn.disabled = false;
            addBtn.innerHTML = `<i class="fas fa-shopping-cart me-2"></i>Add to Cart`;
        }
    }
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

