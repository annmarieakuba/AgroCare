<?php
/**
 * Get customer orders with details
 * Returns all orders for the logged-in customer with order details
 */

// Start output buffering to catch any warnings/errors
ob_start();
header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/order_controller.php';
require_once __DIR__ . '/../classes/order_class.php';
require_once __DIR__ . '/../classes/product_class.php';

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Please log in to view your orders'
    ]);
    ob_end_flush();
    exit;
}

// Check if user is admin (admins shouldn't access customer orders)
if (isset($_SESSION['user_role']) && (int)$_SESSION['user_role'] === 1) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Admin users cannot view customer orders'
    ]);
    ob_end_flush();
    exit;
}

try {
    $customerId = (int)$_SESSION['customer_id'];
    
    // Get all orders for the customer
    $orders = get_customer_orders_ctr($customerId);
    
    if (empty($orders)) {
        echo json_encode([
            'success' => true,
            'data' => [],
            'message' => 'No orders found'
        ]);
        exit;
    }
    
    // Get order details and payment information for each order
    $orderInstance = new Order();
    $product = new Product();
    
    $ordersWithDetails = [];
    
    foreach ($orders as $orderData) {
        $orderId = (int)$orderData['order_id'];
        
        // Get order details (products in the order)
        $detailsStmt = $orderInstance->db->prepare("
            SELECT od.product_id, od.qty, od.unit_price,
                   p.product_title, p.product_image, p.product_desc
            FROM orderdetails od
            INNER JOIN products p ON od.product_id = p.product_id
            WHERE od.order_id = ?
        ");
        $detailsStmt->bind_param("i", $orderId);
        $detailsStmt->execute();
        $orderDetails = $detailsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
        
        // Get payment information
        $paymentStmt = $orderInstance->db->prepare("
            SELECT payment_method, payment_reference, currency, payment_date
            FROM payment
            WHERE order_id = ?
            ORDER BY payment_date DESC
            LIMIT 1
        ");
        $paymentStmt->bind_param("i", $orderId);
        $paymentStmt->execute();
        $paymentData = $paymentStmt->get_result()->fetch_assoc();
        
        // Format order details
        $formattedDetails = [];
        foreach ($orderDetails as $detail) {
            $formattedDetails[] = [
                'product_id' => (int)$detail['product_id'],
                'product_title' => $detail['product_title'],
                'product_image' => $detail['product_image'],
                'product_desc' => $detail['product_desc'],
                'quantity' => (int)$detail['qty'],
                'unit_price' => (float)$detail['unit_price'],
                'line_total' => (float)($detail['qty'] * $detail['unit_price'])
            ];
        }
        
        // Calculate total items
        $totalItems = array_sum(array_column($formattedDetails, 'quantity'));
        
        $ordersWithDetails[] = [
            'order_id' => $orderId,
            'invoice_no' => $orderData['invoice_no'],
            'order_date' => $orderData['order_date'],
            'order_status' => $orderData['order_status'],
            'total_amount' => (float)$orderData['total_amount'],
            'total_items' => $totalItems,
            'items' => $formattedDetails,
            'payment' => $paymentData ? [
                'method' => $paymentData['payment_method'],
                'reference' => $paymentData['payment_reference'],
                'currency' => $paymentData['currency'],
                'date' => $paymentData['payment_date']
            ] : null
        ];
    }
    
    ob_clean();
    echo json_encode([
        'success' => true,
        'data' => $ordersWithDetails,
        'count' => count($ordersWithDetails)
    ]);
    ob_end_flush();
    
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'message' => 'Error fetching orders: ' . $e->getMessage()
    ]);
    ob_end_flush();
}

