// AgroCare Farm Product Management JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Load products, categories, and brands on page load
    loadProducts();
    loadCategories();
    loadBrands();
    
    // Add product form submission
    document.getElementById('addProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        addProduct();
    });
    
    // Edit product form submission
    document.getElementById('editProductForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateProduct();
    });
});

// Load all products
function loadProducts() {
    fetch('../actions/product_actions.php?action=get_all')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayProducts(data.data);
            } else {
                showAlert('Error loading products: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading products', 'danger');
        });
}

// Load categories for dropdowns
function loadCategories() {
    fetch('../actions/fetch_category_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateCategoryDropdowns(data.data);
            } else {
                showAlert('Error loading categories: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading categories', 'danger');
        });
}

// Load brands for dropdowns
function loadBrands() {
    fetch('../actions/fetch_brand_action.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateBrandDropdowns(data.data);
            } else {
                showAlert('Error loading brands: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading brands', 'danger');
        });
}

// Populate category dropdowns
function populateCategoryDropdowns(categories) {
    const addDropdown = document.getElementById('addProductCategory');
    const editDropdown = document.getElementById('editProductCategory');
    
    // Clear existing options except the first one
    addDropdown.innerHTML = '<option value="">Select a category...</option>';
    editDropdown.innerHTML = '<option value="">Select a category...</option>';
    
    categories.forEach(category => {
        const option = `<option value="${category.cat_id}">${category.cat_name}</option>`;
        addDropdown.innerHTML += option;
        editDropdown.innerHTML += option;
    });
}

// Populate brand dropdowns
function populateBrandDropdowns(brands) {
    const addDropdown = document.getElementById('addProductBrand');
    const editDropdown = document.getElementById('editProductBrand');
    
    // Clear existing options except the first one
    addDropdown.innerHTML = '<option value="">Select a brand...</option>';
    editDropdown.innerHTML = '<option value="">Select a brand...</option>';
    
    brands.forEach(brand => {
        const option = `<option value="${brand.brand_id}">${brand.brand_name}</option>`;
        addDropdown.innerHTML += option;
        editDropdown.innerHTML += option;
    });
}

// Display products in table
function displayProducts(products) {
    const tbody = document.getElementById('productsTableBody');
    const noProductsMessage = document.getElementById('noProductsMessage');
    
    if (products.length === 0) {
        tbody.innerHTML = '';
        noProductsMessage.style.display = 'block';
        return;
    }
    
    noProductsMessage.style.display = 'none';
    
    tbody.innerHTML = products.map(product => `
        <tr>
            <td>${product.product_id}</td>
            <td>
                ${product.product_image ? 
                    `<img src="../${product.product_image}" alt="${product.product_title}" class="product-thumbnail" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">` : 
                    '<i class="fas fa-image text-muted" style="font-size: 24px;"></i>'
                }
            </td>
            <td>${product.product_title}</td>
            <td>${product.cat_name || 'N/A'}</td>
            <td>${product.brand_name || 'N/A'}</td>
            <td>₵${parseFloat(product.product_price).toFixed(2)}</td>
            <td>
                <button class="btn btn-sm btn-outline-primary me-2" onclick="editProduct(${product.product_id})">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${product.product_id})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </td>
        </tr>
    `).join('');
}

// Add new product
function addProduct() {
    const form = document.getElementById('addProductForm');
    const formData = new FormData(form);
    
    // Validate form
    if (!validateProductForm(formData)) {
        return;
    }
    
    // Check if a file was actually selected
    const imageInput = document.getElementById('addProductImage');
    const hasImage = imageInput && imageInput.files && imageInput.files.length > 0 && imageInput.files[0].size > 0;
    
    if (hasImage) {
        // Upload image first
        uploadImage(formData, 'add');
    } else {
        // No image selected, proceed without image
        formData.append('product_image', '');
        submitProductForm(formData, 'add');
    }
}

// Update product
function updateProduct() {
    const form = document.getElementById('editProductForm');
    const formData = new FormData(form);
    
    // Validate form
    if (!validateProductForm(formData)) {
        return;
    }
    
    // Get current image path (to preserve if no new image is uploaded)
    const currentImage = document.getElementById('editProductCurrentImage').value;
    const imageInput = document.getElementById('editProductImage');
    
    // Check if a new file was actually selected
    // formData.get('product_image') might return an empty File object even when no file is selected
    const hasNewImage = imageInput && imageInput.files && imageInput.files.length > 0 && imageInput.files[0].size > 0;
    
    if (hasNewImage) {
        // New image selected, upload it
        uploadImage(formData, 'edit');
    } else {
        // No new image selected, keep the current one
        // Remove the empty file from formData and set the current image path
        formData.delete('product_image');
        formData.append('product_image', currentImage || '');
        submitProductForm(formData, 'edit');
    }
}

// Upload image
function uploadImage(formData, action) {
    const imageInput = action === 'edit' ? document.getElementById('editProductImage') : document.getElementById('addProductImage');
    
    if (!imageInput || !imageInput.files || imageInput.files.length === 0) {
        showAlert('Please select an image file', 'danger');
        return;
    }
    
    const imageFile = imageInput.files[0];
    if (imageFile.size === 0) {
        showAlert('Selected file is empty', 'danger');
        return;
    }
    
    const imageFormData = new FormData();
    imageFormData.append('product_image', imageFile);
    imageFormData.append('user_id', 1); // Default user ID
    imageFormData.append('product_id', action === 'edit' ? formData.get('product_id') : 0);
    
    // Show loading state
    const submitBtn = action === 'edit' 
        ? document.querySelector('#editProductForm button[type="submit"]')
        : document.querySelector('#addProductForm button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Uploading...';
    
    // Log upload attempt
    console.log('Uploading image:', {
        fileName: imageFile.name,
        fileSize: imageFile.size,
        fileType: imageFile.type,
        productId: action === 'edit' ? formData.get('product_id') : 0
    });
    
    fetch('../actions/upload_product_image_action.php', {
        method: 'POST',
        body: imageFormData
    })
    .then(response => {
        // Get response text first to see what we're getting
        return response.text().then(text => {
            console.log('Upload response:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Failed to parse JSON:', text);
                throw new Error('Invalid response from server: ' + text.substring(0, 100));
            }
        });
    })
    .then(data => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        
        if (data.success) {
            // Remove the file from formData and set the returned path
            formData.delete('product_image');
            formData.append('product_image', data.file_path);
            submitProductForm(formData, action);
        } else {
            console.error('Upload failed:', data);
            showAlert(data.message || 'Error uploading image', 'danger');
        }
    })
    .catch(error => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        console.error('Upload error:', error);
        showAlert('Error uploading image: ' + error.message, 'danger');
    });
}

// Submit product form
function submitProductForm(formData, action) {
    const url = action === 'add' ? '../actions/add_product_action.php' : '../actions/update_product_action.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            const modalId = action === 'add' ? 'addProductModal' : 'editProductModal';
            document.getElementById(action === 'add' ? 'addProductForm' : 'editProductForm').reset();
            bootstrap.Modal.getInstance(document.getElementById(modalId)).hide();
            loadProducts(); // Reload products
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert(`Error ${action === 'add' ? 'adding' : 'updating'} product`, 'danger');
    });
}

// Edit product
function editProduct(productId) {
    fetch(`../actions/product_actions.php?action=get_single&id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const product = data.data;
                document.getElementById('editProductId').value = product.product_id;
                document.getElementById('editProductTitle').value = product.product_title;
                document.getElementById('editProductPrice').value = product.product_price;
                document.getElementById('editProductCategory').value = product.product_cat;
                document.getElementById('editProductBrand').value = product.product_brand;
                document.getElementById('editProductDesc').value = product.product_desc || '';
                document.getElementById('editProductKeywords').value = product.product_keywords || '';
                
                // Store current image path and show preview if exists
                const currentImageInput = document.getElementById('editProductCurrentImage');
                const imagePreview = document.getElementById('currentImagePreview');
                const imagePreviewImg = document.getElementById('currentImagePreviewImg');
                const imageInput = document.getElementById('editProductImage');
                
                if (product.product_image) {
                    currentImageInput.value = product.product_image;
                    imagePreviewImg.src = '../' + product.product_image;
                    imagePreview.style.display = 'block';
                } else {
                    currentImageInput.value = '';
                    imagePreview.style.display = 'none';
                }
                
                // Reset file input
                imageInput.value = '';
                
                const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
                modal.show();
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error loading product details', 'danger');
        });
}

// Delete product
function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this AgroCare farm product?')) {
        const formData = new FormData();
        formData.append('product_id', productId);
        
        fetch('../actions/delete_product_action.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, 'success');
                loadProducts(); // Reload products
            } else {
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('Error deleting product', 'danger');
        });
    }
}

// Validate product form
function validateProductForm(formData) {
    const productTitle = formData.get('product_title');
    const productPrice = formData.get('product_price');
    const productCat = formData.get('product_cat');
    const productBrand = formData.get('product_brand');
    
    // Clear previous validation
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    
    let isValid = true;
    
    // Validate product title
    if (!productTitle || productTitle.trim().length === 0) {
        const input = document.querySelector('input[name="product_title"]');
        const feedback = input.nextElementSibling;
        input.classList.add('is-invalid');
        feedback.textContent = 'Product title is required';
        isValid = false;
    } else if (productTitle.length > 200) {
        const input = document.querySelector('input[name="product_title"]');
        const feedback = input.nextElementSibling;
        input.classList.add('is-invalid');
        feedback.textContent = 'Product title must be 200 characters or less';
        isValid = false;
    }
    
    // Validate product price
    if (!productPrice || parseFloat(productPrice) <= 0) {
        const input = document.querySelector('input[name="product_price"]');
        const feedback = input.parentElement.nextElementSibling;
        input.classList.add('is-invalid');
        feedback.textContent = 'Product price must be greater than 0';
        isValid = false;
    }
    
    // Validate category
    if (!productCat || productCat === '') {
        const select = document.querySelector('select[name="product_cat"]');
        const feedback = select.nextElementSibling;
        select.classList.add('is-invalid');
        feedback.textContent = 'Please select a category';
        isValid = false;
    }
    
    // Validate brand
    if (!productBrand || productBrand === '') {
        const select = document.querySelector('select[name="product_brand"]');
        const feedback = select.nextElementSibling;
        select.classList.add('is-invalid');
        feedback.textContent = 'Please select a brand';
        isValid = false;
    }
    
    return isValid;
}

// Show alert message
function showAlert(message, type) {
    const alertContainer = document.getElementById('alertContainer');
    const alertId = 'alert-' + Date.now();
    
    const alertHTML = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto-remove alert after 5 seconds
    setTimeout(() => {
        const alert = document.getElementById(alertId);
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
