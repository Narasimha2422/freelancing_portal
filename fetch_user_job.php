<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "freelancing.portal";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Retrieve name and email from the AJAX request
$name = $conn->real_escape_string($_POST['name']);
$email = $conn->real_escape_string($_POST['email']);

// Fetch user_id and job_id based on name and email
$sql = "SELECT users.user_id, jobs.job_id
        FROM users
        JOIN jobs ON users.user_id = jobs.user_id
        WHERE users.name = ? AND users.email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $name, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'user_id' => $row['user_id'], 'job_id' => $row['job_id']]);
} else {
    echo json_encode(['success' => false, 'message' => 'No matching user or job found']);
}

$conn->close();
?>
