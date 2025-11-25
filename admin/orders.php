<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

require_once __DIR__ . '/../settings/core.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: ../login/login.php');
    exit;
}

// Check if user is admin
if (!is_admin()) {
    header('Location: ../login/login.php');
    exit;
}

require_once __DIR__ . '/../settings/db_class.php';

$db = new db_connection();
if (!$db->db_connect()) {
    die("Database connection failed. Please check your database configuration. Error: " . mysqli_connect_error());
}
$conn = $db->db;

// Get all orders with details
$ordersQuery = "
    SELECT o.order_id, o.order_date, o.order_status,
           c.customer_name, c.customer_email,
           COALESCE(SUM(od.qty * COALESCE(od.unit_price, p.product_price)), 0) as total_amount,
           COUNT(od.product_id) as item_count
    FROM orders o
    LEFT JOIN customer c ON o.customer_id = c.customer_id
    LEFT JOIN orderdetails od ON o.order_id = od.order_id
    LEFT JOIN products p ON od.product_id = p.product_id
    GROUP BY o.order_id, o.order_date, o.order_status, c.customer_name, c.customer_email
    ORDER BY o.order_date DESC
";
$ordersResult = mysqli_query($conn, $ordersQuery);
if (!$ordersResult) {
    error_log("Orders page query failed: " . mysqli_error($conn));
}
$orders = $ordersResult ? mysqli_fetch_all($ordersResult, MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Orders Management - AgroCare Farm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .admin-nav {
            background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="dashboard.php">
                <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="category.php"><i class="fas fa-leaf me-1"></i>Categories</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="brand.php"><i class="fas fa-tags me-1"></i>Brands</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="product.php"><i class="fas fa-apple-alt me-1"></i>Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="orders.php"><i class="fas fa-shopping-cart me-1"></i>Orders</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php"><i class="fas fa-home me-1"></i>View Site</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../logout.php"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="fw-bold"><i class="fas fa-shopping-cart me-2"></i>Orders Management</h1>
                <p class="text-muted">View and manage all customer orders</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($orders)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No orders found</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo $order['order_id']; ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($order['order_date'])); ?></td>
                                        <td>
                                            <div><?php echo htmlspecialchars($order['customer_name'] ?? 'Guest'); ?></div>
                                            <small class="text-muted"><?php echo htmlspecialchars($order['customer_email'] ?? ''); ?></small>
                                        </td>
                                        <td><?php echo $order['item_count']; ?> items</td>
                                        <td>₵<?php echo number_format($order['total_amount'] ?? 0, 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $order['order_status'] === 'completed' ? 'success' : 
                                                    ($order['order_status'] === 'pending' ? 'warning' : 'danger'); 
                                            ?>">
                                                <?php echo ucfirst($order['order_status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary" onclick="viewOrderDetails(<?php echo $order['order_id']; ?>)">
                                                <i class="fas fa-eye"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const basePath = '<?php 
            $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
            $baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
            if (substr($baseDir, -6) === '/admin') {
                $baseDir = substr($baseDir, 0, -6);
            }
            $appBasePath = ($baseDir === '' || $baseDir === '.') ? '/' : $baseDir . '/';
            echo htmlspecialchars($appBasePath, ENT_QUOTES); 
        ?>';
        
        function viewOrderDetails(orderId) {
            // Show loading
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching order details',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Fetch order details
            fetch(`../actions/get_admin_order_details.php?order_id=${orderId}`, {
                method: 'GET',
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const order = data.data.order;
                    const items = data.data.items;
                    const payment = data.data.payment;
                    
                    // Build items HTML
                    let itemsHtml = '<div class="table-responsive" style="max-height: 400px; overflow-y: auto;"><table class="table table-sm table-bordered">';
                    itemsHtml += '<thead><tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr></thead><tbody>';
                    
                    items.forEach(item => {
                        itemsHtml += `
                            <tr>
                                <td>${escapeHtml(item.product_title)}</td>
                                <td>${item.quantity}</td>
                                <td>₵${item.unit_price.toFixed(2)}</td>
                                <td>₵${item.line_total.toFixed(2)}</td>
                            </tr>
                        `;
                    });
                    
                    itemsHtml += '</tbody></table></div>';
                    
                    // Build order info HTML
                    let orderInfoHtml = `
                        <div style="text-align: left; margin-bottom: 15px;">
                            <p><strong>Order ID:</strong> #${order.order_id}</p>
                            <p><strong>Invoice:</strong> ${escapeHtml(order.invoice_no || 'N/A')}</p>
                            <p><strong>Date:</strong> ${formatDate(order.order_date)}</p>
                            <p><strong>Status:</strong> <span class="badge bg-${getStatusColor(order.order_status)}">${escapeHtml(order.order_status)}</span></p>
                            <p><strong>Customer:</strong> ${escapeHtml(order.customer_name || 'Guest')}</p>
                            <p><strong>Email:</strong> ${escapeHtml(order.customer_email || 'N/A')}</p>
                            ${payment ? `
                                <p><strong>Payment Method:</strong> ${escapeHtml(payment.payment_method || 'N/A')}</p>
                                <p><strong>Payment Reference:</strong> ${escapeHtml(payment.payment_reference || 'N/A')}</p>
                            ` : ''}
                            <p><strong>Total Amount:</strong> <span style="font-size: 1.2em; color: #2d5016; font-weight: bold;">₵${data.data.total_amount.toFixed(2)}</span></p>
                        </div>
                    `;
                    
                    // Show SweetAlert with order details
                    Swal.fire({
                        title: `Order #${order.order_id} Details`,
                        html: orderInfoHtml + '<hr><h6 style="text-align: left; margin-top: 15px;">Order Items:</h6>' + itemsHtml,
                        width: '700px',
                        confirmButtonText: 'Close',
                        confirmButtonColor: '#2d5016',
                        customClass: {
                            popup: 'text-start'
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Failed to load order details',
                        confirmButtonColor: '#2d5016'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load order details. Please try again.',
                    confirmButtonColor: '#2d5016'
                });
            });
        }
        
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'short', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        function getStatusColor(status) {
            switch(status.toLowerCase()) {
                case 'completed': return 'success';
                case 'pending': return 'warning';
                case 'cancelled': return 'danger';
                default: return 'secondary';
            }
        }
    </script>
</body>
</html>

