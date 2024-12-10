<?php
// Include database connection
require 'database_connection.php';

// Include Razorpay SDK
require 'vendor/autoload.php';

use Razorpay\Api\Api;

// Razorpay credentials
$keyId = "rzp_test_ap5yRZZySRmyvf";
$keySecret = "x2JIZpSyFobqlhQo67nhbYV8";
$api = new Api($keyId, $keySecret);

// Start session
session_start();

// Step 1: Get the job ID from the query parameter
$job_id = $_GET['job_id'] ?? null;

if (!$job_id) {
    die("Invalid job ID.");
}

// Step 2: Fetch job details from the database
try {
    // Update the column name here (use your actual table's primary key column name)
    $query = $pdo->prepare("SELECT * FROM jobs WHERE job_id = :job_id");
    $query->execute(['job_id' => $job_id]);
    $job = $query->fetch();

    if (!$job) {
        die("Job not found.");
    }

    // Extract job details
    $title = $job['title'];
    $description = $job['description'];
    $cost = $job['cost']; // Ensure the `jobs` table has a `price` column for the job fee

    // Convert price to paise (₹500 becomes 50000)
    $amount_in_paise = $cost * 100;

    // Step 3: Create Razorpay order
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $receipt = "txn_" . time();

        try {
            $order = $api->order->create([
                'receipt'         => $receipt,
                'amount'          => $amount_in_paise,
                'currency'        => 'INR',
                'payment_capture' => 1, // Auto-capture payment
            ]);

            // Save the order ID in the session
            $_SESSION['razorpay_order_id'] = $order['id'];

            // Redirect to the payment page
            header("Location: payment_page.php");
            exit;
        } catch (Exception $e) {
            die("Error creating Razorpay order: " . $e->getMessage());
        }
    }
} catch (PDOException $e) {
    die("Error fetching job details: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Transaction</title>
    <link rel="stylesheet" href="tran.css">
</head>
<body>
<header>
        <nav class="navbar">
            <div class="logo">
                <a href="index1.html">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRTdRurK_12ESbJlmq7m5aqSqT-jM821SQ7ow&s" alt="Freelancing Portal Logo">
                    <span>Freelancing Portal</span>
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="index1.html">Home</a></li>
                <li><a href="message.html"> message </a></li>
                <li><a href="contactus.html">Contact</a></li>
                <li><a href="http://localhost/freelancing_portal/results.php">Find Jobs</a></li>
                <li><a href="http://localhost/freelancing_portal/pjob.php">Post a Service</a></li>
                <li><a href="http://localhost/freelancing_portal/profile.php"><img src="https://t3.ftcdn.net/jpg/06/19/26/46/360_F_619264680_x2PBdGLF54sFe7kTBtAvZnPyXgvaRw0Y.jpg" height="50px"></a></li>
            </ul>          
        </nav>
    </header>
    <h1>Payment for Job: <?php echo htmlspecialchars($title); ?></h1>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($description); ?></p>
    <p><strong>Amount:</strong> ₹<?php echo htmlspecialchars($cost); ?></p>

    <form action="transaction.php?job_id=<?php echo $job_id; ?>" method="POST">
        <button type="submit">Pay ₹<?php echo htmlspecialchars($cost); ?></button>
    </form>
</body>
</html>

