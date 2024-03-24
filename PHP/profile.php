<?php
session_start(); // Start the session to access session variables

$username = "root"; 
$password = ""; 
$server = "localhost";  
$database = "stms_database"; 

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: /pages/login_page.html");
    exit();
}

// Fetch teacher's details based on the logged-in username
$logged_in_username = $_SESSION['username'];
$query = "SELECT * FROM teacher WHERE username='$logged_in_username'";
$result = mysqli_query($connection, $query);

if (mysqli_num_rows($result) == 1) {
    // Fetch the teacher's details
    $row = mysqli_fetch_assoc($result);
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['last_name'] = $row['last_name'];
    $_SESSION['teacher_address'] = $row['user_address'];
    $_SESSION['age'] = $row['age'];
    $_SESSION['sex'] = $row['sex'];
    $_SESSION['marital_status'] = $row['marital_status'];
    $_SESSION['registration_id'] = $row['registration_id'];
    $_SESSION['subject_name'] = $row['subject_name'];
    $_SESSION['username'] = $row['username'];
    $_SESSION['email'] = $row['email'];
} else {
    // Redirect to an error page if the teacher's details are not found
    header("Location: /error_page.html");
    exit();
}

// Close the connection
mysqli_close($connection);
?>
