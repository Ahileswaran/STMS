<?php
session_start();

$server = "localhost";
$username = "root";
$password = "";
$database = "stms_database";

if (isset($_POST['auth_code'])) {
    $input_code = $_POST['auth_code'];
    if ($input_code === $_SESSION['auth_code']) {
        $registration_data = $_SESSION['pending_registration'];

        $connection = new mysqli($server, $username, $password, $database);
        if ($connection->connect_error) {
            die("Connection failed: " . $connection->connect_error);
        }

        // Hash the password before storing it
        $hashed_password = password_hash($registration_data['password'], PASSWORD_BCRYPT);

        $sql = "INSERT INTO principal (first_name, last_name, user_address, age, sex, marital_status, registration_id, username, email, user_password, admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssssssss", $registration_data['first_name'], $registration_data['last_name'], $registration_data['user_address'], $registration_data['age'], $registration_data['sex'], $registration_data['marital_status'], $registration_data['registration_id'], $registration_data['username'], $registration_data['mail_id'], $hashed_password);

        if ($stmt->execute()) {
            $sql_login = "INSERT INTO login (username, email, user_password) VALUES (?, ?, ?)";
            $stmt_login = $connection->prepare($sql_login);
            $stmt_login->bind_param("sss", $registration_data['username'], $registration_data['mail_id'], $hashed_password);
            if ($stmt_login->execute()) {
                echo "<script>alert('Registration successful.'); window.location.href = '../../index.php';</script>";
            } else {
                echo "ERROR: Could not execute $sql_login. " . $stmt_login->error;
            }
            $stmt_login->close();
        } else {
            echo "ERROR: Could not execute $sql. " . $stmt->error;
        }
        $stmt->close();
        $connection->close();
        unset($_SESSION['auth_code']);
        unset($_SESSION['pending_registration']);
    } else {
        echo "<script>alert('Incorrect authentication code.');</script>";
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verify Authentication Code</title>
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
        .footer {
            background: #f1f1f1;
            text-align: center;
            padding: 10px;
        }
        @media (max-width: 768px) {
            .header, .footer {
                flex-direction: column;
                align-items: center;
            }
            nav {
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .drop_menu, .Search_field {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
<header class="header">
    <img src="../../images/logo-STMS.jpg" alt="logo" class="logo-image">
    <nav>
        <a class="active button" href="../../index.php">Home</a>
        <a class="active button" href="../profile_redirect.php">Dashboard</a>
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
        <form action="verify_auth_code.php" method="post">
            <label for="auth_code">Enter Authentication Code:</label>
            <input id="auth_code" name="auth_code" type="text" required>
            <button type="submit" value="submit">Submit</button>
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
