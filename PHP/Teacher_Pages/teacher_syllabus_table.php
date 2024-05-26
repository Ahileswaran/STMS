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

// Syllabus table
$teacher_username = $_SESSION['username'];
$syllabus_table_name = "teacher_syllabus_table_" . $teacher_username;

try {
    $syllabus_query = "SELECT * FROM $syllabus_table_name WHERE registration_id = ?";
    $syllabus_stmt = $connection->prepare($syllabus_query);
    $syllabus_stmt->bind_param("s", $_SESSION['registration_id']);
    $syllabus_stmt->execute();
    $syllabus_result = $syllabus_stmt->get_result();
} catch (mysqli_sql_exception $e) {
    // Table does not exist, create it
    $create_syllabus_table_query = "CREATE TABLE $syllabus_table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        registration_id VARCHAR(255) NOT NULL,
        week_id INT NOT NULL,
        assign_date DATE NOT NULL,
        conduct_date DATE NOT NULL,
        start_time TIME NOT NULL,
        lesson_time TIME NOT NULL,
        mastery VARCHAR(255) NOT NULL,
        section_number INT NOT NULL,
        course_content TEXT NOT NULL,
        teaching_date DATE NOT NULL,
        note TEXT
    )";
    if ($connection->query($create_syllabus_table_query) === TRUE) {
        $syllabus_stmt = $connection->prepare($syllabus_query);
        $syllabus_stmt->bind_param("s", $_SESSION['registration_id']);
        $syllabus_stmt->execute();
        $syllabus_result = $syllabus_stmt->get_result();
    } else {
        $syllabus_result = null; // Proceed without the syllabus table
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syllabus Table</title>
    <style>
        .glass-container {
            padding: 20px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
            margin-top: 50px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        .error {
            color: red;
        }
    </style>
</head>

<body>
    <div class="glass-container">
        <h3>Syllabus Table</h3>
        <table border="1">
            <tr>
                <th>Week ID</th>
                <th>Assign Date</th>
                <th>Conduct Date</th>
                <th>Start Time</th>
                <th>Lesson Time</th>
                <th>Mastery</th>
                <th>Section Number</th>
                <th>Course Content</th>
                <th>Teaching Date</th>
                <th>Note</th>
            </tr>
            <?php
            if ($syllabus_result) {
                if ($syllabus_result->num_rows > 0) {
                    while ($row = $syllabus_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['week_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['assign_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['conduct_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['lesson_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['mastery']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['section_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['course_content']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['teaching_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['note']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='error'>No syllabus details available for this user.</td></tr>";
                }
            } else {
                echo "<tr><td colspan='10' class='error'>Syllabus table not available.</td></tr>";
            }
            ?>
        </table>
    </div>
</body>

</html>
