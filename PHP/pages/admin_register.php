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

// Handle the form submission for registration
if (isset($_POST['admin_username']) && isset($_POST['admin_password']) && !$admin_exists) {
    $first_name = $_POST['admin_first_name'];
    $last_name = $_POST['admin_last_name'];
    $user_address = $_POST['admin_address']; // Use 'address' from form input
    $age = $_POST['admin_age'];
    $sex = $_POST['admin_sex'];
    $marital_status = $_POST['admin_marital_status'];
    $registration_id = $_POST['admin_registration_id'];
    $username = $_POST['admin_username'];
    $mail_id = $_POST['admin_mail_id'];
    $password = $_POST['admin_password'];

    // Check if username already exists
    $sql = "SELECT * FROM principal WHERE username = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Username already taken, please choose another.');</script>";
    } else {
        // Insert new admin
        $sql = "INSERT INTO principal (first_name, last_name, user_address, age, sex, marital_status, registration_id, username, email, user_password, admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssssssss", $first_name, $last_name, $user_address, $age, $sex, $marital_status, $registration_id, $username, $mail_id, $password);
        if ($stmt->execute()) {
            // Insert login details into login table
            $sql_login = "INSERT INTO login (username, email, user_password) VALUES (?, ?, ?)";
            $stmt_login = $connection->prepare($sql_login);
            $stmt_login->bind_param("sss", $username, $mail_id, $password);

            if ($stmt_login->execute()) {
                echo "<script>alert('$first_name $last_name added successfully.'); window.location.href = '../index.php';</script>";
            } else {
                echo "ERROR: Could not execute $sql_login. " . $stmt_login->error;
            }
            $stmt_login->close();
        } else {
            echo "ERROR: Could not execute $sql. " . $stmt->error;
        }
        $stmt->close();
    }
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
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        .content{
            margin-top: 100px;
        }
        .status-message {
            display: inline-block;
            margin-left: 200px;
            color: red;
            font-weight: bold;
        }

        .status-message.available {
            color: green;
        }

        .form-container {
            text-align: left;
            margin: auto;
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
            <a class="active button" href="register.php">Register</a>
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
                        <input id="admin_first_name" name="admin_first_name" type="text" placeholder="John" required><br>

                        <label for="admin_last_name">Last Name: </label>
                        <input id="admin_last_name" name="admin_last_name" type="text" placeholder="Doe" required><br>

                        <label for="admin_address">Address: </label>
                        <input id="admin_address" name="admin_address" type="text" placeholder="New York" required><br>

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

    <script src="../../javaScript.js"></script>
</body>

</html>
