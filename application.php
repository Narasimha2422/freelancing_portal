<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_id = $_POST['job_id']; // Get job_id from the form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $skills = $_POST['skills'];
    $about = $_POST['about'];
    $portfolio = $_POST['portfolio'];

    // Validate job_id (optional: check if job_id exists in the jobs table)
    $job_check_sql = "SELECT * FROM jobs WHERE job_id = $job_id";
    $job_check_result = $conn->query($job_check_sql);

    if ($job_check_result->num_rows > 0) {
        // Insert the application into the database
        $sql = "INSERT INTO applications (job_id, username, email, skills, about, portfolio)
                VALUES ('$job_id', '$username', '$email', '$skills', '$about', '$portfolio')";

        if ($conn->query($sql) === TRUE) {
            echo "Application submitted successfully for Job ID: $job_id!";
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Error: Invalid Job ID.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Apply for Job</title>
    <link rel="stylesheet" href="app.css">
</head>
<body>
    <div class="container">
        <h1>Apply for a Job</h1>
        <form method="POST">
            <div class="input-group">
                <label>Job ID (Enter the Job ID manually):</label>
                <input type="text" name="job_id" required>
            </div>

            <div class="input-group">
                <label>Username:</label>
                <input type="text" name="username" required>
            </div>

            <div class="input-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>

            <div class="input-group">
                <label>Skills:</label>
                <textarea name="skills" required></textarea>
            </div>
<!-- 
            <div class="input-group">
                <label>About Yourself:</label>
                <textarea name="about" required></textarea>
            </div> -->

            <div class="input-group">
                <label>Portfolio Link:</label>
                <input type="url" name="portfolio">
            </div>

            <button type="submit">Submit Application</button>
            <a href="http://localhost/freelancing_portal/results.php">View Job Results</a>
        </form>
    </div>
</body>
</html>
