<?php
// job_post.php
session_start();
include('db_connection.php'); // Ensure this file contains your database connection code

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $skills = $_POST['skills'];
    $posted_by = 1; // Replace with actual logged-in user ID from session (e.g., $_SESSION['user_id'])

    $query = "INSERT INTO jobs (title, description, skills_required, posted_by) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sssi', $title, $description, $skills, $posted_by);

    if ($stmt->execute()) {
        echo "Job posted successfully!";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post a Job</title>
</head>
<body>
    <h1>Post a Job</h1>
    <form method="POST" action="">
        <label for="title">Job Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="description">Job Description:</label><br>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="skills">Skills Required:</label><br>
        <input type="text" id="skills" name="skills" required><br><br>

        <button type="submit">Post Job</button>
    </form>
</body>
</html>
