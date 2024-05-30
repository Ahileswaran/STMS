<?php
session_start();

$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reset_password'])) {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
    $username = $_SESSION['reset_username'];

    // Update the password in the database
    $sql = "UPDATE login SET user_password = ? WHERE username = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("ss", $new_password, $username);
    if ($stmt->execute()) {
        echo "Password reset successfully.";
        // Unset the session variables
        unset($_SESSION['auth_code']);
        unset($_SESSION['reset_username']);
        header('Location: login_page.php');
        exit();
    } else {
        echo "Error resetting password.";
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
    <link rel="stylesheet" href="../../styles.css">
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
                <option value="menu0" disabled selected>Downloads</option>
                <option value="teachers_guide">Teachers Guides</option>
                <option value="syllabi">Syllabi</option>
                <option value="resource_page">Resource Books</option>
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
            <form action="reset_password.php" method="post">
                <label for="new_password">New Password: </label>
                <input id="new_password" name="new_password" type="password" required><br><br>

                <button type="submit" name="reset_password">Submit</button>
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
</body>

</html>