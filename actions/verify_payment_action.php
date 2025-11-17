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

// Get reference from query string or POST
$reference = $_GET['reference'] ?? $_POST['reference'] ?? '';

if (empty($reference)) {
    respond([
        'success' => false,
        'message' => 'Payment reference is required.'
    ], 400);
}

if (!isset($_SESSION['customer_id'])) {
    respond([
        'success' => false,
        'message' => 'Please login to verify payment.'
    ], 401);
}

$customerId = (int)$_SESSION['customer_id'];
$guestKey = $_SESSION['cart_guest_key'] ?? ('guest:' . session_id());

try {
    // Get cart data (use stored cart or fetch current)
    $cartData = $_SESSION['pending_payment_cart'] ?? get_user_cart_ctr($customerId, $guestKey);
    
    if (empty($cartData['items'])) {
        respond([
            'success' => false,
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

    respond([
        'success' => true,
        'message' => 'Payment verified successfully. Order created.',
        'order' => [
            'order_id' => $orderResult['order_id'],
            'reference' => $orderResult['invoice_no'],
            'payment_reference' => $orderResult['payment_reference'],
            'total_amount' => $orderResult['total_amount'],
            'currency' => 'NGN',
            'total_items' => $orderResult['total_items']
        ]
    ]);
} catch (Throwable $th) {
    respond([
        'success' => false,
        'message' => $th->getMessage()
    ], 500);
}

?>

