<?php
session_start(); // Start the session

// If session variables are set, unset them
if(isset($_SESSION['username'])) {
    unset($_SESSION['username']);
}

// Destroy the session
session_destroy();

// Redirect to login page or any other appropriate destination
header("Location: ../pages/login_page.html");
exit();
?>