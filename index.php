<?php
//session_start(); // Start the session

$username = "root"; 
$password = ""; 
$server = "localhost";  
$database = "stms_database"; 

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
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

        <!-- Master Time Table -->
        <div class="master-table">

        <p id="currentDateTime"></p> <br>
         <p id="classDay"></p>

         <script>
        // Generate a random number to use as a cache buster
        const cacheBuster = Math.random();

        // Fetch current date and time from the World Time API with cache-busting parameter
        fetch(`http://worldtimeapi.org/api/timezone/Asia/Colombo?cache=${cacheBuster}`)
            .then(response => response.json())
            .then(data => {
            const currentDateTime = new Date(data.datetime);
            const dayOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
            const classDay = dayOfWeek[currentDateTime.getDay()]; // Get the day of the week

            document.getElementById("currentDateTime").textContent = "Current Date and Time: " + currentDateTime.toLocaleString();
            document.getElementById("classDay").textContent = "Class Day: " + classDay;
             })
            .catch(error => {
            console.error('Error fetching data:', error);
            });
        </script>

<script>
    function generateTable() {
        // Get the selected day and time period
        var selectedDay = document.getElementById("daySelector").value;
        var selectedTimePeriod = document.getElementById("timePeriodSelector").value;

        // Send an AJAX request to generate_table.php with the selected day and time period
        $.ajax({
            url: "generate_table.php",
            method: "GET",
            data: { day: selectedDay, time_period: selectedTimePeriod },
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

    <!-- Display profile picture -->
    <img id="upload_pic" src="<?php echo $profile_pic_src; ?>" alt="Profile Picture">
    <img id="upload_pic"></img>

<div class="drop_menu_table">     
<select id="daySelector" name="Day" onchange="generateTable()">
    <option value="menu0" disabled selected>Select Day</option>
    <option value="monday">Monday</option>
    <option value="tuesday">Tuesday</option>
    <option value="wednesday">Wednesday</option>
    <option value="thursday">Thursday</option>
    <option value="friday">Friday</option>
</select>

<select id="timePeriodSelector" name="Time Period">
    <option value="menu0" disabled selected>Select Time Period</option>
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
<!-- Display profile picture -->
<img id="profilePic" src="display_propic.php?class_id=<?php echo $classId ?>&subject=<?php echo $subject ?>&day_of_week=<?php echo $dayOfWeek ?>" alt="Profile Picture">
    <div id="tableContainer">
        <!-- Table generated by PHP will be displayed here -->

       

        </div>
    </div>

<div id="tableContainer">
    <!-- Table generated by PHP will be displayed here -->
</div>

   <?php
   
        // Include the generated table
       //include 'generate_table.php';
    ?>


        </div>
        <div class="mini-gap"></div>

        <!-- Footer -->
        <footer class="footer">
            <p>&copy; School Teachers Management System 2024. All rights reserved. Designed by Dragons.</p>
        </footer>
    </div>
</body>

</html>
