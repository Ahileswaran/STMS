<?php

session_start(); // Start the session

$username = "root"; 
$password = ""; 
$server = "localhost";  
$database = "stms_database"; 

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if (!isset($_SESSION['username'])) {
    // Redirect to login page if user is not logged in
    header("Location: login_page.html");
    exit(); // Stop further execution
}

?>
