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

// Check if form is submitted for creating teacher syllabus table
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["create_syllabus_table"])) {
    $teacher_username = $_POST["username"];
    
    // Check if teacher syllabus table already exists for this username
    $table_name = "teacher_syllabus_table_" . $teacher_username;
    $check_table_query = "SHOW TABLES LIKE '$table_name'";
    $check_table_result = $connection->query($check_table_query);
    
    if ($check_table_result->num_rows > 0) {
        echo "Teacher tsyllabus table already exists for this username.";
    } else {
        // Create teacher syllabus table table
        $create_table_query = "CREATE TABLE $table_name (
            registration_id VARCHAR(20),
            term_id VARCHAR(20),
            class_id VARCHAR(20),
            subject_id VARCHAR(20),
            assign_date DATE,
            week_id VARCHAR(20),
            conduct_date DATE,
            start_time TIME,
            lesson_time VARCHAR(20),
            mastery VARCHAR(500),
            section_number VARCHAR(20),
            course_content VARCHAR(1500),
            teaching_date DATE,
            note VARCHAR(250)
        )";
        
        if ($connection->query($create_table_query) === TRUE) {
            echo "Teacher syllabus table created successfully";
        } else {
            echo "Error creating teacher syllabus table: " . $connection->error;
        }
    }
}
// Check if form is submitted for adding syllabus table data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_data"])) {
    $teacher_username = $_POST["username"];
    $registration_id = $_POST["teacher_id"];
    $term_id = $_POST["term_id"];
    $class_id = $_POST["class_id"];
    $subject_id = $_POST["subject_id"];
    $assign_date = $_POST["assign_date"];
    $week_id = $_POST["week_id"];
    $conduct_date = $_POST["conduct_date"];
    $lesson_time = $_POST["lesson_time"];
    $mastery = isset($_POST["mastery"]) ? $_POST["mastery"] : "";
    $section_number = $_POST["section_number"];
    $course_content = isset($_POST["course_content"]) ? $_POST["course_content"] : "";
    $teaching_date = $_POST["teaching_date"];
    $note = isset($_POST["note"]) ? $_POST["note"] : "";

    // Insert syllabus table data into respective teacher's syllabus table
    $insert_query = "INSERT INTO teacher_syllabus_table_$teacher_username (registration_id, term_id, class_id, subject_id, assign_date, week_id, conduct_date, lesson_time, mastery, section_number, course_content, teaching_date, note) VALUES ('$registration_id', '$term_id', '$class_id', '$subject_id', '$assign_date', '$week_id', '$conduct_date', '$lesson_time', '$mastery', '$section_number', '$course_content', '$teaching_date', '$note')";

    if ($connection->query($insert_query) === TRUE) {
        echo "Syllabus table data added successfully";
    } else {
        echo "Error adding syllabus table data: " . $connection->error;
    }
}


// Check if form is submitted for previewing syllabus table data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["preview_syllabus_table"])) {
    $teacher_username = $_POST["username"];
    
    // Fetch syllabus table data for the specified teacher
    $table_name = "teacher_syllabus_table_" . $teacher_username;
    $preview_query = "SELECT * FROM $table_name";
    $preview_result = $connection->query($preview_query);
    
    if ($preview_result->num_rows > 0) {
        // Display the fetched syllabus table data
        echo "<h3>Syllabus Table Preview:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>Registration ID</th><th>Term ID</th><th>Class ID</th><th>Subject ID</th><th>Assign Date</th><th>Week ID</th><th>Conduct Date</th><th>Lesson Time</th><th>Mastery</th><th>Section Number</th><th>Course Content</th><th>Teaching Date</th><th>Note</th></tr>";
        while($row = $preview_result->fetch_assoc()) {
            echo "<tr><td>".$row["registration_id"]."</td><td>".$row["term_id"]."</td><td>".$row["class_id"]."</td><td>".$row["subject_id"]."</td><td>".$row["assign_date"]."</td><td>".$row["week_id"]."</td><td>".$row["conduct_date"]."</td><td>".$row["lesson_time"]."</td><td>".$row["mastery"]."</td><td>".$row["section_number"]."</td><td>".$row["course_content"]."</td><td>".$row["teaching_date"]."</td><td>".$row["note"]."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "No syllabus table data found for the specified teacher.";
    }
}

// Close connection
$connection->close();
?>
