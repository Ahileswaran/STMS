<?php
$username = "root"; 
$password = ""; 
$server = "localhost";  
$database = "stms_database"; 

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

//echo "Server connected successfully";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Escape user inputs for security
    $first_name = mysqli_real_escape_string($connection, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($connection, $_POST['last_name']);
    $teacher_address = mysqli_real_escape_string($connection, $_POST['address']);
    $age = mysqli_real_escape_string($connection, $_POST['age']);
    $sex = mysqli_real_escape_string($connection, $_POST['sex']);
    $marital_status = mysqli_real_escape_string($connection, $_POST['marital_status']);
    $registration_id = mysqli_real_escape_string($connection, $_POST['teacher_id']);
    $subject_name = mysqli_real_escape_string($connection, $_POST['subject']);
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $email = mysqli_real_escape_string($connection, $_POST['mail_id']);
    $teacher_password = mysqli_real_escape_string($connection, $_POST['password']);
    $user_role = ''; // Initialize user role variable

    // Determine user role based on registration ID prefix
    if (strpos($registration_id, 'PRI') !== false) {
        $user_role = 'principal';
    } elseif (strpos($registration_id, 'TEA') !== false) {
        $user_role = 'teacher';
    }

    // Generate a random verification code
    $verification_code = rand(100000, 999999);

    // Set the sender email as the user-provided email
    $from = $email;

    // Set the recipient email
    $to = $email;

    // Set the subject for the email
    $subject = 'Verification Code for Registration';

    // Set the message body with the verification code
    $message = "Your verification code is: $verification_code";

    // Construct the email headers
    $headers = "From: $from" . "\r\n" .
        'Reply-To: ' . $from . "\r\n" .
        'X-Mailer: PHP/' . phpversion();
    
        // Override SMTP settings using ini_set()
    ini_set('SMTP', 'smtp.mail.yahoo.com');
    ini_set('smtp_port', 587);
    
    // Send the email
    if (mail($to, $subject, $message, $headers)) {
        // Proceed with registration and store verification code in session
        session_start();
        $_SESSION['verification_code'] = $verification_code;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['teacher_address'] = $teacher_address;
        $_SESSION['age'] = $age;
        $_SESSION['sex'] = $sex;
        $_SESSION['marital_status'] = $marital_status;
        $_SESSION['registration_id'] = $registration_id;
        $_SESSION['subject_name'] = $subject_name;
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['teacher_password'] = $teacher_password;
        $_SESSION['user_role'] = $user_role;

    }
    // Attempt insert query execution
    $sql = "INSERT INTO teacher (first_name, last_name, user_address, age, sex, marital_status, registration_id, subject_name, username, email, teacher_password) 
    VALUES ('$first_name', '$last_name', '$teacher_address', '$age', '$sex', '$marital_status', '$registration_id', '$subject_name', '$username', '$email', '$teacher_password')";
    
    if(mysqli_query($connection, $sql)){
        $sql_login = "INSERT INTO login (username, email, teacher_password) VALUES ('$username', '$email', '$teacher_password')";
    if(mysqli_query($connection, $sql_login)){    
        echo "<script>alert('$first_name $last_name added successfully.'); window.location.href = '../index.html';</script>";
     }
        } else{
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($connection);
    }
}

// Close connection
mysqli_close($connection);
?>
