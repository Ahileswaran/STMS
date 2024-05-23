<?php
//session_start(); // Start the session
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
            <a class="active button" href="index.php">Home</a>
            <a class="active button" href="./php/pages/registering_page.php">Register</a>
            <a class="active button" href="./php/pages/login_page.php">Login</a>
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



    <div class="content">
        <!-- main content goes here -->

        <!-- Form container with glass effect -->
        <div class="glass-container background-glass">
            <div class="levae-letter-page">
                <h1>Generate Leave Letter</h1>
            </div>

            <div class="Wrire-letter">
                <h3><b>Write the Levae Letter Here</b></h3>
                <textarea id="longText" name="longText" rows="40" cols="100"></textarea><br><br>
                <button value="txt">Submit</button>
                <button value="txt">Edit</button>
            </div>

            <div class="genarate-letter">
                <h3><b>Genarate Leaeve Letter Here</b></h3>
                <label for="teacher_id">Enter your Teacher ID Here:</label>
                <input valu="txt" placeholder="Enter your teacher id..."><br><br>
                <label for="Address">Enter your Address Here:</label>
                <input valu="txt" placeholder="Enter your Address id..."><br><br>
                <label for="date">Enter The Date Here:</label>
                <input valu="txt" placeholder="Enter the date..."><br><br>
                <label for="reciver_address">Enter Reciver Address Here:</label>
                <input valu="txt" placeholder="Enter reciver address..."><br><br>
                <label for="headline">Enter Headline Here:</label>
                <input valu="txt" placeholder="Enter headline..."><br><br>
                <label for="reason">Enter your Reasons Here:</label>
                <textarea id="longText" name="longText" rows="" cols=""></textarea><br><br>
                <button value="txt">Preview</button>
                <button value="txt">Genarate</button>
                <button value="txt">Edit</button>
            </div>
        </div>
    </div>

   <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <img src="../../images/logo-STMS.jpg" alt="Logo">
                <p>&copy; 2024 School Teachers Management System. All rights reserved.</p>
            </div>
            <div class="footer-links">
                <ul>
                    <li><a href="#">Legal Stuff</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Security</a></li>
                    <li><a href="#">Website Accessibility</a></li>
                    <li><a href="#">Manage Cookies</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <script src="javaScript.js"></script>
</body>

</html>