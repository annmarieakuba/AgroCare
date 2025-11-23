<?php

require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/SMTP.php';
require_once __DIR__ . '/../PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Email Service Class
 * Handles sending order confirmation and other transactional emails
 */
class EmailService
{
    private $mailer;
    private $fromEmail;
    private $fromName;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->fromEmail = 'noreply@agrocare.gh'; // Change to your actual email
        $this->fromName = 'AgroCare - Fresh Farm. Smart Nutrition. Delivered.';
        
        // Configure SMTP (using Gmail as example - adjust for your email provider)
        // For local development, you can use mail() function or configure SMTP
        try {
            // Option 1: Use PHP's mail() function (works on most servers)
            $this->mailer->isMail();
            
            // Option 2: Use SMTP (uncomment and configure if needed)
            /*
            $this->mailer->isSMTP();
            $this->mailer->Host = 'smtp.gmail.com'; // Your SMTP server
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = 'your-email@gmail.com'; // Your email
            $this->mailer->Password = 'your-app-password'; // Your app password
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = 587;
            */
            
            $this->mailer->setFrom($this->fromEmail, $this->fromName);
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';
        } catch (Exception $e) {
            error_log("EmailService initialization error: " . $e->getMessage());
        }
    }

    /**
     * Send order confirmation email
     */
    public function sendOrderConfirmation($customerEmail, $customerName, $orderData)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($customerEmail, $customerName);
            $this->mailer->Subject = 'Order Confirmation - ' . $orderData['invoice_no'];
            
            $htmlBody = $this->generateOrderEmailHTML($customerName, $orderData);
            $textBody = $this->generateOrderEmailText($customerName, $orderData);
            
            $this->mailer->Body = $htmlBody;
            $this->mailer->AltBody = $textBody;
            
            $result = $this->mailer->send();
            
            if ($result) {
                error_log("Order confirmation email sent successfully to: " . $customerEmail);
                return true;
            } else {
                error_log("Failed to send order confirmation email to: " . $customerEmail);
                return false;
            }
        } catch (Exception $e) {
            error_log("Email sending error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    /**
     * Generate HTML email template for order confirmation
     */
    private function generateOrderEmailHTML($customerName, $orderData)
    {
        $currencySymbol = $orderData['currency'] === 'GHS' ? '₵' : ($orderData['currency'] === 'NGN' ? '₦' : '$');
        $items = $orderData['items'] ?? [];
        
        $itemsHTML = '';
        foreach ($items as $item) {
            $itemsHTML .= '
                <tr>
                    <td style="padding: 10px; border-bottom: 1px solid #e0e0e0;">' . htmlspecialchars($item['product_name'] ?? 'Product') . '</td>
                    <td style="padding: 10px; border-bottom: 1px solid #e0e0e0; text-align: center;">' . ($item['qty'] ?? 0) . '</td>
                    <td style="padding: 10px; border-bottom: 1px solid #e0e0e0; text-align: right;">' . $currencySymbol . number_format($item['product_price'] ?? 0, 2) . '</td>
                    <td style="padding: 10px; border-bottom: 1px solid #e0e0e0; text-align: right;">' . $currencySymbol . number_format(($item['qty'] ?? 0) * ($item['product_price'] ?? 0), 2) . '</td>
                </tr>';
        }
        
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - AgroCare</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #2d5016 0%, #4a7c59 100%); padding: 30px; text-align: center;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px;">
                                <i class="fas fa-seedling" style="margin-right: 10px;"></i>AgroCare
                            </h1>
                            <p style="color: #ffffff; margin: 10px 0 0 0; font-size: 14px;">Fresh Farm. Smart Nutrition. Delivered.</p>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 30px;">
                            <h2 style="color: #2d5016; margin-top: 0;">Thank You for Your Order, ' . htmlspecialchars($customerName) . '!</h2>
                            <p style="color: #666; line-height: 1.6;">We\'re excited to confirm your order. Your fresh farm products are being prepared and will be delivered to you soon.</p>
                            
                            <!-- Order Details -->
                            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                                <h3 style="color: #2d5016; margin-top: 0;">Order Details</h3>
                                <table width="100%" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td style="padding: 5px 0; color: #666;"><strong>Order ID:</strong></td>
                                        <td style="padding: 5px 0; text-align: right; color: #333;">#' . htmlspecialchars($orderData['order_id'] ?? 'N/A') . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #666;"><strong>Invoice No:</strong></td>
                                        <td style="padding: 5px 0; text-align: right; color: #333;">' . htmlspecialchars($orderData['invoice_no'] ?? 'N/A') . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #666;"><strong>Payment Reference:</strong></td>
                                        <td style="padding: 5px 0; text-align: right; color: #333;">' . htmlspecialchars($orderData['payment_reference'] ?? 'N/A') . '</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 5px 0; color: #666;"><strong>Order Date:</strong></td>
                                        <td style="padding: 5px 0; text-align: right; color: #333;">' . htmlspecialchars($orderData['order_date'] ?? date('F j, Y')) . '</td>
                                    </tr>
                                </table>
                            </div>
                            
                            <!-- Order Items -->
                            <h3 style="color: #2d5016; margin-top: 30px;">Order Items</h3>
                            <table width="100%" cellpadding="0" cellspacing="0" style="border-collapse: collapse; margin: 20px 0;">
                                <thead>
                                    <tr style="background-color: #f8f9fa;">
                                        <th style="padding: 10px; text-align: left; border-bottom: 2px solid #2d5016;">Product</th>
                                        <th style="padding: 10px; text-align: center; border-bottom: 2px solid #2d5016;">Quantity</th>
                                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid #2d5016;">Unit Price</th>
                                        <th style="padding: 10px; text-align: right; border-bottom: 2px solid #2d5016;">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ' . $itemsHTML . '
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="3" style="padding: 15px 10px; text-align: right; font-weight: bold; border-top: 2px solid #2d5016;">Total Amount:</td>
                                        <td style="padding: 15px 10px; text-align: right; font-weight: bold; font-size: 18px; color: #2d5016; border-top: 2px solid #2d5016;">' . $currencySymbol . number_format($orderData['total_amount'] ?? 0, 2) . '</td>
                                    </tr>
                                </tfoot>
                            </table>
                            
                            <p style="color: #666; line-height: 1.6; margin-top: 30px;">We\'ll send you another email once your order has been shipped. If you have any questions, please don\'t hesitate to contact us.</p>
                            
                            <div style="text-align: center; margin-top: 30px;">
                                <a href="' . (defined('APP_BASE_URL') ? APP_BASE_URL : 'http://localhost') . '/index.php" style="display: inline-block; background-color: #2d5016; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold;">Continue Shopping</a>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8f9fa; padding: 20px; text-align: center; color: #666; font-size: 12px;">
                            <p style="margin: 0;">&copy; ' . date('Y') . ' AgroCare. All rights reserved.</p>
                            <p style="margin: 5px 0 0 0;">Accra, Ghana | Fresh Farm. Smart Nutrition. Delivered.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>';
    }

    /**
     * Generate plain text email for order confirmation
     */
    private function generateOrderEmailText($customerName, $orderData)
    {
        $currencySymbol = $orderData['currency'] === 'GHS' ? '₵' : ($orderData['currency'] === 'NGN' ? '₦' : '$');
        $items = $orderData['items'] ?? [];
        
        $text = "Thank You for Your Order, {$customerName}!\n\n";
        $text .= "We're excited to confirm your order. Your fresh farm products are being prepared and will be delivered to you soon.\n\n";
        $text .= "ORDER DETAILS\n";
        $text .= "Order ID: #" . ($orderData['order_id'] ?? 'N/A') . "\n";
        $text .= "Invoice No: " . ($orderData['invoice_no'] ?? 'N/A') . "\n";
        $text .= "Payment Reference: " . ($orderData['payment_reference'] ?? 'N/A') . "\n";
        $text .= "Order Date: " . ($orderData['order_date'] ?? date('F j, Y')) . "\n\n";
        $text .= "ORDER ITEMS\n";
        $text .= str_repeat("-", 50) . "\n";
        
        foreach ($items as $item) {
            $text .= ($item['product_name'] ?? 'Product') . " x " . ($item['qty'] ?? 0) . " = " . $currencySymbol . number_format(($item['qty'] ?? 0) * ($item['product_price'] ?? 0), 2) . "\n";
        }
        
        $text .= str_repeat("-", 50) . "\n";
        $text .= "Total Amount: " . $currencySymbol . number_format($orderData['total_amount'] ?? 0, 2) . "\n\n";
        $text .= "We'll send you another email once your order has been shipped.\n\n";
        $text .= "AgroCare - Fresh Farm. Smart Nutrition. Delivered.\n";
        $text .= "Accra, Ghana\n";
        
        return $text;
    }
}

