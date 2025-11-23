<?php
session_start();

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if (substr($baseDir, -5) === '/view') {
    $baseDir = substr($baseDir, 0, -5);
}
$appBasePath = ($baseDir === '' || $baseDir === '.') ? '/' : $baseDir . '/';

// Get search parameters
$query = trim($_GET['query'] ?? '');
$category = (int)($_GET['category'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search Results - AgroCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../css/all_product.css" rel="stylesheet">
    <style>
        .search-header {
            background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);
            padding: 120px 0 60px;
            color: white;
        }
        .product-card-search {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            height: 100%;
            border: 2px solid transparent;
        }
        .product-card-search:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(45, 80, 22, 0.2);
            border-color: #2d5016;
        }
        .product-image-search {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-image-search img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .product-image-search i {
            font-size: 4rem;
            color: #2d5016;
            opacity: 0.5;
        }
        .product-price-search {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2d5016;
            margin: 0.5rem 0;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
                <i class="fas fa-seedling me-2"></i>AgroCare
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
                        <a class="nav-link" href="all_product.php"><i class="fas fa-apple-alt me-1"></i>Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="product_search_result.php"><i class="fas fa-search me-1"></i>Search</a>
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
                                <?php if (isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1): ?>
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

    <!-- Search Header -->
    <section class="search-header">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">
                <i class="fas fa-search me-3"></i>Search Products
            </h1>
            <form class="search-form" action="product_search_result.php" method="GET">
                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" class="form-control form-control-lg" name="query" 
                               value="<?php echo htmlspecialchars($query); ?>" 
                               placeholder="Search products..." required>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select form-select-lg" name="category" id="categoryFilter">
                            <option value="">All Categories</option>
                            <!-- Categories will be loaded via AJAX -->
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-light btn-lg w-100">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Search Results -->
    <section class="py-5">
        <div class="container">
            <div id="searchResultsContainer">
                <div class="text-center py-5">
                    <div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-3 text-muted">Searching products...</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer py-5" style="background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3 text-white"><i class="fas fa-seedling me-2"></i>AgroCare</h5>
                    <p class="text-light">Fresh Farm. Smart Nutrition. Delivered.</p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3 text-white">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="../index.php" class="text-light text-decoration-none">Home</a></li>
                        <li><a href="all_product.php" class="text-light text-decoration-none">Products</a></li>
                        <li><a href="../curiosity_box.php" class="text-light text-decoration-none">Curiosity Box</a></li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h6 class="fw-bold mb-3 text-white">Contact</h6>
                    <p class="text-light mb-0">Accra, Ghana</p>
                    <p class="text-light mb-0">info@agrocare.gh</p>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.2);">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0 text-light">&copy; <?php echo date('Y'); ?> AgroCare. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        window.APP_BASE_PATH = '<?php echo htmlspecialchars($appBasePath, ENT_QUOTES); ?>';
        const searchQuery = '<?php echo htmlspecialchars($query, ENT_QUOTES); ?>';
        const searchCategory = <?php echo $category; ?>;
    </script>
    <script src="../js/cart.js"></script>
    <script src="../js/search_results.js?v=<?php echo time(); ?>"></script>
    <script src="../js/ai_chatbot.js?v=<?php echo time(); ?>"></script>
</body>
</html>

