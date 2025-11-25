<?php
session_start();

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if (substr($baseDir, -5) === '/view') {
    $baseDir = substr($baseDir, 0, -5);
}
$appBasePath = ($baseDir === '' || $baseDir === '.') ? '/' : $baseDir . '/';

// Redirect if not logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: ' . $appBasePath . 'login/login.php?redirect=order_history');
    exit;
}

// Redirect admins away from order history (they shouldn't see customer orders)
if (isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1) {
    header('Location: ' . $appBasePath . 'admin/product.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Orders - AgroCare Farm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 80px;
        }
        .order-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .order-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .order-header {
            background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0;
        }
        .status-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }
        .order-item {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
        }
        .order-item:last-child {
            border-bottom: none;
        }
        .product-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }
        .empty-state i {
            font-size: 64px;
            color: #dee2e6;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
                <i class="fas fa-seedling me-2"></i>AgroCare Farm
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php"><i class="fas fa-home me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_product.php"><i class="fas fa-apple-alt me-1"></i>All Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart me-1"></i>Cart</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            <i class="fas fa-shopping-cart me-1"></i>Cart
                            <span class="badge bg-light text-success ms-1" data-cart-count style="display: none;">0</span>
                        </a>
                    </li>
                    <?php if (isset($_SESSION['customer_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'User'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item active" href="order_history.php"><i class="fas fa-receipt me-2"></i>My Orders</a></li>
                                <?php if (isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="../admin/category.php"><i class="fas fa-leaf me-2"></i>Manage Categories</a></li>
                                    <li><a class="dropdown-item" href="../admin/brand.php"><i class="fas fa-tags me-2"></i>Manage Brands</a></li>
                                    <li><a class="dropdown-item" href="../admin/product.php"><i class="fas fa-apple-alt me-2"></i>Manage Products</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                <?php endif; ?>
                                <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../login/register.php"><i class="fas fa-user-plus me-1"></i>Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../login/login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container" style="padding-top: 20px; padding-bottom: 40px;">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 fw-bold text-success mb-1">
                            <i class="fas fa-receipt me-2"></i>My Orders
                        </h1>
                        <p class="text-muted mb-0">View your order history and track your purchases</p>
                    </div>
                    <a href="all_product.php" class="btn btn-outline-success">
                        <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        </div>

        <div id="ordersContainer">
            <div class="text-center py-5">
                <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-muted">Loading your orders...</p>
            </div>
        </div>
    </main>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-receipt me-2"></i>Order Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <!-- Order details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/order_history.js?v=<?php echo time(); ?>"></script>
    <script>
        // Ensure basePath ends with / for consistent path construction
        const rawBasePath = '<?php echo htmlspecialchars($appBasePath, ENT_QUOTES); ?>';
        window.APP_BASE_PATH = rawBasePath.endsWith('/') ? rawBasePath : rawBasePath + '/';
        console.log('Base path set to:', window.APP_BASE_PATH);
    </script>
</body>
</html>

