<?php
$username = "root"; 
$password = "pass@123"; 
$server = "127.0.0.1";   
$database = "stms_database"; 

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

echo "Server connected successfully";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Escape user inputs for security
    $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
    $teacher_address = mysqli_real_escape_string($connection, $_POST['address']);
    $age = mysqli_real_escape_string($connection, $_POST['age']);
    $sex = mysqli_real_escape_string($connection, $_POST['sex']);
    $marital_status = mysqli_real_escape_string($connection, $_POST['marital_status']);
    $registration_id = mysqli_real_escape_string($connection, $_POST['teacher_id']);
    $subject_name = mysqli_real_escape_string($connection, $_POST['subject']);
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $email = mysqli_real_escape_string($connection, $_POST['mail_id']);
    $teacher_password = mysqli_real_escape_string($connection, $_POST['password']);

    // Attempt insert query execution
    $sql = "INSERT INTO teacher (first_name, last_name, teacher_address, age, sex, marital_status, registration_id, subject_name, username, mail_id, teacher_password, email) 
    VALUES ('$first_name', '$last_name', '$teacher_address', '$age', '$sex', '$marital_status', '$registration_id', '$subject_name', '$username', '$teacher_password', '$email')";
    if(mysqli_query($connection, $sql)){
        echo "Records added successfully.";
    } else{
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($connection);
    }
}

// Close connection
mysqli_close($connection);
?>
