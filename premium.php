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
	<title>Premium Membership - AgroCare | Elevate Your Experience</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
	<link href="css/index.css?v=<?php echo time(); ?>" rel="stylesheet">
	<style>
		.premium-hero {
			background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);
			padding: 120px 0 80px;
			color: white;
			text-align: center;
		}
		.membership-card {
			background: white;
			border-radius: 15px;
			padding: 40px;
			box-shadow: 0 5px 20px rgba(0,0,0,0.1);
			transition: all 0.3s ease;
			border: 3px solid transparent;
			height: 100%;
			position: relative;
		}
		.membership-card:hover {
			transform: translateY(-10px);
			box-shadow: 0 15px 40px rgba(45, 80, 22, 0.2);
		}
		.membership-card.premium {
			border-color: #ffc107;
		}
		.membership-card.premium::before {
			content: 'BEST VALUE';
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
		.feature-check {
			color: #28a745;
			margin-right: 10px;
		}
		.feature-x {
			color: #dc3545;
			margin-right: 10px;
		}
		.comparison-table {
			background: white;
			border-radius: 15px;
			overflow: hidden;
			box-shadow: 0 5px 20px rgba(0,0,0,0.1);
		}
		.testimonial-card {
			background: white;
			border-radius: 15px;
			padding: 30px;
			box-shadow: 0 5px 20px rgba(0,0,0,0.1);
			height: 100%;
		}
		.testimonial-card .stars {
			color: #ffc107;
			margin-bottom: 15px;
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
					<li class="nav-item"><a class="nav-link" href="curiosity_box.php"><i class="fas fa-box me-1"></i>Curiosity Box</a></li>
					<li class="nav-item"><a class="nav-link active" href="premium.php"><i class="fas fa-crown me-1"></i>Premium</a></li>
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
	<section class="premium-hero">
		<div class="container">
			<h1 class="display-3 fw-bold mb-4">Elevate Your AgroCare Experience</h1>
			<p class="lead mb-4">Unlock exclusive benefits, advanced features, and premium support with our membership tiers</p>
			<a href="#membership-tiers" class="btn btn-light btn-lg">
				<i class="fas fa-crown me-2"></i>Explore Memberships
			</a>
		</div>
	</section>

	<!-- Membership Tiers -->
	<section class="py-5" id="membership-tiers">
		<div class="container">
			<h2 class="display-5 fw-bold text-center mb-5" style="color: #2d5016;">Choose Your Membership</h2>
			<div class="row g-4">
				<!-- Basic (Free) -->
				<div class="col-lg-4">
					<div class="membership-card">
						<h3 class="fw-bold mb-3" style="color: #2d5016;">Basic</h3>
						<div class="mb-4">
							<span class="display-4 fw-bold" style="color: #2d5016;">Free</span>
						</div>
						<ul class="list-unstyled mb-4">
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Browse all products</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Standard pricing</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Basic AI chatbot access (3 queries/day)</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Pay-per-delivery fees</li>
							<li class="mb-2"><i class="fas fa-times feature-x"></i>No discounts</li>
							<li class="mb-2"><i class="fas fa-times feature-x"></i>Limited AI features</li>
						</ul>
						<button class="btn w-100" style="background: #6c757d; color: white;" disabled>Current Plan</button>
					</div>
				</div>

				<!-- Curiosity Box Subscriber -->
				<div class="col-lg-4">
					<div class="membership-card">
						<h3 class="fw-bold mb-3" style="color: #2d5016;">Curiosity Box Subscriber</h3>
						<div class="mb-4">
							<span class="display-4 fw-bold" style="color: #2d5016;">₵80-150</span>
							<span class="text-muted">/month</span>
						</div>
						<ul class="list-unstyled mb-4">
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Everything in Basic</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Monthly personalized protein box</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>10% discount on all store purchases</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Free delivery on subscription boxes</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Unlimited AI Dietitian access</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Exclusive recipes and educational content</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Loyalty points: 2x earning rate</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Access to subscriber community forum</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Flexible subscription management</li>
						</ul>
						<a href="curiosity_box.php" class="btn w-100" style="background: linear-gradient(135deg, #2d5016, #4a7c59); color: white;">
							Subscribe Now
						</a>
					</div>
				</div>

				<!-- Premium Member -->
				<div class="col-lg-4">
					<div class="membership-card premium">
						<h3 class="fw-bold mb-3" style="color: #2d5016;">Premium Member</h3>
						<div class="mb-4">
							<span class="display-4 fw-bold" style="color: #2d5016;">₵200</span>
							<span class="text-muted">/month</span>
							<div class="small text-muted">or ₵2,000/year (save ₵400)</div>
						</div>
						<ul class="list-unstyled mb-4">
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Everything in Curiosity Box tier</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>15% discount on ALL purchases</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Free delivery on ALL orders (no minimum)</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Priority customer support (24/7 human agent)</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Advanced AI features (voice assistant, multi-language)</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Monthly video call with nutritionist</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Quarterly farm visit opportunity</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Exclusive access to limited-edition products</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Birthday surprises and seasonal bonus boxes</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Early access to new features and products</li>
							<li class="mb-2"><i class="fas fa-check feature-check"></i>Referral bonuses: Earn ₵20 per successful referral</li>
						</ul>
						<button class="btn w-100 btn-warning" onclick="upgradeToPremium()">
							<i class="fas fa-crown me-2"></i>Join Premium Today
						</button>
						<div class="text-center mt-3">
							<small class="text-muted">
								<i class="fas fa-shield-alt me-1"></i>30-Day Money-Back Guarantee
							</small>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Comparison Table -->
	<section class="py-5 bg-light">
		<div class="container">
			<h2 class="display-5 fw-bold text-center mb-5" style="color: #2d5016;">Feature Comparison</h2>
			<div class="comparison-table">
				<table class="table table-hover mb-0">
					<thead style="background: linear-gradient(135deg, #2d5016, #4a7c59); color: white;">
						<tr>
							<th>Feature</th>
							<th class="text-center">Basic</th>
							<th class="text-center">Curiosity Box</th>
							<th class="text-center">Premium</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Product Access</td>
							<td class="text-center"><i class="fas fa-check text-success"></i></td>
							<td class="text-center"><i class="fas fa-check text-success"></i></td>
							<td class="text-center"><i class="fas fa-check text-success"></i></td>
						</tr>
						<tr>
							<td>Discount on Purchases</td>
							<td class="text-center"><i class="fas fa-times text-danger"></i></td>
							<td class="text-center">10%</td>
							<td class="text-center">15%</td>
						</tr>
						<tr>
							<td>Free Delivery</td>
							<td class="text-center"><i class="fas fa-times text-danger"></i></td>
							<td class="text-center">Subscription boxes only</td>
							<td class="text-center">All orders</td>
						</tr>
						<tr>
							<td>AI Dietitian Access</td>
							<td class="text-center">3 queries/day</td>
							<td class="text-center">Unlimited</td>
							<td class="text-center">Unlimited + Advanced</td>
						</tr>
						<tr>
							<td>Priority Support</td>
							<td class="text-center"><i class="fas fa-times text-danger"></i></td>
							<td class="text-center"><i class="fas fa-times text-danger"></i></td>
							<td class="text-center"><i class="fas fa-check text-success"></i> 24/7</td>
						</tr>
						<tr>
							<td>Nutritionist Consultation</td>
							<td class="text-center"><i class="fas fa-times text-danger"></i></td>
							<td class="text-center"><i class="fas fa-times text-danger"></i></td>
							<td class="text-center"><i class="fas fa-check text-success"></i> Monthly</td>
						</tr>
						<tr>
							<td>Farm Visits</td>
							<td class="text-center"><i class="fas fa-times text-danger"></i></td>
							<td class="text-center"><i class="fas fa-times text-danger"></i></td>
							<td class="text-center"><i class="fas fa-check text-success"></i> Quarterly</td>
						</tr>
						<tr>
							<td>Referral Bonuses</td>
							<td class="text-center"><i class="fas fa-times text-danger"></i></td>
							<td class="text-center"><i class="fas fa-times text-danger"></i></td>
							<td class="text-center">₵20 per referral</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</section>

	<!-- Testimonials -->
	<section class="py-5">
		<div class="container">
			<h2 class="display-5 fw-bold text-center mb-5" style="color: #2d5016;">What Our Members Say</h2>
			<div class="row g-4">
				<div class="col-lg-6">
					<div class="testimonial-card">
						<div class="stars">
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
						</div>
						<p class="mb-3">"As a university student, the Curiosity Box has been a game-changer. The personalized protein portions and recipes help me maintain a healthy diet on a budget. The educational materials are fantastic!"</p>
						<div class="d-flex align-items-center">
							<div class="me-3">
								<i class="fas fa-user-circle" style="font-size: 2.5rem; color: #2d5016;"></i>
							</div>
							<div>
								<h6 class="fw-bold mb-0">Sarah M.</h6>
								<small class="text-muted">University Student, Curiosity Box Subscriber</small>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="testimonial-card">
						<div class="stars">
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
						</div>
						<p class="mb-3">"The Premium membership is worth every cedi! The 15% discount, free delivery, and monthly nutritionist calls have transformed how I approach my fitness goals. The farm visits are an incredible bonus!"</p>
						<div class="d-flex align-items-center">
							<div class="me-3">
								<i class="fas fa-user-circle" style="font-size: 2.5rem; color: #2d5016;"></i>
							</div>
							<div>
								<h6 class="fw-bold mb-0">Kwame A.</h6>
								<small class="text-muted">Fitness Enthusiast, Premium Member</small>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="testimonial-card">
						<div class="stars">
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
						</div>
						<p class="mb-3">"Our family loves the CSA model! Prepaying for the seasonal share helps us plan our meals, and we love knowing we're directly supporting local farmers. The boxes are always fresh and full of surprises."</p>
						<div class="d-flex align-items-center">
							<div class="me-3">
								<i class="fas fa-user-circle" style="font-size: 2.5rem; color: #2d5016;"></i>
							</div>
							<div>
								<h6 class="fw-bold mb-0">Ama K.</h6>
								<small class="text-muted">Family Subscriber, CSA Member</small>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="testimonial-card">
						<div class="stars">
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
							<i class="fas fa-star"></i>
						</div>
						<p class="mb-3">"As a restaurant owner, the B2B partnership with AgroCare has been excellent. The premium membership gives us priority access to rare items and the bulk discounts help our bottom line."</p>
						<div class="d-flex align-items-center">
							<div class="me-3">
								<i class="fas fa-user-circle" style="font-size: 2.5rem; color: #2d5016;"></i>
							</div>
							<div>
								<h6 class="fw-bold mb-0">David O.</h6>
								<small class="text-muted">Restaurant Owner, Premium B2B Partner</small>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- CTA Section -->
	<section class="py-5" style="background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);">
		<div class="container text-center text-white">
			<h2 class="display-5 fw-bold mb-4">Ready to Elevate Your Experience?</h2>
			<p class="lead mb-4">Join Premium today and get 30 days free trial. No credit card required.</p>
			<div class="d-flex justify-content-center gap-3 flex-wrap">
				<button class="btn btn-light btn-lg" onclick="upgradeToPremium()">
					<i class="fas fa-crown me-2"></i>Try Premium Free for 30 Days
				</button>
				<a href="curiosity_box.php" class="btn btn-outline-light btn-lg">
					<i class="fas fa-box me-2"></i>Start with Curiosity Box
				</a>
			</div>
			<div class="mt-4">
				<small>
					<i class="fas fa-shield-alt me-1"></i>Money-back guarantee | 
					<i class="fas fa-lock me-1"></i>Secure payment | 
					<i class="fas fa-sync-alt me-1"></i>Cancel anytime
				</small>
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
						<li><a href="curiosity_box.php" class="text-light text-decoration-none">Curiosity Box</a></li>
						<li><a href="view/all_product.php" class="text-light text-decoration-none">Products</a></li>
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
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script>
		window.APP_BASE_PATH = '<?php echo htmlspecialchars($appBasePath, ENT_QUOTES); ?>';
		function upgradeToPremium() {
			<?php if (isset($_SESSION['customer_id'])): ?>
				Swal.fire({
					icon: 'info',
					title: 'Coming Soon!',
					text: 'Premium membership upgrade coming soon! You will be redirected to checkout.',
					confirmButtonColor: '#ffc107',
					confirmButtonText: 'OK'
				});
				// In a real implementation, this would redirect to premium checkout
			<?php else: ?>
				Swal.fire({
					icon: 'warning',
					title: 'Login Required',
					text: 'Please login or register to upgrade to Premium membership.',
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

