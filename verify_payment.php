<?php
require 'vendor/autoload.php';
require 'database_connection.php';

use Razorpay\Api\Api;

// Razorpay API credentials
$keyId = "rzp_test_ap5yRZZySRmyvf";
$keySecret = "x2JIZpSyFobqlhQo67nhbYV8";
$api = new Api($keyId, $keySecret);

// Get payment details from the query string
$razorpay_payment_id = $_GET['razorpay_payment_id'] ?? null;
$razorpay_order_id = $_GET['razorpay_order_id'] ?? null;
$razorpay_signature = $_GET['razorpay_signature'] ?? null;

if (!$razorpay_payment_id || !$razorpay_order_id || !$razorpay_signature) {
    die("Invalid payment details.");
}

// Verify payment signature
try {
    $attributes = [
        'razorpay_order_id' => $razorpay_order_id,
        'razorpay_payment_id' => $razorpay_payment_id,
        'razorpay_signature' => $razorpay_signature
    ];
    $api->utility->verifyPaymentSignature($attributes);

    // Mark the payment as complete in the database
    $query = $pdo->prepare("UPDATE jobs SET payment_status = 'complete' WHERE id = :job_id");
    $query->execute(['job_id' => $job_id]);

    echo "Payment verified successfully!";
} catch (Exception $e) {
    die("Payment verification failed: " . $e->getMessage());
}
?>
