<?php
// Start session to manage user information if needed
session_start();

// Database connection
$con = new mysqli("localhost", "root", "", "freelancing.portal"); // Adjust credentials as needed

// Check connection
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Initialize error and success message variables
$error_message = "";
$success_message = "";

// Dummy user_id for demonstration (replace with dynamic user authentication data)
$user_id = $_SESSION['user_id'] ?? 1; // Example: Replace with actual user session data

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize form input
    $title = $con->real_escape_string($_POST['title']);
    $description = $con->real_escape_string($_POST['description']);
    $category = $con->real_escape_string($_POST['category']);
    $experience = $con->real_escape_string($_POST['experience']);
    $location = $con->real_escape_string($_POST['location']);
    $cost = $con->real_escape_string($_POST['cost']);
    $deadline = $con->real_escape_string($_POST['deadline']);

    // Validate fields
    if (!empty($title) && !empty($description) && !empty($category) && !empty($experience) && !empty($location) && !empty($cost) && !empty($deadline)) {
        if (!is_numeric($cost)) {
            $error_message = "Cost must be a valid number.";
        } elseif (strtotime($deadline) <= strtotime('now')) {
            $error_message = "Deadline must be a future date.";
        } else {
            // Step 1: Validate user_id exists
            $check_user_query = "SELECT * FROM users WHERE user_id = ?";
            $stmt = $con->prepare($check_user_query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Step 2: Insert job details into the database
                $query = "INSERT INTO jobs (user_id, title, description, category, experience, location, cost, deadline) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $con->prepare($query);
                $stmt->bind_param("issssssd", $user_id, $title, $description, $category, $experience, $location, $cost, $deadline);

                if ($stmt->execute()) {
                    $success_message = "Job posted successfully!";
                    header("Location: index1.html"); // Redirect after success
                    exit;
                } else {
                    $error_message = "Error: Could not post the job. Please try again.";
                }
            } else {
                $error_message = "Error: The specified user_id does not exist in the users table.";
            }
        }
    } else {
        $error_message = "Please fill in all required fields.";
    }
}

$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancing Portal | Post Job</title>
    <style>
        /* General Styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; line-height: 1.6; background: linear-gradient(to bottom, #ffffff, #f0f0f0); color: #333; }
        .navbar { display: flex; justify-content: space-between; align-items: center; padding: 20px 50px; background-color: white; position: sticky; top: 0; z-index: 1000; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .logo img { height: 40px; margin-right: 10px; }
        .logo span { font-size: 1.2em; font-weight: bold; color: #111827; }
        .nav-links { list-style: none; display: flex; align-items: center; }
        .nav-links li { margin-left: 20px; }
        .nav-links a { color: #333; text-decoration: none; font-size: 16px; transition: color 0.3s ease; }
        .nav-links a:hover { color: rgb(105, 231, 33); }
        section { padding: 20px; max-width: 600px; margin: 20px auto; background-color: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }
        .post-job-form label { display: block; font-weight: bold; margin-top: 15px; }
        .post-job-form input, .post-job-form select, .post-job-form textarea { width: 100%; padding: 10px; margin-top: 5px; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; }
        .post-job-form button { width: 100%; padding: 10px; margin-top: 20px; background-color: #0073e6; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; }
        .post-job-form button:hover { background-color: #005bb5; }
    </style>
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
                <li><a href="contactus.html">Contact</a></li>
                <li><a href="http://localhost/freelancing_portal/results.php">Find Jobs</a></li>
                <li><a href="post_job.php">Post a Service</a></li>
                <li><a href="http://localhost/freelancing_portal/profile.php"><img src="https://t3.ftcdn.net/jpg/06/19/26/46/360_F_619264680_x2PBdGLF54sFe7kTBtAvZnPyXgvaRw0Y.jpg" height="50px"></a></li>
            </ul>
        </nav>
    </header>

    <!-- Job Posting Form -->
    <section class="post-job-form">
        <form action="" method="post">
            <!-- Display success or error message -->
            <?php if (!empty($success_message)): ?>
                <div style="color: green; text-align: center; margin-bottom: 15px;"><?php echo htmlspecialchars($success_message); ?></div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div style="color: red; text-align: center; margin-bottom: 15px;"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <label for="job-title">Job Title</label>
            <input type="text" id="job-title" name="title" placeholder="Enter the job title" required>

            <label for="job-description">Job Description</label>
            <textarea id="job-description" name="description" rows="6" placeholder="Provide a detailed job description" required></textarea>

            <label for="job-category">Job Category</label>
            <select id="job-category" name="category" required>
                <option value="">Select a category</option>
                <option value="development-and-it">Development & IT</option>
                <option value="video-and-animation">Video & Animation</option>
                <option value="design-and-creative">Design & Creative</option>
                <option value="finance-and-accounts">Finance & Accounts</option>
                <option value="typing-and-translation">Typing & Translation</option>
            </select>

            <label for="experience">Required Experience</label>
            <select id="experience" name="experience" required>
                <option value="">Select experience</option>
                <option value="Beginner">Beginner</option>
                <option value="Intermediate">Intermediate</option>
                <option value="Expert">Expert</option>
            </select>

            <label for="location">Location</label>
            <input type="text" id="location" name="location" placeholder="e.g., Remote, New York" required>

            <label for="pay">Set Cost</label>
            <input type="text" id="cost" name="cost" placeholder="Enter cost" required>

            <label for="deadline">Deadline</label>
            <input type="date" id="deadline" name="deadline" required>

            <button type="submit">Post Job</button>
        </form>
    </section>
</body>
</html>
