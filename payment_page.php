<?php
// Start session
session_start();

// Fetch Razorpay order ID from session
$order_id = $_SESSION['razorpay_order_id'] ?? null;

if (!$order_id) {
    die("No order found. Please initiate the transaction again.");
}

// Razorpay API Key
$keyId = "rzp_test_ap5yRZZySRmyvf";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Razorpay Payment</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <h1>Complete Your Payment</h1>
    <button id="rzp-button1">Pay Now</button>

    <script>
        // Ensure Razorpay order ID is loaded
        var orderId = "<?php echo $order_id; ?>";
        if (!orderId) {
            alert("Order ID is missing. Please try again.");
        }

        // Razorpay Checkout options
        var options = {
            "key": "<?php echo $keyId; ?>", // Razorpay API Key
            "amount": "50000", // Amount in paise (₹500)
            "currency": "INR",
            "order_id": orderId, // Pass the order ID from PHP
            "name": "Freelancing Portal",
            "description": "Transaction Payment",
            "handler": function (response) {
                // Handle successful payment
                alert("Payment successful!");
                console.log(response);

                // Redirect to verify payment
                window.location.href = "verify_payment.php?razorpay_payment_id=" + response.razorpay_payment_id +
                    "&razorpay_order_id=" + response.razorpay_order_id +
                    "&razorpay_signature=" + response.razorpay_signature;
            },
            "theme": {
                "color": "#3399cc"
            }
        };

        var rzp1 = new Razorpay(options);

        // Open Razorpay popup on button click
        document.getElementById('rzp-button1').onclick = function (e) {
            rzp1.open();
            e.preventDefault();
        }
    </script>
</body>
</html>
