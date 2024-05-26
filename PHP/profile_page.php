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
        echo "Table $table_name created successfully";
        $result_timetable = mysqli_query($connection, $query_timetable);
    } else {
        die("Error creating table: " . $connection->error);
    }
}

// Fetch profile picture from database
$session_username = $_SESSION['username'];
$sql = "SELECT profile_pic FROM profile_picture WHERE username = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $session_username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
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

try {
    $syllabus_query = "SELECT * FROM $syllabus_table_name WHERE registration_id = ?";
    $syllabus_stmt = $connection->prepare($syllabus_query);
    $syllabus_stmt->bind_param("s", $_SESSION['registration_id']);
    $syllabus_stmt->execute();
    $syllabus_result = $syllabus_stmt->get_result();
} catch (mysqli_sql_exception $e) {
    // Table does not exist, create it
    $create_syllabus_table_query = "CREATE TABLE $syllabus_table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        registration_id VARCHAR(255) NOT NULL,
        week_id INT NOT NULL,
        assign_date DATE NOT NULL,
        conduct_date DATE NOT NULL,
        start_time TIME NOT NULL,
        lesson_time TIME NOT NULL,
        mastery VARCHAR(255) NOT NULL,
        section_number INT NOT NULL,
        course_content TEXT NOT NULL,
        teaching_date DATE NOT NULL,
        note TEXT
    )";
    if ($connection->query($create_syllabus_table_query) === TRUE) {
        echo "Table $syllabus_table_name created successfully";
        $syllabus_stmt = $connection->prepare($syllabus_query);
        $syllabus_stmt->bind_param("s", $_SESSION['registration_id']);
        $syllabus_stmt->execute();
        $syllabus_result = $syllabus_stmt->get_result();
    } else {
        $syllabus_result = null; // Proceed without the syllabus table
    }
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
    <header class="header">
        <img src="../images/logo-STMS.jpg" alt="logo" class="logo-image">
        <nav>
            <a class="active button" href="../index.php">Home</a>
            <a class="active button" href="./pages/registering_page.php">Register</a>
            <a class="active button" href="./pages/login_page.php">Login</a>
        </nav>
        <div class="drop_menu">
            <select name="menu" onchange="redirect(this)">
                <option value="menu0" disabled selected>Downloads</option>
                <option value="teachers_guide">Teachers Guides</option>
                <option value="syllabi">Syllabi</option>
                <option value="resource_page">Resource Books</option>
            </select>
        </div>
        <div class="Search_field">
            <input type="text" name="search" placeholder="Search...">
            <button type="submit">Search</button>
        </div>

        <div class="login_detail">
            <?php
            // Check if user is logged in
            if (isset($_SESSION['username'])) {
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
                echo "<a class='active button' href='../pages/login_page.php'>Login</a>";
            }
            ?>
        </div>
    </header>

    <div class="admin-dashboard">
        <!-- Admin Dashboard Navigation -->
        <nav class="admin-nav">
            <ul>
                <li><a href="../../STMS/PHP/Teacher_Pages/profile_view.php" id="profile-link">Profile</a></li>
                <li><a href="../../STMS/PHP/Teacher_Pages/teacher_time_table.php" id="time-table-link">Time Table</a></li>
                <li><a href="../../STMS/PHP/Teacher_Pages/teacher_syllabus_table.php" id="view-syllabus-link">View Syllabus</a></li>
                <li><a href="#" id="leave-letter-link">Create Leave Letter</a></li>
                <li><a href="#" id="settings-link">Settings</a></li>
            </ul>
        </nav>
    </div>

    <div class="main-content" id="main-content">
        <!-- Place the main content here -->
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function loadPage(url) {
                fetch(url)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('main-content').innerHTML = html;
                    })
                    .catch(error => {
                        console.error('Error loading page:', error);
                    });
            }

            document.getElementById('profile-link').addEventListener('click', function(event) {
                event.preventDefault();
                loadPage('../../STMS/PHP/Teacher_Pages/profile_view.php');
            });


            document.getElementById('time-table-link').addEventListener('click', function(event) {
                event.preventDefault();
                loadPage('../../STMS/PHP/Teacher_Pages/teacher_time_table.php');
            });

            document.getElementById('view-syllabus-link').addEventListener('click', function(event) {
                event.preventDefault();
                loadPage('../../STMS/PHP/Teacher_Pages/teacher_syllabus_table.php');
            });

            document.getElementById('leave-letter-link').addEventListener('click', function(event) {
                event.preventDefault();
                loadPage('#');
            });

            document.getElementById('settings-link').addEventListener('click', function(event) {
                event.preventDefault();
                loadPage('#');
            });
        });
    </script>


    <script src="javaScript.js"></script>
</body>

</html>
