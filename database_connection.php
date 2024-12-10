<?php
// Database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=freelancing.portal', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
