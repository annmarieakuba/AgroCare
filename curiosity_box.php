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
		
		/* Animated Box Styles */
		@keyframes openFlap {
			0%, 100% { transform: rotateX(0deg); }
			50% { transform: rotateX(-120deg); }
		}
		
		@keyframes openFlapLeft {
			0%, 100% { transform: rotateY(0deg); }
			50% { transform: rotateY(90deg); }
		}
		
		@keyframes openFlapRight {
			0%, 100% { transform: rotateY(0deg); }
			50% { transform: rotateY(-90deg); }
		}
		
		@keyframes popOut {
			0% {
				opacity: 0;
				transform: translateY(0) scale(0.5);
			}
			20% {
				opacity: 1;
				transform: translateY(-30px) scale(1.2);
			}
			40% {
				transform: translateY(-40px) scale(1);
			}
			60% {
				transform: translateY(-40px) scale(1) rotate(5deg);
			}
			80% {
				transform: translateY(-40px) scale(1) rotate(-5deg);
			}
			100% {
				opacity: 1;
				transform: translateY(-40px) scale(1) rotate(0deg);
			}
		}
		
		.curiosity-box-animated {
			perspective: 1000px;
		}
		
		.box-item {
			opacity: 0;
		}
		
		.animated-box-container {
			overflow: visible;
		}
		
		/* Video/GIF Container Styles */
		.video-showcase-container {
			position: relative;
			border-radius: 20px;
			overflow: hidden;
			box-shadow: 0 15px 50px rgba(0,0,0,0.2);
			background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%);
			padding: 40px;
		}
		
		.video-placeholder {
			width: 100%;
			height: 400px;
			background: rgba(255,255,255,0.1);
			border-radius: 15px;
			display: flex;
			align-items: center;
			justify-content: center;
			position: relative;
			overflow: hidden;
		}
		
		.video-placeholder video,
		.video-placeholder img {
			width: 100%;
			height: 100%;
			object-fit: contain;
			border-radius: 15px;
			background: transparent;
		}
		
		.play-button-overlay {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			width: 80px;
			height: 80px;
			background: rgba(255,255,255,0.9);
			border-radius: 50%;
			display: flex;
			align-items: center;
			justify-content: center;
			cursor: pointer;
			transition: all 0.3s ease;
			z-index: 10;
		}
		
		.play-button-overlay:hover {
			background: white;
			transform: translate(-50%, -50%) scale(1.1);
		}
		
		.play-button-overlay i {
			font-size: 2rem;
			color: #2d5016;
			margin-left: 5px;
		}
		
		/* Enhanced Box Opening Animation */
		@keyframes openTopFlap {
			0% { transform: translateX(-50%) rotateX(0deg); }
			15% { transform: translateX(-50%) rotateX(-120deg); }
			85% { transform: translateX(-50%) rotateX(-120deg); }
			100% { transform: translateX(-50%) rotateX(0deg); }
		}
		
		@keyframes boxShake {
			0%, 100% { transform: translateX(-50%) translateY(0) rotate(0deg); }
			5% { transform: translateX(-50%) translateY(-5px) rotate(-2deg); }
			10% { transform: translateX(-50%) translateY(0) rotate(2deg); }
			15% { transform: translateX(-50%) translateY(-3px) rotate(0deg); }
			20%, 100% { transform: translateX(-50%) translateY(0) rotate(0deg); }
		}
		
		@keyframes contentGlow {
			0%, 100% { opacity: 0.1; transform: translate(-50%, -50%) scale(1); }
			50% { opacity: 0.3; transform: translate(-50%, -50%) scale(1.1); }
		}
		
		@keyframes popOutFish {
			0% { opacity: 0; transform: translateX(-50%) translateY(0) scale(0.3) rotate(0deg); }
			20% { opacity: 1; transform: translateX(-50%) translateY(-80px) scale(1.2) rotate(15deg); }
			25% { transform: translateX(-50%) translateY(-90px) scale(1) rotate(-10deg); }
			30% { transform: translateX(-50%) translateY(-85px) scale(1.1) rotate(5deg); }
			35% { transform: translateX(-50%) translateY(-90px) scale(1) rotate(0deg); }
			85% { opacity: 1; transform: translateX(-50%) translateY(-90px) scale(1) rotate(0deg); }
			100% { opacity: 0; transform: translateX(-50%) translateY(-90px) scale(0.8) rotate(0deg); }
		}
		
		@keyframes popOutBook {
			0% { opacity: 0; transform: translateX(-50%) translateY(0) scale(0.3) rotate(0deg); }
			25% { opacity: 1; transform: translateX(-50%) translateY(-70px) scale(1.2) rotate(-20deg); }
			30% { transform: translateX(-50%) translateY(-75px) scale(1) rotate(15deg); }
			35% { transform: translateX(-50%) translateY(-70px) scale(1.1) rotate(-5deg); }
			40% { transform: translateX(-50%) translateY(-75px) scale(1) rotate(0deg); }
			85% { opacity: 1; transform: translateX(-50%) translateY(-75px) scale(1) rotate(0deg); }
			100% { opacity: 0; transform: translateX(-50%) translateY(-75px) scale(0.8) rotate(0deg); }
		}
		
		@keyframes popOutCap {
			0% { opacity: 0; transform: translateX(50%) translateY(0) scale(0.3) rotate(0deg); }
			30% { opacity: 1; transform: translateX(50%) translateY(-75px) scale(1.2) rotate(25deg); }
			35% { transform: translateX(50%) translateY(-80px) scale(1) rotate(-15deg); }
			40% { transform: translateX(50%) translateY(-75px) scale(1.1) rotate(8deg); }
			45% { transform: translateX(50%) translateY(-80px) scale(1) rotate(0deg); }
			85% { opacity: 1; transform: translateX(50%) translateY(-80px) scale(1) rotate(0deg); }
			100% { opacity: 0; transform: translateX(50%) translateY(-80px) scale(0.8) rotate(0deg); }
		}
		
		@keyframes popOutFlask {
			0% { opacity: 0; transform: translateX(-50%) translateY(0) scale(0.3) rotate(0deg); }
			35% { opacity: 1; transform: translateX(-50%) translateY(-60px) scale(1.2) rotate(-15deg); }
			40% { transform: translateX(-50%) translateY(-65px) scale(1) rotate(10deg); }
			45% { transform: translateX(-50%) translateY(-60px) scale(1.1) rotate(-5deg); }
			50% { transform: translateX(-50%) translateY(-65px) scale(1) rotate(0deg); }
			85% { opacity: 1; transform: translateX(-50%) translateY(-65px) scale(1) rotate(0deg); }
			100% { opacity: 0; transform: translateX(-50%) translateY(-65px) scale(0.8) rotate(0deg); }
		}
		
		@keyframes popOutGift {
			0% { opacity: 0; transform: translateX(50%) translateY(0) scale(0.3) rotate(0deg); }
			40% { opacity: 1; transform: translateX(50%) translateY(-70px) scale(1.2) rotate(20deg); }
			45% { transform: translateX(50%) translateY(-75px) scale(1) rotate(-12deg); }
			50% { transform: translateX(50%) translateY(-70px) scale(1.1) rotate(6deg); }
			55% { transform: translateX(50%) translateY(-75px) scale(1) rotate(0deg); }
			85% { opacity: 1; transform: translateX(50%) translateY(-75px) scale(1) rotate(0deg); }
			100% { opacity: 0; transform: translateX(50%) translateY(-75px) scale(0.8) rotate(0deg); }
		}
		
		@keyframes sparkle {
			0%, 100% { opacity: 0; transform: scale(0) rotate(0deg); }
			20% { opacity: 1; transform: scale(1.5) rotate(180deg); }
			30% { opacity: 0.8; transform: scale(1) rotate(360deg); }
			50% { opacity: 1; transform: scale(1.2) rotate(540deg); }
			70% { opacity: 0.6; transform: scale(0.8) rotate(720deg); }
			85% { opacity: 0; transform: scale(0) rotate(900deg); }
		}
		
		.curiosity-box-animation-container {
			min-height: 400px;
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
						<!-- Animated Box Opening -->
						<div class="animated-box-container position-relative" style="min-height: 400px; display: flex; align-items: center; justify-content: center;">
							<!-- The Box -->
							<div class="curiosity-box-animated position-relative" style="width: 200px; height: 200px;">
								<!-- Box Base -->
								<div class="box-base" style="width: 200px; height: 150px; background: #2d5016; border-radius: 10px; position: relative; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
									<!-- Box Flaps (opening animation) -->
									<div class="box-flap box-flap-top" style="width: 200px; height: 50px; background: #4a7c59; border-radius: 10px 10px 0 0; position: absolute; top: -50px; left: 0; transform-origin: bottom center; animation: openFlap 2s ease-in-out infinite;"></div>
									<div class="box-flap box-flap-left" style="width: 50px; height: 150px; background: #3a6a4a; border-radius: 10px 0 0 10px; position: absolute; top: 0; left: -50px; transform-origin: right center; animation: openFlapLeft 2s ease-in-out infinite;"></div>
									<div class="box-flap box-flap-right" style="width: 50px; height: 150px; background: #3a6a4a; border-radius: 0 10px 10px 0; position: absolute; top: 0; right: -50px; transform-origin: left center; animation: openFlapRight 2s ease-in-out infinite;"></div>
								</div>
								
								<!-- Items popping out -->
								<div class="box-item item-1" style="position: absolute; top: -80px; left: 50%; transform: translateX(-50%); animation: popOut 2s ease-in-out infinite; animation-delay: 0.5s;">
									<i class="fas fa-fish" style="font-size: 3rem; color: #4a7c59; filter: drop-shadow(0 5px 10px rgba(0,0,0,0.3));"></i>
								</div>
								<div class="box-item item-2" style="position: absolute; top: -100px; left: 20%; transform: translateX(-50%); animation: popOut 2s ease-in-out infinite; animation-delay: 0.7s;">
									<i class="fas fa-book" style="font-size: 2.5rem; color: #ffc107; filter: drop-shadow(0 5px 10px rgba(0,0,0,0.3));"></i>
								</div>
								<div class="box-item item-3" style="position: absolute; top: -90px; right: 15%; transform: translateX(50%); animation: popOut 2s ease-in-out infinite; animation-delay: 0.9s;">
									<i class="fas fa-graduation-cap" style="font-size: 2.5rem; color: #2d5016; filter: drop-shadow(0 5px 10px rgba(0,0,0,0.3));"></i>
								</div>
								<div class="box-item item-4" style="position: absolute; bottom: -60px; left: 30%; transform: translateX(-50%); animation: popOut 2s ease-in-out infinite; animation-delay: 1.1s;">
									<i class="fas fa-flask" style="font-size: 2.5rem; color: #17a2b8; filter: drop-shadow(0 5px 10px rgba(0,0,0,0.3));"></i>
								</div>
								<div class="box-item item-5" style="position: absolute; bottom: -70px; right: 25%; transform: translateX(50%); animation: popOut 2s ease-in-out infinite; animation-delay: 1.3s;">
									<i class="fas fa-gift" style="font-size: 3rem; color: #dc3545; filter: drop-shadow(0 5px 10px rgba(0,0,0,0.3));"></i>
								</div>
							</div>
						</div>
						<h3 class="mb-3 mt-4" style="color: #2d5016;">What's Inside?</h3>
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

	<!-- Video/GIF Showcase Section -->
	<section class="py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-12 mb-4">
					<h2 class="display-5 fw-bold text-center mb-4" style="color: #2d5016;">
						<i class="fas fa-play-circle me-2"></i>See What's Inside!
					</h2>
					<p class="text-center text-muted lead mb-5">Watch a quick preview of what you'll receive in your Curiosity Box</p>
				</div>
				<div class="col-lg-10 mx-auto">
					<div class="video-showcase-container">
						<div class="video-placeholder" id="curiosityBoxVideo">
							<!-- Curiosity Box Preview GIF -->
							<?php
							// Check if GIF exists, try common filenames
							$gifPaths = [
								'images/curiosity_box.gif',
								'images/curiosity_box_preview.gif',
								'images/whats_inside.gif',
								'images/curiosity_box_animation.gif'
							];
							$gifFound = false;
							$gifPath = '';
							
							foreach ($gifPaths as $path) {
								if (file_exists(__DIR__ . '/' . $path)) {
									$gifPath = $path;
									$gifFound = true;
									break;
								}
							}
							
							if ($gifFound):
							?>
								<img src="<?php echo htmlspecialchars($gifPath); ?>" alt="Curiosity Box - What's Inside Preview" style="width: 100%; height: 100%; object-fit: contain; background: transparent;">
							<?php endif; ?>
						</div>
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
		
		// Video/GIF Playback Handler
		document.addEventListener('DOMContentLoaded', function() {
			const videoContainer = document.getElementById('curiosityBoxVideo');
			const playButton = document.getElementById('playButton');
			const video = videoContainer.querySelector('video');
			
			if (video) {
				// Show play button overlay
				playButton.style.display = 'flex';
				
				// Handle play button click
				playButton.addEventListener('click', function() {
					if (video.paused) {
						video.play();
						playButton.style.display = 'none';
					}
				});
				
				// Show play button when video ends (if not looping)
				video.addEventListener('ended', function() {
					if (!video.loop) {
						playButton.style.display = 'flex';
					}
				});
				
				// Show play button on pause
				video.addEventListener('pause', function() {
					playButton.style.display = 'flex';
				});
			}
		});
		
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

