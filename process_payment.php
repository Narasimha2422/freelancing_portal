<?php
require('vendor/autoload.php'); // Razorpay SDK autoload
use Razorpay\Api\Api;

// Razorpay API Keys
$api_key = "your_razorpay_key_id";
$api_secret = "your_razorpay_key_secret";

// Database connection
$conn = new mysqli("localhost", "root", "", "freelance_portal");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch POST data
$user_id = $_POST['user_id'];
$job_id = $_POST['job_id'];
$amount = $_POST['amount']; // Amount in INR

// Create a Razorpay order
$api = new Api($api_key, $api_secret);

$orderData = [
    'receipt' => uniqid(),
    'amount' => $amount * 100, // Convert to paise
    'currency' => 'INR',
    'payment_capture' => 1 // Auto-capture payment
];

$razorpayOrder = $api->order->create($orderData);
$order_id = $razorpayOrder['id'];

// Save the order to the database
$stmt = $conn->prepare("INSERT INTO transactions (user_id, job_id, payment_status, amount, razorpay_order_id) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iisds", $user_id, $job_id, $payment_status, $amount, $order_id);
$payment_status = "pending";
$stmt->execute();
$stmt->close();

$conn->close();

// Return Razorpay order details to the frontend
echo json_encode(['order_id' => $order_id, 'amount' => $amount]);
?>
