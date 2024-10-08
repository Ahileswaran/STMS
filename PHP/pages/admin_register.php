<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the PHPMailer files
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

session_start(); // Start the session

$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if an admin is already set
$admin_exists = false;
$sql = "SELECT * FROM principal WHERE admin = 1";
$stmt = $connection->prepare($sql);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    $admin_exists = true;
}
$stmt->close();

// Handle the AJAX request for checking username availability
if (isset($_POST['check_username'])) {
    $username = $_POST['username'];

    $sql = "SELECT * FROM principal WHERE username = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "taken";
    } else {
        echo "available";
    }
    $stmt->close();
    exit();
}

// Generate random authentication code
function generateRandomCode($length = 6)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Handle the form submission for registration
if (isset($_POST['admin_username']) && isset($_POST['admin_password']) && !$admin_exists) {
    $first_name = $_POST['admin_first_name'];
    $last_name = $_POST['admin_last_name'];
    $user_address = $_POST['admin_address'];
    $age = $_POST['admin_age'];
    $sex = $_POST['admin_sex'];
    $marital_status = $_POST['admin_marital_status'];
    $registration_id = $_POST['admin_registration_id'];
    $username = $_POST['admin_username'];
    $mail_id = $_POST['admin_mail_id'];
    $password = $_POST['admin_password'];

    // Generate random authentication code
    $auth_code = generateRandomCode();

    // Store auth code and registration data in session
    $_SESSION['auth_code'] = $auth_code;
    $_SESSION['pending_registration'] = [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'user_address' => $user_address,
        'age' => $age,
        'sex' => $sex,
        'marital_status' => $marital_status,
        'registration_id' => $registration_id,
        'username' => $username,
        'mail_id' => $mail_id,
        'password' => $password
    ];

    // Send authentication code to user's email using PHPMailer with Mailtrap Email Sending SMTP
    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth   = true;
        $mail->Port       = 2525;
        $mail->Username   = 'da5691c478f9ff';
        $mail->Password   = 'f134c5d3c35f46';

        // Recipients
        $mail->setFrom('website.stms@gmail.com', 'STMS');
        $mail->addAddress($mail_id, $first_name . ' ' . $last_name); // Add a recipient

        // Content
        $mail->isHTML(true);                       // Set email format to HTML
        $mail->Subject = 'Your Authentication Code';
        $mail->Body    = "Your authentication code is: $auth_code";

        $mail->send();
        header('Location: verify_auth_code.php');
        exit();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// Fetch profile picture from database
$profile_pic_src = '../../images/profile-pic.png'; 

if (isset($_SESSION['username'])) {
    $session_username = $_SESSION['username'];
    $sql = "SELECT profile_pic FROM profile_picture WHERE username = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $session_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($profile_pic_data);
        $stmt->fetch();
        $profile_pic = base64_encode($profile_pic_data);
        $profile_pic_src = 'data:image/jpeg;base64,' . $profile_pic;
    }
    $stmt->close();
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            margin: 0;
        }
        .content {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .status-message {
            display: inline-block;
            margin-left: 150px;
            color: red;
            font-weight: bold;
        }

        .status-message.available {
            color: green;
        }

        .form-container {
            justify-content: center;
            margin-top: 150px;
            text-align: left;
            width: 50%;
        }

        .radio-buttons {
            display: flex;
            gap: 10px;
        }

    </style>
    <script>
        $(document).ready(function() {
            // Username availability check
            $("#admin_username").keyup(function() {
                var username = $(this).val().trim();

                if (username != '') {
                    $.ajax({
                        url: 'admin_register.php',
                        type: 'post',
                        data: {
                            check_username: 1,
                            username: username
                        },
                        success: function(response) {
                            if (response == 'taken') {
                                $("#username_status").html("Username already taken, please choose another.").removeClass("available").addClass("taken");
                            } else if (response == 'available') {
                                $("#username_status").html("Username is available.").removeClass("taken").addClass("available");
                            }
                        }
                    });
                } else {
                    $("#username_status").html("").removeClass("available taken");
                }
            });
        });
    </script>
</head>

<body>
    <header class="header">
        <img src="../../images/logo-STMS.jpg" alt="logo" class="logo-image">
        <nav>
            <a class="active button" href="../../index.php">Home</a>
            <a class="active button" href="../profile_redirect.php">Dashboard</a>
            <a class="active button" href="register.php">Register</a>
            <a class="active button" href="./login_page.php">Login</a>
        </nav>
        <div class="drop_menu">
            <select name="menu" onchange="redirect(this)">
                <option value="" disabled selected>Downloads</option>
                <option value="https://nie.lk/seletguide">Teachers Guides</option>
                <option value="https://nie.lk/selesyll">Syllabi</option>
                <option value="https://nie.lk/showom">Other Materials</option>
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
        </div>

    </header>

    <div class="content">
        <div class="form-container">
            <?php if ($admin_exists) : ?>
                <div id="admin-message" class="status-message">An admin is already set.</div>
            <?php else : ?>
                <div id="admin-form">
                    <form class="register-form" action="admin_register.php" method="post">
                        <label for="admin_first_name">First Name: </label>
                        <input id="admin_first_name" name="admin_first_name" type="text" placeholder="Siva" required><br>

                        <label for="admin_last_name">Last Name: </label>
                        <input id="admin_last_name" name="admin_last_name" type="text" placeholder="Vasan" required><br>

                        <label for="admin_address">Address: </label>
                        <input id="admin_address" name="admin_address" type="text" placeholder="Colombo" required><br>

                        <label for="admin_age">Age: </label>
                        <input id="admin_age" name="admin_age" type="text" required><br>

                        <label for="admin_sex">Sex: </label>
                        <div class="radio-buttons">
                            <input type="radio" id="admin_male" name="admin_sex" value="Male" required> <label for="admin_male">Male</label>
                            <input type="radio" id="admin_female" name="admin_sex" value="Female" required> <label for="admin_female">Female</label>
                        </div><br>

                        <label for="admin_marital_status">Marital Status: </label>
                        <input id="admin_marital_status" name="admin_marital_status" type="text" required><br>

                        <label for="admin_registration_id">Registration Number: </label>
                        <input id="admin_registration_id" name="admin_registration_id" type="text" required><br>

                        <label for="admin_username">Username: </label>
                        <input id="admin_username" name="admin_username" type="text" required>
                        <span id="username_status" class="status-message"></span><br>

                        <label for="admin_mail_id">Mail Address: </label>
                        <input id="admin_mail_id" name="admin_mail_id" type="email" required><br>

                        <label for="admin_password">Password: </label>
                        <input id="admin_password" name="admin_password" type="password" required><br>

                        <button type="submit" value="submit">Submit</button>
                    </form>
                </div>
            <?php endif; ?>
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
                    <li><a href="about_page.php">About</a></li>
                    <li><a href="manage_cookies.php">Manage Cookies</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <script src="../../JavaScripts/javaScript.js"></script>
    <script>
        function redirect(select) {
            var url = select.value;
            if (url) {
                window.open(url, '_blank');
            }
        }
    </script>
</body>

</html>