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

// Check if form is submitted for creating teacher time table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_time_table"])) {
    $teacher_username = $_POST["username"];
    
    // Check if teacher time table already exists for this username
    $table_name = "teacher_time_table_" . $teacher_username;
    $check_table_query = "SHOW TABLES LIKE '$table_name'";
    $check_table_result = $connection->query($check_table_query);
    
    if ($check_table_result->num_rows > 0) {
        echo "Teacher time table already exists for this username.";
    } else {
        // Create teacher time table table
        $create_table_query = "CREATE TABLE $table_name (
            registration_id VARCHAR(20),
            class_id VARCHAR(20),
            subject_id VARCHAR(20),
            class_day DATE,
            start_time TIME,
            end_time TIME
        )";
        
        if ($connection->query($create_table_query) === TRUE) {
            echo "Teacher time table created successfully";
        } else {
            echo "Error creating teacher time table: " . $connection->error;
        }
    }
}
// Check if form is submitted for adding time table data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_data"])) {
    $teacher_username = $_POST["username"];
    $registration_id = $_POST["teacher_id"];
    $class_id = $_POST["class_id"];
    $subject_id = $_POST["subject_id"];
    $class_day = $_POST["class_day"];
    $start_time = $_POST["start_time"];
    $end_time = $_POST["end_time"];

    // Insert time table data into respective teacher's time table
    $insert_query = "INSERT INTO teacher_time_table_$teacher_username (registration_id, class_id, subject_id, class_day, start_time, end_time) VALUES ('$registration_id', '$class_id', '$subject_id', '$class_day', '$start_time', '$end_time')";
    
    if ($connection->query($insert_query) === TRUE) {
        echo "Time table data added successfully";
    } else {
        echo "Error adding time table data: " . $connection->error;
    }
}

// Check if form is submitted for previewing time table data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["preview_time_table"])) {
    $teacher_username = $_POST["username"];
    
    // Fetch time table data for the specified teacher
    $table_name = "teacher_time_table_" . $teacher_username;
    $preview_query = "SELECT * FROM $table_name";
    $preview_result = $connection->query($preview_query);
    
    if ($preview_result->num_rows > 0) {
        // Display the fetched time table data
        echo "<h3>Time Table Preview:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Registration ID</th><th>Class ID</th><th>Subject ID</th><th>Class Day</th><th>Start Time</th><th>End Time</th></tr>";
        while($row = $preview_result->fetch_assoc()) {
            echo "<tr><td>".$row["registration_id"]."</td><td>".$row["class_id"]."</td><td>".$row["subject_id"]."</td><td>".$row["class_day"]."</td><td>".$row["start_time"]."</td><td>".$row["end_time"]."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No time table data found for the specified teacher.";
    }
}

// Close connection
$connection->close();
?>
