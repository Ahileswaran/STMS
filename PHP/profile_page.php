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
        term_id INT NOT NULL,
        class_id VARCHAR(255) NOT NULL,
        subject_id VARCHAR(255) NOT NULL,
        assign_date DATE NOT NULL,
        week_id INT NOT NULL,
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
    <style>
        .teacher-main-content {
            width: calc(100% - 237px);
            height: calc(100vh - 100px);
            border: none;
            position: relative;
            top: 100px;
            left: 237px;
        }

        iframe.main-content {
            width: calc(100% - 237px);
            height: calc(100vh - 100px);
            border: none;
            position: fixed;
            top: 100px;
            left: 237px;
        }

        .admin-nav ul {
            list-style-type: none;
            padding: 0;
        }

        .admin-nav li {
            margin: 10px 0;
        }

        .admin-nav a {
            display: block;
            padding: 10px;
            color: #000;
            text-decoration: none;
            border-radius: 5px;
        }

        .admin-nav a:hover {
            background-color: #ddd;
        }

        .admin-nav a.active {
            background-color: #4CAF50;
            color: white;
        }
    </style>
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
            <form action="../PHP/pages/search.php" method="GET">
                <input type="text" name="search" placeholder="Search..." required>
                <button type="submit">Search</button>
            </form>
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
                <li><a href="../../STMS/PHP/Teacher_Pages/profile.php" target="main-frame" id="profile-link">Profile</a></li>
                <li><a href="../../STMS/PHP/Teacher_Pages/submit_leave_form.php" target="main-frame" id="leave-Form-link">Submit Leave Form</a></li>
                <li><a href="../../STMS/PHP/Teacher_Pages/teacher_time_table.php" target="main-frame" id="time-table-link">Time Table</a></li>
                <li><a href="../../STMS/PHP/Teacher_Pages/teacher_syllabus_table.php" target="main-frame" id="view-syllabus-link">View Syllabus</a></li>
                <li><a href="../../STMS/PHP/Teacher_Pages/write_leave_letter.php" target="main-frame" id="leave-letter-link">Write Leave Letter</a></li>
                <li><a href="../../STMS/PHP/Teacher_Pages/assessment_planner.php" target="main-frame" id="assessment-planner-link">Assessment Planner</a></li>
            </ul>
        </nav>
    </div>

    <div class="teacher-main-content">


    </div>

    <iframe src="../PHP/Teacher_Pages/profile.php" class="main-content" name="main-frame" id="main-content"></iframe>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const links = document.querySelectorAll(".admin-nav a");

            links.forEach(link => {
                link.addEventListener("click", function() {
                    links.forEach(l => l.classList.remove("active"));
                    this.classList.add("active");
                });
            });
        });
    </script>
</body>

</html>
