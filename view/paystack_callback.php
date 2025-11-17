<?php
session_start();

$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
$baseDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
if (substr($baseDir, -5) === '/view') {
    $baseDir = substr($baseDir, 0, -5);
}
$appBasePath = ($baseDir === '' || $baseDir === '.') ? '/' : $baseDir . '/';

// Get reference from Paystack redirect
$reference = $_GET['reference'] ?? '';

if (empty($reference)) {
    header('Location: ' . $appBasePath . 'view/checkout.php?error=no_reference');
    exit;
}

// Store reference in session for verification page
$_SESSION['paystack_reference'] = $reference;

// Redirect to verification
header('Location: ' . $appBasePath . 'view/payment_success.php');
exit;
?>

