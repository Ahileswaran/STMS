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
if(isset($_SESSION['username'])) {
    // Retrieve user's role from the database
    $username = $_SESSION['username'];
    $query = "SELECT user_role FROM login WHERE username = '$username'";
    $result = $connection->query($query);
    
    if($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $user_role = $row['user_role'];
        
        // Redirect based on user's role
        if($user_role == 'teacher') {
            header("Location: profile_page.php");
            exit();
        } elseif($user_role == 'principal') {
            header("Location: admin_profile_page.php");
            exit();
        }
    } else {
        // If user role not found, redirect to login page
        header("Location: login_page.html");
        exit();
    }
} else {
    // If user is not logged in, redirect to login page
    header("Location: login_page.html");
    exit();
}
?>
