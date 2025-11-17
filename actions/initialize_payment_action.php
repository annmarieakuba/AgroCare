<?php
// Start output buffering to prevent any warnings/errors from breaking JSON
ob_start();

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../controllers/payment_controller.php';

function read_input()
{
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
        $data = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
            return $data;
        }
    }
    return $_POST;
}

function respond($payload, $code = 200)
{
    ob_clean();
    http_response_code($code);
    echo json_encode($payload);
    ob_end_flush();
    exit;
}

if (!isset($_SESSION['customer_id'])) {
    respond([
        'success' => false,
        'message' => 'Please login before proceeding with payment.'
    ], 401);
}

$customerId = (int)$_SESSION['customer_id'];
$customerEmail = $_SESSION['customer_email'] ?? '';
$guestKey = $_SESSION['cart_guest_key'] ?? ('guest:' . session_id());

if (empty($customerEmail)) {
    respond([
        'success' => false,
        'message' => 'Customer email is required for payment.'
    ], 400);
}

try {
    // Get cart data
    $cartData = get_user_cart_ctr($customerId, $guestKey);
    if (empty($cartData['items'])) {
        respond([
            'success' => false,
            'message' => 'Your cart is empty. Add items before checking out.'
        ], 400);
    }

    $summary = $cartData['summary'] ?? [];
    $amount = (float)($summary['subtotal'] ?? 0);

    if ($amount <= 0) {
        respond([
            'success' => false,
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
    $paymentData = initialize_paystack_payment_ctr($amount, $customerEmail, $reference, $metadata);

    // Store reference in session for verification
    $_SESSION['pending_payment_reference'] = $reference;
    $_SESSION['pending_payment_cart'] = $cartData;

    respond([
        'success' => true,
        'authorization_url' => $paymentData['authorization_url'],
        'access_code' => $paymentData['access_code'],
        'reference' => $reference,
        'amount' => $amount,
        'email' => $customerEmail
    ]);
} catch (Throwable $th) {
    respond([
        'success' => false,
        'message' => $th->getMessage()
    ], 500);
}

?>

