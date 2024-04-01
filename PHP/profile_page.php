<?php
//session_start(); // Start the session
require_once 'login.php';
//session_start(); // Start the session to access session variables

$username = "root"; 
$password = ""; 
$server = "localhost";  
$database = "stms_database"; 

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Fetch timetable data
$query_timetable = "SELECT * FROM teacher_time_table_tntea1250 WHERE registration_id='{$_SESSION['registration_id']}'";
$result_timetable = mysqli_query($connection, $query_timetable);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>School Teacher Management System</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="../styles.css">
    <script src="../JavaScript.js"></script>
</head>

<body>
    <!-- Main container with glass effect -->
    <div class="glass-box-container">
        <!-- Banner glass container -->
        <div class="glass-container title-container">
            <img src="../imgs/logo-STMS.png" alt="Banner" class="banner-image-full">
        </div>

        <!-- Banner image taking up the entire screen -->
        <img src="../imgs/banner.png" alt="Banner" class="banner-image-full">

        <!-- Mini gap between the body and the second glass container -->
        <div class="mini-gap"></div>

        <!-- Body glass container with the navigation bar -->
        <div class="glass-container nav-container">
            <!-- Container for navigation -->
            <nav>
                <a class="active button" href="../index.html">Home</a>
                <a class="active button" href="../pages/registering_page.html">Register</a>
                <a class="active button" href="../pages/login_page.html">Login</a>
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

        <!-- Profile container with glass effect -->
        <div class="glass-container background-glass">
            <div class="profile-pic-container">
                <img src="../imgs/profile-pic.png" alt="Profile Picture">
            </div>
            <h4>First Name: <?php echo $_SESSION['first_name']; ?></h4><br>
            <h4>Last Name: <?php echo $_SESSION['last_name']; ?></h4><br>
            <h4>Address: <?php echo $_SESSION['user_address']; ?></h4><br>
            <h4>Age: <?php echo $_SESSION['age']; ?></h4><br>
            <h4>Sex: <?php echo $_SESSION['sex']; ?></h4><br>
            <h4>Marital Status: <?php echo $_SESSION['marital_status']; ?></h4><br>
            <h4>Registration Id: <?php echo $_SESSION['registration_id']; ?></h4><br>
            <h4>Subject: <?php echo $_SESSION['subject_name']; ?></h4><br>
            <h4>User Name: <?php echo $_SESSION['username']; ?></h4><br>
            <h4>E-mail: <?php echo $_SESSION['email']; ?></h4><br>

          <!-- Timetable section -->
          <!-- Time Table For Teacher  -->
        <div class="master-table">
            <table>
                <caption>
                    <h3>Time Table</h3>
                    <h5>Subject: Science</h5>
                </caption>
        <div class="timetable">
        <h3>Timetable:</h3>
        <table border="1">
        <tr>
            <th></th>
            <?php
            // Define an array to map numerical representation of days to their names
            $daysOfWeek = array(1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday');
            // Get unique class days from the timetable
            $unique_days_query = "SELECT DISTINCT class_day FROM teacher_time_table_tntea1250 WHERE registration_id='{$_SESSION['registration_id']}' ORDER BY class_day";
            $unique_days_result = mysqli_query($connection, $unique_days_query);
            while ($day_row = mysqli_fetch_assoc($unique_days_result)) {
                $dayOfWeek = date('N', strtotime($day_row['class_day'])); // Get the numerical representation of the day
                echo "<th>{$daysOfWeek[$dayOfWeek]}</th>"; // Display the day name
            }
            ?>
        </tr>
        <?php
        // Get unique class times from the timetable
        $unique_times_query = "SELECT DISTINCT start_time, end_time FROM teacher_time_table_tntea1250 WHERE registration_id='{$_SESSION['registration_id']}' ORDER BY start_time";
        $unique_times_result = mysqli_query($connection, $unique_times_query);
        while ($time_row = mysqli_fetch_assoc($unique_times_result)) {
            echo "<tr>";
            echo "<th>{$time_row['start_time']} - {$time_row['end_time']}</th>"; // Display the time slot
            // Get data for each day and time slot
            mysqli_data_seek($unique_days_result, 0);
            while ($day_row = mysqli_fetch_assoc($unique_days_result)) {
                $query_timetable = "SELECT class_id FROM teacher_time_table_tntea1250 WHERE registration_id='{$_SESSION['registration_id']}' AND class_day='{$day_row['class_day']}' AND start_time='{$time_row['start_time']}'";
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
    </div>

    <!-- Footer with rich text -->
    <footer class="footer">
        <p>&copy; School Teachers Management System 2024. All rights reserved. Designed by Dragons.</p>
    </footer>

</body>

</html>
