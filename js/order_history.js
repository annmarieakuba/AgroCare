/**
 * Order History Management JavaScript
 * Handles fetching and displaying customer order history
 */

// Get base path - ensure it ends with /
const basePath = (window.APP_BASE_PATH || '').replace(/\/$/, '') + '/';

// Format currency
function formatCurrency(amount) {
    return `₵${Number(amount).toFixed(2)}`;
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-GB', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Get status badge class
function getStatusClass(status) {
    const statusLower = status.toLowerCase();
    if (statusLower === 'completed') return 'status-completed';
    if (statusLower === 'pending') return 'status-pending';
    if (statusLower === 'cancelled') return 'status-cancelled';
    return 'status-pending';
}

// Load orders on page load
document.addEventListener('DOMContentLoaded', function() {
    loadOrders();
});

// Load all orders
function loadOrders() {
    const container = document.getElementById('ordersContainer');
    
    // Construct action path (basePath already ends with /)
    const actionPath = `${basePath}actions/get_customer_orders_action.php`;
    
    console.log('Fetching orders from:', actionPath); // Debug log
    
    fetch(actionPath, {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => {
        // Check if response is ok
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        // Get response text first to check for errors
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Invalid JSON response:', text);
                throw new Error('Invalid response from server. Please check console for details.');
            }
        });
    })
    .then(data => {
        if (data.success) {
            if (data.data && data.data.length > 0) {
                displayOrders(data.data);
            } else {
                displayEmptyState();
            }
        } else {
            displayError(data.message || 'Failed to load orders');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        displayError('An error occurred while loading your orders: ' + error.message);
    });
}

// Display orders
function displayOrders(orders) {
    const container = document.getElementById('ordersContainer');
    
    if (orders.length === 0) {
        displayEmptyState();
        return;
    }
    
    container.innerHTML = orders.map(order => `
        <div class="order-card">
            <div class="order-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">
                            <i class="fas fa-receipt me-2"></i>Order #${order.order_id}
                        </h5>
                        <small class="opacity-75">
                            <i class="fas fa-calendar me-1"></i>${formatDate(order.order_date)}
                        </small>
                    </div>
                    <div class="text-end">
                        <div class="status-badge ${getStatusClass(order.order_status)}">
                            ${order.order_status.charAt(0).toUpperCase() + order.order_status.slice(1)}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="p-3">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-file-invoice me-2"></i>Invoice:</strong>
                            <span class="text-muted">${order.invoice_no}</span>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <strong><i class="fas fa-box me-2"></i>Items:</strong>
                            <span class="text-muted">${order.total_items} item(s)</span>
                        </div>
                    </div>
                    
                    <!-- Order Items Preview (first 3 items) -->
                    <div class="mb-3">
                        ${order.items.slice(0, 3).map(item => `
                            <div class="order-item d-flex align-items-center">
                                <img src="${basePath}${item.product_image || 'images/placeholder.png'}" 
                                     alt="${item.product_title}" 
                                     class="product-image me-3"
                                     onerror="this.src='${basePath}images/placeholder.png'">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">${item.product_title}</div>
                                    <small class="text-muted">
                                        ${item.quantity} × ${formatCurrency(item.unit_price)}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold text-success">${formatCurrency(item.line_total)}</div>
                                </div>
                            </div>
                        `).join('')}
                        ${order.items.length > 3 ? `
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    +${order.items.length - 3} more item(s)
                                </small>
                            </div>
                        ` : ''}
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <div>
                            <strong>Total Amount:</strong>
                            <span class="h5 text-success ms-2">${formatCurrency(order.total_amount)}</span>
                        </div>
                        <button class="btn btn-outline-success btn-sm" onclick="viewOrderDetails(${order.order_id})">
                            <i class="fas fa-eye me-1"></i>View Details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Display empty state
function displayEmptyState() {
    const container = document.getElementById('ordersContainer');
    container.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-receipt"></i>
            <h4 class="text-muted">No Orders Yet</h4>
            <p class="text-muted mb-4">You haven't placed any orders yet. Start shopping to see your orders here!</p>
            <a href="all_product.php" class="btn btn-success btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
            </a>
        </div>
    `;
}

// Display error
function displayError(message) {
    const container = document.getElementById('ordersContainer');
    container.innerHTML = `
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>${message}
        </div>
    `;
}

// View order details
function viewOrderDetails(orderId) {
    // Find the order data from the displayed orders
    const actionPath = `${basePath}actions/get_customer_orders_action.php`;
    
    fetch(actionPath, {
        method: 'GET',
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.data) {
            const order = data.data.find(o => o.order_id === orderId);
            if (order) {
                showOrderDetailsModal(order);
            } else {
                alert('Order not found');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load order details');
    });
}

// Show order details modal
function showOrderDetailsModal(order) {
    const modalContent = document.getElementById('orderDetailsContent');
    
    modalContent.innerHTML = `
        <div class="mb-4">
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Order ID:</strong> #${order.order_id}
                </div>
                <div class="col-md-6">
                    <strong>Invoice Number:</strong> ${order.invoice_no}
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <strong>Order Date:</strong> ${formatDate(order.order_date)}
                </div>
                <div class="col-md-6">
                    <strong>Status:</strong>
                    <span class="status-badge ${getStatusClass(order.order_status)} ms-2">
                        ${order.order_status.charAt(0).toUpperCase() + order.order_status.slice(1)}
                    </span>
                </div>
            </div>
            ${order.payment ? `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Payment Method:</strong> ${order.payment.method}
                    </div>
                    <div class="col-md-6">
                        <strong>Payment Reference:</strong> ${order.payment.reference || 'N/A'}
                    </div>
                </div>
            ` : ''}
        </div>
        
        <h6 class="mb-3"><i class="fas fa-shopping-bag me-2"></i>Order Items</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${order.items.map(item => `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${basePath}${item.product_image || 'images/placeholder.png'}" 
                                         alt="${item.product_title}" 
                                         class="product-image me-2"
                                         onerror="this.src='${basePath}images/placeholder.png'">
                                    <div>
                                        <div class="fw-semibold">${item.product_title}</div>
                                        ${item.product_desc ? `<small class="text-muted">${item.product_desc.substring(0, 50)}...</small>` : ''}
                                    </div>
                                </div>
                            </td>
                            <td>${item.quantity}</td>
                            <td>${formatCurrency(item.unit_price)}</td>
                            <td class="text-end fw-bold">${formatCurrency(item.line_total)}</td>
                        </tr>
                    `).join('')}
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">Total Amount:</th>
                        <th class="text-end text-success">${formatCurrency(order.total_amount)}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
    modal.show();
}

