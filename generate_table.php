<?php
//session_start(); // Start the session
require_once 'display_propic.php';

// Check if the day and time period parameters are set in the request
if (isset($_GET['day']) && isset($_GET['time_period'])) {
    $currentDay = strtolower($_GET['day']);
    $currentTimePeriod = $_GET['time_period'];
} else {
    // Default to 'friday' and 'full' time period if parameters are not provided
    $currentDay = 'friday';
    $currentTimePeriod = 'full';
}

$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Define an array to hold the grades
$grades = ["Grade_6", "Grade_7", "Grade_8", "Grade_9", "Grade_10", "Grade_11", "Grade_12_Arts", "Grade_12_Science", "Grade_12_Maths"];

$profilePicUrl = 'display_propic.php?'; // URL to fetch profile pictures

// Fetch profile pictures from the database
$profilePictures = [];
$query = "SELECT username, profile_pic FROM profile_picture";
$result = $connection->query($query);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $profilePictures[$row['username']] = $row['profile_pic'];
    }
}

// Map the selected time period value to the corresponding time slots
$timePeriodMap = [
    "full" => [
        1 => '07:50:00 - 08:30:00',
        2 => '08:30:00 - 09:10:00',
        3 => '09:10:00 - 09:50:00',
        4 => '09:50:00 - 10:30:00',
        5 => '10:50:00 - 11:30:00',
        6 => '11:30:00 - 12:10:00',
        7 => '12:10:00 - 12:50:00',
        8 => '12:50:00 - 13:30:00'
    ],
    "time_7" => 1,
    "time_8" => 2,
    "time_9" => 3,
    "time_9_50" => 4,
    "time_10" => 5,
    "time_11" => 6,
    "time_12" => 7,
    "time_1" => 8
];

// Get the selected time slots based on the current time period
if (isset($timePeriodMap[$currentTimePeriod])) {
    $selectedTimeSlots = $timePeriodMap[$currentTimePeriod];
} else {
    // Default to full time period if invalid time period selected
    $selectedTimeSlots = $timePeriodMap["full"];
}

// Modify the SQL query to join class_time_table with master_time_table based on the period
$sql = "SELECT c.class_id, m.period, c.$currentDay AS subject, m.username
        FROM class_time_table c
        INNER JOIN master_time_table m ON c.start_time = m.start_time AND c.end_time = m.end_time
        WHERE c.$currentDay IS NOT NULL";

// Adjust the query based on the selected time period
if ($currentTimePeriod !== "full" && !is_array($selectedTimeSlots)) {
    // Modify the query to fetch data for the selected time period
    $sql .= " AND m.period = '$selectedTimeSlots'";
}

$sql .= " ORDER BY m.period";

$result = $connection->query($sql);

// Initialize an array to hold class schedules for each grade
$classSchedules = [];
foreach ($grades as $grade) {
    $classSchedules[$grade] = [];
}

// Populate class schedules based on fetched data
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $classSchedules[$row['class_id']][] = [
            'class_id' => $row['class_id'], // Ensure class_id is included
            'period' => $row["period"],
            'time' => $timePeriodMap["full"][$row["period"]],
            'subject' => $row['subject'],
            'username' => $row['username'] // Include the username
        ];
    }
}

// Close the connection
$connection->close();

// Determine the maximum number of classes among all grades
$maxClasses = 0;
foreach ($classSchedules as $gradeSchedule) {
    $maxClasses = max($maxClasses, count($gradeSchedule));
}

// Split the grades into two arrays for two tables
$grades_table1 = array_slice($grades, 0, 5);
$grades_table2 = array_slice($grades, 5);

// Include the CSS styles in the output
echo "<style>
/* General styles */
.container {
	width: 95%;
	max-width: 1800px;
	margin: 0 auto;
	padding: 23px;
	box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
	border-radius: 8px;
	overflow: hidden;
	background-color: #fff;
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
    color: #555; /* Darker color for the username */
    color: #26a9dd;
    font-weight: bold;
}

/* Image container styles */
.image-container {
    display: inline-block;
    position: relative;
    overflow: hidden;
}

.image-container img {
    width: 50px;
    height: 50px;
    transition: transform 0.3s ease;
}

.image-container:hover img {
    transform: scale(1.5); /* Scale image to 1.5 times its size on hover */
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
    .image-container img {
        width: 40px; 
        height: 40px;
    }
}

</style>";

// Display the first table for grades 6-10
echo "<div class='container'>";
echo "<table border='1'>";
echo "<caption><h3>Time Table - Grades 6 to 10</h3></caption>";
echo "<tr><th>Period</th>";
foreach ($grades_table1 as $grade) {
    echo "<th>$grade</th>";
}
echo "</tr>";

if (is_array($selectedTimeSlots)) {
    // Display all time slots for the full time period
    foreach ($selectedTimeSlots as $period => $timeSlot) {
        echo "<tr>";
        echo "<td>" . $timeSlot . "</td>";
        foreach ($grades_table1 as $grade) {
            echo "<td>";
            $classFound = false;
            foreach ($classSchedules[$grade] as $class) {
                if ($class['period'] == $period) {
                    echo "<div class='name'>" . $class['subject'] . "</div><br>";
                    echo "<div class='username'>Username: " . $class['username'] . "</div><br>";
                    if (isset($profilePictures[$class['username']])) {
                        echo "<div class='image-container'><img src='data:image/jpeg;base64," . base64_encode($profilePictures[$class['username']]) . "' alt='User Image'></div>";
                    } else {
                        echo "Profile picture not found";
                    }
                    $classFound = true;
                    break;
                }
            }
            if (!$classFound) {
                echo "No class";
            }
            echo "</td>";
        }
        echo "</tr>";
    }
} else {
    // Display the time slots for the selected time period
    for ($i = 0; $i < $maxClasses; $i++) {
        echo "<tr>";
        // Display the period
        echo "<td>" . $timePeriodMap["full"][$selectedTimeSlots] . "</td>";
        foreach ($grades_table1 as $grade) {
            echo "<td>";
            if (isset($classSchedules[$grade][$i]) && $classSchedules[$grade][$i]['period'] == $selectedTimeSlots) {
                // Display the subject name, username, and profile picture
                echo "<div class='name'>" . $classSchedules[$grade][$i]['subject'] . "</div><br>";
                echo "<div class='username'>Username: " . $classSchedules[$grade][$i]['username'] . "</div><br>";
                // Check if profile picture exists for the username
                if (isset($profilePictures[$classSchedules[$grade][$i]['username']])) {
                    echo "<div class='image-container'><img src='data:image/jpeg;base64," . base64_encode($profilePictures[$classSchedules[$grade][$i]['username']]) . "' alt='User Image'></div>";
                } else {
                    echo "Profile picture not found";
                }
            } else {
                echo "No class";
            }
            echo "</td>";
        }
        echo "</tr>";
    }
}
echo "</table>";
echo "</div>";

// Display the second table for grades 11-12
echo "<div class='container'>";
echo "<table border='1'>";
echo "<caption><h3>Time Table - Grades 11 to 12</h3></caption>";
echo "<tr><th>Period</th>";
foreach ($grades_table2 as $grade) {
    echo "<th>$grade</th>";
}
echo "</tr>";

if (is_array($selectedTimeSlots)) {
    // Display all time slots for the full time period
    foreach ($selectedTimeSlots as $period => $timeSlot) {
        echo "<tr>";
        echo "<td>" . $timeSlot . "</td>";
        foreach ($grades_table2 as $grade) {
            echo "<td>";
            $classFound = false;
            foreach ($classSchedules[$grade] as $class) {
                if ($class['period'] == $period) {
                    echo "<div class='name'>" . $class['subject'] . "</div><br>";
                    echo "<div class='username'>Username: " . $class['username'] . "</div><br>";
                    if (isset($profilePictures[$class['username']])) {
                        echo "<div class='image-container'><img src='data:image/jpeg;base64," . base64_encode($profilePictures[$class['username']]) . "' alt='User Image'></div>";
                    } else {
                        echo "Profile picture not found";
                    }
                    $classFound = true;
                    break;
                }
            }
            if (!$classFound) {
                echo "No class";
            }
            echo "</td>";
        }
        echo "</tr>";
    }
} else {
    // Display the time slots for the selected time period
    for ($i = 0; $i < $maxClasses; $i++) {
        echo "<tr>";
        // Display the period
        echo "<td>" . $timePeriodMap["full"][$selectedTimeSlots] . "</td>";
        foreach ($grades_table2 as $grade) {
            echo "<td>";
            if (isset($classSchedules[$grade][$i]) && $classSchedules[$grade][$i]['period'] == $selectedTimeSlots) {
                // Display the subject name, username, and profile picture
                echo "<div class='name'>" . $classSchedules[$grade][$i]['subject'] . "</div><br>";
                echo "<div class='username'>Username: " . $classSchedules[$grade][$i]['username'] . "</div><br>";
                // Check if profile picture exists for the username
                if (isset($profilePictures[$classSchedules[$grade][$i]['username']])) {
                    echo "<div class='image-container'><img src='data:image/jpeg;base64," . base64_encode($profilePictures[$classSchedules[$grade][$i]['username']]) . "' alt='User Image'></div>";
                } else {
                    echo "Profile picture not found";
                }
            } else {
                echo "No class";
            }
            echo "</td>";
        }
        echo "</tr>";
    }
}
echo "</table>";
echo "</div>";
?>

