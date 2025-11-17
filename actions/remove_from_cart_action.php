<?php
// Start output buffering to prevent any warnings/errors from breaking JSON
ob_start();

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/cart_controller.php';

function read_request_body()
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

function json_response($payload, $code = 200)
{
    ob_clean();
    http_response_code($code);
    echo json_encode($payload);
    ob_end_flush();
    exit;
}

// Clear any output that might have been generated
ob_clean();

$input = read_request_body();
$cartId = isset($input['cart_id']) ? (int)$input['cart_id'] : 0;

if ($cartId <= 0) {
    json_response([
        'success' => false,
        'message' => 'Cart item not specified.'
    ], 400);
}

$customerId = isset($_SESSION['customer_id']) ? (int)$_SESSION['customer_id'] : null;
$guestKey = $_SESSION['cart_guest_key'] ?? ('guest:' . session_id());

try {
    remove_from_cart_ctr($cartId, $customerId, $guestKey);
    $cartData = get_user_cart_ctr($customerId, $guestKey);

    json_response([
        'success' => true,
        'message' => 'Item removed from cart.',
        'cart' => $cartData
    ]);
} catch (Throwable $th) {
    json_response([
        'success' => false,
        'message' => $th->getMessage()
    ], 500);
}

