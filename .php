<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = intval($_POST['job_id']);
    $reviewer_id = $_SESSION['user_id'];
    $review_text = trim($_POST['review_text']);
    $rating = intval($_POST['rating']);

    if ($job_id > 0 && !empty($review_text) && $rating > 0) {
        $stmt = $conn->prepare("INSERT INTO reviews (job_id, reviewer_id, review_text, rating, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iisi", $job_id, $reviewer_id, $review_text, $rating);

        if ($stmt->execute()) {
            header("Location: service.php?job_id=$job_id&review_success=1");
        } else {
            echo "Error submitting review: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Invalid review submission.";
    }
}
$conn->close();
?>
2