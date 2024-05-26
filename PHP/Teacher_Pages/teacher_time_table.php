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

// Fetch timetable data
$username = $_SESSION['username'];
$table_name = "teacher_time_table_" . $username; // Construct the table name dynamically

try {
    $query_timetable = "SELECT * FROM $table_name WHERE registration_id='{$_SESSION['registration_id']}'";
    $result_timetable = mysqli_query($connection, $query_timetable);
} catch (mysqli_sql_exception $e) {
    // Table does not exist, create it
    $create_table_query = "CREATE TABLE $table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        registration_id VARCHAR(255) NOT NULL,
        class_day VARCHAR(255) NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        class_id VARCHAR(255) NOT NULL
    )";
    if ($connection->query($create_table_query) === TRUE) {
        $result_timetable = mysqli_query($connection, $query_timetable);
    } else {
        die("Error creating table: " . $connection->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Table</title>
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
    </style>
</head>

<body>
    <div class="glass-container">
        <caption>
            <h3>Time Table</h3>
            <h5>Subject: <?php echo $_SESSION['subject_name']; ?></h5>
        </caption>
        <div class="timetable">
            <table border="1">
                <tr>
                    <th></th>
                    <?php
                    // Define an array to map numerical representation of days to their names
                    $daysOfWeek = array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday');
                    // Get unique class days from the timetable
                    $unique_days_query = "SELECT DISTINCT class_day FROM $table_name WHERE registration_id='{$_SESSION['registration_id']}' ORDER BY class_day";
                    $unique_days_result = mysqli_query($connection, $unique_days_query);
                    while ($day_row = mysqli_fetch_assoc($unique_days_result)) {
                        $dayOfWeek = date('N', strtotime($day_row['class_day'])); // Get the numerical representation of the day
                        echo "<th>{$daysOfWeek[$dayOfWeek]}</th>"; // Display the day name
                    }
                    ?>
                </tr>
                <?php
                // Get unique class times from the timetable
                $unique_times_query = "SELECT DISTINCT start_time, end_time FROM $table_name WHERE registration_id='{$_SESSION['registration_id']}' ORDER BY start_time";
                $unique_times_result = mysqli_query($connection, $unique_times_query);
                while ($time_row = mysqli_fetch_assoc($unique_times_result)) {
                    echo "<tr>";
                    echo "<th>{$time_row['start_time']} - {$time_row['end_time']}</th>"; // Display the time slot
                    // Get data for each day and time slot
                    mysqli_data_seek($unique_days_result, 0);
                    while ($day_row = mysqli_fetch_assoc($unique_days_result)) {
                        $query_timetable = "SELECT class_id FROM $table_name WHERE registration_id='{$_SESSION['registration_id']}' AND class_day='{$day_row['class_day']}' AND start_time='{$time_row['start_time']}'";
                        $result_timetable = mysqli_query($connection, $query_timetable);
                        $data = '';
                        while ($row = mysqli_fetch_assoc($result_timetable)) {
                            $data .= "{$row['class_id']}<br>";
                        }
                        echo "<td>{$data}</td>";
                    }
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>
</body>

</html>
