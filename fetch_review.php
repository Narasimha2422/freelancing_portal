<?php
// Database connection
$host = "localhost";
$user = "root";
$password = ""; // Your MySQL password
$dbname = "freelancing.portal";

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch reviews
$sql = "SELECT username, rating, review, created_at FROM reviews ORDER BY created_at DESC";
$result = $conn->query($sql);

$reviews = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}

echo json_encode($reviews);

$conn->close();
?>
