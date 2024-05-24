<?php
//session_start(); // Start the session
//require_once 'login.php';
//session_start(); // Start the session to access session variables
require_once 'stay_login.php';

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

$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
    <header class="header">
        <img src="../images/logo-STMS.jpg" alt="logo" class="logo-image">
        <nav>
            <a class="active button" href="../index.php">Home</a>
            <a class="active button" href="../pages/PHP/registering_page.php">Register</a>
            <a class="active button" href="../PHP/pages/login_page.php">Login</a>
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

    <div class="content">
        <!-- main content goes here -->
    </div>


    </div>

    <!-- Profile container with glass effect -->
    <div class="glass-container background-glass">
        <div class="profile-pic-container">
            <!-- Display profile picture -->
            <img id="upload_pic" src="<?php echo $profile_pic_src; ?>" alt="Profile Picture">
            <img id="upload_pic"></img>
        </div>

        <h4>First Name: <?php echo $_SESSION['first_name']; ?></h4><br>
        <h4>Last Name: <?php echo $_SESSION['last_name']; ?></h4><br>
        <h4>Address: <?php echo $_SESSION['user_address']; ?></h4><br>
        <h4>Age: <?php echo $_SESSION['age']; ?></h4><br>
        <h4>Sex: <?php echo $_SESSION['sex']; ?></h4><br>
        <h4>Marital Status: <?php echo $_SESSION['marital_status']; ?></h4><br>
        <h4>Registration Id: <?php echo $_SESSION['registration_id']; ?></h4><br>
        <h4>Subject: <?php echo $_SESSION['subject_name']; ?></h4><br>
        <h4>User Name: <?php echo $_SESSION['username']; ?></h4><br>
        <h4>E-mail: <?php echo $_SESSION['email']; ?></h4><br>
        <!-- <h4>Uer Role: <?php echo $_SESSION['user_role']; ?></h4><br> -->
    </div>

    <form action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
        <div class="add-profile-pic">
            <label for="add_pic">Add Profile Picture:</label>
            <button type="button" id="add_pic">Add</button>
            <input type="file" id="file_input" name="profile_pic" style="display: none;">
        </div>
    </form>

   
    </div>
    
    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <img src="../images/logo-STMS.jpg" alt="Logo">
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

    <script src="../javaScript.js"></script>
</body>

</html>