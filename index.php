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

// Fetch current day and time
$currentDay = date("l"); // Get current day in full text (e.g., Monday)
$currentHour = date("H:i:s");

// Check if it's a weekday (Monday to Friday)
if ($currentDay != "Saturday" && $currentDay != "Sunday") {
    // Query class timetable for the current day and time
    $query = "SELECT * FROM class_time_table_Grade_6 WHERE $currentDay <= '$currentHour' AND '$currentHour' <= $currentDay";
    $result = $connection->query($query);

    // Check if there are any classes scheduled
    if ($result->num_rows > 0) {
        $hasClasses = true;
    } else {
        $hasClasses = false;
    }

    // If there are no classes scheduled, display a message
    if (!$hasClasses) {
        $noSchoolMessage = "No classes scheduled for today. Enjoy your day!";
    }
} else {
    // If it's Saturday or Sunday, set hasClasses to false
    $hasClasses = false;
    $noSchoolMessage = "It's a weekend! Enjoy your break!";
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
        

        <div class="date-time-info">
            <script>
                // Fetch current date and time from the World Time API
                fetch("http://worldtimeapi.org/api/timezone/Asia/Colombo")
                .then(response => response.json())
                .then(data => {
                    const currentDateTime = new Date(data.datetime);
                    const dayOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                    const classDay = dayOfWeek[currentDateTime.getDay()]; // Get the day of the week

                    document.getElementById("currentDateTime").textContent = "Date: " + currentDateTime.toLocaleDateString();
                    document.getElementById("currentTime").textContent = "Time: " + currentDateTime.toLocaleTimeString();
                    document.getElementById("classDay").textContent = "Day: " + classDay;
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
            </script>
        </div>

        <!-- Master Time Table -->
        <div class="master-table">

        <!-- Display message if no classes scheduled -->
        <?php if (!$hasClasses): ?>

        <div class="no-school-message">
            <p><?php echo $noSchoolMessage; ?></p>
        </div>

        <?php endif; ?>
         <div class = "display-time">
         <p id="currentDateTime"></p>
         <p id="currentTime"></p>
         <p id="classDay"></p>

        </div>

            <table>
                <caption>
                    <h3>Master Time Table</h3>
                </caption>
                <table>
                    <?php
                    if ($hasClasses) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<th>" . $row['class_id'] . "</th>";
                            echo "<td class='profile_circle'>" . $row['subject_id'] . "</td>";
                            // Fetch and display teacher's profile picture
                            $teacherUsername = ""; // Provide teacher's username here
                            $query_teacher = "SELECT profile_pic FROM profile_picture WHERE username = '$teacherUsername'";
                            $result_teacher = $connection->query($query_teacher);
                            if ($result_teacher->num_rows > 0) {
                                $row_teacher = $result_teacher->fetch_assoc();
                                // Display profile picture if available
                                echo '<td><img src="data:image/jpeg;base64,'.base64_encode($row_teacher['profile_pic']).'" alt="Profile Picture" class="profile-pic"></td>';
                            } else {
                                echo "<td>No Profile Picture</td>";
                            }
                            echo "</tr>";
                        }
                    }
                    ?>
                </table>
                
            </table>
        </div>
        <div class="mini-gap"></div>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; School Teachers Management System 2024. All rights reserved. Designed by Dragons.</p>
        </footer>
    </div>
</body>

</html>
