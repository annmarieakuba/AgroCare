<?php

require_once __DIR__ . '/../classes/paystack_class.php';
require_once __DIR__ . '/../classes/order_class.php';
require_once __DIR__ . '/cart_controller.php';

function paystack_instance()
{
    static $paystack = null;
    if ($paystack === null) {
        $paystack = new PaystackPayment();
    }
    return $paystack;
}

/**
 * Initialize Paystack payment
 * 
 * @param float $amount Order total amount
 * @param string $email Customer email
 * @param string $reference Unique transaction reference
 * @param array $metadata Additional metadata
 * @return array Paystack initialization response
 */
function initialize_paystack_payment_ctr($amount, $email, $reference, $metadata = [])
{
    return paystack_instance()->initializeTransaction($amount, $email, $reference, $metadata);
}

/**
 * Verify Paystack payment and complete order
 * 
 * @param string $reference Transaction reference
 * @param int $customerId Customer ID
 * @param array $cartData Cart data
 * @param string $currency Currency code
 * @return array Order details
 */
function verify_paystack_payment_ctr($reference, $customerId, $cartData, $currency = 'NGN')
{
    $paystack = paystack_instance();
    $order = new Order();

    // Verify payment with Paystack
    $verification = $paystack->verifyTransaction($reference);

    if ($verification['status'] !== 'success') {
        throw new RuntimeException('Payment verification failed. Transaction status: ' . $verification['status']);
    }

    // Calculate total
    $items = $cartData['items'] ?? [];
    $subtotal = 0.0;
    foreach ($items as $item) {
        $qty = (int)($item['qty'] ?? 0);
        $unitPrice = (float)($item['product_price'] ?? 0);
        $subtotal += $qty * $unitPrice;
    }
    $subtotal = round($subtotal, 2);

    // Verify amount matches
    $paidAmount = $verification['amount'] / 100; // Convert from kobo/cents
    if (abs($paidAmount - $subtotal) > 0.01) {
        throw new RuntimeException('Payment amount mismatch. Expected: ' . $subtotal . ', Paid: ' . $paidAmount);
    }

    // Generate invoice
    $invoiceNo = $order->generateInvoiceNumber();

    // Create order and process payment
    $order->db->begin_transaction();
    try {
        $orderId = $order->createOrder($customerId, $invoiceNo, 'completed', $subtotal);

        foreach ($items as $item) {
            $order->addOrderDetail(
                $orderId,
                (int)$item['product_id'],
                (int)$item['qty'],
                (float)$item['product_price']
            );
        }

        $order->recordPayment(
            $orderId,
            $customerId,
            $subtotal,
            $currency,
            'Paystack',
            $reference
        );

        $order->db->commit();

        return [
            'order_id' => $orderId,
            'invoice_no' => $invoiceNo,
            'payment_reference' => $reference,
            'total_amount' => $subtotal,
            'total_items' => $cartData['summary']['total_items'] ?? array_sum(array_column($items, 'qty')),
            'paystack_data' => $verification
        ];
    } catch (Throwable $th) {
        $order->db->rollback();
        throw $th;
    }
}

/**
 * Get Paystack public key for frontend
 * 
 * @return string Public key
 */
function get_paystack_public_key_ctr()
{
    return paystack_instance()->getPublicKey();
}

?>

