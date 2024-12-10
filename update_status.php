<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $application_id = intval($_POST['application_id']);
    $status = $_POST['status'];

    // Validate status input
    if (!in_array($status, ['selected', 'not_selected'])) {
        die("Invalid status.");
    }

    // Ensure only the job owner can update the status
    $sql = "SELECT j.user_id 
            FROM applications a 
            JOIN jobs j ON a.job_id = j.job_id 
            WHERE a.application_id = $application_id AND j.user_id = $user_id";

    $result = $conn->query($sql);
    if ($result->num_rows === 0) {
        die("Unauthorized action.");
    }

    // Update the application status
    $update_sql = "UPDATE applications SET status = '$status' WHERE application_id = $application_id";
    if ($conn->query($update_sql)) {
        echo "Status updated successfully.";
        header("Location: view_application.php");
    } else {
        echo "Error updating status: " . $conn->error;
    }
}
?>
