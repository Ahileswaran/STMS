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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['username']; // Automatically fill the username from session
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

    $sql = "INSERT INTO teacher_leave_form (username, name, post, department, leave_type, leave_days, leave_taken_current_year, appointment_date, leave_start_date, duty_resume_date, leave_reason, leave_period_address, applicant_signature) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("sssssiissssss", $username, $name, $post, $department, $leave_type, $leave_days, $leave_taken_current_year, $appointment_date, $leave_start_date, $duty_resume_date, $leave_reason, $leave_period_address, $applicant_signature);

    if ($stmt->execute()) {
        echo "Leave form submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $connection->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Leave Form</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.7);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            color: #333;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container label {
            display: block;
            margin: 10px 0 5px;
            color: #333;
            font-weight: bold;
        }

        .form-container input[type="text"],
        .form-container input[type="date"],
        .form-container input[type="number"],
        .form-container textarea,
        .form-container select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .form-container button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            width: 100%;
            border-radius: 5px;
            font-size: 16px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        @media (max-width: 600px) {
            .form-container {
                padding: 15px;
            }

            .form-container button {
                padding: 12px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Teacher Leave Form</h2>
        <form method="POST" action="submit_leave_form.php">
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
