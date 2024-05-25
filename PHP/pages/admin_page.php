<?php
session_start(); // Start the session
//require_once 'php/stay_login.php';
//require_once 'profile_page.php';
//require_once 'admin_profile_page.php';

$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../../styles.css">
</head>

<body>
    <header class="header">
        <img src="../../images/logo-STMS.jpg" alt="logo" class="logo-image">
        <nav>
            <a class="active button" href="../../index.php">Home</a>
            <a class="active button" href="../../PHP/pages/registering_page.php">Register</a>
            <a class="active button" href="../../PHP/pages/login_page.php">Login</a>
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
                echo "<a href='php/profile_redirect.php'>Profile</a>";
                echo "<a href='php/logout.php'>Logout</a>";
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
                <li><a href="../Admin_Pages/profile.php" id="profile-link">Profile</a></li>
                <li><a href="../Admin_Pages/edit_teachers.php" id="edit-teachers-link">Manage Teachers</a></li>
                <li><a href="../Admin_Pages/delete_teachers.php" id="delete-teachers-link">Delete Teachers</a></li>
                <li><a href="../Admin_Pages/edit_timetable.php" id="edit-timetable-link">Mange Timetable</a></li>
                <li><a href="../Admin_Pages/edit_master_timetable.php" id="edit-master-timetable-link">Manage Master Timetable</a></li>
                <li><a href="../Admin_Pages/edit_slider_images.php" id="edit-slider-images-link">Edit Slider Images</a></li>
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
                loadPage('../Admin_Pages/profile.php');
            });


            document.getElementById('edit-teachers-link').addEventListener('click', function(event) {
                event.preventDefault();
                loadPage('../Admin_Pages/edit_teachers.php');
            });

            document.getElementById('delete-teachers-link').addEventListener('click', function(event) {
                event.preventDefault();
                loadPage('../Admin_Pages/delete_teachers.php');
            });

            document.getElementById('edit-timetable-link').addEventListener('click', function(event) {
                event.preventDefault();
                loadPage('../Admin_Pages/edit_timetable.php');
            });

            document.getElementById('edit-master-timetable-link').addEventListener('click', function(event) {
                event.preventDefault();
                loadPage('../Admin_Pages/edit_master_timetable.php');
            });

            document.getElementById('edit-slider-images-link').addEventListener('click', function(event) {
                event.preventDefault();
                loadPage('../Admin_Pages/edit_slider_images.php');
            });
        });
    </script>

</body>

</html>
