<?php
session_start(); // Start the session


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Handle the form submission for password reset request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reset_request'])) {
    $username = mysqli_real_escape_string($connection, $_POST['username']);
    $email = mysqli_real_escape_string($connection, $_POST['email']);

    // Check if the username and email address are in the login table
    $sql = "SELECT * FROM login WHERE username = ? AND email = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Generate random authentication code
        $auth_code = bin2hex(random_bytes(3)); // 6-character code

        // Store auth code and username in session
        $_SESSION['auth_code'] = $auth_code;
        $_SESSION['reset_username'] = $username;

        // Send authentication code to user's email using PHPMailer with Mailtrap SMTP
        require '../PHPMailer/src/Exception.php';
        require '../PHPMailer/src/PHPMailer.php';
        require '../PHPMailer/src/SMTP.php';

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
            $mail->addAddress($email, $username); // Add a recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Your Password Reset Code';
            $mail->Body    = "Your password reset code is: $auth_code";

            $mail->send();
            echo "Reset code sent to your email.";
            header('Location: verify_reset_code.php');
            exit();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Incorrect username or email.";
    }
    $stmt->close();
}

// Fetch profile picture from database
$profile_pic_src = 'path_to_default_image.jpg'; // Default profile picture

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
    <title>Reset Password</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
</head>

<body>
    <header class="header">
        <img src="../../images/logo-STMS.jpg" alt="logo" class="logo-image">
        <nav>
            <a class="active button" href="../../index.php">Home</a>
            <a class="active button" href="#">Register</a>
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
    </header>

    <div class="content">
        <div class="form-container">
            <h2>Reset Password</h2>
            <form action="reset_password_request.php" method="post">
                <label for="username">Username: </label>
                <input id="username" name="username" type="text" required><br><br>

                <label for="email">Email Address: </label>
                <input id="email" name="email" type="email" required><br><br>

                <button type="submit" name="reset_request">Submit</button>
            </form>
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