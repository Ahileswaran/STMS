<?php
//session_start(); // Start the session

$username = "root"; 
$password = ""; 
$server = "localhost";  
$database = "stms_database"; 

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

<?php
$username = "root"; 
$password = ""; 
$server = "localhost";  
$database = "stms_database"; 
$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$currentDay = $_POST['classDay'] ?? '';

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
        echo "<td>" . $row['subject'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . $connection->error;
}
$connection->close();
?>


