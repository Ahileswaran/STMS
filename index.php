<?php
session_start(); // Start the session

// error logging function 
function log_error($error_message)
{
    $log_file = __DIR__ . '/error_log.txt'; 
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

    $profile_pic_src = 'images/profile-pic.png';

    if (isset($_SESSION['username'])) {
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
        }

        $stmt->close();
    }

    // Fetch slider images from database
    $slider_images = [];
    $sql = "SELECT caption, slider_pic FROM slider_picture";
    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare statement failed: " . $connection->error);
    }

    $stmt->execute();
    $stmt->bind_result($caption, $slider_pic_data);

    while ($stmt->fetch()) {
        $slider_pic = base64_encode($slider_pic_data);
        $slider_images[] = [
            'caption' => $caption,
            'slider_pic' => 'data:image/jpeg;base64,' . $slider_pic
        ];
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
    <link rel="stylesheet" href="CSS/styles.css">
</head>

<body>
    <header class="header">
        <img src="images/logo-STMS.jpg" alt="logo" class="logo-image">
        <nav>
            <a class="active button" href="index.php">Home</a>
            <a class="active button" href="../STMS/PHP/profile_redirect.php">Dashboard</a>
            <a class="active button" href="PHP/pages/registering_page.php">Register</a>
            <a class="active button" href="PHP/pages/login_page.php">Login</a>
        </nav>
        <div class="drop_menu">
            <select name="menu" onchange="redirect(this)">
                <option value="" disabled selected>Downloads</option>
                <option value="https://nie.lk/seletguide">Teachers Guides</option>
                <option value="https://nie.lk/selesyll">Syllabi</option>
                <option value="https://nie.lk/showom">Other Materials</option>
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
                        <a href='./PHP/profile_redirect.php'>Profile</a>
                        <a href='./PHP/logout.php'>Logout</a>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <!-- Placeholder for maintaining layout -->
            <div class="login_detail_placeholder"></div>
        <?php endif; ?>

    </header>
    <div class="slider-container">
        <div class="slider">
            <?php foreach ($slider_images as $index => $image) : ?>
                <div class="slider-item <?php echo $index === 0 ? 'active' : ''; ?>" id="image_<?php echo $index + 1; ?>">
                    <img class="animated bounceInRight slider-img" src="<?php echo $image['slider_pic']; ?>">
                    <div class="row">
                        <h3 class="animated slideInLeft slider-caption mb-2"><?php echo $image['caption']; ?></h3>
                    </div>
                </div>
            <?php endforeach; ?>
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
                            const dayOfWeek = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                            const classDay = dayOfWeek[currentDateTime.getDay()]; // Get the day of the week
                            document.getElementById("currentDateTime").textContent = "Current Date and Time: " + currentDateTime.toLocaleString();
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
                    <button id="refreshButton" onclick="generateTable()">View Table</button>
                </div>
            </div>
            <script>
                function generateTable() {
                    // Get the selected day
                    var selectedDay = document.getElementById("daySelector").value;
                    // Send an AJAX request to generate_table.php with the selected day
                    $.ajax({
                        url: "./PHP/Master_Table/generate_table.php",
                        method: "GET",
                        data: {
                            day: selectedDay
                        },
                        success: function(response) {
                            // Display the generated table in the tableContainer
                            document.getElementById("tableContainer").innerHTML = response;
                            // Call filterTables to filter out duplicate tables
                            filterTables();
                        },
                        error: function(xhr, status, error) {
                            console.error("Error generating table:", error);
                        }
                    });
                }
                // Call generateTable function initially to load the default table
                generateTable();

                function filterTables() {
                    const tableContainer = document.getElementById('tableContainer');
                    const tables = tableContainer.getElementsByTagName('table');
                    const captions = {};

                    for (let i = 0; i < tables.length; i++) {
                        const caption = tables[i].getElementsByTagName('caption')[0];
                        if (caption) {
                            const captionText = caption.innerText;

                            if (captions[captionText]) {
                                // If the caption is already found, hide this table
                                tables[i].style.display = 'none';
                            } else {
                                // If the caption is not found, keep this table and mark the caption as found
                                captions[captionText] = true;
                                tables[i].style.display = '';
                            }
                        }
                    }
                }
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
                    <li><a href="PHP/pages/about_page.php">About</a></li>
                    <li><a href="PHP/pages/manage_cookies.php">Manage Cookies</a></li>
                </ul>
            </div>
        </div>
    </footer>
    <script src="JavaScripts/javaScript.js"></script>
    <script>
        function redirect(select) {
            var url = select.value;
            if (url) {
                window.open(url, '_blank');
            }
        }
    </script>
</body>

</html>