<?php
// Start output buffering to prevent any warnings/errors from breaking JSON
ob_start();

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/cart_controller.php';

// Clear any output that might have been generated
ob_clean();

$customerId = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
$guestKey = $_SESSION['cart_guest_key'] ?? ('guest:' . session_id());

try {
    $cartData = get_user_cart_ctr($customerId, $guestKey);
    echo json_encode([
        'success' => true,
        'cart' => $cartData,
        'customer_id' => $customerId
    ]);
} catch (Throwable $th) {
    ob_clean();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $th->getMessage()
    ]);
}

ob_end_flush();
?>

