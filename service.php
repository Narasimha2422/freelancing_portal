<?php
include 'db_connect.php';

// Fetch job details using job_id from the GET request
$job_id = isset($_GET['job_id']) ? intval($_GET['job_id']) : 0;
if ($job_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE job_id = ?");
    $stmt->bind_param("i", $job_id);
    $stmt->execute();
    $job = $stmt->get_result()->fetch_assoc();
} else {
    $job = null;
}

// Fetch reviews for the current job
$reviews_stmt = $conn->prepare("SELECT r.review_text, r.rating, r.created_at, u.username 
                                FROM reviews r
                                JOIN users u ON r.reviewer_id = u.user_id
                                WHERE r.job_id = ?
                                ORDER BY r.created_at DESC");
$reviews_stmt->bind_param("i", $job_id);
$reviews_stmt->execute();
$reviews = $reviews_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Freelancing Portal | Your Link to the Best Freelancers and Clients</title>
    <link rel="stylesheet" href="service.css">
    <style>
         .btn-primary {
            background-color: #25e61a; 
            color: #ffffff;
            display: inline-block; 
            border: none; 
            padding: 10px 100px; 
            font-size: 16px; 
            border-radius: 5px; 
            cursor: pointer; 
            text-align: center; 
            text-decoration: none; 
            transition: background-color 0.3s ease; 
        }

        .btn-primary:hover {
            background-color: #0a823c; /* Darker green on hover */
        }

        /* Remove List Styles */
        ul {
            list-style: none; 
            padding: 0; 
            margin: 0; 
        }

        li {
            list-style: none;
        }

        /* Review Section */
        .review-item {
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }

        .rating {
            color: #FFD700; /* Gold stars */
        }
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
                <li>
                    <a href="profile.php" class="profile-icon"><img src="https://cdn3.iconfinder.com/data/icons/essential-rounded/64/Rounded-31-512.png" height="30px" alt="Profile Icon"></a>
                </li>
            </ul>            
        </nav>
    </header>

    <section class="service-header">
        <?php if ($job): ?>
            <h1><?php echo htmlspecialchars($job['title']); ?></h1>
            <p><?php echo htmlspecialchars($job['description']); ?></p>
        <?php else: ?>
            <h1>Service Not Found</h1>
            <p>Sorry, we couldn't find the details of the requested service.</p>
        <?php endif; ?>
    </section>

    <section class="service-details">
        <div class="service-description">
            <h2>About This Service</h2>
            <?php if ($job): ?>
                <p><strong>Category:</strong> <?php echo htmlspecialchars($job['category']); ?></p>
                <p><strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?></p>
                <p><strong>Cost:</strong> $<?php echo htmlspecialchars($job['cost']); ?></p>
                <p><strong>Deadline:</strong> <?php echo htmlspecialchars($job['deadline']); ?></p>
                <p><small>Posted on: <?php echo htmlspecialchars($job['created_at']); ?></small></p>
            <?php else: ?>
                <p>Details about this service are currently unavailable.</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <p class="desc">We Can Ensure</p>
            <ul class="lists">
                <li class="list">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Quality Assurance</span>
                </li>
                <li class="list">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Convenient Payment Options</span>
                </li>
                <li class="list">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Customer Support</span>
                </li>
            </ul>
            <li>
                <button class="btn-primary" onclick="window.location.href='application.php'">Click Me</button>
           </li>

        </div>
    </section>

    <section class="service-reviews">
        <h2>Customer Reviews</h2>
        <div class="review-list">
            <?php if ($reviews->num_rows > 0): ?>
                <?php while ($review = $reviews->fetch_assoc()): ?>
                    <div class="review-item">
                        <p><strong><?php echo htmlspecialchars($review['username']); ?>:</strong> <?php echo htmlspecialchars($review['review_text']); ?></p>
                        <p class="rating">Rating: <?php echo str_repeat('★', $review['rating']); ?></p>
                        <p><small>Posted on: <?php echo htmlspecialchars($review['created_at']); ?></small></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No reviews yet. Be the first to review this service!</p>
            <?php endif; ?>
        </div>
        <a href="review.php?job_id=<?php echo $job_id; ?>" class="btn-primary">Write a Review</a>
    </section>

    <footer>
        <p>&copy; 2024 Freelancing Portal. All rights reserved.</p>
    </footer>
</body>
</html>
