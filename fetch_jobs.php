<?php
// fetch_jobs.php
include('db_connect.php');

// Modify the query to only fetch public jobs
$query = "SELECT * FROM jobs WHERE visibility = 'public' ORDER BY posted_at DESC";
$result = $conn->query($query);

$jobs = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;  // Add the job to the jobs array
    }
}

header('Content-Type: application/json');
echo json_encode($jobs);  // Return the jobs as a JSON response
?>
