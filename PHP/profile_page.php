<?php

 require_once 'stay_login.php';
 //require_once 'profile_page.php';
 //require_once 'admin_profile_page.php';
//require_once 'login.php';

// session_start(); // Start the session to access session variables

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

$query_timetable = "SELECT * FROM $table_name WHERE registration_id='{$_SESSION['registration_id']}'";
$result_timetable = mysqli_query($connection, $query_timetable);


// Fetch profile picture from database
$session_username = $_SESSION['username'];
$sql = "SELECT profile_pic FROM profile_picture WHERE username = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $session_username);
$stmt->execute();
$stmt->store_result();

if($stmt->num_rows > 0) {
    // Profile picture found, display it
    $stmt->bind_result($profile_pic_data);
    $stmt->fetch();
    $profile_pic = base64_encode($profile_pic_data);
    $profile_pic_src = 'data:image/jpeg;base64,' . $profile_pic;
} else {
    // Profile picture not found, use a default image
    $profile_pic_src = 'path_to_default_image.jpg'; // Replace with the path to your default image
}

// Syllabus table
$teacher_username = $_SESSION['username']; 
$syllabus_table_name = "teacher_syllabus_table_" . $teacher_username;

$syllabus_query = "SELECT * FROM $syllabus_table_name WHERE registration_id = ?";
$syllabus_stmt = $connection->prepare($syllabus_query);
$syllabus_stmt->bind_param("s", $_SESSION['registration_id']);
$syllabus_stmt->execute();
$syllabus_result = $syllabus_stmt->get_result();

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
    

<head>
    <title>School Teacher Management System</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../JavaScripts/profile_pic.js"></script>
    <link rel="stylesheet" href="../styles.css">
    <script src="../javaScript.js"></script>
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
                <a class="active button" href="../index.php">Home</a>
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

            

<div class="login_detail">
    <?php
    // Check if user is logged in
    if(isset($_SESSION['username'])) {
        // If logged in, display the profile picture and username
        echo "<div class='dropdown_details'>";
        echo "<img src='$profile_pic_src' alt='Profile Picture' class='profile-pic'>";
        echo "<div class='dropdown-content'>";
        echo "<p class='welcome-message'>Welcome, " . $_SESSION['username'] . "</p>";
        echo "<a href='logout.php'>Logout</a>";
        echo "</div>";
        echo "</div>";
    } else {
        // If not logged in, display login option
        echo "<a class='active button' href='../pages/login_page.html'>Login</a>";
    }
    ?>
</div>
      


        </div>

        <!-- Profile container with glass effect -->
        <div class="glass-container background-glass">
            <div class="profile-pic-container">
                    <!-- Display profile picture -->
                <img id="upload_pic" src="<?php echo $profile_pic_src; ?>" alt="Profile Picture">
                 <img id="upload_pic"></img>
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
            <!-- <h4>Uer Role: <?php echo $_SESSION['user_role']; ?></h4><br> -->

            <form action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
                <div class="add-profile-pic">
                    <label for="add_pic">Add Profile Picture:</label>
                    <button type="button" id="add_pic">Add</button>
                    <input type="file" id="file_input" name="profile_pic" style="display: none;">
                </div>
            </form>

            <!-- Time Table For Teacher  -->
            <div class="master-table">
                <table>
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

    <div class="syllabus_table">
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
        switch ($syllabus_result->num_rows) {
            case 0:
                echo "<tr><td colspan='10' class='error'>No syllabus details available for this user.</td></tr>";
                break;
            default:
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
                break;
        }
        ?>
    </table>
</div>


                </table>
            </div>

        </div>
    </div>

    <!-- Footer with rich text -->
    <footer class="footer">
        <p>&copy; School Teacher Management System 2024. All rights reserved. Designed by Dragons.</p>
    </footer>

</body>

</html>