<?php
$username = "root"; 
$password = ""; 
$server = "localhost";  
$database = "stms_database"; 

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if the username parameter is set for username suggestion
if(isset($_POST['username'])) {
    $input_username = mysqli_real_escape_string($connection, $_POST['username']);

    // Query to check if the username exists in the teacher table
    $query = "SELECT username FROM teacher WHERE username LIKE '$input_username%'";
    $result = mysqli_query($connection, $query);

    $suggestions = '';
    while ($row = mysqli_fetch_assoc($result)) {
        $suggestions .= $row['username'] . '<br>';
    }

    echo $suggestions;
    exit; // Stop further execution after sending username suggestions
}

// Close connection
mysqli_close($connection);
?>
