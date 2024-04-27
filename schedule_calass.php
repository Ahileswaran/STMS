<?php
// Your existing code for connecting to the database and retrieving class schedule

$currentDay = $_POST['day'] ?? '';
$selectedTimePeriod = $_POST['time_period'] ?? '';

// Assuming $currentDay is sanitized and safe to use directly in the query
$sql = "SELECT class_id, start_time, end_time, `$currentDay` as subject FROM class_time_table WHERE `$currentDay` IS NOT NULL ORDER BY start_time";
$result = $connection->query($sql);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Class ID</th><th>Start Time</th><th>End Time</th><th>Subject</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['class_id'] . "</td>";
        echo "<td>" . $row['start_time'] . "</td>";
        echo "<td>" . $row['end_time'] . "</td>";
        // Check if subject exists and is not an empty string
        if (isset($row['subject']) && $row['subject'] !== '') {
            echo "<td>" . $row['subject'] . "</td>";
        } else {
            echo "<td>No Scheduled Class</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . $connection->error;
}

$connection->close();
?>
