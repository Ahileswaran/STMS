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

$stmt->close();


// Fetch leave status from database
$leave_status_sql = "SELECT id, leave_granted FROM teacher_leave_form WHERE username = ? ORDER BY id DESC LIMIT 1";
$leave_stmt = $connection->prepare($leave_status_sql);
$leave_stmt->bind_param("s", $session_username);
$leave_stmt->execute();
$leave_stmt->store_result();

$leave_status_message = "No leave requests found.";
if ($leave_stmt->num_rows > 0) {
    $leave_stmt->bind_result($leave_id, $leave_granted);
    $leave_stmt->fetch();
    if ($leave_granted === NULL) {
        $leave_status_message = "Leave request ID $leave_id: Pending";
    } elseif ($leave_granted == 1) {
        $leave_status_message = "Leave request ID $leave_id: Granted";
    } else {
        $leave_status_message = "Leave request ID $leave_id: Not Approved";
    }
}

$leave_stmt->close();
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <style>
        .glass-container {
            padding: 20px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
            margin-top: 50px;
        }

        .profile-pic-container {
            text-align: center;
        }

        .profile-pic-container img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 2px solid #ccc;
            margin-bottom: 20px;
        }

        h4 {
            margin: 10px 0;
        }

        .add-profile-pic {
            text-align: center;
            margin-top: 20px;
        }

        .add-profile-pic label {
            display: block;
            margin-bottom: 10px;
        }

        .add-profile-pic button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .add-profile-pic button:hover {
            background-color: #45a049;
        }

        .add-profile-pic input[type="file"] {
            display: none;
        }

        .message-granted {
            color: green;
        }

        .message-not-granted {
            color: red;
        }

        .message-pending {
            color: orange;
        }
    </style>
</head>

<body>
    <div class="glass-container">
        <div class="profile-pic-container">
            <img id="upload_pic" src="<?php echo $profile_pic_src; ?>" alt="Profile Picture">
        </div>
        <h4>First Name: <?php echo $_SESSION['first_name']; ?></h4>
        <h4>Last Name: <?php echo $_SESSION['last_name']; ?></h4>
        <h4>Address: <?php echo $_SESSION['user_address']; ?></h4>
        <h4>Age: <?php echo $_SESSION['age']; ?></h4>
        <h4>Sex: <?php echo $_SESSION['sex']; ?></h4>
        <h4>Marital Status: <?php echo $_SESSION['marital_status']; ?></h4>
        <h4>Registration Id: <?php echo $_SESSION['registration_id']; ?></h4>
        <h4>Subject: <?php echo $_SESSION['subject_name']; ?></h4>
        <h4>Username: <?php echo $_SESSION['username']; ?></h4>
        <h4>Email: <?php echo $_SESSION['email']; ?></h4>

        <h4 class="<?php echo $leave_granted === NULL ? 'message-pending' : ($leave_granted == 1 ? 'message-granted' : 'message-not-granted'); ?>">
            <?php echo $leave_status_message; ?>
        </h4>
    </div>

    <form action="upload_profile_pic.php" method="post" enctype="multipart/form-data">
        <div class="add-profile-pic">
            <label for="file_input">Add Profile Picture:</label>
            <button type="button" id="add_pic_button">Add</button>
            <input type="file" id="file_input" name="profile_pic">
        </div>
    </form>

    <script>
        document.getElementById('add_pic_button').addEventListener('click', function() {
            document.getElementById('file_input').click();
        });
    </script>
</body>

</html>