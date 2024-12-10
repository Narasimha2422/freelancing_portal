<?php
session_start();
include 'db_connect.php';

// Get the search query from the user input
$search_query = isset($_GET['query']) ? trim($_GET['query']) : '';
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

// Prepare the SQL query to fetch job postings and application statuses
$stmt = $conn->prepare("
    SELECT jobs.*, 
           (SELECT GROUP_CONCAT(CONCAT(users.username, ': ', applications.status) SEPARATOR ', ') 
            FROM applications
            JOIN users ON applications.application_id = users.user_id
            WHERE applications.job_id = jobs.job_id) AS application_status,
           jobs.user_id AS job_owner_id 
    FROM jobs 
    WHERE 
        LOWER(title) LIKE CONCAT('%', ?, '%') OR 
        LOWER(cost) LIKE CONCAT('%', ?, '%') OR 
        LOWER(location) LIKE CONCAT('%', ?, '%') OR 
        LOWER(category) LIKE CONCAT('%', ?, '%') 
    ORDER BY created_at DESC
");

$search_param = strtolower($search_query);
$stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Results</title>
    <link rel="stylesheet" href="result.css">
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
        <div class="search-box">
            <input type="text" placeholder="Search..">
        </div>
        <ul class="nav-links">
            <li><a href="index1.html">Home</a></li>
            <li><a href="contactus.html">Contact</a></li>
            <li><a href="results.php">Find Jobs</a></li>
            <li><a href="pjob.php">Post a Service</a></li>
            <li><a href="profile.php"><img src="https://t3.ftcdn.net/jpg/06/19/26/46/360_F_619264680_x2PBdGLF54sFe7kTBtAvZnPyXgvaRw0Y.jpg" height="50px"></a></li>
        </ul>
    </nav>
</header>
<div class="container">
    <h1>Job Results</h1>
    <form method="GET" action="results.php">
        <input type="text" name="query" placeholder="Search jobs..." value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">Search</button>
    </form>

    <div>
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="job">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p><strong>cost:</strong> <?php echo htmlspecialchars($row['cost']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
                    <p><strong>Posted On:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
                    <p>
                        <strong>Application Status:</strong> 
                        <span class="status">
                            <?php echo htmlspecialchars($row['application_status'] ?: 'No applications yet'); ?>
                        </span>
                    </p>
                    <div class="action-buttons">
                    <a href="service.php?job_id=<?php echo urlencode($row['job_id']); ?>" class="btn-primary">View Details</a>
                        <?php if (
                            $row['job_owner_id'] == $user_id && 
                            strpos(strtolower($row['application_status']), 'complete') !== false
                        ): ?>
                            <a href="transaction.php?job_id=<?php echo $row['job_id']; ?>" class="btn-secondary">Go to Transaction</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No jobs found. Try refining your search query.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
