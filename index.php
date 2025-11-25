<?php
session_start();

// Redirect admin users to dashboard instead of showing customer homepage
if (isset($_SESSION['customer_id']) && isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1) {
    header('Location: admin/dashboard.php');
    exit;
}

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if (substr($baseDir, -5) === '/view') {
    $baseDir = substr($baseDir, 0, -5);
}
$appBasePath = ($baseDir === '' || $baseDir === '.') ? '/' : $baseDir . '/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
	<meta http-equiv="Pragma" content="no-cache">
	<meta http-equiv="Expires" content="0">
	<title>AgroCare - Fresh Farm. Smart Nutrition. Delivered. | Accra, Ghana</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<link href="css/index.css?v=<?php echo time(); ?>" rel="stylesheet">
</head>
<body>
	<!-- Navigation -->
	<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);">
		<div class="container">
			<a class="navbar-brand fw-bold" href="index.php">
				<i class="fas fa-seedling me-2"></i>AgroCare
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav me-auto">
					<li class="nav-item">
						<a class="nav-link active" href="index.php"><i class="fas fa-home me-1"></i>Home</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="view/all_product.php"><i class="fas fa-apple-alt me-1"></i>Products</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="curiosity_box.php"><i class="fas fa-box me-1"></i>Curiosity Box</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="premium.php"><i class="fas fa-crown me-1"></i>Premium</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="view/product_search_result.php"><i class="fas fa-search me-1"></i>Search</a>
					</li>
				</ul>
				<ul class="navbar-nav">
					<li class="nav-item">
						<a class="nav-link" href="view/cart.php">
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
								<?php if (!isset($_SESSION['user_role']) || (int)$_SESSION['user_role'] !== 1): ?>
									<li><a class="dropdown-item" href="view/order_history.php"><i class="fas fa-receipt me-2"></i>My Orders</a></li>
								<?php endif; ?>
								<?php if (isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1): ?>
									<li><hr class="dropdown-divider"></li>
									<li><a class="dropdown-item" href="admin/dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard</a></li>
									<li><hr class="dropdown-divider"></li>
								<?php endif; ?>
								<li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
							</ul>
						</li>
					<?php else: ?>
						<li class="nav-item">
							<a class="nav-link" href="login/register.php"><i class="fas fa-user-plus me-1"></i>Register</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="login/login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Hero Section -->
	<section class="hero-section">
		<div class="hero-overlay"></div>
		<div class="container">
			<div class="row align-items-center min-vh-100">
				<div class="col-lg-6">
					<div class="hero-content text-white">
						<h1 class="display-3 fw-bold mb-4">
							Fresh Farm. Smart Nutrition. Delivered.
						</h1>
						<p class="lead mb-3" style="font-size: 1.3rem;">Sustainable & Affordable</p>
						<p class="mb-4" style="font-size: 1.1rem;">Connecting smallholder farmers directly with consumers through AI-driven personalized nutrition and sustainable food delivery in Ghana.</p>
						<div class="hero-buttons">
							<a href="view/all_product.php" class="btn btn-light btn-lg me-3 mb-2">
								<i class="fas fa-shopping-bag me-2"></i>Shop Now
							</a>
							<a href="curiosity_box.php" class="btn btn-outline-light btn-lg mb-2">
								<i class="fas fa-box me-2"></i>Try Curiosity Box
							</a>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="hero-image text-center">
						<div class="hero-visual">
							<i class="fas fa-seedling hero-icon"></i>
							<div class="floating-elements">
								<i class="fas fa-apple-alt float-icon" style="top: 20%; left: 10%;"></i>
								<i class="fas fa-fish float-icon" style="top: 60%; right: 15%;"></i>
								<i class="fas fa-carrot float-icon" style="bottom: 20%; left: 20%;"></i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Value Proposition Section -->
	<section class="value-proposition-section py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);">
		<div class="container">
			<div class="row text-center mb-5">
				<div class="col-12">
					<h2 class="display-4 fw-bold mb-3" style="color: #2d5016;">For Consumers</h2>
					<p class="lead text-muted">Experience the future of personalized nutrition</p>
				</div>
			</div>
			<div class="row g-4">
				<div class="col-lg-3 col-md-6">
					<div class="value-card text-center p-4 h-100">
						<div class="value-icon mb-3">
							<i class="fas fa-robot"></i>
						</div>
						<h4 class="fw-bold mb-3" style="color: #2d5016;">AI-Driven Personalized Diet Plans</h4>
						<p class="text-muted">Get customized nutrition recommendations based on your health goals and preferences.</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="value-card text-center p-4 h-100">
						<div class="value-icon mb-3">
							<i class="fas fa-box-open"></i>
						</div>
						<h4 class="fw-bold mb-3" style="color: #2d5016;">Affordable Subscription Boxes</h4>
						<p class="text-muted">Curiosity Boxes starting from GHS 80/month with personalized protein portions and recipes.</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="value-card text-center p-4 h-100">
						<div class="value-icon mb-3">
							<i class="fas fa-leaf"></i>
						</div>
						<h4 class="fw-bold mb-3" style="color: #2d5016;">Farm-to-Table Freshness</h4>
						<p class="text-muted">Direct from smallholder farmers to your doorstep, ensuring maximum freshness.</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="value-card text-center p-4 h-100">
						<div class="value-icon mb-3">
							<i class="fas fa-mobile-alt"></i>
						</div>
						<h4 class="fw-bold mb-3" style="color: #2d5016;">Mobile-Friendly Access</h4>
						<p class="text-muted">Shop, subscribe, and manage your nutrition plan from any device, anywhere.</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Featured Products Section -->
	<section class="featured-products-section py-5">
		<div class="container">
			<div class="row text-center mb-5">
				<div class="col-12">
					<h2 class="display-4 fw-bold mb-3" style="color: #2d5016;">Featured Products</h2>
					<p class="lead text-muted">Fresh vegetables, fruits, fish, and pork products - Fair to Farmers</p>
				</div>
			</div>
			<div class="row g-4" id="featuredProductsGrid">
				<!-- Featured products will be loaded via AJAX -->
			</div>
		</div>
	</section>

	<!-- Curiosity Box Showcase with Carousel -->
	<section class="curiosity-box-showcase py-5" style="background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%); position: relative;">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-6 mb-4 mb-lg-0">
					<div class="curiosity-box-visual text-center position-relative" id="curiosityBoxVisual" style="cursor: pointer;">
						<div class="box-icon-wrapper">
							<i class="fas fa-box-open" style="font-size: 12rem; color: rgba(255,255,255,0.9);"></i>
						</div>
						<div class="hover-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.1); border-radius: 20px; opacity: 0; transition: opacity 0.3s;">
							<div class="d-flex align-items-center justify-content-center h-100">
								<span class="text-white fw-bold" style="font-size: 1.2rem;"><i class="fas fa-info-circle me-2"></i>Hover for Details</span>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 text-white">
					<h2 class="display-4 fw-bold mb-4">Your Monthly Protein Adventure</h2>
					<p class="lead mb-4">Discover the Curiosity Box - a personalized monthly subscription that brings fresh protein, recipes, and nutrition education to your doorstep.</p>
					<ul class="list-unstyled mb-4" style="font-size: 1.1rem;">
						<li class="mb-3"><i class="fas fa-check-circle me-2"></i>Personalized protein portions (fish, pork, or mixed)</li>
						<li class="mb-3"><i class="fas fa-check-circle me-2"></i>Step-by-step recipes with ingredients</li>
						<li class="mb-3"><i class="fas fa-check-circle me-2"></i>Nutritional education materials</li>
						<li class="mb-3"><i class="fas fa-check-circle me-2"></i>Protein experiment challenges</li>
						<li class="mb-3"><i class="fas fa-check-circle me-2"></i>Surprise seasonal items</li>
					</ul>
					<div class="pricing-info mb-4">
						<div class="d-flex align-items-center mb-2">
							<span class="badge bg-light text-success me-2" style="font-size: 1rem; padding: 0.5rem 1rem;">Starting at GHS 80/month</span>
						</div>
						<div class="d-flex align-items-center">
							<span class="badge bg-warning text-dark me-2" style="font-size: 1rem; padding: 0.5rem 1rem;">Seasonal Share: GHS 400 for 6 months</span>
						</div>
					</div>
					<a href="curiosity_box.php" class="btn btn-light btn-lg">
						<i class="fas fa-box me-2"></i>Subscribe Now
					</a>
				</div>
			</div>
		</div>
		
		<!-- Curiosity Box Carousel Banner -->
		<div class="container mt-5">
			<div id="curiosityCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
				<div class="carousel-inner rounded" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
					<div class="carousel-item active">
						<div class="text-center text-white p-4">
							<h4 class="fw-bold mb-2"><i class="fas fa-gift me-2"></i>What's New: Curiosity Box Premium</h4>
							<p class="mb-0">Get 15% off all products when you subscribe to our Premium Curiosity Box!</p>
						</div>
					</div>
					<div class="carousel-item">
						<div class="text-center text-white p-4">
							<h4 class="fw-bold mb-2"><i class="fas fa-fish me-2"></i>Fresh Protein Delivered Monthly</h4>
							<p class="mb-0">Personalized portions of fish, pork, or mixed proteins with recipes included!</p>
						</div>
					</div>
					<div class="carousel-item">
						<div class="text-center text-white p-4">
							<h4 class="fw-bold mb-2"><i class="fas fa-book me-2"></i>Nutrition Education Included</h4>
							<p class="mb-0">Learn about nutrition with every box - recipes, tips, and educational materials!</p>
						</div>
					</div>
				</div>
				<button class="carousel-control-prev" type="button" data-bs-target="#curiosityCarousel" data-bs-slide="prev">
					<span class="carousel-control-prev-icon"></span>
				</button>
				<button class="carousel-control-next" type="button" data-bs-target="#curiosityCarousel" data-bs-slide="next">
					<span class="carousel-control-next-icon"></span>
				</button>
			</div>
		</div>
	</section>
	
	<!-- Curiosity Box Hover Modal -->
	<div class="modal fade" id="curiosityBoxModal" tabindex="-1">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-success text-white">
					<h5 class="modal-title"><i class="fas fa-box-open me-2"></i>Curiosity Box Explained</h5>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-6 mb-3">
							<h5 class="fw-bold text-success"><i class="fas fa-fish me-2"></i>What's Inside?</h5>
							<ul class="list-unstyled">
								<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Personalized protein portions</li>
								<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Step-by-step recipes</li>
								<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Nutritional education materials</li>
								<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Protein experiment challenges</li>
								<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Surprise seasonal items</li>
							</ul>
						</div>
						<div class="col-md-6 mb-3">
							<h5 class="fw-bold text-success"><i class="fas fa-star me-2"></i>Benefits</h5>
							<ul class="list-unstyled">
								<li class="mb-2"><i class="fas fa-check text-success me-2"></i>15% discount on all products</li>
								<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Free delivery</li>
								<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Priority access to new items</li>
								<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Exclusive recipes</li>
								<li class="mb-2"><i class="fas fa-check text-success me-2"></i>AI dietitian premium features</li>
							</ul>
						</div>
					</div>
					<div class="alert alert-info">
						<i class="fas fa-info-circle me-2"></i><strong>Perfect for:</strong> Families looking for convenient, healthy protein options with educational content to improve nutrition knowledge.
					</div>
					<div class="text-center mt-3">
						<a href="curiosity_box.php" class="btn btn-success btn-lg">
							<i class="fas fa-box me-2"></i>Learn More & Subscribe
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- How It Works Section -->
	<section class="how-it-works-section py-5">
		<div class="container">
			<div class="row text-center mb-5">
				<div class="col-12">
					<h2 class="display-4 fw-bold mb-3" style="color: #2d5016;">How It Works</h2>
					<p class="lead text-muted">Get fresh, personalized nutrition in 4 simple steps</p>
				</div>
			</div>
			<div class="row g-4">
				<div class="col-lg-3 col-md-6">
					<div class="step-card text-center p-4 h-100">
						<div class="step-number mb-3">1</div>
						<div class="step-icon mb-3">
							<i class="fas fa-search"></i>
						</div>
						<h4 class="fw-bold mb-3" style="color: #2d5016;">Browse & Select</h4>
						<p class="text-muted">Browse our products or get AI recommendations based on your health goals and preferences.</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="step-card text-center p-4 h-100">
						<div class="step-number mb-3">2</div>
						<div class="step-icon mb-3">
							<i class="fas fa-cog"></i>
						</div>
						<h4 class="fw-bold mb-3" style="color: #2d5016;">Customize Your Order</h4>
						<p class="text-muted">Customize your order or Curiosity Box with your preferred proteins, dietary goals, and family size.</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="step-card text-center p-4 h-100">
						<div class="step-number mb-3">3</div>
						<div class="step-icon mb-3">
							<i class="fas fa-seedling"></i>
						</div>
						<h4 class="fw-bold mb-3" style="color: #2d5016;">Farmers Prepare</h4>
						<p class="text-muted">Our partner farmers prepare your fresh items with care, ensuring quality and traceability.</p>
					</div>
				</div>
				<div class="col-lg-3 col-md-6">
					<div class="step-card text-center p-4 h-100">
						<div class="step-number mb-3">4</div>
						<div class="step-icon mb-3">
							<i class="fas fa-truck"></i>
						</div>
						<h4 class="fw-bold mb-3" style="color: #2d5016;">Fast Delivery</h4>
						<p class="text-muted">Fast delivery to your doorstep in Accra and surrounding areas. Fresh, guaranteed.</p>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Search Section -->
	<section class="search-section py-5 bg-light">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-8">
					<div class="search-card p-4">
						<h3 class="text-center mb-4" style="color: #2d5016;">
							<i class="fas fa-search me-2"></i>Find Your Perfect Products
						</h3>
						<form class="search-form" action="view/product_search_result.php" method="GET">
							<div class="row g-3">
								<div class="col-md-6">
									<input type="text" class="form-control form-control-lg" name="query" placeholder="Search products..." required>
								</div>
								<div class="col-md-3">
									<select class="form-select form-select-lg" name="category" id="homeCategoryFilter">
										<option value="">All Categories</option>
										<!-- Categories will be loaded via AJAX -->
									</select>
								</div>
								<div class="col-md-3">
									<button type="submit" class="btn btn-success btn-lg w-100">
										<i class="fas fa-search me-2"></i>Search
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Categories Section -->
	<section class="categories-section py-5">
		<div class="container">
			<div class="row text-center mb-5">
				<div class="col-12">
					<h2 class="display-5 fw-bold mb-3" style="color: #2d5016;">Shop by Category</h2>
					<p class="lead text-muted">Explore our diverse range of fresh products</p>
				</div>
			</div>
			<div class="row g-4" id="categoriesGrid">
				<!-- Categories will be loaded via AJAX -->
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer class="footer py-5" style="background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 mb-4">
					<h5 class="fw-bold mb-3 text-white">
						<i class="fas fa-seedling me-2"></i>AgroCare
					</h5>
					<p class="text-light mb-2">Fresh Farm. Smart Nutrition. Delivered.</p>
					<p class="text-light">Connecting smallholder farmers with consumers through AI-driven personalized nutrition in Ghana.</p>
				</div>
				<div class="col-lg-4 mb-4">
					<h6 class="fw-bold mb-3 text-white">Quick Links</h6>
					<ul class="list-unstyled">
						<li><a href="view/all_product.php" class="text-light text-decoration-none"><i class="fas fa-apple-alt me-2"></i>All Products</a></li>
						<li><a href="curiosity_box.php" class="text-light text-decoration-none"><i class="fas fa-box me-2"></i>Curiosity Box</a></li>
						<li><a href="premium.php" class="text-light text-decoration-none"><i class="fas fa-crown me-2"></i>Premium Membership</a></li>
						<li><a href="view/product_search_result.php" class="text-light text-decoration-none"><i class="fas fa-search me-2"></i>Search Products</a></li>
					</ul>
				</div>
				<div class="col-lg-4 mb-4">
					<h6 class="fw-bold mb-3 text-white">Contact Info</h6>
					<ul class="list-unstyled text-light">
						<li><i class="fas fa-map-marker-alt me-2"></i>Accra, Ghana</li>
						<li><i class="fas fa-phone me-2"></i>+233 24 123 4567</li>
						<li><i class="fas fa-envelope me-2"></i>info@agrocare.gh</li>
					</ul>
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

	<!-- AI Chatbot Widget (will be added in separate file) -->
	<div id="aiChatbotWidget"></div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script>
		window.APP_BASE_PATH = '<?php echo htmlspecialchars($appBasePath, ENT_QUOTES); ?>';
	</script>
	<script src="js/cart.js"></script>
	<script src="js/index.js?v=<?php echo time(); ?>"></script>
	<script src="js/ai_chatbot.js?v=<?php echo time(); ?>"></script>
</body>
</html>
