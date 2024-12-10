<?php
session_start();
include 'db_connect.php';

if (!isset($_GET['job_id'])) {
    header("Location: service.php");
    exit();
}

$job_id = intval($_GET['job_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave a Review</title>
    <link rel="stylesheet" href="review.css">
</head>
<body>
    <h2>Leave a Review</h2>
    <form action="submit_review.php" method="POST">
        <input type="hidden" name="job_id" value="<?php echo $job_id; ?>">

        <label for="username">Name:</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($_SESSION['username'] ?? ''); ?>" readonly required>

        <label for="rating">Rating:</label>
        <select id="rating" name="rating" required>
            <option value="5">5 - Excellent</option>
            <option value="4">4 - Good</option>
            <option value="3">3 - Average</option>
            <option value="2">2 - Poor</option>
            <option value="1">1 - Very Poor</option>
        </select>

        <label for="review_text">Review:</label>
        <textarea id="review_text" name="review_text" rows="4" required></textarea>

        <button type="submit">Submit Review</button>
    </form>
</body>
</html>
