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

// Fetch profile picture from database
$session_username = $_SESSION['username'];
$sql = "SELECT profile_pic FROM profile_picture WHERE username = ?";
$stmt = $connection->prepare($sql);
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


if (!isset($_SESSION['search_results'])) {
    header("Location: ../../index.php");
    exit();
}

$search_results = $_SESSION['search_results'];
unset($_SESSION['search_results']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    
    <style>
     
        .content {
            padding: 200px;
            justify-content: center;
            max-width: 800px;
            margin: auto;
        }
        
        .content h1 {
            text-align: center;
            margin-bottom: 20px;
            color: white;
        }
        
        .content ul {
            list-style: none;
            padding: 0;
        }
        
        .content ul li {
            background-color: #f9f9f9;
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        
        .footer {
            background-color: #333;
            color: white;
            padding: 20px;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        
        .footer-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer-logo img {
            height: 50px;
        }
        
        .footer-links ul {
            list-style: none;
            padding: 0;
            display: flex;
        }
        
        .footer-links ul li {
            margin: 0 10px;
        }
        
        .footer-links ul li a {
            color: white;
            text-decoration: none;
        }
        
        .footer-links ul li a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
            }
            
            .header nav {
                margin-bottom: 10px;
            }
            
            .footer-container {
                flex-direction: column;
            }
            
            .footer-links ul {
                flex-direction: column;
                align-items: center;
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
            <a class="active button" href="../../PHP/pages/registering_page.php">Register</a>
            <a class="active button" href="../../PHP/pages/login_page.php">Login</a>
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
            <form action="./search.php" method="GET">
                <input type="text" name="search" placeholder="Search..." required>
                <button type="submit">Search</button>
            </form>
        </div>

        <div class="login_detail">
            <?php
            // Check if user is logged in
            if (isset($_SESSION['username'])) {
                // If logged in, display the profile picture and username
                echo "<div class='dropdown_details'>";
                echo "<img src='$profile_pic_src' alt='Profile Picture' class='profile-pic'>";
                echo "<div class='dropdown-content'>";
                echo "<p class='welcome-message'>Welcome, " . $_SESSION['username'] . "</p>";
                echo "<a href='../profile_redirect.php'>Profile</a>";
                echo "<a href='../logout.php'>Logout</a>";
                echo "</div>";
                echo "</div>";
            } else {
                // If not logged in, display login option
                echo "<a class='active button' href='../pages/login_page.php'>Login</a>";
            }
            ?>
        </div>
    </header>

    <div class="content">
        <h1>Search Results</h1>
        <?php if (empty($search_results)) : ?>
            <p>No results found for your search.</p>
        <?php else : ?>
            <ul>
                <?php foreach ($search_results as $result) : ?>
                    <li>
                        <strong>Name:</strong> <?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?><br>
                        <strong>Address:</strong> <?php echo htmlspecialchars($result['user_address']); ?><br>
                        <strong>Subject:</strong> <?php echo htmlspecialchars($result['subject_name']); ?><br>
                        <strong>Email:</strong> <?php echo htmlspecialchars($result['email']); ?><br>
                        <strong>Username:</strong> <?php echo htmlspecialchars($result['username']); ?><br>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <footer class="footer">
        <div class="footer-container">
            <div class="footer-logo">
                <img src="../../Images/logo-STMS.jpg" alt="Logo">
                <p>&copy; 2024 School Teachers Management System. All rights reserved.</p>
            </div>
            <div class="footer-links">
                <ul>
                    <li><a href="PHP/pages/about_page.php">About</a></li>
                    <li><a href="PHP/pages/manage_cookies.php">Manage Cookies</a></li>
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
