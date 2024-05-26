<?php
session_start(); // Start the session

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
        .footer {
            position: fixed;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .cookie-settings {
            margin-top: 200px;
            margin-bottom: 20px;
            margin-left: 280px;
        }

        .cookie-settings label {
            display: block;
            margin-bottom: 10px;
        }

        .cookie-settings input {
            margin-right: 10px;
        }

        .cookie-settings button {
            display: block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .cookie-settings button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <header class="header">
        <img src="../../images/logo-STMS.jpg" alt="logo" class="logo-image">
        <nav>
            <a class="active button" href="../../index.php">Home</a>
            <a class="active button" href="../pages/registering_page.php">Register</a>
            <a class="active button" href="../pages/login_page.php">Login</a>
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

    <div class="container">
        <h1>Manage Cookies</h1>
        <div class="cookie-settings">
            <label>
                <input type="checkbox" id="acceptSessionCookies"> Accept Session Cookies
            </label>
            <label>
                <input type="checkbox" id="acceptAuthCookies"> Accept Authentication Cookies
            </label>
            <label>
                <input type="checkbox" id="acceptPreferenceCookies"> Accept Preference Cookies
            </label>
            <label>
                <input type="checkbox" id="acceptAnalyticsCookies"> Accept Analytics Cookies
            </label>
            <button id="saveCookieSettings">Save Settings</button>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const acceptSessionCookies = document.getElementById("acceptSessionCookies");
            const acceptAuthCookies = document.getElementById("acceptAuthCookies");
            const acceptPreferenceCookies = document.getElementById("acceptPreferenceCookies");
            const acceptAnalyticsCookies = document.getElementById("acceptAnalyticsCookies");
            const saveCookieSettings = document.getElementById("saveCookieSettings");

            // Load cookie settings from localStorage
            acceptSessionCookies.checked = localStorage.getItem("acceptSessionCookies") === "true";
            acceptAuthCookies.checked = localStorage.getItem("acceptAuthCookies") === "true";
            acceptPreferenceCookies.checked = localStorage.getItem("acceptPreferenceCookies") === "true";
            acceptAnalyticsCookies.checked = localStorage.getItem("acceptAnalyticsCookies") === "true";

            saveCookieSettings.addEventListener("click", function () {
                localStorage.setItem("acceptSessionCookies", acceptSessionCookies.checked);
                localStorage.setItem("acceptAuthCookies", acceptAuthCookies.checked);
                localStorage.setItem("acceptPreferenceCookies", acceptPreferenceCookies.checked);
                localStorage.setItem("acceptAnalyticsCookies", acceptAnalyticsCookies.checked);
                alert("Cookie settings saved!");
            });
        });
    </script>

    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <img src="../../images/logo-STMS.jpg" alt="Logo">
                <p>&copy; 2024 School Teachers Management System. All rights reserved.</p>
            </div>
            <div class="footer-links">
                <ul>
                    <li><a href="../pages/manage_cookies.php">About</a></li>
                    <li><a href="../pages/manage_cookies.php">Manage Cookies</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>

</html>