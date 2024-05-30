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

    // Query to check if the username or email exists in the principal table
    $query_principal = "SELECT * FROM principal WHERE username = ? OR email = ?";
    $stmt = $connection->prepare($query_principal);
    $stmt->bind_param("ss", $input_username_or_email, $input_username_or_email);
    $stmt->execute();
    $result_principal = $stmt->get_result();

    if ($result_principal->num_rows === 1) {
        $user_row = $result_principal->fetch_assoc();
        $hashed_password = $user_row['user_password'];

        if (password_verify($input_password, $hashed_password)) {
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
            header("Location: ../php/pages/admin_page.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password. Please try again.');</script>";
        }
    } else {
        // Query to check if the username or email exists in the teacher table
        $query_teacher = "SELECT * FROM teacher WHERE username = ? OR email = ?";
        $stmt = $connection->prepare($query_teacher);
        $stmt->bind_param("ss", $input_username_or_email, $input_username_or_email);
        $stmt->execute();
        $result_teacher = $stmt->get_result();

        if ($result_teacher->num_rows === 1) {
            $user_row = $result_teacher->fetch_assoc();
            $hashed_password = $user_row['user_password'];

            if (password_verify($input_password, $hashed_password)) {
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
                echo "<script>alert('Incorrect password. Please try again.');</script>";
            }
        } else {
            echo "<script>alert('Username or email not found. Please try again.');</script>";
        }
    }
    $stmt->close();
}

$connection->close();
?>
