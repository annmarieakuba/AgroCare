<?php
/**
 * Paystack Connection Test
 * Visit this file in your browser to test Paystack API connection
 */

require_once __DIR__ . '/settings/paystack_config.php';
require_once __DIR__ . '/classes/paystack_class.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Paystack Connection Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
        .success { color: green; background: #e8f5e9; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #ffebee; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #e3f2fd; padding: 10px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Paystack Connection Test</h1>
    
    <?php
    echo '<div class="info"><strong>Configuration:</strong><br>';
    echo 'Currency: ' . PAYSTACK_CURRENCY . '<br>';
    echo 'API URL: ' . PAYSTACK_API_URL . '<br>';
    echo 'Public Key: ' . substr(PAYSTACK_PUBLIC_KEY, 0, 20) . '...<br>';
    echo 'Secret Key: ' . substr(PAYSTACK_SECRET_KEY, 0, 20) . '...<br>';
    echo 'Callback URL: ' . (defined('PAYSTACK_CALLBACK_URL') ? PAYSTACK_CALLBACK_URL : 'Not defined') . '<br>';
    echo '</div>';
    
    try {
        $paystack = new PaystackPayment();
        
        echo '<div class="info"><strong>Testing Paystack API Connection...</strong></div>';
        
        // Test with a small amount
        $testAmount = 1.00; // $1.00
        $testEmail = 'test@example.com';
        $testReference = 'TEST-' . time();
        
        echo '<div class="info">Attempting to initialize transaction:<br>';
        echo 'Amount: $' . $testAmount . ' (' . ($testAmount * 100) . ' cents)<br>';
        echo 'Email: ' . $testEmail . '<br>';
        echo 'Reference: ' . $testReference . '<br>';
        echo 'Currency: ' . PAYSTACK_CURRENCY . '<br>';
        echo '</div>';
        
        $result = $paystack->initializeTransaction($testAmount, $testEmail, $testReference, [
            'test' => true,
            'source' => 'connection_test'
        ]);
        
        echo '<div class="success"><strong>✓ Success!</strong><br>';
        echo 'Authorization URL: <a href="' . htmlspecialchars($result['authorization_url']) . '" target="_blank">' . htmlspecialchars($result['authorization_url']) . '</a><br>';
        echo '</div>';
        
        echo '<div class="info"><strong>Full Response:</strong></div>';
        echo '<pre>' . htmlspecialchars(json_encode($result, JSON_PRETTY_PRINT)) . '</pre>';
        
    } catch (Exception $e) {
        echo '<div class="error"><strong>✗ Error:</strong><br>';
        echo htmlspecialchars($e->getMessage()) . '<br>';
        echo '</div>';
        
        echo '<div class="info"><strong>Error Details:</strong></div>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    }
    ?>
    
    <hr>
    <p><strong>Instructions:</strong></p>
    <ul>
        <li>If you see a green success message, your Paystack integration is working!</li>
        <li>If you see a red error, check the error message for details</li>
        <li>Common issues:
            <ul>
                <li><strong>Currency not supported:</strong> Change PAYSTACK_CURRENCY in settings/paystack_config.php</li>
                <li><strong>Invalid API keys:</strong> Verify your keys in the Paystack dashboard</li>
                <li><strong>Network error:</strong> Check your server's internet connection</li>
            </ul>
        </li>
    </ul>
</body>
</html>

