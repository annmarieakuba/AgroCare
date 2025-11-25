<?php
// Get order details for admin
ob_start();
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../settings/db_class.php';

// Check if user is logged in and is admin
if (!is_logged_in() || !is_admin()) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    ob_end_flush();
    exit;
}

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($orderId <= 0) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Invalid order ID'
    ]);
    ob_end_flush();
    exit;
}

try {
    $db = new db_connection();
    if (!$db->db_connect()) {
        throw new Exception('Database connection failed');
    }
    $conn = $db->db;
    
    // Get order information
    $orderQuery = "
        SELECT o.order_id, o.invoice_no, o.order_date, o.order_status,
               c.customer_name, c.customer_email, c.customer_contact
        FROM orders o
        LEFT JOIN customer c ON o.customer_id = c.customer_id
        WHERE o.order_id = ?
    ";
    $orderStmt = mysqli_prepare($conn, $orderQuery);
    mysqli_stmt_bind_param($orderStmt, "i", $orderId);
    mysqli_stmt_execute($orderStmt);
    $orderResult = mysqli_stmt_get_result($orderStmt);
    $order = mysqli_fetch_assoc($orderResult);
    
    if (!$order) {
        throw new Exception('Order not found');
    }
    
    // Get order items
    $itemsQuery = "
        SELECT od.product_id, od.qty, 
               COALESCE(od.unit_price, p.product_price) as unit_price,
               p.product_title, p.product_image, p.product_desc
        FROM orderdetails od
        INNER JOIN products p ON od.product_id = p.product_id
        WHERE od.order_id = ?
    ";
    $itemsStmt = mysqli_prepare($conn, $itemsQuery);
    mysqli_stmt_bind_param($itemsStmt, "i", $orderId);
    mysqli_stmt_execute($itemsStmt);
    $itemsResult = mysqli_stmt_get_result($itemsStmt);
    $items = mysqli_fetch_all($itemsResult, MYSQLI_ASSOC);
    
    // Get payment information
    $paymentQuery = "
        SELECT payment_method, payment_reference, currency, payment_date, amt
        FROM payment
        WHERE order_id = ?
        ORDER BY payment_date DESC
        LIMIT 1
    ";
    $paymentStmt = mysqli_prepare($conn, $paymentQuery);
    mysqli_stmt_bind_param($paymentStmt, "i", $orderId);
    mysqli_stmt_execute($paymentStmt);
    $paymentResult = mysqli_stmt_get_result($paymentStmt);
    $payment = mysqli_fetch_assoc($paymentResult);
    
    // Format items
    $formattedItems = [];
    $totalAmount = 0;
    foreach ($items as $item) {
        $unitPrice = (float)$item['unit_price'];
        $quantity = (int)$item['qty'];
        $lineTotal = $unitPrice * $quantity;
        $totalAmount += $lineTotal;
        
        $formattedItems[] = [
            'product_id' => (int)$item['product_id'],
            'product_title' => $item['product_title'],
            'product_image' => $item['product_image'],
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'line_total' => $lineTotal
        ];
    }
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'data' => [
            'order' => $order,
            'items' => $formattedItems,
            'total_amount' => $totalAmount,
            'payment' => $payment
        ]
    ]);
    ob_end_flush();
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    ob_end_flush();
}
?>

