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
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header class="header">
        <img src="images/logo-STMS.jpg" alt="logo" class="logo-image">
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
                 echo "<a class='active button' href='../pages/login_page.php'>Login</a>";
            }
         ?>
        </div>

    </header>

    
    <div class="carousel">
        <div class="carousel-item active" id="image_1">
            <img class="animated bounceInRight carousel-img" src="../STMS/images/carousel/pic1.jpg">
            <div class="row">
                <h3 class="animated slideInLeft carousel-title mb-2">Events</h3>
            </div>
        </div>
        <div class="carousel-item">
            <img class="animated bounceInRight carousel-img" src="../STMS/images/carousel/pic2.jpg">
            <div class="row">
                <h3 class="animated slideInLeft carousel-title mb-2">Meetings</h3>
            </div>
        </div>
        <div class="carousel-item">
            <img class="animated bounceInRight carousel-img" src="../STMS/images/carousel/pic3.jpg">
            <div class="row">
                <h3 class="animated slideInLeft carousel-title mb-2">Celebration</h3>
            </div>
        </div>
    </div>



    <div class="content">
        <!-- main content goes here -->


        <div class="master-table">
            <p id="currentDateTime"></p>
            <p id="classDay"></p>

            <script>
            // Generate a random number to use as a cache buster
            const cacheBuster = Math.random();

            // Fetch current date and time from the World Time API with cache-busting parameter
            fetch(`http://worldtimeapi.org/api/timezone/Asia/Colombo?cache=${cacheBuster}`)
                .then(response => response.json())
                .then(data => {
                    const currentDateTime = new Date(data.datetime);
                    const dayOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday",
                        "Saturday"
                    ];
                    const classDay = dayOfWeek[currentDateTime.getDay()]; // Get the day of the week

                    document.getElementById("currentDateTime").textContent = "Current Date and Time: " +
                        currentDateTime.toLocaleString();
                    document.getElementById("classDay").textContent = "Class Day: " + classDay;
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
            </script>

            <div class="drop_menu_table">
                <select id="daySelector" name="Day" onchange="generateTable()">

                    <option value="monday">Monday</option>
                    <option value="tuesday">Tuesday</option>
                    <option value="wednesday">Wednesday</option>
                    <option value="thursday">Thursday</option>
                    <option value="friday">Friday</option>
                </select>

                <select id="timePeriodSelector" name="Time Period">

                    <option value="full">Full Time Period</option>
                    <option value="time_7">07:50:00 - 08:30:00</option>
                    <option value="time_8">08:30:00 - 09:10:00</option>
                    <option value="time_9">09:10:00 - 09:50:00</option>
                    <option value="time_9_50">09:50:00 - 10:30:00</option>
                    <option value="time_10">10:50:00 - 11:30:00</option>
                    <option value="time_11">11:30:00 - 12:10:00</option>
                    <option value="time_12">12:10:00 - 12:50:00</option>
                    <option value="time_1">12:50:00 - 13:30:00</option>
                </select>

                <button id="refreshButton" onclick="generateTable()">View Table</button>

            </div>


            <script>
            function generateTable() {
                // Get the selected day and time period
                var selectedDay = document.getElementById("daySelector").value;
                var selectedTimePeriod = document.getElementById("timePeriodSelector").value;

                // Send an AJAX request to generate_table.php with the selected day and time period
                $.ajax({
                    url: "generate_table.php",
                    method: "GET",
                    data: {
                        day: selectedDay,
                        time_period: selectedTimePeriod
                    },
                    success: function(response) {
                        // Display the generated table in the tableContainer
                        document.getElementById("tableContainer").innerHTML = response;
                    },
                    error: function(xhr, status, error) {
                        console.error("Error generating table:", error);
                    }
                });
            }

            // Call generateTable function initially to load the default table
            generateTable();
            </script>

            <div id="tableContainer">
                <!-- Table generated by PHP will be displayed here -->
            </div>

        </div>

    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-social">
                <ul>
                    <li><a href="#"><img src="imgs/social-facebook.png" alt="Facebook"></a></li>
                    <li><a href="#"><img src="imgs/social-instagram.png" alt="Instagram"></a></li>
                    <li><a href="#"><img src="imgs/social-youtube.png" alt="YouTube"></a></li>
                    <li><a href="#"><img src="imgs/social-twitter.png" alt="Twitter"></a></li>
                    <li><a href="#"><img src="imgs/social-linkedin.png" alt="LinkedIn"></a></li>
                    <li><a href="#"><img src="imgs/social-medium.png" alt="Medium"></a></li>
                    <li><a href="#"><img src="imgs/social-tiktok.png" alt="TikTok"></a></li>
                </ul>
            </div>
            <div class="footer-logo">
                <img src="images/logo-STMS.jpg" alt="Logo">
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