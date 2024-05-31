<?php
//session_start(); // Start the session
require_once 'php/stay_login.php';
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
    <link rel="stylesheet" href="../../CSS/styles.css">
    <script src="../../JavaScripts/javaScript.js"></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }

        .glass-box-container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }

        .glass-container {
            padding: 20px;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .title-container {
            text-align: center;
        }

        .banner-image-full {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border-radius: 5px;
            margin: 0 10px;
            transition: background-color 0.3s ease-in-out;
        }

        nav a:hover {
            background-color: #0056b3;
        }

        .drop_menu select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin: 0 10px;
        }

        .Search_field input {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            width: 200px;
            margin: 0 10px;
        }

        .search_button button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out;
        }

        .search_button button:hover {
            background-color: #45a049;
        }

        .login_detail {
            display: flex;
            align-items: center;
        }

        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .dropdown_details {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 5px;
        }

        .dropdown_details:hover .dropdown-content {
            display: block;
        }

        .dropdown-content p, .dropdown-content a {
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .content {
            margin: 20px 0;
        }

        .background-glass {
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .substitute-page h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .edit-delete-teacher {
            margin-bottom: 20px;
        }

        .edit-delete-teacher label {
            display: block;
            margin-bottom: 5px;
        }

        .edit-delete-teacher input {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .edit-delete-teacher button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-right: 10px;
            transition: background-color 0.3s ease-in-out;
        }

        .edit-delete-teacher button:hover {
            background-color: #45a049;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #007BFF;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }

        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
            }

            .nav-container nav, .drop_menu, .Search_field, .search_button, .login_detail {
                margin-bottom: 10px;
            }

            .login_detail {
                flex-direction: column;
            }

            .search_button button {
                width: 100%;
            }
        }
    </style>
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

            <div class="content">
                <!-- main content goes here -->
            </div>


        </div>
        <!-- Form container with glass effect -->
        <div class="glass-container background-glass">
          <div class="substitute-page">
            <h1>Substitute Teacher</h1>
          </div>
          <div class="edit-delete-teacher">
            <label for="input"><b>Display Available Teachers: </b></label><br><br>
            <label for="input"><b>Teacher ID: </b></label>
            <input type="text" placeholder="Insert teacher id..."><br><br>
            <label for="input"><b>Class: </b></label>
            <input type="text" placeholder="Insert class..."><br><br>
            <button value="search">Display</button>
          </div>

          <div class="edit-delete-teacher">
            <label for="input"><b>Teacher Who Take Leave: </b></label>
            <input type="text" placeholder="Insert teacher id..."><br><br>
            <label for="input"><b>Assign New Teacher: </b></label>
            <input type="text" placeholder="Insert teacher id..."><br><br>
            <button value="Edit">Assign</button>
            <button value="delete">Change</button>
          </div>

        </div>
    </div>

    <!-- Footer with rich text -->
    <footer class="footer">
        <p>&copy; School Teachers Management System 2024. All rights reserved. Designed by Dragons.</p>
    </footer>

</body>

</html>
