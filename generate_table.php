<?php
//session_start(); // Start the session

// Check if the day and time period parameters are set in the request
if(isset($_GET['day']) && isset($_GET['time_period'])) {
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

// Map the selected time period value to the corresponding time slots
$timePeriodMap = [
    "full" => [
        '07:50:00 - 08:30:00',
        '08:30:00 - 09:10:00',
        '09:10:00 - 09:50:00',
        '09:50:00 - 10:30:00',
        '10:50:00 - 11:30:00', 
        '11:30:00 - 12:10:00',
        '12:10:00 - 12:50:00',
        '12:50:00 - 13:30:00'
    ],
    "time_7" => '07:50:00 - 08:30:00',
    "time_8" => '08:30:00 - 09:10:00',
    "time_9" => '09:10:00 - 09:50:00',
    "time_9_50" => '09:50:00 - 10:30:00',
    "time_10" => '10:50:00 - 11:30:00',
    "time_11" => '11:30:00 - 12:10:00',
    "time_12" => '12:10:00 - 12:50:00',
    "time_1" => '12:50:00 - 13:30:00'
];

// Get the selected time slots based on the current time period
if(isset($timePeriodMap[$currentTimePeriod])) {
    $selectedTimeSlots = $timePeriodMap[$currentTimePeriod];
} else {
    // Default to full time period if invalid time period selected
    $selectedTimeSlots = $timePeriodMap["full"];
}

// Modify the SQL query to filter results based on the selected day and time period
$sql = "SELECT class_id, start_time, end_time, $currentDay as subject FROM class_time_table WHERE $currentDay IS NOT NULL";

// Adjust the query based on the selected time period
if (!is_array($selectedTimeSlots)) {
    // Modify the query to fetch data for the selected time period
    $sql .= " AND start_time <= '$selectedTimeSlots' AND end_time >= '$selectedTimeSlots'";
}

$sql .= " ORDER BY start_time";

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
            'time' => $row["start_time"] . " - " . $row["end_time"],
            'subject' => $row['subject']
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

// Display the first table for grades 6-10
echo "<table border='1'>";
echo "<caption><h3>Time Table - Grades 6 to 10</h3></caption>";
echo "<tr><th>Time</th>";
foreach ($grades_table1 as $grade) {
    echo "<th>$grade</th>";
}
echo "</tr>";

if (is_array($selectedTimeSlots)) {
    // Display all time slots for the full time period
    foreach ($selectedTimeSlots as $timeSlot) {
        echo "<tr>";
        echo "<td>" . $timeSlot . "</td>";
        foreach ($grades_table1 as $grade) {
            echo "<td>";
            foreach ($classSchedules[$grade] as $class) {
                if ($class['time'] === $timeSlot) {
                    echo $class['subject'] . "<br>";
                }
            }
            echo "</td>";
        }
        echo "</tr>";
    }
} else {
    // Display the time slots for the selected time period
    for ($i = 0; $i < $maxClasses; $i++) {
        echo "<tr>";
        // Display the time slot
        echo "<td>" . $selectedTimeSlots . "</td>";
        foreach ($grades_table1 as $grade) {
            echo "<td>";
            if (isset($classSchedules[$grade][$i]) && $classSchedules[$grade][$i]['time'] === $selectedTimeSlots) {
                echo $classSchedules[$grade][$i]['subject'];
            }
            echo "</td>";
        }
        echo "</tr>";
    }
}
echo "</table>";

// Display the second table for grades 11-12
echo "<table border='1'>";
echo "<caption><h3>Time Table - Grades 11 to 12</h3></caption>";
echo "<tr><th>Time</th>";
foreach ($grades_table2 as $grade) {
    echo "<th>$grade</th>";
}
echo "</tr>";

if (is_array($selectedTimeSlots)) {
    // Display all time slots for the full time period
    foreach ($selectedTimeSlots as $timeSlot) {
        echo "<tr>";
        echo "<td>" . $timeSlot . "</td>";
        foreach ($grades_table2 as $grade) {
            echo "<td>";
            foreach ($classSchedules[$grade] as $class) {
                if ($class['time'] === $timeSlot) {
                    echo $class['subject'] . "<br>";
                }
            }
            echo "</td>";
        }
        echo "</tr>";
    }
} else {
    // Display the time slots for the selected time period
    for ($i = 0; $i < $maxClasses; $i++) {
        echo "<tr>";
        // Display the time slot
        echo "<td>" . $selectedTimeSlots . "</td>";
        foreach ($grades_table2 as $grade) {
            echo "<td>";
            if (isset($classSchedules[$grade][$i]) && $classSchedules[$grade][$i]['time'] === $selectedTimeSlots) {
                echo $classSchedules[$grade][$i]['subject'];
            }
            echo "</td>";
        }
        echo "</tr>";
    }
}
echo "</table>";
?>
