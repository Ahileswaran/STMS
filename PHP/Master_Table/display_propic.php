<?php
session_start(); // Start the session

// Include database connection
include 'generate_table.php';

// Database connection details
$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

// Create a new database connection
$connection = new mysqli($server, $username, $password, $database);

// Check if the connection was successful
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if the day and time period parameters are set in the request
if(isset($_GET['day']) && isset($_GET['time_period'])) {
    $currentDay = strtolower($_GET['day']);
    $currentTimePeriod = $_GET['time_period'];
} else {
    // Default to 'friday' and 'full' time period if parameters are not provided
    $currentDay = 'friday';
    $currentTimePeriod = 'full';
}



// Check if class ID, subject, and day of the week are received via GET request
if(isset($_GET['class_id']) && isset($_GET['subject']) && isset($_GET['day_of_week'])) {
    // Get the class ID, subject, and day of the week from the GET request
    $classId = $_GET['class_id'];
    $subject = $_GET['subject'];
    $dayOfWeek = $_GET['day_of_week'];

    // Query to fetch the username based on the provided parameters
    $usernameQuery = "SELECT username FROM master_time_table WHERE class_id = '$classId' AND subject_id = '$subject' AND class_day = '$dayOfWeek' AND start_time = '07:50:00'"; // Assuming you want to match the start time as well
    
    // Execute the username query
    $usernameResult = $connection->query($usernameQuery);

    // Check if the query was successful and fetch the username
    if ($usernameResult && $usernameResult->num_rows > 0) {
        $usernameRow = $usernameResult->fetch_assoc();
        $username = $usernameRow['username'];

        // Query to fetch the profile picture based on the username
        $profileQuery = "SELECT profile_pic FROM profile_picture WHERE username = '$username'";
        
        // Execute the profile query
        $profileResult = $connection->query($profileQuery);

        // Check if the query was successful and fetch the profile picture
        if ($profileResult && $profileResult->num_rows > 0) {
            $profileRow = $profileResult->fetch_assoc();
            $profilePic = $profileRow['profile_pic'];

            // Display the profile picture
            echo "<img src='data:image/jpeg;base64," . base64_encode($profilePic) . "' width='100' height='100'>";
        } else {
            // Display a placeholder if no profile picture is found
            echo "<img src='placeholder.jpg' width='100' height='100'>";
        }
    } else {
        // Display a placeholder if no username is found
        echo "<img src='placeholder.jpg' width='100' height='100'>";
    }
} else {
    // Handle the case if parameters are not provided
    echo "Error: Missing parameters.";
}

// Close the database connection
$connection->close();
?>
