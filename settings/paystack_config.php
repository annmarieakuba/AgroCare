<?php
/**
 * Paystack Configuration
 * 
 * Replace these with your actual Paystack keys from your dashboard:
 * https://dashboard.paystack.com/#/settings/developer
 */

// Public key (used in frontend JavaScript)
define('PAYSTACK_PUBLIC_KEY', 'pk_test_ca492b9787289153c69d2c9757c7a81babc52639');

// Secret key (used in backend PHP - keep this secure!)
define('PAYSTACK_SECRET_KEY', 'sk_test_c931cd7fde5b564318dc920028a8c3e16409163a');

// Paystack API base URL
define('PAYSTACK_API_URL', 'https://api.paystack.co');

// Currency (USD, NGN, GHS, ZAR, etc.) - Check your Paystack dashboard for supported currencies
define('PAYSTACK_CURRENCY', 'GHS');

// Get base URL for callback
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$basePath = rtrim($scriptPath, '/');
define('APP_BASE_URL', $protocol . $host . $basePath);

// Paystack callback URL
define('PAYSTACK_CALLBACK_URL', APP_BASE_URL . '/view/paystack_callback.php');

?>
