<?php
session_start();

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if (substr($baseDir, -5) === '/view') {
    $baseDir = substr($baseDir, 0, -5);
}
$appBasePath = ($baseDir === '' || $baseDir === '.') ? '/' : $baseDir . '/';

// Get reference from session or GET
$reference = $_SESSION['paystack_reference'] ?? $_GET['reference'] ?? '';

if (empty($reference)) {
    header('Location: ' . $appBasePath . 'view/checkout.php?error=no_reference');
    exit;
}

// Clear the reference from session after use
unset($_SESSION['paystack_reference']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Verification - AgroCare Farm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .verification-container {
            min-height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .verification-card {
            max-width: 600px;
            width: 100%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
                <i class="fas fa-seedling me-2"></i>AgroCare Farm
            </a>
        </div>
    </nav>

    <main class="container verification-container" style="padding-top: 100px;">
        <div class="verification-card">
            <div class="card shadow-lg">
                <div class="card-body p-5 text-center">
                    <div id="verificationStatus">
                        <div class="spinner-border text-success mb-4" role="status" style="width: 4rem; height: 4rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h3 class="mb-3">Verifying Payment...</h3>
                        <p class="text-muted">Please wait while we confirm your payment.</p>
                    </div>
                    <div id="verificationResult" style="display: none;"></div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const basePath = '<?php echo htmlspecialchars($appBasePath, ENT_QUOTES); ?>';
        const reference = '<?php echo htmlspecialchars($reference, ENT_QUOTES); ?>';

        async function verifyPayment() {
            try {
                const response = await fetch(`${basePath}actions/paystack_verify_payment.php`, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ reference: reference })
                });

                const text = await response.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid response from server');
                }

                if (data.status === 'success' && data.verified) {
                    showSuccess(data);
                } else {
                    showError(data.message || 'Payment verification failed');
                }
            } catch (error) {
                console.error('Verification error:', error);
                showError(error.message || 'An error occurred during payment verification');
            }
        }

        function showSuccess(data) {
            const statusDiv = document.getElementById('verificationStatus');
            const resultDiv = document.getElementById('verificationResult');
            
            statusDiv.style.display = 'none';
            resultDiv.style.display = 'block';
            
            resultDiv.innerHTML = `
                <div class="mb-4">
                    <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                </div>
                <h3 class="text-success mb-3">Payment Successful!</h3>
                <p class="lead mb-4">Your order has been confirmed and is being processed.</p>
                
                <div class="card bg-light mb-4">
                    <div class="card-body text-start">
                        <h5 class="card-title mb-3"><i class="fas fa-receipt me-2"></i>Order Details</h5>
                        <div class="row mb-2">
                            <div class="col-5"><strong>Order ID:</strong></div>
                            <div class="col-7">#${data.order_id || '—'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5"><strong>Invoice No:</strong></div>
                            <div class="col-7">${data.invoice_no || '—'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5"><strong>Payment Reference:</strong></div>
                            <div class="col-7">${data.payment_reference || '—'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5"><strong>Total Amount:</strong></div>
                            <div class="col-7">${data.currency === 'NGN' ? '₦' : data.currency === 'GHS' ? '₵' : data.currency === 'USD' ? '$' : '₵'}${data.total_amount || '0.00'}</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5"><strong>Items:</strong></div>
                            <div class="col-7">${data.item_count || 0} item(s)</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5"><strong>Payment Method:</strong></div>
                            <div class="col-7">${data.payment_method || 'Paystack'}</div>
                        </div>
                        <div class="row">
                            <div class="col-5"><strong>Date:</strong></div>
                            <div class="col-7">${data.order_date || '—'}</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid gap-2">
                    <a href="${basePath}index.php" class="btn btn-success btn-lg">
                        <i class="fas fa-home me-2"></i>Return to Home
                    </a>
                    <a href="${basePath}view/all_product.php" class="btn btn-outline-success">
                        <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
                    </a>
                </div>
            `;
        }

        function showError(message) {
            const statusDiv = document.getElementById('verificationStatus');
            const resultDiv = document.getElementById('verificationResult');
            
            statusDiv.style.display = 'none';
            resultDiv.style.display = 'block';
            
            resultDiv.innerHTML = `
                <div class="mb-4">
                    <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                </div>
                <h3 class="text-danger mb-3">Payment Verification Failed</h3>
                <p class="lead mb-4">${message}</p>
                
                <div class="d-grid gap-2">
                    <a href="${basePath}view/checkout.php" class="btn btn-danger btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Return to Checkout
                    </a>
                    <a href="${basePath}view/cart.php" class="btn btn-outline-secondary">
                        <i class="fas fa-shopping-cart me-2"></i>View Cart
                    </a>
                </div>
            `;
        }

        // Start verification when page loads
        document.addEventListener('DOMContentLoaded', verifyPayment);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

