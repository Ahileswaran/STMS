<?php
session_start(); // Start the session

// Define the error logging function or include it
function log_error($error_message)
{
    $log_file = __DIR__ . '/error_log.txt'; // Adjust path as needed
    $current_time = date('Y-m-d H:i:s');
    $log_message = "[{$current_time}] Error: {$error_message}\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);
}

$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

try {
    // Establish database connection
    $connection = new mysqli($server, $username, $password, $database);
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }

    // Fetch profile picture from database
    $session_username = $_SESSION['username'];
    $sql = "SELECT profile_pic FROM profile_picture WHERE username = ?";
    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare statement failed: " . $connection->error);
    }

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

    $stmt->close();
    $connection->close();
} catch (Exception $e) {
    log_error($e->getMessage());
    header("Location: PHP/pages/404.php?error=" . urlencode($e->getMessage()));
    exit();
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
            <a class="active button" href="PHP/pages/registering_page.php">Register</a>
            <a class="active button" href="PHP/pages/login_page.php">Login</a>
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
            <form action="./PHP/pages/search.php" method="GET">
                <input type="text" name="search" placeholder="Search..." required>
                <button type="submit">Search</button>
            </form>
        </div>


        <?php if (isset($_SESSION['username'])) : ?>
            <div class="login_detail">
                <div class='dropdown_details'>
                    <img src='<?php echo $profile_pic_src; ?>' alt='Profile Picture' class='profile-pic'>
                    <div class='dropdown-content'>
                        <p class='welcome-message'>Welcome, <?php echo $_SESSION['username']; ?></p>
                        <a href='PHP/pages/profile_redirect.php'>Profile</a>
                        <a href='PHP/logout.php'>Logout</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <div class="slider">
        <div class="slider-item active" id="image_1">
            <img class="animated bounceInRight slider-img" src="images/slider/pic1.jpg">
            <div class="row">
                <h3 class="animated slideInLeft slider-caption mb-2">Events</h3>
            </div>
        </div>
        <div class="slider-item">
            <img class="animated bounceInRight slider-img" src="images/slider/pic2.jpg">
            <div class="row">
                <h3 class="animated slideInLeft slider-caption mb-2">Meetings</h3>
            </div>
        </div>
        <div class="slider-item">
            <img class="animated bounceInRight slider-img" src="images/slider/pic3.jpg">
            <div class="row">
                <h3 class="animated slideInLeft slider-caption mb-2">Celebration</h3>
            </div>
        </div>
    </div>

    <div class="content">
        <!-- main content goes here -->

        <div class="master-table">

            <div class="worldclock">
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