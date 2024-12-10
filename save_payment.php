<?php
// save_payment.php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'freelancing.portal');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $paymentId = $data['payment_id'];
    $orderId = $data['order_id'];
    $jobId = $data['job_id'];

    // Save payment details in database
    $stmt = $conn->prepare("INSERT INTO transactions (job_id, payment_id, order_id, status) VALUES (?, ?, ?, ?)");
    $status = 'Completed';
    $stmt->bind_param('isss', $jobId, $paymentId, $orderId, $status);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Payment details saved successfully!"]);
    } else {
        http_response_code(500);
        echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
    }
}
?>
