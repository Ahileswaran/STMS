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

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../styles.css">
    <script src="javaScript.js"></script>
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
                <a class="active button" href="/pages/registering_page.html">Register</a>
                <a class="active button" href="/pages/login_page.html">Login</a>

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


    <div class="login_detail">
    <?php
    // Check if user is logged in
    if(isset($_SESSION['username'])) {
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
        echo "<a class='active button' href='../pages/login_page.html'>Login</a>";
    }
    ?>
</div>



            <div id="google_translate_element">
                <select onchange="changeLanguage(this)">
                    <option value="en">English</option>
                    <option value="ta">தமிழ்</option>
                    <option value="si">සිංහල</option>
                </select>
            </div>

            <div class="content">
                <!-- main content goes here -->
            </div>


        </div>
        <!-- Form container with glass effect -->
        <div class="glass-container background-glass">
          <div class="about-page">
            <h1>About</h1>
            <h3>School Teachers Management System</h3>
            <p>
                <li>This Teachers Management System is developed and designed by Dragons.</li>
                <li>This system has important pages.</li>
                <li>This system is administered by the school principal.</li>
                <li>Teachers only have access to this system when they log in using the login option.</li>
                <li>Before teachers log in, they should create their profile page with the Register option given in the navigation bar.</li>
                <li>Teachers must create a timetable for their subject.</li>
                <li>The timetable for each teacher is automatically fetched and combined together and shown as a master table on the Home page for this system.</li>
                <li>Teachers can generate or write a leave letter if required; this option is given on their profile page.</li>
                <li>The principal assigns a new teacher to the class when another teacher takes leave.</li>
                <li>The principal has full admin control and can add or remove teachers and assign new classes when needed.</li>
                <li>The principal monitors all activity in the system.</li>
                <li>The system has a download option; teachers can download teacher's guides or syllabi for their subjects.</li>
                <li>The system has a syllabus page for three terms for each teacher for their subject.</li>
                <li>The syllabus page is interactive and shows the next event and reminder notifications.</li>
            </ul>
            </p>
          </div>
            
        </div>
    </div>

    <!-- Footer with rich text -->
    <footer class="footer">
        <p>&copy; School Teachers Management System 2024. All rights reserved. Designed by Dragons.</p>
    </footer>

</body>

</html>
