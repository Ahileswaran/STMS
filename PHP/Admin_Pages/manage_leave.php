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

// Handle form approval
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_id']) && !isset($_POST['search_substitute']) && !isset($_POST['select_substitute'])) {
    $form_id = $_POST['form_id'];
    $approval = isset($_POST['approval']) ? $_POST['approval'] : null;
    $supervising_officer_signature = isset($_POST['supervising_officer_signature']) ? $_POST['supervising_officer_signature'] : '';
    $department_officer_signature = isset($_POST['department_officer_signature']) ? $_POST['department_officer_signature'] : '';

    if ($approval !== null && $supervising_officer_signature && $department_officer_signature) {
        $sql = "UPDATE teacher_leave_form SET supervising_officer_signature = ?, department_officer_signature = ?, leave_granted = ? WHERE id = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ssii", $supervising_officer_signature, $department_officer_signature, $approval, $form_id);
        $stmt->execute();
    }
}

// Fetch all pending leave forms
$sql = "SELECT * FROM teacher_leave_form WHERE leave_granted IS NULL";
$result = $connection->query($sql);

$pending_forms = [];
if ($result) {
    $pending_forms = $result->fetch_all(MYSQLI_ASSOC);
}

// Find substitute teacher
$substitutes = [];
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search_substitute'])) {
    $day = $_POST['day'];
    $period = $_POST['period'];
    $subject = $_POST['subject'];
    $form_id = $_POST['form_id'];

    $sql = "
    SELECT DISTINCT t.username, t.registration_id
    FROM master_time_table t
    WHERE t.class_day = ? AND t.period = ? AND t.subject_id = ? AND t.username != (
        SELECT username FROM teacher_leave_form WHERE id = ?
    )";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("sssi", $day, $period, $subject, $form_id);
    $stmt->execute();
    $substitute_result = $stmt->get_result();
    $substitutes = $substitute_result->fetch_all(MYSQLI_ASSOC);
}

// Handle substitute selection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['select_substitute'])) {
    $form_id = $_POST['form_id'];
    $substitute_username = $_POST['substitute_username'];

    $sql = "UPDATE teacher_leave_form SET substitute_officer_signature = ? WHERE id = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("si", $substitute_username, $form_id);
    $stmt->execute();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>Manage Teacher Leave Forms</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        .container {
            display: flex;
            flex-direction: column;
            padding: 20px;
        }

        .scrollable {
            overflow-y: auto;
            height: 1000px;
            flex: 1;
            max-width: 1174px;
            margin-left: 380px;
            margin-top: 200px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .form-container {
            flex: 1;
            padding: 20px;
            margin-left: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            max-width: 282px;
            margin: 0 auto;
            margin-top: 0px;
            margin-bottom: 0px;
            margin-left: auto;
            padding: 2rem;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 71px;
        }

        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        .dropdown_details {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown_details:hover .dropdown-content {
            display: block;
        }

        .dropdown-content p,
        .dropdown-content a {
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .welcome-message {
            margin: 0;
        }

        .approval-form {
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
        }

        .approval-form button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .approval-form button:hover {
            background-color: #45a049;
        }

        .message-granted {
            color: green;
        }

        .message-not-granted {
            color: red;
        }
    </style>
</head>

<body>
    <header class="header">
        <img src="../../images/logo-STMS.jpg" alt="logo" class="logo-image">
        <nav>
            <a class="active button" href="../../index.php">Home</a>
            <a class="active button" href="../../PHP/pages/registering_page.php">Register</a>
            <a class="active button" href="../../PHP/pages/login_page.php">Login</a>
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

    <div class="container">
        <?php if (!empty($pending_forms)) : ?>
            <div class="scrollable">
                <h2>Pending Teacher Leave Forms</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Post</th>
                            <th>Department</th>
                            <th>Leave Type</th>
                            <th>Leave Days</th>
                            <th>Leave Taken Current Year</th>
                            <th>First Appointment Date</th>
                            <th>Leave Starting Date</th>
                            <th>Duty Resume Date</th>
                            <th>Reason for Leave</th>
                            <th>Leave Period Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_forms as $form) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($form['name']); ?></td>
                                <td><?php echo htmlspecialchars($form['post']); ?></td>
                                <td><?php echo htmlspecialchars($form['department']); ?></td>
                                <td><?php echo htmlspecialchars($form['leave_type']); ?></td>
                                <td><?php echo htmlspecialchars($form['leave_days']); ?></td>
                                <td><?php echo htmlspecialchars($form['leave_taken_current_year']); ?></td>
                                <td><?php echo htmlspecialchars($form['appointment_date']); ?></td>
                                <td><?php echo htmlspecialchars($form['leave_start_date']); ?></td>
                                <td><?php echo htmlspecialchars($form['duty_resume_date']); ?></td>
                                <td><?php echo htmlspecialchars($form['leave_reason']); ?></td>
                                <td><?php echo htmlspecialchars($form['leave_period_address']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="form_id" value="<?php echo $form['id']; ?>">
                                        <label for="supervising_officer_signature">Supervising Officer Signature:</label>
                                        <input type="text" id="supervising_officer_signature" name="supervising_officer_signature" required>

                                        <label for="department_officer_signature">Department Officer Signature:</label>
                                        <input type="text" id="department_officer_signature" name="department_officer_signature" required>

                                        <label for="approval">Approval:</label>
                                        <select id="approval" name="approval" required>
                                            <option value="1">Grant</option>
                                            <option value="0">Not Grant</option>
                                        </select>

                                        <button type="submit">Submit</button>
                                    </form>

                                    <button onclick="showSubstituteForm(<?php echo $form['id']; ?>)">Find Substitute Teacher</button>

                                    <div id="substituteForm<?php echo $form['id']; ?>" style="display: none;">
                                        <form method="POST">
                                            <input type="hidden" name="form_id" value="<?php echo $form['id']; ?>">
                                            <label for="day">Day:</label>
                                            <input type="text" id="day" name="day" required>

                                            <label for="period">Period:</label>
                                            <input type="text" id="period" name="period" required>

                                            <label for="subject">Subject:</label>
                                            <input type="text" id="subject" name="subject" required>

                                            <button type="submit" name="search_substitute">Search</button>
                                        </form>
                                    </div>

                                    <?php if (isset($form['leave_granted'])) : ?>
                                        <p class="<?php echo $form['leave_granted'] ? 'message-granted' : 'message-not-granted'; ?>">
                                            Leave <?php echo $form['leave_granted'] ? 'Granted' : 'Not Granted'; ?>
                                        </p>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p>There are no pending leave requests!</p>
        <?php endif; ?>

        <?php if (!empty($substitutes)) : ?>
            <div class="scrollable">
                <h2>Available Substitute Teachers</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Registration ID</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($substitutes as $substitute) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($substitute['username']); ?></td>
                                <td><?php echo htmlspecialchars($substitute['registration_id']); ?></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="form_id" value="<?php echo $_POST['form_id']; ?>">
                                        <input type="hidden" name="substitute_username" value="<?php echo $substitute['username']; ?>">
                                        <button type="submit" name="select_substitute">Select</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function showSubstituteForm(formId) {
            var form = document.getElementById('substituteForm' + formId);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
    </script>
</body>

</html>
