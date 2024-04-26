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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="styles.css">
    <script src="javaScript.js"></script>
</head>

<body>
    <!-- Main container with glass effect -->
    <div class="glass-box-container">
        <!-- Banner glass container -->
        <div class="glass-container title-container">
            <img src="imgs/logo-STMS.png" alt="Banner" class="banner-image-full">
        </div>

        <!-- Banner image taking up the entire screen -->
        <img src="imgs/banner.png" alt="Banner" class="banner-image-full">

        <!-- Mini gap between the body and the second glass container -->
        <div class="mini-gap"></div>

        <!-- Body glass container with the navigation bar -->
        <div class="glass-container nav-container">
            <!-- Container for navigation -->
            <nav>
                <a class="active button" href="index.html">Home</a>
                <a class="active button" href="./pages/registering_page.html">Register</a>
                <a class="active button" href="./pages/login_page.html">Login</a>
            </nav>

            <!-- Dropdown menu -->
            <div class="drop_menu">                
                <select name="menu" onchange="redirect(this)">
                    <option value="menu0" disabled selected>Downloads</option>
                    <option value="teachers_guide">Teachers Guides</option>
                    <option value="syllabi">Syllabi</option>
                    <option value="resource_page">Resource Books</option>
                </select>
            </div>

             <!-- Input Field -->
            <div class="Search_field">                               
                <input type="text" name="search" placeholder="Search...">
            </div>

            <!-- Search Button -->
            <div class="search_button">
                <button type="submit">Search</button>
            </div>

            <div class="content">
                <!-- main content goes here -->
            </div>


        </div>

      

        <!-- Slider container with gap -->
        <div class="glass-container slider-container">
            <!-- Slider images -->
            <div class="slider">
                <img src="imgs/slider_imgs/day_img.png" alt="Slider Image 1" class="slider-img">
                <img src="imgs/slider_imgs/hbd_img.png" alt="Slider Image 2" class="slider-img">
                <img src="imgs/slider_imgs/meet_img.png" alt="Slider Image 3" class="slider-img">
            </div>

            <!-- Navigation buttons -->
            <div class="slider-nav">
                <div class="nav-btn" data-index="0"></div>
                <div class="nav-btn" data-index="1"></div>
                <div class="nav-btn" data-index="2"></div>
            </div>
        </div>

        <!-- Master Time Table -->
        <div class="master-table">
        <div class="display_date">
        <p id="currentDateTime"></p> <br>
         <p id="classDay"></p>
        </div>
        <!-- Display area for class schedules -->
        <div id="classScheduleDisplay">
            <!-- Class schedules will be loaded here by AJAX -->
        </div>

        <!-- Script to fetch the current day and load schedules -->
        <script>
// Generate a random number to use as a cache buster
const cacheBuster = Math.random();

// Fetch current date and time from the World Time API with cache-busting parameter
fetch(`http://worldtimeapi.org/api/timezone/Asia/Colombo?cache=${cacheBuster}`)
    .then(response => response.json())
    .then(data => {
        const currentDateTime = new Date(data.datetime);
        const dayOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
        const classDay = dayOfWeek[currentDateTime.getDay()]; // Get the day of the week

        // Send the day of the week to the server-side PHP script
        $.ajax({
    type: "POST",
    url: "schedule_class.php", // Replace with the actual path to schedule_class.php
    data: { classDay: classDay }, // Send the day of the week as data
    success: function(response) {
        // Handle the response from schedule_class.php here
        console.log("Data received:", response);
    },
    error: function(xhr, status, error) {
        console.error('Error sending data to server:', error);
    }
});


        // Update the HTML content (optional)
        document.getElementById("currentDateTime").textContent = "Current Date and Time: " + currentDateTime.toLocaleString();
        document.getElementById("classDay").textContent = "Class Day: " + classDay;
    })
    .catch(error => {
        console.error('Error fetching data:', error);
    });
        </script>


<?php
// Define an array to hold the grades
$grades = ["Grade_6", "Grade_7", "Grade_8", "Grade_9", "Grade_10", "Grade_11", "Grade_12_Arts", "Grade_12_Science", "Grade_12_Maths"];

// Receive the day of the week sent by JavaScript
$currentDay = isset($_POST['classDay']) ? $_POST['classDay'] : "";

// Construct the SQL query
if (!empty($currentDay)) {
    // If $currentDay is not empty, include it in the query
    $sql = "SELECT class_id, start_time, end_time, `$currentDay` as subject FROM class_time_table WHERE `$currentDay` IS NOT NULL ORDER BY start_time";
} else {
    // If $currentDay is empty, select all days and handle it accordingly in your PHP logic
    $sql = "SELECT class_id, start_time, end_time FROM class_time_table";
}

// Execute the SQL query
$result = $connection->query($sql);

// Add a debugging statement to check if the query executed successfully
if (!$result) {
    echo json_encode(['error' => "Error executing query: " . $connection->error]);
    exit;
}

// Initialize an array to hold class schedules for each grade
$classSchedules = [];
foreach ($grades as $grade) {
    $classSchedules[$grade] = [];
}

// Populate class schedules based on fetched data
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $classSchedules[$row['class_id']][] = [
            'time' => $row["start_time"] . " - " . $row["end_time"],
            'class_id' => $row['class_id'],
            'subject' => isset($row['subject']) ? $row['subject'] : '' // Add subject to the array
        ];
    }
} else {
    // Add an error message if no data is found for the current day
    echo json_encode(['error' => "No data found for the current day."]);
    exit;
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

$timeSlots = [
  '07:50:00 - 08:30:00',
  '08:30:00 - 09:10:00',
  '09:10:00 - 09:50:00',
  '09:50:00 - 10:30:00',
  '10:50:00 - 11:30:00', 
  '11:30:00 - 12:10:00',
  '12:10:00 - 12:50:00',
  '12:50:00 - 13:30:00'
];

for ($i = 0; $i < $maxClasses; $i++) {
  echo "<tr>";
  // Display the time slot
  echo "<td>" . $timeSlots[$i] . "</td>";
  foreach ($grades_table1 as $grade) {
    echo "<td>";
    if (isset($classSchedules[$grade][$i]['subject']) && $classSchedules[$grade][$i]['subject'] !== '') {
      echo $classSchedules[$grade][$i]['subject'];
    } else {
      echo "No Schedule Class";
    }
    echo "</td>";
  }
  echo "</tr>";
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

for ($i = 0; $i < $maxClasses; $i++) {
  echo "<tr>";
  // Display the time slot
  echo "<td>" . $timeSlots[$i] . "</td>";
  foreach ($grades_table2 as $grade) {
    echo "<td>";
    if (isset($classSchedules[$grade][$i]['subject']) && $classSchedules[$grade][$i]['subject'] !== '') {
      echo $classSchedules[$grade][$i]['subject'];
    } else {
      echo "No Schedule Class";
    }
    echo "</td>";
  }
  echo "</tr>";
}
echo "</table>";
?>
        </div>
        <div class="mini-gap"></div>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; School Teachers Management System 2024. All rights reserved. Designed by Dragons.</p>
        </footer>
    </div>
</body>

</html>
