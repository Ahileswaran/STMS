<?php
// Check if the day parameter is set in the request
$currentDay = isset($_GET['day']) ? strtolower($_GET['day']) : 'friday';

$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

$connection = new mysqli($server, $username, $password, $database);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Define an array to hold the grades
$grades = ["Grade_1", "Grade_2", "Grade_3", "Grade_4", "Grade_5", "Grade_6", "Grade_7", "Grade_8", "Grade_9", "Grade_10", "Grade_11", "Grade_12_Arts", "Grade_12_Science", "Grade_12_Maths"];

// Fetch profile pictures from the database
$profilePictures = [];
$query = "SELECT username, profile_pic FROM profile_picture";
$result = $connection->query($query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $profilePictures[$row['username']] = $row['profile_pic'];
    }
}

// Map the time slots
$timeSlots = [
    1 => '07:50:00 - 08:30:00',
    2 => '08:30:00 - 09:10:00',
    3 => '09:10:00 - 09:50:00',
    4 => '09:50:00 - 10:30:00',
    5 => '10:50:00 - 11:30:00',
    6 => '11:30:00 - 12:10:00',
    7 => '12:10:00 - 12:50:00',
    8 => '12:50:00 - 13:30:00'
];

// Modify the SQL query to join class_time_table with master_time_table and teacher_classes based on the period, username, and subject
$sql = "SELECT c.class_id, m.period, c.$currentDay AS subject, m.username, t.subject_name
        FROM class_time_table c
        INNER JOIN master_time_table m ON c.start_time = m.start_time AND c.end_time = m.end_time
        INNER JOIN teacher_classes t ON m.username = t.username AND c.$currentDay = t.subject_name
        WHERE c.$currentDay IS NOT NULL AND (
            c.class_id = t.class_1 OR
            c.class_id = t.class_2 OR
            c.class_id = t.class_3
        )
        ORDER BY m.period";

$result = $connection->query($sql);

// Initialize an array to hold class schedules for each grade
$classSchedules = [];
foreach ($grades as $grade) {
    $classSchedules[$grade] = [];
}

// Populate class schedules based on fetched data
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classSchedules[$row['class_id']][$row['period']] = [
            'class_id' => $row['class_id'],
            'period' => $row["period"],
            'time' => isset($timeSlots[$row["period"]]) ? $timeSlots[$row["period"]] : '',
            'subject' => $row['subject'],
            'username' => $row['username']
        ];
    }
}

// Close the connection
$connection->close();

// Split the grades into three arrays for three tables
$grades_table1 = array_slice($grades, 0, 5);
$grades_table2 = array_slice($grades, 5, 5);
$grades_table3 = array_slice($grades, 10);

// Include the CSS styles in the output
echo "<style>
/* General styles */

body,
html {
    height: 100%;
    margin: 0;

    flex-direction: column;
}

.container {
    max-width: 1800px;
    margin: 0 auto;
    padding: 5px;

}

table {
    width: 100%;
    border-collapse: collapse;
    background-color: #ffffff;
    border-radius: 8px;
    overflow: hidden;

}

caption {
    font-size: 1.5rem;
    font-weight: bold;
    padding: 10px 20px;
    text-align: left;
    border: 1px solid #ddd;
    border-bottom: none;
    background-color: #3fd2ff;
    border-radius: 8px 8px 0 0;
}

th, td {
    padding: 12px 24px;
    border: 1px solid #ddd;
    text-align: center; /* Center the text */
    color: #333; /* Default text color */
}

th {
    background-color: #2593dd;
    color: #ffffff; /* White text for headers */
    font-weight: bold;
    transition: background-color 0.3s;
}

td {
    transition: background-color 0.3s;
}

tr:nth-child(even) td {
    background-color: #f9f9f9;
}

tr:hover td {
    background-color: #f1f1f1;
}

/* Column specific styles */
th:nth-child(1), td:nth-child(1) {
    background-color: #2593dd; /* Blue background */
    color: #0d0d0d; /* Black text */
    font-weight: bold;
}

th:nth-child(2), td:nth-child(2) {
    background-color: #d0f0c0; /* Light Green background */
    color: #333; /* Dark text */
}

th:nth-child(3), td:nth-child(3) {
    background-color: #f9e79f; /* Light Yellow background */
    color: #333; /* Dark text */
}

th:nth-child(4), td:nth-child(4) {
    background-color: #f5b7b1; /* Light Pink background */
    color: #333; /* Dark text */
}

th:nth-child(5), td:nth-child(5) {
    background-color: #aed6f1; /* Light Blue background */
    color: #333; /* Dark text */
}

th:nth-child(6), td:nth-child(6) {
    background-color: #e8daef; /* Light Purple background */
    color: #333; /* Dark text */
}

.description {
    width: 30%;
}

.data-text {
    width: 17%;
}

/* Name and Username styles */
.name {
    font-size: 1.2rem;
    color: #2593dd; /* Blue color for the name */
    font-weight: bold;
}

.username {
    font-size: 1rem;
    color: #26a9dd; /* Blue color for the username */
    font-weight: bold;
    margin-top: 5px;
}

/* Image container styles */
.image-container {
    display: flex;
    align-items: center;
    justify-content: center;
    margin-top: 5px;
    padding: 5px;
    background-color: #f1f1f1;
    border-radius: 50%;
    width: 60px;
    height: 60px;
    overflow: hidden;
}

.image-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.image-container:hover img {
    transform: scale(1.2); /* Scale image to 1.2 times its size on hover */
}

/* Make table responsive */
@media (max-width: 768px) {
    th, td {
        padding: 10px;
        font-size: 0.9rem;
    }
    .name, .username {
        font-size: 0.9rem; 
    }
}

@media (max-width: 480px) {
    th, td {
        padding: 8px;
        font-size: 0.8rem; 
    }
    .name, .username {
        font-size: 0.8rem; 
    }
    .image-container {
        width: 40px; 
        height: 40px;
    }
}
.master-table {
    margin:auto;
    margin-top: 20px;
}


</style>";

// Function to render a cell for a grade
if (!function_exists('renderCell')) {
    function renderCell($grade, $period, $classSchedules, $profilePictures)
    {
        if (isset($classSchedules[$grade][$period])) {
            $class = $classSchedules[$grade][$period];
            echo "<div class='name'>" . $class['subject'] . "</div>";
            echo "<div class='username'>" . $class['username'] . "</div>";
            if (isset($profilePictures[$class['username']])) {
                echo "<div class='image-container'><img src='data:image/jpeg;base64," . base64_encode($profilePictures[$class['username']]) . "' alt='User Image'></div>";
            } else {
                echo "<div class='image-container'><img src='placeholder.jpg' alt='No Profile Picture'></div>";
            }
        } else {
            echo "No class";
        }
    }
}

// Display the first table for grades 1-5
echo "<div class='container'>";
echo "<table border='1'>";
echo "<caption><h3>Time Table - Grades 1 to 5</h3></caption>";
echo "<tr><th>Period</th>";
foreach ($grades_table1 as $grade) {
    echo "<th>$grade</th>";
}
echo "</tr>";

// Display all time slots except the specified ones for grades 1-5
foreach ($timeSlots as $period => $timeSlot) {
    // Skip specific time slots for grades 1-5
    if (in_array($timeSlot, ['10:50:00 - 11:30:00', '11:30:00 - 12:10:00', '12:10:00 - 12:50:00', '12:50:00 - 13:30:00'])) {
        continue;
    }

    echo "<tr>";
    echo "<td>" . $timeSlot . "</td>";
    foreach ($grades_table1 as $grade) {
        echo "<td>";
        // Skip displaying "No class" for specific grades and period
        if ($period == 4 && in_array($grade, ['Grade_1', 'Grade_2', 'Grade_3', 'Grade_4'])) {
            echo "";
        } else {
            renderCell($grade, $period, $classSchedules, $profilePictures);
        }
        echo "</td>";
    }
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// Display the second table for grades 6-10
echo "<div class='container'>";
echo "<table border='1'>";
echo "<caption><h3>Time Table - Grades 6 to 10</h3></caption>";
echo "<tr><th>Period</th>";
foreach ($grades_table2 as $grade) {
    echo "<th>$grade</th>";
}
echo "</tr>";

// Display all time slots
foreach ($timeSlots as $period => $timeSlot) {
    echo "<tr>";
    echo "<td>" . $timeSlot . "</td>";
    foreach ($grades_table2 as $grade) {
        echo "<td>";
        renderCell($grade, $period, $classSchedules, $profilePictures);
        echo "</td>";
    }
    echo "</tr>";
}
echo "</table>";
echo "</div>";

// Display the third table for grades 11-12
echo "<div class='container'>";
echo "<table border='1'>";
echo "<caption><h3>Time Table - Grades 11 to 12</h3></caption>";
echo "<tr><th>Period</th>";
foreach ($grades_table3 as $grade) {
    echo "<th>$grade</th>";
}
echo "</tr>";

// Display all time slots
foreach ($timeSlots as $period => $timeSlot) {
    echo "<tr>";
    echo "<td>" . $timeSlot . "</td>";
    foreach ($grades_table3 as $grade) {
        echo "<td>";
        renderCell($grade, $period, $classSchedules, $profilePictures);
        echo "</td>";
    }
    echo "</tr>";
}
echo "</table>";
echo "</div>";
?>
