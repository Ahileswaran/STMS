<?php
session_start(); // Start the session

$username = "root"; 
$password = ""; 
$server = "localhost";  
$database = "stms_database"; 

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $input_username_or_email = mysqli_real_escape_string($connection, $_POST['user-name']);
    $input_password = mysqli_real_escape_string($connection, $_POST['password']);

    // Query to check if the username or email and password belong to a principal
    $query_principal = "SELECT * FROM principal WHERE (username='$input_username_or_email' OR email='$input_username_or_email') AND user_password='$input_password'";
    $result_principal = mysqli_query($connection, $query_principal);

    if (mysqli_num_rows($result_principal) == 1) {
        $user_row = mysqli_fetch_assoc($result_principal);
        // Store user information in session variables
        $_SESSION['first_name'] = $user_row['first_name'];
        $_SESSION['last_name'] = $user_row['last_name'];
        $_SESSION['user_address'] = $user_row['user_address'];
        $_SESSION['age'] = $user_row['age'];
        $_SESSION['sex'] = $user_row['sex'];
        $_SESSION['marital_status'] = $user_row['marital_status'];
        $_SESSION['registration_id'] = $user_row['registration_id'];
        $_SESSION['subject_name'] = $user_row['subject_name'];
        $_SESSION['username'] = $user_row['username'];
        $_SESSION['email'] = $user_row['email'];
        // Redirect to principal profile page
        header("Location: admin_profile_page.php");
        exit();
    } else {
        // Fetch user information from the teacher table if not found in principal
        $query_teacher = "SELECT * FROM teacher WHERE (username='$input_username_or_email' OR email='$input_username_or_email') AND user_password='$input_password'";
        $result_teacher = mysqli_query($connection, $query_teacher);
        if (mysqli_num_rows($result_teacher) == 1) {
            $user_row = mysqli_fetch_assoc($result_teacher);
            // Store user information in session variables
            $_SESSION['first_name'] = $user_row['first_name'];
            $_SESSION['last_name'] = $user_row['last_name'];
            $_SESSION['user_address'] = $user_row['user_address'];
            $_SESSION['age'] = $user_row['age'];
            $_SESSION['sex'] = $user_row['sex'];
            $_SESSION['marital_status'] = $user_row['marital_status'];
            $_SESSION['registration_id'] = $user_row['registration_id'];
            $_SESSION['subject_name'] = $user_row['subject_name'];
            $_SESSION['username'] = $user_row['username'];
            $_SESSION['email'] = $user_row['email'];
            // Redirect to teacher profile page
            header("Location: profile_page.php");
            exit();
        } else {
            // Incorrect username or password, display error message
            echo "<script>alert('Incorrect username or password. Please try again.');</script>";
        }
    }
}

// Close connection
//mysqli_close($connection);
?>
