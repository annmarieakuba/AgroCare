<?php
// Start output buffering to prevent any warnings/errors from breaking JSON
ob_start();

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../controllers/payment_controller.php';

function respond($payload, $code = 200)
{
    ob_clean();
    http_response_code($code);
    echo json_encode($payload);
    ob_end_flush();
    exit;
}

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    respond([
        'status' => 'error',
        'message' => 'Please login before proceeding with payment.'
    ], 401);
}

$customerId = (int)$_SESSION['customer_id'];
$customerEmail = $_SESSION['customer_email'] ?? '';
$guestKey = $_SESSION['cart_guest_key'] ?? ('guest:' . session_id());

// Get email from POST if provided, otherwise use session email
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$email = $input['email'] ?? $customerEmail;

if (empty($email)) {
    respond([
        'status' => 'error',
        'message' => 'Email address is required for payment.'
    ], 400);
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond([
        'status' => 'error',
        'message' => 'Invalid email address format.'
    ], 400);
}

try {
    // Get cart data
    $cartData = get_user_cart_ctr($customerId, $guestKey);
    if (empty($cartData['items'])) {
        respond([
            'status' => 'error',
            'message' => 'Your cart is empty. Add items before checking out.'
        ], 400);
    }

    $summary = $cartData['summary'] ?? [];
    $amount = (float)($summary['subtotal'] ?? 0);

    if ($amount <= 0) {
        respond([
            'status' => 'error',
            'message' => 'Invalid order amount.'
        ], 400);
    }

    // Generate unique reference
    $reference = 'AGRO-' . time() . '-' . strtoupper(bin2hex(random_bytes(4)));

    // Prepare metadata
    $metadata = [
        'customer_id' => $customerId,
        'customer_name' => $_SESSION['customer_name'] ?? 'Customer',
        'cart_items' => count($cartData['items'])
    ];

    // Initialize Paystack payment
    $paymentData = initialize_paystack_payment_ctr($amount, $email, $reference, $metadata);

    // Store reference and cart data in session for verification
    $_SESSION['pending_payment_reference'] = $reference;
    $_SESSION['pending_payment_cart'] = $cartData;
    $_SESSION['pending_payment_amount'] = $amount;
    $_SESSION['pending_payment_email'] = $email;

    respond([
        'status' => 'success',
        'authorization_url' => $paymentData['authorization_url'],
        'reference' => $reference,
        'access_code' => $paymentData['access_code'],
        'message' => 'Redirecting to payment gateway...'
    ]);
} catch (Throwable $th) {
    respond([
        'status' => 'error',
        'message' => $th->getMessage()
    ], 500);
}

?>

