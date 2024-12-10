<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_id = intval($_POST['job_id']);
    $reviewer_id = $_SESSION['user_id']; // Logged-in user ID
    $review_text = trim($_POST['review_text']);
    $rating = intval($_POST['rating']);

    // Fetch the reviewed_user_id from the jobs table
    $job_stmt = $conn->prepare("SELECT user_id FROM jobs WHERE job_id = ?");
    $job_stmt->bind_param("i", $job_id);
    $job_stmt->execute();
    $job_result = $job_stmt->get_result();

    if ($job_result->num_rows > 0) {
        $job = $job_result->fetch_assoc();
        $reviewed_user_id = $job['user_id']; // This is the job poster's user ID

        // Insert the review into the database
        $stmt = $conn->prepare("INSERT INTO reviews (job_id, reviewer_id, reviewed_user_id, review_text, rating, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiisi", $job_id, $reviewer_id, $reviewed_user_id, $review_text, $rating);

        if ($stmt->execute()) {
            header("Location: service.php?job_id=$job_id&review_success=1");
        } else {
            echo "Error submitting review: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Error: Job not found.";
    }
    $job_stmt->close();
}
$conn->close();
?>
