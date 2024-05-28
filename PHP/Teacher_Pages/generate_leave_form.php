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

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $post = $_POST['post'];
    $department = $_POST['department'];
    $leave_type = $_POST['leave_type'];
    $leave_days = $_POST['leave_days'];
    $leave_taken_current_year = $_POST['leave_taken_current_year'];
    $appointment_date = $_POST['appointment_date'];
    $leave_start_date = $_POST['leave_start_date'];
    $duty_resume_date = $_POST['duty_resume_date'];
    $leave_reason = $_POST['leave_reason'];
    $leave_period_address = $_POST['leave_period_address'];
    $applicant_signature = $_POST['applicant_signature'];

    $sql = "INSERT INTO teacher_leave_form (name, post, department, leave_type, leave_days, leave_taken_current_year, appointment_date, leave_start_date, duty_resume_date, leave_reason, leave_period_address, applicant_signature) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("sssiiissssss", $name, $post, $department, $leave_type, $leave_days, $leave_taken_current_year, $appointment_date, $leave_start_date, $duty_resume_date, $leave_reason, $leave_period_address, $applicant_signature);
    $stmt->execute();

    // Check for errors
    if ($stmt->error) {
        echo "Error: " . $stmt->error;
    } else {
        echo "Leave form submitted successfully!";
    }

    $stmt->close();
    $connection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Teacher Leave Form</title>
    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .form-container label {
            display: block;
            margin: 10px 0 5px;
        }

        .form-container input[type="text"],
        .form-container input[type="date"],
        .form-container input[type="number"],
        .form-container textarea,
        .form-container select {
            width: 100%;
            padding: 8px;
            margin: 5px 0 10px;
        }

        .form-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            width: 100%;
        }

        .form-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Teacher Leave Form</h2>
        <form method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="post">Post:</label>
            <input type="text" id="post" name="post" required>

            <label for="department">Department:</label>
            <input type="text" id="department" name="department" required>

            <label for="leave_type">Leave Type:</label>
            <select id="leave_type" name="leave_type" required>
                <option value="Casual">Casual</option>
                <option value="Illness">Illness</option>
                <option value="Duty">Duty</option>
                <option value="Others">Others</option>
            </select>

            <label for="leave_days">How many days of leave:</label>
            <input type="number" id="leave_days" name="leave_days" required>

            <label for="leave_taken_current_year">How many leave has been taken this current year:</label>
            <input type="number" id="leave_taken_current_year" name="leave_taken_current_year" required>

            <label for="appointment_date">First Appointment Date:</label>
            <input type="date" id="appointment_date" name="appointment_date" required>

            <label for="leave_start_date">Leave Starting Date:</label>
            <input type="date" id="leave_start_date" name="leave_start_date" required>

            <label for="duty_resume_date">Duty doing Date:</label>
            <input type="date" id="duty_resume_date" name="duty_resume_date" required>

            <label for="leave_reason">Reason for the leave:</label>
            <textarea id="leave_reason" name="leave_reason" rows="3" required></textarea>

            <label for="leave_period_address">Leave period Address:</label>
            <textarea id="leave_period_address" name="leave_period_address" rows="3" required></textarea>

            <label for="applicant_signature">Applicant Signature:</label>
            <input type="text" id="applicant_signature" name="applicant_signature" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
