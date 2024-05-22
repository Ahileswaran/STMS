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
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../../styles.css">
    <script src="../../javaScript.js"></script>
</head>

<body>
    <!-- Main container with glass effect -->
    <div class="glass-box-container">
        <!-- Banner glass container -->
        <div class="glass-container title-container">
            <img src="../../imgs/logo-STMS.png" alt="Banner" class="banner-image-full">
        </div>

        <!-- Banner image taking up the entire screen -->
        <img src="../../imgs/banner.png" alt="Banner" class="banner-image-full">

        <!-- Mini gap between the body and the second glass container -->
        <div class="mini-gap"></div>

        <!-- Body glass container with the navigation bar -->
        <div class="glass-container nav-container">
            <!-- Container for navigation -->
            <nav>
                <a class="active button" href="../../index.php">Home</a>
                <a class="active button" href="#">Register</a>
                <a class="active button" href="./login_page.php">Login</a>
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


            <div class="content">
                <!-- main content goes here -->
            </div>

        </div>

        <!-- Form container with glass effect -->
        <div class="glass-container background-glass">
            <div class="teacher-profile">
                <form class="register-form" action="../PHP/user_data.php" method="post">

                    <label for="first_name">First Name: </label>
                    <input id="first_name" name="first_name" type="text"><br><br>

                    <label for="last_name">Last Name: </label>
                    <input id="last_name" name="last_name" type="text"><br><br>
    

                    <label for="address">Address: </label>
                    <input id="address" name="address" type="text"><br><br>

                    <label for="age">Age: </label>
                    <input id="age" name="age" type="text"><br><br>

                    <label for="sex">Sex: </label>
                    <div class="radio-buttons">
                        <input type="radio" id="male" name="sex" value="Male"> <label for="male">Male</label>
                        <input type="radio" id="female" name="sex" value="Female"> <label for="female">Female</label>
                    </div><br><br>

                    <label for="marital_status">Marital Status: </label>
                    <input id="marital_status" name="marital_status" type="text"><br><br>

                    <label for="teacher_id">Registration Number: </label>
                    <input id="teacher_id" name="teacher_id" type="text"><br><br>

                    <label for="subject">Subject: </label>
                    <input id="subject" name="subject" type="text"><br><br>

                    <label for="username">User Name: </label>
                    <input id="username" name="username" type="text"><br><br>
                    
                     <div id="username-suggestions"></div>

                    <label for="mail_id">Mail Address: </label>
                    <input id="mail_id" name="mail_id" type="text"><br><br>

                    <label for="password">Password: </label>
                    <input id="password" name="password" type="password"><br><br>

                    <button type="submit" value="submit">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer with rich text -->
    <footer class="footer">
        <p>&copy; School Teachers Management System 2024. All rights reserved. Designed by Dragons.</p>
    </footer>

</body>

</html>
