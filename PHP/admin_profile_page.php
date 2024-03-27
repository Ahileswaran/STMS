<?php
session_start(); // Start the session
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>School Teacher Management System</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
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
                <a class="active button" href="../index.html">Home</a>
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

            <div class="content">
                <!-- main content goes here -->
            </div>


        </div>

        <!-- Profile container with glass effect -->
        <div class="glass-container background-glass">
            <div class="profile-pic-container">
                <img src="../imgs/profile-pic.png" alt="Profile Picture">
            </div>
            <h4>First Name: <?php echo $_SESSION['first_name']; ?></h4><br>
            <h4>Last Name: <?php echo $_SESSION['last_name']; ?></h4><br>
            <h4>Address: <?php echo $_SESSION['teacher_address']; ?></h4><br>
            <h4>Age: <?php echo $_SESSION['age']; ?></h4><br>
            <h4>Sex: <?php echo $_SESSION['sex']; ?></h4><br>
            <h4>Marital Status: <?php echo $_SESSION['marital_status']; ?></h4><br>
            <h4>Registration Id: <?php echo $_SESSION['registration_id']; ?></h4><br>
            <h4>Subject: <?php echo $_SESSION['subject_name']; ?></h4><br>
            <h4>User Name: <?php echo $_SESSION['username']; ?></h4><br>
            <h4>E-mail: <?php echo $_SESSION['email']; ?></h4><br>
        </div>
        
               <!-- Form container with glass effect -->
               <div class="glass-container background-glass">
          <div class="admin-page">
            <h1>Administration</h1>
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
