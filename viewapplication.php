<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $application_id = intval($_POST['application_id']);
    $action = $_POST['action']; // 'selected', 'not_selected', 'complete', 'incomplete'

    // Determine status based on action
    $status_map = [
        'selected' => 'Selected',
        'not_selected' => 'Not Selected',
        'complete' => 'Complete',
        'incomplete' => 'Incomplete'
    ];

    $status = $status_map[$action] ?? null;
    if (!$status) {
        die("Invalid action.");
    }

    // Update status for the application
    $update_sql = "UPDATE applications a
                   JOIN jobs j ON a.job_id = j.job_id
                   SET a.status = ?
                   WHERE a.application_id = ? AND j.user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sii", $status, $application_id, $user_id);

    if ($stmt->execute()) {
        $message = "Application status updated to $status.";
    } else {
        $message = "Failed to update status: " . $stmt->error;
    }
}

// Fetch applications for jobs posted by the logged-in user
$sql = "SELECT a.application_id, a.username, a.email, a.skills, a.about, a.portfolio, a.status, j.title
        FROM applications a
        JOIN jobs j ON a.job_id = j.job_id
        WHERE j.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Applications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 50px;
        background-color:white;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

        .logo a {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: white;
        }

        .logo img {
            height: 40px;
            margin-right: 10px;
        }

        .logo span {
            font-size: 1.2em;
            font-weight: bold;
            color:#111827;
        }

        .nav-links {
            list-style: none;
            display: flex;
            align-items: center;
        }

        .nav-links li {
            margin-left: 20px;
        }

        .nav-links a {
            color: #333;
            text-decoration: none;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: rgb(105, 231, 33);
        }

        .btn-primary {
            background-color:rgb(105, 231, 33);
            padding: 0.8em 1.2em 0.8em 1em;
            font-size: 16px;
        }
        
        .btn-primary:hover {
            background-color: green;
        }
        
        .search-box input[type="text"] {
            size: 50px;
            border: 5px solid grey;
            border-radius: 4px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .application {
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .application p {
            margin: 5px 0;
        }
        .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .btn {
            padding: 8px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-select {
            background-color: #28a745;
            color: #fff;
        }
        .btn-not-select {
            background-color: #dc3545;
            color: #fff;
        }
        .btn-complete {
            background-color: #007bff;
            color: #fff;
        }
        .btn-incomplete {
            background-color: #ffc107;
            color: #fff;
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
            <div class="search-box">
                <input type="text" placeholder="Search..">
            </div>
            <ul class="nav-links">
                <li><a href="index1.html">Home</a></li>
                <li><a href="contactus.html">Contact</a></li>
                <li><a href="http://localhost/freelancing_portal/results.php">Find Jobs</a></li>
                <li><a href="http://localhost/freelancing_portal/pjob.php">Post a Service</a></li>
                <li><a href="http://localhost/freelancing_portal/profile.php"><img src="https://t3.ftcdn.net/jpg/06/19/26/46/360_F_619264680_x2PBdGLF54sFe7kTBtAvZnPyXgvaRw0Y.jpg" height="50px"></a></li>
            </ul>          
        </nav>
    </header>
    <div class="container">
        <h1>Applications for Your Jobs</h1>
        <?php if (isset($message)): ?>
            <p style="color: green;"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="application">
                    <p><strong>Job Title:</strong> <?php echo htmlspecialchars($row['title']); ?></p>
                    <p><strong>Applicant Name:</strong> <?php echo htmlspecialchars($row['username']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></p>
                    <p><strong>Skills:</strong> <?php echo htmlspecialchars($row['skills']); ?></p>
                    <p><strong>About:</strong> <?php echo htmlspecialchars($row['about']); ?></p>
                    <p><strong>Portfolio:</strong> <a href="<?php echo htmlspecialchars($row['portfolio']); ?>" target="_blank"><?php echo htmlspecialchars($row['portfolio']); ?></a></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status'] ?: 'Pending'); ?></p>

                    <div class="buttons">
                        <form method="POST">
                            <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                            <button type="submit" name="action" value="selected" class="btn btn-select">Select</button>
                            <button type="submit" name="action" value="not_selected" class="btn btn-not-select">Not Select</button>
                        </form>
                        <form method="POST">
                            <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                            <button type="submit" name="action" value="complete" class="btn btn-complete">Completed</button>
                            <button type="submit" name="action" value="incomplete" class="btn btn-incomplete">Incomplete</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No applications found for your jobs.</p>
        <?php endif; ?>
    </div>
</body>
</html>
