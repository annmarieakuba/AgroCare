<?php
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
require_once __DIR__ . '/../classes/order_class.php';
require_once __DIR__ . '/../classes/product_class.php';

$db = new Database();
$orderInstance = new Order();
$productInstance = new Product();

// Get earnings (total revenue from completed orders)
$earningsQuery = "
    SELECT COALESCE(SUM(p.amount), 0) as total_earnings,
           COUNT(DISTINCT p.order_id) as total_orders
    FROM payment p
    INNER JOIN orders o ON p.order_id = o.order_id
    WHERE o.order_status = 'completed'
";
$earningsResult = $db->conn->query($earningsQuery);
$earnings = $earningsResult->fetch_assoc();

// Get recent orders
$recentOrdersQuery = "
    SELECT o.order_id, o.order_date, o.order_status, 
           SUM(od.qty * od.unit_price) as total_amount,
           COUNT(od.orderdetail_id) as item_count
    FROM orders o
    LEFT JOIN orderdetails od ON o.order_id = od.order_id
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
    LIMIT 10
";
$recentOrders = $db->conn->query($recentOrdersQuery)->fetch_all(MYSQLI_ASSOC);

// Get most bought products
$topProductsQuery = "
    SELECT p.product_id, p.product_title, p.product_image,
           SUM(od.qty) as total_quantity,
           SUM(od.qty * od.unit_price) as total_revenue
    FROM orderdetails od
    INNER JOIN products p ON od.product_id = p.product_id
    GROUP BY p.product_id, p.product_title, p.product_image
    ORDER BY total_quantity DESC
    LIMIT 10
";
$topProducts = $db->conn->query($topProductsQuery)->fetch_all(MYSQLI_ASSOC);

// Get order statistics
$orderStatsQuery = "
    SELECT 
        COUNT(*) as total_orders,
        SUM(CASE WHEN order_status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
        SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
        SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
    FROM orders
";
$orderStats = $db->conn->query($orderStatsQuery)->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - AgroCare Farm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            padding: 25px;
            margin-bottom: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
        .stat-card {
            background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
        }
        .stat-card.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        .stat-card.warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        }
        .stat-card.danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }
        .stat-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
        }
        .admin-nav {
            background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark admin-nav">
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
                        <a class="nav-link active" href="dashboard.php"><i class="fas fa-tachometer-alt me-1"></i>Dashboard</a>
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
                        <a class="nav-link" href="orders.php"><i class="fas fa-shopping-cart me-1"></i>Orders</a>
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
                <h1 class="fw-bold"><i class="fas fa-tachometer-alt me-2"></i>Management Portal</h1>
                <p class="text-muted">Overview of your e-commerce platform</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">Total Earnings</div>
                            <div class="stat-value">₵<?php echo number_format($earnings['total_earnings'] ?? 0, 2); ?></div>
                        </div>
                        <i class="fas fa-coins fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">Total Orders</div>
                            <div class="stat-value"><?php echo $orderStats['total_orders'] ?? 0; ?></div>
                        </div>
                        <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">Pending Orders</div>
                            <div class="stat-value"><?php echo $orderStats['pending_orders'] ?? 0; ?></div>
                        </div>
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small">Completed Orders</div>
                            <div class="stat-value"><?php echo $orderStats['completed_orders'] ?? 0; ?></div>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Recent Orders -->
            <div class="col-lg-8">
                <div class="dashboard-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="fw-bold"><i class="fas fa-shopping-cart me-2"></i>Recent Orders</h4>
                        <a href="orders.php" class="btn btn-sm btn-outline-success">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($recentOrders)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No orders yet</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($recentOrders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['order_id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
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
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="col-lg-4">
                <div class="dashboard-card">
                    <h4 class="fw-bold mb-3"><i class="fas fa-chart-line me-2"></i>Most Bought Products</h4>
                    <div class="list-group">
                        <?php if (empty($topProducts)): ?>
                            <div class="text-center text-muted py-4">No sales data yet</div>
                        <?php else: ?>
                            <?php foreach ($topProducts as $index => $product): ?>
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-success me-2"><?php echo $index + 1; ?></span>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold"><?php echo htmlspecialchars($product['product_title']); ?></div>
                                            <small class="text-muted">
                                                <?php echo $product['total_quantity']; ?> sold • 
                                                ₵<?php echo number_format($product['total_revenue'], 2); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

