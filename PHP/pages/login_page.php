<?php
session_start(); // Ensure the session is started

$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$login_form_disabled = false;

try {
    // Check if user is logged in
    if (isset($_SESSION['username'])) {
        $session_username = $_SESSION['username'];

        // Fetch profile picture from database
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

        require_once '../stay_login.php';
        $login_form_disabled = true;
    } else {
        throw new Exception("User not logged in.");
    }
} catch (Exception $e) {
    $profile_pic_src = 'path_to_default_image.jpg'; // Replace with the path to your default image
    $login_form_disabled = false;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        /* Additional CSS to ensure footer placement */
        body,
        html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
        }

        .disabled-field {
            background-color: #f0f0f0;
            cursor: not-allowed;
        }

        .message {
            color: red;
            /* Customize this color as needed */
            font-weight: bold;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <header class="header">
        <img src="../../images/logo-STMS.jpg" alt="logo" class="logo-image">
        <nav>
            <a class="active button" href="../../index.php">Home</a>
            <a class="active button" href="./registering_page.php">Register</a>
            <a class="active button" href="#">Login</a>
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
            <form action="search.php" method="GET">
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
                        <a href='../profile_redirect.php'>Profile</a>
                        <a href='../logout.php'>Logout</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <div class="content">
        <!-- main content goes here -->

        <div class="form-container">
            <div class="teacher-profile">
                <?php if ($login_form_disabled) : ?>
                    <div class="message">Please log out before attempting to log in again.</div>
                <?php endif; ?>
                <form class="register-form" action="../../PHP/login.php" method="post" id="loginForm">
                    <label for="user-name">User Name/Mail: </label>
                    <input id="user-name" name="user-name" type="text" required placeholder="User Name or Mail id..." autocomplete="username" <?php if ($login_form_disabled) echo 'disabled class="disabled-field"'; ?>><br><br>

                    <label for="password">Password: </label>
                    <input id="password" name="password" type="password" required placeholder="Password..." autocomplete="current-password" <?php if ($login_form_disabled) echo 'disabled class="disabled-field"'; ?>><br><br>

                    <button type="submit" value="enter" <?php if ($login_form_disabled) echo 'disabled class="disabled-field"'; ?>>Login</button>

                    <a href="registering_page.php" class="login_link">Register</a><br>
                    <a href="reset-password.php" class="login_link">Forgot password</a>
                </form>
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
</body>

</html>