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
$leave_granted = NULL;
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

// Fetch today's syllabus notifications
$syllabus_table_name = "teacher_syllabus_table_" . $session_username;
$current_date = date("Y-m-d");
$syllabus_sql = "SELECT assign_date, class_id, course_content FROM $syllabus_table_name WHERE assign_date = ?";
$syllabus_stmt = $connection->prepare($syllabus_sql);
$syllabus_stmt->bind_param("s", $current_date);
$syllabus_stmt->execute();
$syllabus_stmt->store_result();

$syllabus_notifications = [];
if ($syllabus_stmt->num_rows > 0) {
    $syllabus_stmt->bind_result($assign_date, $class_id, $course_content);
    while ($syllabus_stmt->fetch()) {
        $syllabus_notifications[] = [
            'assign_date' => $assign_date,
            'class_id' => $class_id,
            'course_content' => $course_content
        ];
    }
}

$syllabus_stmt->close();

// Handle post request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $success = false;
    if (isset($_POST['update_details'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $user_address = $_POST['user_address'];
        $age = $_POST['age'];
        $sex = $_POST['sex'];
        $marital_status = $_POST['marital_status'];
        $email = $_POST['email'];

        $sql = "UPDATE principal SET first_name = ?, last_name = ?, user_address = ?, age = ?, sex = ?, marital_status = ?, email = ? WHERE username = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssssssss", $first_name, $last_name, $user_address, $age, $sex, $marital_status, $email, $session_username);

        if ($stmt->execute()) {
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['user_address'] = $user_address;
            $_SESSION['age'] = $age;
            $_SESSION['sex'] = $sex;
            $_SESSION['marital_status'] = $marital_status;
            $_SESSION['email'] = $email;
            $update_message = "Details updated successfully.";
            $success = true;
        } else {
            $update_message = "Error updating details.";
        }
        $stmt->close();
    }

    // Handle profile picture upload within the same form submission
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $profile_pic = file_get_contents($_FILES['profile_pic']['tmp_name']);
        $sql = "REPLACE INTO profile_picture (username, profile_pic) VALUES (?, ?)";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("sb", $session_username, $profile_pic);
        $stmt->send_long_data(1, $profile_pic);

        if ($stmt->execute()) {
            $upload_message = "Profile picture uploaded successfully.";
            $success = true;
        } else {
            $upload_message = "Error uploading profile picture.";
        }
        $stmt->close();
    }

    // Handle profile picture deletion
    if (isset($_POST['delete_pic'])) {
        $sql = "DELETE FROM profile_picture WHERE username = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("s", $session_username);

        if ($stmt->execute()) {
            $delete_message = "Profile picture deleted successfully.";
            $success = true;
        } else {
            $delete_message = "Error deleting profile picture.";
        }

        $stmt->close();
    }
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }
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
        .pro-content {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .pro-content h4 {
            margin: 10px 0;
            font-weight: normal;
        }
        .pro-content h4 span.label {
            font-weight: bold;
            color: #333;
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
        .notification {
            background-color: #f9f9f9;
            border-left: 4px solid #007bff;
            padding: 10px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .notification h4 {
            margin: 0;
        }
        .success-message {
            color: green;
        }
        .error-message {
            color: red;
        }
        .edit-form input {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .edit-form button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        .edit-form button:hover {
            background-color: #45a049;
        }
        .edit-button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }
        .edit-button:hover {
            background-color: #0056b3;
        }
        .delete-button {
            padding: 10px 20px;
            background-color: #FF0000;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 20px;
        }
        .delete-button:hover {
            background-color: #cc0000;
        }
    </style>
</head>

<body>
    <div class="glass-container">
        <div class="profile-pic-container">
            <img id="upload_pic" src="<?php echo $profile_pic_src; ?>" alt="Profile Picture">
        </div>
        <div class="pro-content">
            <?php if (isset($update_message)): ?>
                <p class="<?php echo $success ? 'success-message' : 'error-message'; ?>"><?php echo $update_message; ?></p>
            <?php endif; ?>

            <?php if (isset($upload_message)): ?>
                <p class="<?php echo $success ? 'success-message' : 'error-message'; ?>"><?php echo $upload_message; ?></p>
            <?php endif; ?>

            <?php if (isset($delete_message)): ?>
                <p class="<?php echo $success ? 'success-message' : 'error-message'; ?>"><?php echo $delete_message; ?></p>
            <?php endif; ?>

            <div id="view_details">
                <h4><span class="label">First Name:</span> <?php echo $_SESSION['first_name']; ?></h4>
                <h4><span class="label">Last Name:</span> <?php echo $_SESSION['last_name']; ?></h4>
                <h4><span class="label">Address:</span> <?php echo $_SESSION['user_address']; ?></h4>
                <h4><span class="label">Age:</span> <?php echo $_SESSION['age']; ?></h4>
                <h4><span class="label">Sex:</span> <?php echo $_SESSION['sex']; ?></h4>
                <h4><span class="label">Marital Status:</span> <?php echo $_SESSION['marital_status']; ?></h4>
                <h4><span class="label">Registration Id:</span> <?php echo $_SESSION['registration_id']; ?></h4>
                <h4><span class="label">Username:</span> <?php echo $_SESSION['username']; ?></h4>
                <h4><span class="label">Email:</span> <?php echo $_SESSION['email']; ?></h4>
                <h4 class="<?php echo $leave_granted === NULL ? 'message-pending' : ($leave_granted == 1 ? 'message-granted' : 'message-not-granted'); ?>">
                    <?php echo $leave_status_message; ?>
                </h4>
                <?php if (!empty($syllabus_notifications)) { ?>
                    <div class="notification">
                        <h4>Today's Assignments:</h4>
                        <?php foreach ($syllabus_notifications as $notification) { ?>
                            <span><?php echo htmlspecialchars($notification['assign_date']); ?> - Class: <?php echo htmlspecialchars($notification['class_id']); ?> - Content: <?php echo htmlspecialchars($notification['course_content']); ?></span><br>
                        <?php } ?>
                    </div>
                <?php } ?>
                <button id="edit_button" class="edit-button">Edit</button>
            </div>

            <div id="edit_details" class="edit-form" style="display: none;">
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="text" name="first_name" value="<?php echo $_SESSION['first_name']; ?>">
                    <input type="text" name="last_name" value="<?php echo $_SESSION['last_name']; ?>">
                    <input type="text" name="user_address" value="<?php echo $_SESSION['user_address']; ?>">
                    <input type="text" name="age" value="<?php echo $_SESSION['age']; ?>">
                    <input type="text" name="sex" value="<?php echo $_SESSION['sex']; ?>">
                    <input type="text" name="marital_status" value="<?php echo $_SESSION['marital_status']; ?>">
                    <input type="text" name="email" value="<?php echo $_SESSION['email']; ?>">
                    <div class="add-profile-pic">
                        <label for="file_input">Add Profile Picture:</label>
                        <button type="button" id="add_pic_button">Add</button>
                        <input type="file" id="file_input" name="profile_pic" onchange="previewImage(event)">
                    </div>
                    <button type="submit" name="update_details">Save</button>
                    <button type="submit" name="delete_pic" class="delete-button">Delete Profile Picture</button>
                </form>
                <button id="cancel_button" class="edit-button">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('add_pic_button').addEventListener('click', function() {
            document.getElementById('file_input').click();
        });

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function(){
                var output = document.getElementById('upload_pic');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

        document.getElementById('edit_button').addEventListener('click', function() {
            document.getElementById('view_details').style.display = 'none';
            document.getElementById('edit_details').style.display = 'block';
        });

        document.getElementById('cancel_button').addEventListener('click', function() {
            document.getElementById('edit_details').style.display = 'none';
            document.getElementById('view_details').style.display = 'block';
        });
    </script>
</body>

</html>
