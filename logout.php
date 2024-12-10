<?php
session_start(); // Start session

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Optional: Clear cookies if you're using them
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to the homepage or login page
header("Location: index.html"); // Replace 'index.php' with your desired page
exit();
?>
