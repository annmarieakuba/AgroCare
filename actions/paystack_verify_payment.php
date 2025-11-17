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

// Get reference from POST or GET
$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$reference = $input['reference'] ?? $_GET['reference'] ?? '';

if (empty($reference)) {
    respond([
        'status' => 'error',
        'message' => 'Payment reference is required.'
    ], 400);
}

if (!isset($_SESSION['customer_id'])) {
    respond([
        'status' => 'error',
        'message' => 'Please login to verify payment.'
    ], 401);
}

$customerId = (int)$_SESSION['customer_id'];
$guestKey = $_SESSION['cart_guest_key'] ?? ('guest:' . session_id());

try {
    // Get cart data from session (stored during initialization)
    $cartData = $_SESSION['pending_payment_cart'] ?? get_user_cart_ctr($customerId, $guestKey);
    
    if (empty($cartData['items'])) {
        respond([
            'status' => 'error',
            'message' => 'Cart data not found.'
        ], 400);
    }

    // Verify payment and complete order
    $orderResult = verify_paystack_payment_ctr($reference, $customerId, $cartData, 'NGN');

    // Clear cart after successful payment
    empty_cart_ctr($customerId, $guestKey);

    // Clear pending payment session data
    unset($_SESSION['pending_payment_reference']);
    unset($_SESSION['pending_payment_cart']);
    unset($_SESSION['pending_payment_amount']);
    unset($_SESSION['pending_payment_email']);

    respond([
        'status' => 'success',
        'verified' => true,
        'message' => 'Payment successful! Order confirmed.',
        'order_id' => $orderResult['order_id'],
        'invoice_no' => $orderResult['invoice_no'],
        'total_amount' => number_format($orderResult['total_amount'], 2),
        'currency' => 'NGN',
        'order_date' => date('F j, Y'),
        'customer_name' => $_SESSION['customer_name'] ?? 'Customer',
        'item_count' => $orderResult['total_items'],
        'payment_reference' => $orderResult['payment_reference'],
        'payment_method' => 'Paystack',
        'customer_email' => $_SESSION['customer_email'] ?? ''
    ]);
} catch (Throwable $th) {
    respond([
        'status' => 'error',
        'verified' => false,
        'message' => $th->getMessage()
    ], 500);
}

?>

