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
    <title>School Teacher Management System</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../JavaScripts/profile_pic.js"></script>
    <link rel="stylesheet" href="../styles.css">
    <script src="javaScript.js"></script>
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
                <a class="active button" href="../pages/registering_page.html">Register</a>
                <a class="active button" href="../pages/login_page.html">Login</a>
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
        echo "<a href='logout.php'>Logout</a>";
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
        
               <!-- Form container with glass effect -->
               <div class="glass-container background-glass">
          <div class="admin-page">
            <h1>Administration</h1>
          </div>
          <div class="edit-delete-teacher">
                <label for="input"><b>Teacher Time Table Management</b></label><br><br>
                <button onclick="window.location.href='../pages/teacher_time_table.html'">Add</button>
                <button value="delete">Remove</button>
            </div>

            <div class="edit-delete-teacher">
                <label for="input"><b>Teacher Syllabus Table Management</b></label><br><br>
                <button onclick="window.location.href='../pages/teacher_syllabus.html'">Add</button>
                <button value="delete">Remove</button>
            </div>

            <div class="edit-delete-teacher">
                <label for="input"><b>Search Teacher: </b></label>
                <input type="text" placeholder="Insert teacher id...">
                <button value="search">Search</button><br><br>
                <button value="Edit">Edit</button>
                <button value="delete">Delete</button>
            </div>

            <div class="edit-delete-teacher">
                <label for="input"><b>Search Teacher: </b></label>
                <input type="text" placeholder="Insert teacher id...">
                <button value="search">Search</button><br><br>
                <button value="Edit">Edit Time Table</button>
            </div>

            <div class="edit-delete-teacher">
                <label for="input"><b>Edit Master Time Table</b></label><br><br>
                <button value="Edit">Edit</button>
                <button value="delete">Delete</button>
            </div>

            <div class="edit-delete-teacher">
                <label for="input"><b>Edit Slider Images</b></label><br><br>
                <button value="Edit">Edit</button>
                <button value="delete">Delete</button>
            </div>
        </div>
    </div>

    

    <!-- Footer with rich text -->
    <footer class="footer">
        <p>&copy; School Teachers Management System 2024. All rights reserved. Designed by Dragons.</p>
    </footer>

</body>

</html>