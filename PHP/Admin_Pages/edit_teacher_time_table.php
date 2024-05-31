<?php
session_start();

$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

$connection = new mysqli($server, $username, $password, $database);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$table_name = "";
$teacher_time_table = [];
$feedback = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['find_username'])) {
        $find_username = $_POST['username'];
        $table_name = "teacher_time_table_" . strtolower($find_username);
        $table_name_escaped = $connection->real_escape_string($table_name);
        $result = $connection->query("SHOW TABLES LIKE '$table_name_escaped'");
        if ($result->num_rows == 1) {
            $_SESSION['table_name'] = $table_name_escaped;
        } else {
            $feedback = "Table for username '$find_username' does not exist.";
        }
    } elseif (isset($_SESSION['table_name'])) {
        $table_name = $_SESSION['table_name'];

        if (isset($_POST['delete_registration_id'])) {
            // Delete operation
            $delete_registration_id = $_POST['delete_registration_id'];
            $delete_class_id = $_POST['delete_class_id'];
            $delete_subject_id = $_POST['delete_subject_id'];
            $delete_class_day = $_POST['delete_class_day'];
            $delete_start_time = $_POST['delete_start_time'];
            $delete_end_time = $_POST['delete_end_time'];

            $sql = "DELETE FROM $table_name WHERE registration_id=? AND class_id=? AND subject_id=? AND class_day=? AND start_time=? AND end_time=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("ssssss", $delete_registration_id, $delete_class_id, $delete_subject_id, $delete_class_day, $delete_start_time, $delete_end_time);
            $stmt->execute();
        } else {
            // Create or Update operation
            $registration_id = $_POST['registration_id'];
            $class_id = $_POST['class_id'];
            $subject_id = $_POST['subject_id'];
            $class_day = $_POST['class_day'];
            $start_time = $_POST['start_time'];
            $end_time = $_POST['end_time'];

            if (isset($_POST['update_registration_id'])) {
                // Update existing record
                $update_registration_id = $_POST['update_registration_id'];
                $update_class_id = $_POST['update_class_id'];
                $update_subject_id = $_POST['update_subject_id'];
                $update_class_day = $_POST['update_class_day'];
                $update_start_time = $_POST['update_start_time'];
                $update_end_time = $_POST['update_end_time'];

                $sql = "UPDATE $table_name SET class_id=?, subject_id=?, class_day=?, start_time=?, end_time=? WHERE registration_id=? AND class_id=? AND subject_id=? AND class_day=? AND start_time=? AND end_time=?";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("sssssssssss", $class_id, $subject_id, $class_day, $start_time, $end_time, $update_registration_id, $update_class_id, $update_subject_id, $update_class_day, $update_start_time, $update_end_time);
            } else {
                // Insert new record
                $sql = "INSERT INTO $table_name (registration_id, class_id, subject_id, class_day, start_time, end_time) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $connection->prepare($sql);
                $stmt->bind_param("ssssss", $registration_id, $class_id, $subject_id, $class_day, $start_time, $end_time);
            }
            $stmt->execute();
        }
    }
}

if (!empty($table_name)) {
    $sql = "SELECT * FROM $table_name";
    $result = $connection->query($sql);

    if ($result) {
        $teacher_time_table = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Teacher Time Table Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: none;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 90%;
            max-width: 922px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            margin-left: 250px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-container input,
        .form-container select,
        .form-container button {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
        }

        .form-container button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        .scrollable {
            overflow-x: auto;
            margin-top: 50px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f4f4f4;
        }

        .edit-button,
        .delete-button {
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .edit-button {
            background-color: #4CAF50;
            color: white;
        }

        .delete-button {
            background-color: #f44336;
            color: white;
        }

        .edit-button:hover {
            background-color: #45a049;
        }

        .delete-button:hover {
            background-color: #e53935;
        }

        @media (max-width: 768px) {
            .container {
                width: 100%;
                margin: 20px;
                padding: 10px;
            }

            .form-container input,
            .form-container select,
            .form-container button {
                font-size: 14px;
            }

            table,
            th,
            td {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Find Teacher Time Table</h2>
        <form method="POST" class="form-container">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <button type="submit" name="find_username">Find</button>
        </form>
        <p><?php echo $feedback; ?></p>

        <?php if (!empty($teacher_time_table)) : ?>
            <div class="scrollable">
                <h2>Edit Teacher Time Table: <?php echo htmlspecialchars($table_name); ?></h2>
                <table>
                    <thead>
                        <tr>
                            <th>Registration ID</th>
                            <th>Class ID</th>
                            <th>Subject ID</th>
                            <th>Class Day</th>
                            <th>Start Time</th>
                            <th>End Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($teacher_time_table as $row) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['registration_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['class_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject_id']); ?></td>
                                <td><?php echo htmlspecialchars($row['class_day']); ?></td>
                                <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                                <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                                <td>
                                    <button class="edit-button" data-registration-id="<?php echo $row['registration_id']; ?>" data-class-id="<?php echo $row['class_id']; ?>" data-subject-id="<?php echo $row['subject_id']; ?>" data-class-day="<?php echo $row['class_day']; ?>" data-start-time="<?php echo $row['start_time']; ?>" data-end-time="<?php echo $row['end_time']; ?>">Edit</button>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="delete_registration_id" value="<?php echo $row['registration_id']; ?>">
                                        <input type="hidden" name="delete_class_id" value="<?php echo $row['class_id']; ?>">
                                        <input type="hidden" name="delete_subject_id" value="<?php echo $row['subject_id']; ?>">
                                        <input type="hidden" name="delete_class_day" value="<?php echo $row['class_day']; ?>">
                                        <input type="hidden" name="delete_start_time" value="<?php echo $row['start_time']; ?>">
                                        <input type="hidden" name="delete_end_time" value="<?php echo $row['end_time']; ?>">
                                        <button type="submit" class="delete-button">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="form-container">
                <h3>Add/Edit Teacher Time Table</h3>
                <form id="teacher-time-table-form" method="POST">
                    <input type="hidden" name="update_registration_id" id="update_registration_id">
                    <input type="hidden" name="update_class_id" id="update_class_id">
                    <input type="hidden" name="update_subject_id" id="update_subject_id">
                    <input type="hidden" name="update_class_day" id="update_class_day">
                    <input type="hidden" name="update_start_time" id="update_start_time">
                    <input type="hidden" name="update_end_time" id="update_end_time">
                    <label for="registration_id">Registration ID:</label>
                    <input type="text" name="registration_id" id="registration_id" required>
                    <label for="class_id">Class ID:</label>
                    <input type="text" name="class_id" id="class_id" required>
                    <label for="subject_id">Subject ID:</label>
                    <input type="text" name="subject_id" id="subject_id" required>
                    <label for="class_day">Class Day:</label>
                    <input type="text" name="class_day" id="class_day" required>
                    <label for="start_time">Start Time:</label>
                    <input type="time" name="start_time" id="start_time" required>
                    <label for="end_time">End Time:</label>
                    <input type="time" name="end_time" id="end_time" required>
                    <button type="submit">Save</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function bindEditFunction() {
                document.querySelectorAll('.edit-button').forEach(button => {
                    button.addEventListener('click', function() {
                        const registration_id = this.dataset.registrationId;
                        const class_id = this.dataset.classId;
                        const subject_id = this.dataset.subjectId;
                        const class_day = this.dataset.classDay;
                        const start_time = this.dataset.startTime;
                        const end_time = this.dataset.endTime;
                        editRecord(registration_id, class_id, subject_id, class_day, start_time, end_time);
                    });
                });
            }

            function editRecord(registration_id, class_id, subject_id, class_day, start_time, end_time) {
                document.getElementById('update_registration_id').value = registration_id;
                document.getElementById('update_class_id').value = class_id;
                document.getElementById('update_subject_id').value = subject_id;
                document.getElementById('update_class_day').value = class_day;
                document.getElementById('update_start_time').value = start_time;
                document.getElementById('update_end_time').value = end_time;
                document.getElementById('registration_id').value = registration_id;
                document.getElementById('class_id').value = class_id;
                document.getElementById('subject_id').value = subject_id;
                document.getElementById('class_day').value = class_day;
                document.getElementById('start_time').value = start_time;
                document.getElementById('end_time').value = end_time;
            }

            bindEditFunction();
        });
    </script>
</body>

</html>
