<?php
session_start(); // Start the session

// Define the error logging function or include it
function log_error($error_message) {
    $log_file = __DIR__ . '/../../error_log.txt'; // Adjust path as needed
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
    header("Location: 404.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        /* Your CSS styles */
    </style>
</head>

<body>
    <header class="header">
        <img src="../../images/logo-STMS.jpg" alt="logo" class="logo-image">
        <nav>
            <a class="active button" href="index.php">Home</a>
            <a class="active button" href="registering_page.php">Register</a>
            <a class="active button" href="login_page.php">Login</a>
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
            <input type="text" name="search" placeholder="Search...">
            <button type="submit">Search</button>
        </div>

        <?php if (isset($_SESSION['username'])) : ?>
            <div class="login_detail">
                <div class='dropdown_details'>
                    <img src='<?php echo $profile_pic_src; ?>' alt='Profile Picture' class='profile-pic'>
                    <div class='dropdown-content'>
                        <p class='welcome-message'>Welcome, <?php echo $_SESSION['username']; ?></p>
                        <a href='login_page.php'>Profile</a>
                        <a href='../logout.php'>Logout</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <div class="content">
        <!-- main content goes here -->
        <p>Content for the page...</p>
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

    <script>
        function redirect(menu) {
            const value = menu.value;
            let url = '';
            switch (value) {
                case 'teachers_guide':
                    url = 'path_to_teachers_guide';
                    break;
                case 'syllabi':
                    url = 'path_to_syllabi';
                    break;
                case 'resource_page':
                    url = 'path_to_resource_page';
                    break;
                default:
                    break;
            }
            if (url) {
                window.location.href = url;
            }
        }
    </script>
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
