<?php
require 'vendor/autoload.php';

use Razorpay\Api\Api;

$keyId = "YOUR_RAZORPAY_KEY";
$keySecret = "YOUR_RAZORPAY_SECRET";

try {
    $api = new Api($keyId, $keySecret);
    echo "Razorpay SDK is working!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
