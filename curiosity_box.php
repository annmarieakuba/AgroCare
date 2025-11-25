<?php
session_start();

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
	<title>Curiosity Box - AgroCare | Monthly Protein Adventure</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<link href="css/index.css?v=<?php echo time(); ?>" rel="stylesheet">
	<style>
		.curiosity-box-hero {
			background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);
			padding: 120px 0 80px;
			color: white;
		}
		.box-content-visual {
			background: white;
			border-radius: 20px;
			padding: 40px;
			box-shadow: 0 10px 40px rgba(0,0,0,0.1);
			text-align: center;
		}
		.pricing-card {
			background: white;
			border-radius: 15px;
			padding: 30px;
			box-shadow: 0 5px 20px rgba(0,0,0,0.1);
			transition: all 0.3s ease;
			border: 3px solid transparent;
			height: 100%;
		}
		.pricing-card:hover {
			transform: translateY(-10px);
			border-color: #2d5016;
			box-shadow: 0 15px 40px rgba(45, 80, 22, 0.2);
		}
		.pricing-card.featured {
			border-color: #ffc107;
			position: relative;
		}
		.pricing-card.featured::before {
			content: 'POPULAR';
			position: absolute;
			top: -15px;
			right: 20px;
			background: #ffc107;
			color: #000;
			padding: 5px 15px;
			border-radius: 20px;
			font-size: 0.8rem;
			font-weight: bold;
		}
		.benefit-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
			gap: 20px;
			margin: 40px 0;
		}
		.benefit-item {
			background: white;
			padding: 20px;
			border-radius: 10px;
			box-shadow: 0 3px 10px rgba(0,0,0,0.1);
			text-align: center;
		}
		.benefit-item i {
			font-size: 2.5rem;
			color: #2d5016;
			margin-bottom: 15px;
		}
		.customization-option {
			background: #f8f9fa;
			border-radius: 10px;
			padding: 20px;
			margin-bottom: 20px;
		}
		.csa-section {
			background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
			padding: 60px 0;
			border-radius: 20px;
			margin: 40px 0;
		}
	</style>
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
					<li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home me-1"></i>Home</a></li>
					<li class="nav-item"><a class="nav-link" href="view/all_product.php"><i class="fas fa-apple-alt me-1"></i>Products</a></li>
					<li class="nav-item"><a class="nav-link active" href="curiosity_box.php"><i class="fas fa-box me-1"></i>Curiosity Box</a></li>
					<li class="nav-item"><a class="nav-link" href="premium.php"><i class="fas fa-crown me-1"></i>Premium</a></li>
					<li class="nav-item"><a class="nav-link" href="view/product_search_result.php"><i class="fas fa-search me-1"></i>Search</a></li>
				</ul>
				<ul class="navbar-nav">
					<li class="nav-item"><a class="nav-link" href="view/cart.php"><i class="fas fa-shopping-cart me-1"></i>Cart <span class="badge bg-light text-success ms-1" data-cart-count style="display: none;">0</span></a></li>
					<?php if (isset($_SESSION['customer_id'])): ?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
								<i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['customer_name'] ?? 'User'); ?>
							</a>
							<ul class="dropdown-menu">
								<li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
							</ul>
						</li>
					<?php else: ?>
						<li class="nav-item"><a class="nav-link" href="login/register.php"><i class="fas fa-user-plus me-1"></i>Register</a></li>
						<li class="nav-item"><a class="nav-link" href="login/login.php"><i class="fas fa-sign-in-alt me-1"></i>Login</a></li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
	</nav>

	<!-- Hero Section -->
	<section class="curiosity-box-hero">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-6 mb-4 mb-lg-0">
					<h1 class="display-3 fw-bold mb-4">Your Monthly Protein Adventure</h1>
					<p class="lead mb-4">Discover personalized protein portions, recipes, and nutrition education delivered fresh to your doorstep every month.</p>
					<a href="#subscribe" class="btn btn-light btn-lg">
						<i class="fas fa-box me-2"></i>Start Your Protein Journey
					</a>
				</div>
				<div class="col-lg-6">
					<div class="box-content-visual">
						<i class="fas fa-box-open" style="font-size: 10rem; color: #2d5016; margin-bottom: 20px;"></i>
						<h3 class="mb-3" style="color: #2d5016;">What's Inside?</h3>
						<ul class="list-unstyled text-start">
							<li class="mb-2"><i class="fas fa-fish me-2" style="color: #4a7c59;"></i>Personalized protein portions</li>
							<li class="mb-2"><i class="fas fa-book me-2" style="color: #4a7c59;"></i>Step-by-step recipes</li>
							<li class="mb-2"><i class="fas fa-graduation-cap me-2" style="color: #4a7c59;"></i>Nutritional education materials</li>
							<li class="mb-2"><i class="fas fa-flask me-2" style="color: #4a7c59;"></i>Protein experiment challenges</li>
							<li class="mb-2"><i class="fas fa-gift me-2" style="color: #4a7c59;"></i>Surprise seasonal items</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Customization Options -->
	<section class="py-5">
		<div class="container">
			<h2 class="display-5 fw-bold text-center mb-5" style="color: #2d5016;">Customize Your Box</h2>
			<div class="row">
				<div class="col-lg-4">
					<div class="customization-option">
						<h4 class="fw-bold mb-3" style="color: #2d5016;"><i class="fas fa-fish me-2"></i>Protein Preference</h4>
						<div class="form-check mb-2">
							<input class="form-check-input" type="radio" name="protein" id="fish" value="fish" checked>
							<label class="form-check-label" for="fish">Fish-Focused</label>
						</div>
						<div class="form-check mb-2">
							<input class="form-check-input" type="radio" name="protein" id="pork" value="pork">
							<label class="form-check-label" for="pork">Pork-Focused</label>
						</div>
						<div class="form-check mb-2">
							<input class="form-check-input" type="radio" name="protein" id="mixed" value="mixed">
							<label class="form-check-label" for="mixed">Mixed</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="protein" id="vegetarian" value="vegetarian">
							<label class="form-check-label" for="vegetarian">Vegetarian Add-ons</label>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="customization-option">
						<h4 class="fw-bold mb-3" style="color: #2d5016;"><i class="fas fa-bullseye me-2"></i>Dietary Goals</h4>
						<div class="form-check mb-2">
							<input class="form-check-input" type="radio" name="goal" id="muscle" value="muscle" checked>
							<label class="form-check-label" for="muscle">Muscle Building</label>
						</div>
						<div class="form-check mb-2">
							<input class="form-check-input" type="radio" name="goal" id="weight" value="weight">
							<label class="form-check-label" for="weight">Weight Management</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="goal" id="balanced" value="balanced">
							<label class="form-check-label" for="balanced">Balanced Nutrition</label>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="customization-option">
						<h4 class="fw-bold mb-3" style="color: #2d5016;"><i class="fas fa-users me-2"></i>Family Size</h4>
						<select class="form-select" id="familySize">
							<option value="1-2">1-2 People</option>
							<option value="3-4" selected>3-4 People</option>
							<option value="5+">5+ People</option>
						</select>
						<div class="mt-3">
							<h4 class="fw-bold mb-3" style="color: #2d5016;"><i class="fas fa-calendar me-2"></i>Frequency</h4>
							<div class="form-check mb-2">
								<input class="form-check-input" type="radio" name="frequency" id="monthly" value="monthly" checked>
								<label class="form-check-label" for="monthly">Monthly</label>
							</div>
							<div class="form-check">
								<input class="form-check-input" type="radio" name="frequency" id="biweekly" value="biweekly">
								<label class="form-check-label" for="biweekly">Bi-Weekly</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Pricing Tiers -->
	<section class="py-5 bg-light" id="subscribe">
		<div class="container">
			<h2 class="display-5 fw-bold text-center mb-5" style="color: #2d5016;">Choose Your Plan</h2>
			<div class="row g-4">
				<div class="col-lg-4">
					<div class="pricing-card">
						<h3 class="fw-bold mb-3" style="color: #2d5016;">Basic Box</h3>
						<div class="mb-4">
							<span class="display-4 fw-bold" style="color: #2d5016;">₵80</span>
							<span class="text-muted">/month</span>
						</div>
						<ul class="list-unstyled mb-4">
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Personalized protein portions</li>
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Basic recipes</li>
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Nutritional guides</li>
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Free delivery</li>
						</ul>
						<button class="btn w-100" style="background: linear-gradient(135deg, #2d5016, #4a7c59); color: white;" onclick="subscribeToBox('basic')">
							Subscribe Now
						</button>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="pricing-card featured">
						<h3 class="fw-bold mb-3" style="color: #2d5016;">Premium Box</h3>
						<div class="mb-4">
							<span class="display-4 fw-bold" style="color: #2d5016;">₵150</span>
							<span class="text-muted">/month</span>
						</div>
						<ul class="list-unstyled mb-4">
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Everything in Basic</li>
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Rare and seasonal items</li>
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Advanced recipes</li>
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Premium educational content</li>
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Priority support</li>
						</ul>
						<button class="btn w-100 btn-warning" onclick="subscribeToBox('premium')">
							Subscribe Now
						</button>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="pricing-card">
						<h3 class="fw-bold mb-3" style="color: #2d5016;">Seasonal Share (CSA)</h3>
						<div class="mb-4">
							<span class="display-4 fw-bold" style="color: #2d5016;">₵400</span>
							<span class="text-muted">/6 months</span>
						</div>
						<ul class="list-unstyled mb-4">
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Prepaid 6-month commitment</li>
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Direct farmer support</li>
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Guaranteed fresh boxes</li>
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Best value per box</li>
							<li class="mb-2"><i class="fas fa-check text-success me-2"></i>Farmer connection stories</li>
						</ul>
						<button class="btn w-100" style="background: linear-gradient(135deg, #2d5016, #4a7c59); color: white;" onclick="subscribeToBox('csa')">
							Subscribe Now
						</button>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Subscriber Benefits -->
	<section class="py-5">
		<div class="container">
			<h2 class="display-5 fw-bold text-center mb-5" style="color: #2d5016;">Exclusive Subscriber Benefits</h2>
			<div class="benefit-grid">
				<div class="benefit-item">
					<i class="fas fa-star"></i>
					<h5 class="fw-bold mt-3">Priority Access</h5>
					<p class="text-muted">First access to rare and seasonal items</p>
				</div>
				<div class="benefit-item">
					<i class="fas fa-percent"></i>
					<h5 class="fw-bold mt-3">Discount Rewards</h5>
					<p class="text-muted">10-15% off all regular store purchases</p>
				</div>
				<div class="benefit-item">
					<i class="fas fa-truck"></i>
					<h5 class="fw-bold mt-3">Free Delivery</h5>
					<p class="text-muted">No delivery fees on Curiosity Box deliveries</p>
				</div>
				<div class="benefit-item">
					<i class="fas fa-book-open"></i>
					<h5 class="fw-bold mt-3">Exclusive Recipes</h5>
					<p class="text-muted">Access to premium recipe library and cooking videos</p>
				</div>
				<div class="benefit-item">
					<i class="fas fa-robot"></i>
					<h5 class="fw-bold mt-3">AI Dietitian Premium</h5>
					<p class="text-muted">Advanced personalized meal planning features</p>
				</div>
				<div class="benefit-item">
					<i class="fas fa-newspaper"></i>
					<h5 class="fw-bold mt-3">Farmers' Stories</h5>
					<p class="text-muted">Monthly newsletter featuring your supported farmers</p>
				</div>
				<div class="benefit-item">
					<i class="fas fa-coins"></i>
					<h5 class="fw-bold mt-3">Loyalty Points</h5>
					<p class="text-muted">Earn points on every box toward future purchases</p>
				</div>
				<div class="benefit-item">
					<i class="fas fa-sliders-h"></i>
					<h5 class="fw-bold mt-3">Flexible Management</h5>
					<p class="text-muted">Pause, skip, or customize boxes anytime</p>
				</div>
				<div class="benefit-item">
					<i class="fas fa-users"></i>
					<h5 class="fw-bold mt-3">Community Access</h5>
					<p class="text-muted">Join subscriber-only nutrition workshops and challenges</p>
				</div>
				<div class="benefit-item">
					<i class="fas fa-bell"></i>
					<h5 class="fw-bold mt-3">Early Bird Specials</h5>
					<p class="text-muted">Get notified of new products before public launch</p>
				</div>
			</div>
		</div>
	</section>

	<!-- CSA Model Explanation -->
	<section class="csa-section">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-6 mb-4 mb-lg-0">
					<h2 class="display-5 fw-bold mb-4" style="color: #2d5016;">Invest in Your Food Future</h2>
					<p class="lead mb-4">Community Supported Agriculture (CSA) connects you directly with local farmers, ensuring sustainable food production and guaranteed freshness.</p>
					<ul class="list-unstyled">
						<li class="mb-3"><i class="fas fa-check-circle me-2" style="color: #2d5016;"></i>Help farmers plan production with prepaid seasonal shares</li>
						<li class="mb-3"><i class="fas fa-check-circle me-2" style="color: #2d5016;"></i>Guaranteed fresh boxes throughout the season</li>
						<li class="mb-3"><i class="fas fa-check-circle me-2" style="color: #2d5016;"></i>Deeper connection to local agriculture</li>
						<li class="mb-3"><i class="fas fa-check-circle me-2" style="color: #2d5016;"></i>Support sustainable farming practices</li>
					</ul>
				</div>
				<div class="col-lg-6 text-center">
					<div class="csa-visual" style="background: white; padding: 40px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
						<i class="fas fa-handshake" style="font-size: 8rem; color: #2d5016; margin-bottom: 20px;"></i>
						<h4 class="fw-bold mb-3" style="color: #2d5016;">Farmer-to-Subscriber Relationship</h4>
						<p class="text-muted">Your prepayment helps farmers invest in seeds, equipment, and sustainable practices, while you receive the freshest produce throughout the season.</p>
					</div>
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
						<li><a href="index.php" class="text-light text-decoration-none">Home</a></li>
						<li><a href="view/all_product.php" class="text-light text-decoration-none">Products</a></li>
						<li><a href="premium.php" class="text-light text-decoration-none">Premium</a></li>
					</ul>
				</div>
				<div class="col-lg-4 mb-4">
					<h6 class="fw-bold mb-3 text-white">Contact</h6>
					<p class="text-light mb-0"><i class="fas fa-map-marker-alt me-2"></i>Accra, Ghana</p>
					<p class="text-light mb-0"><i class="fas fa-phone me-2"></i>+233 24 123 4567</p>
					<p class="text-light mb-0"><i class="fas fa-envelope me-2"></i>info@agrocare.gh</p>
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
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script>
		window.APP_BASE_PATH = '<?php echo htmlspecialchars($appBasePath, ENT_QUOTES); ?>';
		function subscribeToBox(plan) {
			<?php if (isset($_SESSION['customer_id'])): ?>
				Swal.fire({
					icon: 'info',
					title: 'Coming Soon!',
					text: 'Subscription feature coming soon! You will be redirected to checkout.',
					confirmButtonColor: '#2d5016',
					confirmButtonText: 'OK'
				});
				// In a real implementation, this would redirect to subscription checkout
			<?php else: ?>
				Swal.fire({
					icon: 'warning',
					title: 'Login Required',
					text: 'Please login or register to subscribe to Curiosity Box.',
					confirmButtonColor: '#2d5016',
					confirmButtonText: 'Go to Login',
					showCancelButton: true,
					cancelButtonText: 'Cancel'
				}).then((result) => {
					if (result.isConfirmed) {
						window.location.href = '<?php echo $appBasePath; ?>login/login.php';
					}
				});
			<?php endif; ?>
		}
	</script>
	<script src="js/cart.js"></script>
	<script src="js/ai_chatbot.js?v=<?php echo time(); ?>"></script>
</body>
</html>

