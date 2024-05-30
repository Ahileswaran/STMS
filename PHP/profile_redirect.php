<?php
require_once 'stay_login.php';

$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if the user is logged in
if (isset($_SESSION['username'])) {
    // Retrieve the logged-in username
    $username = $_SESSION['username'];

    // Check if the username exists in the principal table
    $query = "SELECT username FROM principal WHERE username = '$username'";
    $result = $connection->query($query);

    if ($result->num_rows > 0) {
        // If username is found in the principal table, redirect to admin page
        header("Location: ../PHP/pages/admin_page.php");
        exit();
    } else {
        // If username is not found in the principal table, assume it is a teacher and redirect to profile page
        header("Location: ../PHP/profile_page.php");
        exit();
    }
} else {
    // If user is not logged in, redirect to login page
    header("Location: login_page.php");
    exit();
}

$connection->close();
?>
