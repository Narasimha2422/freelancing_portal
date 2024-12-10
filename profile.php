<?php
session_start();
include 'db_connect.php';

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve user details from session
$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if user data is available
if (!$user) {
    echo "User not found!";
    exit();
}

// Fetch user's jobs from the database
$job_query = "SELECT * FROM jobs WHERE user_id = ? ORDER BY created_at DESC";
$job_stmt = $conn->prepare($job_query);
$job_stmt->bind_param("i", $user_id);
$job_stmt->execute();
$user_jobs = $job_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancing Portal | Profile Page</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <!-- Header and Navbar -->
    <header>
        <nav class="navbar">
            <div class="logo">
                <a href="index.php">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRTdRurK_12ESbJlmq7m5aqSqT-jM821SQ7ow&s" alt="Freelancing Portal Logo">
                    <span>Freelancing Portal</span>
                </a>
            </div>
            <ul class="nav-links">
                <li><a href="index1.html">Home</a></li>
                <li><a href="contactus.php">Contact</a></li>
                <li><a href="profile.php"><img src="https://cdn3.iconfinder.com/data/icons/essential-rounded/64/Rounded-31-512.png" height="30px"></a></li>
            </ul>
        </nav>
    </header>

    <!-- Profile Section -->
    <section class="profile">
        <div class="profile-header">
            <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'https://via.placeholder.com/150'); ?>" alt="Profile Picture" class="profile-picture">
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['username']); ?></h1>
                <p><?php echo htmlspecialchars($user['about'] ?? "Web Developer"); ?></p>
                <p>Location: <?php echo htmlspecialchars($user['location'] ?? "Not specified"); ?></p>
            </div>
        </div>

        <div class="profile-content">
            <!-- About Section -->
            <section class="about">
                <h2>About Me</h2>
                <p><?php echo htmlspecialchars($user['about'] ?? "No additional information provided."); ?></p>
            </section>

            <!-- Skills Section -->
            <section class="skills">
                <h2>Skills</h2>
                <ul>
                    <?php
                    $skills = explode(",", $user['skills'] ?? "HTML, CSS, JavaScript"); 
                    foreach ($skills as $skill) {
                        echo "<li>" . htmlspecialchars(trim($skill)) . "</li>";
                    }
                    ?>
                </ul>
            </section>

            <!-- User's Jobs Section -->
            <section class="user-jobs">
                <h2>My Posted Jobs</h2>
                <?php if ($user_jobs->num_rows > 0): ?>
                    <div class="job-list">
                        <?php while ($job = $user_jobs->fetch_assoc()): ?>
                            <div class="job-card">
                                <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                <p><strong>Category:</strong> <?php echo htmlspecialchars($job['category']); ?></p>
                                <p><strong>Experience:</strong> <?php echo htmlspecialchars($job['experience']); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                                <p><strong>Cost:</strong> $<?php echo htmlspecialchars(number_format($job['cost'], 2)); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($job['description']); ?></p>
                                <a href="viewapplication.php?job_id=<?php echo htmlspecialchars($job['job_id']); ?>" class="btn-primary">View Applications</a>
                                <!-- Fetch and Display Reviews for This Job -->
                                <h4>Reviews:</h4>
                                <?php
                                $review_stmt = $conn->prepare("SELECT r.review_text, r.rating, r.created_at, u.username 
                                                               FROM reviews r
                                                               JOIN users u ON r.reviewer_id = u.user_id
                                                               WHERE r.job_id = ?
                                                               ORDER BY r.created_at DESC");
                                $review_stmt->bind_param("i", $job['job_id']);
                                $review_stmt->execute();
                                $reviews = $review_stmt->get_result();

                                if ($reviews->num_rows > 0): ?>
                                    <div class="review-list">
                                        <?php while ($review = $reviews->fetch_assoc()): ?>
                                            <div class="review-item">
                                                <p><strong><?php echo htmlspecialchars($review['username']); ?>:</strong> <?php echo htmlspecialchars($review['review_text']); ?></p>
                                                <p>Rating: <?php echo str_repeat('★', $review['rating']); ?></p>
                                                <p><small>Posted on: <?php echo htmlspecialchars($review['created_at']); ?></small></p>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <p>No reviews yet for this job.</p>
                                <?php endif; ?>
                                <?php $review_stmt->close(); ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <p>No jobs posted yet.</p>
                <?php endif; ?>
            </section>

            <!-- Logout Button -->
            <div class="logout">
            <a href="logout.php">Logout</a>
            </div>
        </div>
    </section>

    <!-- Footer Section -->
    <footer>
        <p>&copy; 2024 Freelancing Portal. All rights reserved.</p>
        <div class="social-media">
            <a href="#">Facebook</a> |
            <a href="#">Twitter</a> |
            <a href="#">LinkedIn</a>
        </div>
    </footer>
</body>
</html>

<?php
$job_stmt->close();
$conn->close();
?>
