<?php

require_once __DIR__ . '/../settings/paystack_config.php';

// Ensure callback URL is defined
if (!defined('PAYSTACK_CALLBACK_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    $basePath = rtrim($scriptPath, '/');
    define('PAYSTACK_CALLBACK_URL', $protocol . $host . $basePath . '/view/paystack_callback.php');
}

/**
 * Paystack Payment Integration Class
 * Handles Paystack API interactions for payment processing
 */
class PaystackPayment
{
    private $secretKey;
    private $publicKey;
    private $apiUrl;

    public function __construct()
    {
        $this->secretKey = PAYSTACK_SECRET_KEY;
        $this->publicKey = PAYSTACK_PUBLIC_KEY;
        $this->apiUrl = PAYSTACK_API_URL;
    }

    /**
     * Initialize a Paystack transaction
     * 
     * @param float $amount Amount in kobo (for NGN) or cents (for USD)
     * @param string $email Customer email
     * @param string $reference Unique transaction reference
     * @param array $metadata Additional metadata (optional)
     * @return array Paystack response with authorization_url
     */
    public function initializeTransaction($amount, $email, $reference, $metadata = [])
    {
        $url = $this->apiUrl . '/transaction/initialize';
        
        $data = [
            'email' => $email,
            'amount' => (int)($amount * 100), // Convert to kobo/cents (must be integer)
            'reference' => $reference,
            'currency' => PAYSTACK_CURRENCY,
            'metadata' => $metadata
        ];
        
        // Add callback URL only if defined (optional for Paystack)
        $callbackUrl = $this->getCallbackUrl();
        if (!empty($callbackUrl)) {
            $data['callback_url'] = $callbackUrl;
        }

        $response = $this->makeRequest('POST', $url, $data);
        
        if (!$response['status']) {
            $errorMsg = $response['message'] ?? 'Unknown error';
            // Include more details if available
            if (isset($response['data']['gateway_response'])) {
                $errorMsg .= ' - ' . $response['data']['gateway_response'];
            }
            throw new RuntimeException('Paystack API error: ' . $errorMsg);
        }

        return $response['data'];
    }

    /**
     * Verify a Paystack transaction
     * 
     * @param string $reference Transaction reference
     * @return array Transaction details
     */
    public function verifyTransaction($reference)
    {
        $url = $this->apiUrl . '/transaction/verify/' . $reference;
        $response = $this->makeRequest('GET', $url);

        if (!$response['status']) {
            throw new RuntimeException('Paystack verification failed: ' . ($response['message'] ?? 'Unknown error'));
        }

        return $response['data'];
    }

    /**
     * Make HTTP request to Paystack API
     * 
     * @param string $method HTTP method (GET, POST)
     * @param string $url Full API URL
     * @param array $data Request data (for POST)
     * @return array Decoded JSON response
     */
    private function makeRequest($method, $url, $data = [])
    {
        $ch = curl_init($url);
        
        $headers = [
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/json'
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_CUSTOMREQUEST => $method
        ]);

        if ($method === 'POST' && !empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new RuntimeException('Paystack API request failed: ' . $error);
        }

        $decoded = json_decode($response, true);
        
        if ($httpCode !== 200) {
            $message = $decoded['message'] ?? 'HTTP ' . $httpCode;
            // Include more error details if available
            if (isset($decoded['data']['gateway_response'])) {
                $message .= ' - ' . $decoded['data']['gateway_response'];
            }
            throw new RuntimeException('Paystack API error: ' . $message);
        }

        return $decoded;
    }

    /**
     * Get the callback URL for payment verification
     * 
     * @return string Full callback URL
     */
    private function getCallbackUrl()
    {
        return PAYSTACK_CALLBACK_URL;
    }

    /**
     * Get public key for frontend use
     * 
     * @return string Public key
     */
    public function getPublicKey()
    {
        return $this->publicKey;
    }
}

?>

