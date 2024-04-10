<?php
session_start(); // Start the session
require_once 'admin_profile_page.php';
require_once 'profile_page.php';
require_once 'login.php'; // Include login.php to initialize the session

// Debugging: Print session variables to check if they are set correctly
echo "Session Username: " . $_SESSION['username'] . "<br>";

$username = "root"; 
$password = ""; 
$server = "localhost";  
$database = "stms_database"; 

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if the username is set in the session
if (isset($_SESSION['username'])) {
    $session_username = $_SESSION['username'];

    // Debugging: Print the session username to ensure it is set correctly
    echo "Session Username: " . $session_username . "<br>";

    // Check if a file was uploaded without errors
    if (isset($_FILES["profile_pic"]) && $_FILES["profile_pic"]["error"] == 0) {
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        $file_extension = pathinfo($_FILES["profile_pic"]["name"], PATHINFO_EXTENSION);

        // Debugging: Print file information for debugging
        echo "File name: " . $_FILES["profile_pic"]["name"] . "<br>";
        echo "File extension: " . $file_extension . "<br>";

        // Check if the file type is allowed
        if (in_array($file_extension, $allowed_types)) {
            // Read the uploaded file content
            $profile_pic_data = file_get_contents($_FILES["profile_pic"]["tmp_name"]);

            // Debugging: Print profile picture data length for debugging
            echo "Profile picture data length: " . strlen($profile_pic_data) . "<br>";

            // Check if the session username exists in the login table
            $stmt = $connection->prepare("SELECT username, user_role FROM login WHERE username = ?");
            $stmt->bind_param("s", $session_username);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                // Username exists in the login table, proceed to update profile picture
                $row = $result->fetch_assoc();
                $user_role = $row['user_role'];
                $stmt->close();

                // Prepare and execute SQL to update the user's profile picture in the profile_picture table
                $sql = "INSERT INTO profile_picture (username, profile_pic) VALUES (?, ?)";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("ss", $session_username, $profile_pic_data);
                $stmt->execute();

                // Close statement
                $stmt->close();

                // Determine the profile page based on the user's role
                $profile_page = ($user_role === 'principal') ? "admin_profile_page.php" : "profile_page.php";

                // Redirect the user to their profile page
                header("Location: $profile_page");
                exit();
            } else {
                // Username not found in the login table
                echo "Invalid username.";
            }
        } else {
            // File type not allowed
            echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    } else {
        // No file uploaded or error occurred
        echo "Error uploading file.";
    }
} else {
    // Username not found in session
    echo "Username not found in session.";
}
?>
