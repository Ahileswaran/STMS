<?php
session_start(); // Start the session

$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

try {
    // Establish database connection
    $connection = new mysqli($server, $username, $password, $database);
    if ($connection->connect_error) {
        throw new Exception("Connection failed: " . $connection->connect_error);
    }

    // Fetch profile picture from database
    $session_username = $_SESSION['username'];
    $sql = "SELECT profile_pic FROM profile_picture WHERE username = ?";
    $stmt = $connection->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Prepare statement failed: " . $connection->error);
    }

    $stmt->bind_param("s", $session_username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Profile picture found, display it
        $stmt->bind_result($profile_pic_data);
        $stmt->fetch();
        $profile_pic = base64_encode($profile_pic_data);
        $profile_pic_src = 'data:image/jpeg;base64,' . $profile_pic;
    } else {
        // Profile picture not found, use a default image
        $profile_pic_src = 'path_to_default_image.jpg'; // Replace with the path to your default image
    }

    $stmt->close();
    $connection->close();
} catch (Exception $e) {
    log_error($e->getMessage());
    header("Location: 404.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <style>
        .about-page {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
            background: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-top: 120px;
            margin-bottom: 40px;
        }

        .about-page h1,
        .about-page h3 {
            text-align: center;
        }

        .about-page p,
        .about-page ul {
            font-size: 16px;
            line-height: 1.6;
            color: #333;
        }

        .about-page ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        .about-page li {
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .about-page {
                padding: 15px;
                margin: 10px;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <img src="../../images/logo-STMS.jpg" alt="logo" class="logo-image">
        <nav>
            <a class="active button" href="../../index.php">Home</a>
            <a class="active button" href="../pages/registering_page.php">Register</a>
            <a class="active button" href="../pages/login_page.php">Login</a>
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
            <input type="text" name="search" placeholder="Search...">
            <button type="submit">Search</button>
        </div>

        <?php if (isset($_SESSION['username'])) : ?>
            <div class="login_detail">
                <div class='dropdown_details'>
                    <img src='<?php echo $profile_pic_src; ?>' alt='Profile Picture' class='profile-pic'>
                    <div class='dropdown-content'>
                        <p class='welcome-message'>Welcome, <?php echo $_SESSION['username']; ?></p>
                        <a href='login_page.php'>Profile</a>
                        <a href='../logout.php'>Logout</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <div class="content">
        <!-- main content goes here -->
        <div class="about-page">
            <h1>About</h1>
            <h3>School Teachers Management System</h3>
            <p>
                Welcome to the School Teachers Management System (STMS), a comprehensive platform designed and developed by Dragons to streamline the management of school teachers and their activities. Our system includes several important features:
            </p>
            <ul>
                <li>The system is administered by the school principal, who has full control over all activities.</li>
                <li>Teachers can access the system only after logging in with their credentials.</li>
                <li>Before logging in, teachers must create their profiles using the Register option.</li>
                <li>Teachers are required to create a timetable for their subjects, which is then combined into a master timetable displayed on the Home page.</li>
                <li>The system allows teachers to generate or write leave letters if needed, available through their profile page.</li>
                <li>The principal can assign substitute teachers when regular teachers take leave.</li>
                <li>The principal can add or remove teachers and assign classes as needed.</li>
                <li>All activities within the system are monitored by the principal to ensure smooth operation.</li>
                <li>Teachers can download guides and syllabi for their subjects from the Downloads section.</li>
                <li>The system provides an interactive syllabus page for each subject, detailing the syllabus for three terms and displaying upcoming events and reminders.</li>
            </ul>
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
                    <li><a href="../pages/manage_cookies.php">About</a></li>
                    <li><a href="../pages/manage_cookies.php">Manage Cookies</a></li>
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