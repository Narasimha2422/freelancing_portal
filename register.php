<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    include 'db_connect.php'; // Make sure this file correctly sets up the $conn variable

    // Check if the connection was successful
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $about = $_POST['about'];
    $skills = $_POST['skills'];
    $experience = $_POST['experience'];
    $portfolio = $_POST['portfolio'];
    $contact = $_POST['contact'];

    // Profile picture upload
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file);
        $profile_picture = $target_file;
    } else {
        $profile_picture = NULL;
    }

    // Prepare and execute SQL statement
    $sql = "INSERT INTO users (username, email, password, about, skills, experience, portfolio, contact, profile_picture)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssss", $username, $email, $password, $about, $skills, $experience, $portfolio, $contact, $profile_picture);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Freelancing Portal</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <main>
    <div class="container">
    <div class="header">
        <form action="register.php" method="POST" enctype="multipart/form-data">
        <div class="input-group">
                    <label for="username">Full Name</label>
                    <input type="text" id="username" name="username" required placeholder="Enter your full name" maxlength="100" >
                </div>
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required placeholder="Enter your email">
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required placeholder="Create a password" minlength="8">
                </div>
                <div class="input-group">
                    <label for="confirmpassword">Confirm Password</label>
                    <input type="password" id="confirmpassword" name="confirmpassword" required placeholder="Confirm your password">
                </div>
                <div class="input-group">
                    <label for="about">About You</label>
                    <textarea id="about" name="about" required placeholder="Tell us a little about yourself..."></textarea>
                </div>
                <div class="input-group">
                    <label for="skills">Skills (comma-separated)</label>
                    <input type="text" id="skills" name="skills" required placeholder="e.g., Web Development, Graphic Design">
                </div>
                <div class="input-group">
                    <label for="experience">Experience Level</label>
                    <select id="experience" name="experience" required>
                        <option value="Beginner">Beginner</option>
                        <option value="Intermediate">Intermediate</option>
                        <option value="Expert">Expert</option>
                    </select>
                </div>
                <div class="input-group">
                    <label for="portfolio">Portfolio Link</label>
                    <input type="url" id="portfolio" name="portfolio" placeholder="https://yourportfolio.com">
                </div>
                <div class="input-group">
                    <label for="contact">Contact Information</label>
                    <input type="text" id="contact" name="contact" placeholder="e.g., Email or LinkedIn profile link">
                </div>
                <div class="form-group">
                    <label for="profilepicture">Profile Picture</label>
                    <input type="file" id="profilepicture" name="profilepicture" accept="image/*">
                </div>
            <button type="submit">Register</button>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
    </div>
    </main>
</body>
</html>
