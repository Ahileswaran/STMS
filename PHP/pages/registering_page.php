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

// Handle the AJAX request for checking username availability
if (isset($_POST['check_username'])) {
    $username = $_POST['username'];

    $sql = "SELECT * FROM teacher WHERE username = ?";
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
if (isset($_POST['username']) && isset($_POST['password'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $age = $_POST['age'];
    $sex = $_POST['sex'];
    $marital_status = $_POST['marital_status'];
    $teacher_id = $_POST['teacher_id'];
    $subject = $_POST['subject'];
    $username = $_POST['username'];
    $mail_id = $_POST['mail_id'];
    $password = $_POST['password'];

    // Check if username already exists
    $sql = "SELECT * FROM teacher WHERE username = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Username already taken, please choose another.');</script>";
    } else {
        // Insert new user
        $sql = "INSERT INTO teacher (first_name, last_name, address, age, sex, marital_status, teacher_id, subject, username, mail_id, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sssssssssss", $first_name, $last_name, $address, $age, $sex, $marital_status, $teacher_id, $subject, $username, $mail_id, $password);
        if ($stmt->execute() === TRUE) {
            echo "<script>alert('Registration successful!');</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $connection->error;
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
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        .username-status {
            display: inline-block;
            margin-left: 200px;
            color: red;
            font-weight: bold;
        }

        .username-status.available {
            color: green;
        }
    </style>
    <script>
        $(document).ready(function() {
            $("#username").keyup(function() {
                var username = $(this).val().trim();

                if (username != '') {
                    $.ajax({
                        url: 'registering_page.php',
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
        </div>

    </header>

    <div class="content">
        <div class="form-container">
            <form class="register-form" action="registering_page.php" method="post">
                <label for="first_name">First Name: </label>
                <input id="first_name" name="first_name" type="text" placeholder="Vasuky" required><br>

                <label for="last_name">Last Name: </label>
                <input id="last_name" name="last_name" type="text" placeholder="Nathan" required><br>

                <label for="address">Address: </label>
                <input id="address" name="address" type="text" placeholder="Colombo first street" required><br>

                <label for="age">Age: </label>
                <input id="age" name="age" type="text" required><br>

                <label for="sex">Sex: </label>
                <div class="radio-buttons">
                    <input type="radio" id="male" name="sex" value="Male" required> <label for="male">Male</label>
                    <input type="radio" id="female" name="sex" value="Female" required> <label for="female">Female</label>
                </div><br>

                <label for="marital_status">Marital Status: </label>
                <input id="marital_status" name="marital_status" type="text" required><br>

                <label for="teacher_id">Registration Number: </label>
                <input id="teacher_id" name="teacher_id" type="text" placeholder="Principal 'TN|PRI...' Teacher 'TN|TEA...'" required><br>

                <label for="subject">Subject: </label>
                <input id="subject" name="subject" type="text" required><br>

                <label for="username">User Name: </label>
                <input id="username" name="username" type="text" placeholder="Vasuky_N" required>
                <span id="username_status" class="username-status"></span><br>

                <label for="mail_id">Mail Address: </label>
                <input id="mail_id" name="mail_id" type="email" placeholder="vasuky@example.com" required><br>

                <label for="password">Password: </label>
                <input id="password" name="password" type="password" required><br>

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
                    <li><a href="#">Legal Stuff</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Security</a></li>
                    <li><a href="#">Website Accessibility</a></li>
                    <li><a href="#">Manage Cookies</a></li>
                </ul>
            </div>
        </div>
    </footer>

    <script src="../../javaScript.js"></script>
</body>

</html>