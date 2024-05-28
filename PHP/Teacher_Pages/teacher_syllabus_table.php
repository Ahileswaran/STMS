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

// Check if the user is an admin
//$is_admin = $_SESSION['role'] === 'admin';

// Syllabus table
$teacher_username = $_SESSION['username'];
$syllabus_table_name = "teacher_syllabus_table_" . $teacher_username;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $registration_id = $_SESSION['registration_id'];
        $week_id = $_POST['week_id'];
        $assign_date = $_POST['assign_date'];
        $conduct_date = $_POST['conduct_date'];
        $start_time = $_POST['start_time'];
        $lesson_time = $_POST['lesson_time'];
        $mastery = $_POST['mastery'];
        $section_number = $_POST['section_number'];
        $course_content = $_POST['course_content'];
        $teaching_date = $_POST['teaching_date'];
        $note = $_POST['note'];
        $id = $_POST['id'];

        if ($action == 'create') {
            $create_query = "INSERT INTO $syllabus_table_name (registration_id, week_id, assign_date, conduct_date, start_time, lesson_time, mastery, section_number, course_content, teaching_date, note)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($create_query);
            $stmt->bind_param("sisssssssss", $registration_id, $week_id, $assign_date, $conduct_date, $start_time, $lesson_time, $mastery, $section_number, $course_content, $teaching_date, $note);
            $stmt->execute();
        } elseif ($action == 'update') {
            $update_query = "UPDATE $syllabus_table_name SET week_id = ?, assign_date = ?, conduct_date = ?, start_time = ?, lesson_time = ?, mastery = ?, section_number = ?, course_content = ?, teaching_date = ?, note = ?
                             WHERE id = ? AND registration_id = ?";
            $stmt = $connection->prepare($update_query);
            $stmt->bind_param("issssssssssi", $week_id, $assign_date, $conduct_date, $start_time, $lesson_time, $mastery, $section_number, $course_content, $teaching_date, $note, $id, $registration_id);
            $stmt->execute();
        } elseif ($action == 'delete' && $is_admin) {
            $delete_query = "DELETE FROM $syllabus_table_name WHERE id = ? AND registration_id = ?";
            $stmt = $connection->prepare($delete_query);
            $stmt->bind_param("is", $id, $registration_id);
            $stmt->execute();
        }
    }
}

try {
    $syllabus_query = "SELECT * FROM $syllabus_table_name WHERE registration_id = ?";
    $syllabus_stmt = $connection->prepare($syllabus_query);
    $syllabus_stmt->bind_param("s", $_SESSION['registration_id']);
    $syllabus_stmt->execute();
    $syllabus_result = $syllabus_stmt->get_result();
} catch (mysqli_sql_exception $e) {
    // Table does not exist, create it
    $create_syllabus_table_query = "CREATE TABLE $syllabus_table_name (
        id INT AUTO_INCREMENT PRIMARY KEY,
        registration_id VARCHAR(255) NOT NULL,
        week_id INT NOT NULL,
        assign_date DATE NOT NULL,
        conduct_date DATE NOT NULL,
        start_time TIME NOT NULL,
        lesson_time TIME NOT NULL,
        mastery VARCHAR(255) NOT NULL,
        section_number INT NOT NULL,
        course_content TEXT NOT NULL,
        teaching_date DATE NOT NULL,
        note TEXT
    )";
    if ($connection->query($create_syllabus_table_query) === TRUE) {
        $syllabus_stmt = $connection->prepare($syllabus_query);
        $syllabus_stmt->bind_param("s", $_SESSION['registration_id']);
        $syllabus_stmt->execute();
        $syllabus_result = $syllabus_stmt->get_result();
    } else {
        $syllabus_result = null; // Proceed without the syllabus table
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syllabus Table</title>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 20px;
        }

        .glass-container {
            background: rgba(255, 255, 255, 0.7);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px;
            padding: 20px;
            flex: 1 1 45%;
            min-width: 300px;
            max-width: 600px;
        }

        .glass-container h3 {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .error {
            color: red;
        }

        .form-container input, .form-container select, .form-container .quill-editor, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        .form-container button.cancel {
            background-color: #f44336;
        }

        .form-container button.cancel:hover {
            background-color: #e41e1e;
        }

        .buttons-container button {
            background-color: #007BFF;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
        }

        .buttons-container button:hover {
            background-color: #0056b3;
        }

        @media (max-width: 768px) {
            .glass-container {
                padding: 10px;
                margin: 10px;
            }

            table {
                font-size: 0.9em;
            }
        }
    </style>
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var quill = new Quill('#course-content-editor', {
                theme: 'snow'
            });

            document.getElementById('syllabus-form').onsubmit = function() {
                var courseContent = document.querySelector('.ql-editor').innerHTML;
                document.getElementById('course_content').value = courseContent;
            };
        });

        function fillForm(data) {
            document.getElementById('id').value = data.id;
            document.getElementById('week_id').value = data.week_id;
            document.getElementById('assign_date').value = data.assign_date;
            document.getElementById('conduct_date').value = data.conduct_date;
            document.getElementById('start_time').value = data.start_time;
            document.getElementById('lesson_time').value = data.lesson_time;
            document.getElementById('mastery').value = data.mastery;
            document.getElementById('section_number').value = data.section_number;
            document.getElementById('course_content').value = data.course_content;
            document.querySelector('.ql-editor').innerHTML = data.course_content;
            document.getElementById('teaching_date').value = data.teaching_date;
            document.getElementById('note').value = data.note;
            document.getElementById('action').value = 'update';
        }

        function resetForm() {
            document.getElementById('syllabus-form').reset();
            document.querySelector('.ql-editor').innerHTML = '';
            document.getElementById('action').value = 'create';
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="glass-container">
            <h3>Syllabus Table</h3>
            <table border="1">
                <tr>
                    <th>Week ID</th>
                    <th>Assign Date</th>
                    <th>Conduct Date</th>
                    <th>Start Time</th>
                    <th>Lesson Time</th>
                    <th>Mastery</th>
                    <th>Section Number</th>
                    <th>Course Content</th>
                    <th>Teaching Date</th>
                    <th>Note</th>
                    <th>Actions</th>
                </tr>
                <?php
                if ($syllabus_result) {
                    if ($syllabus_result->num_rows > 0) {
                        while ($row = $syllabus_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['week_id']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['assign_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['conduct_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['lesson_time']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['mastery']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['section_number']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['course_content']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['teaching_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['note']) . "</td>";
                            echo "<td>
                                    <button onclick='fillForm(" . json_encode($row) . ")'>Edit</button>";
                            if ($is_admin) {
                                echo "<form method='post' action='' style='display:inline;'>
                                        <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                        <input type='hidden' name='action' value='delete'>
                                        <button type='submit'>Delete</button>
                                      </form>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='11' class='error'>No syllabus details available for this user.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='error'>Syllabus table not available.</td></tr>";
                }
                ?>
            </table>
        </div>

        <div class="glass-container form-container">
            <h3>Add/Edit Syllabus Entry</h3>
            <form id="syllabus-form" method="post" action="">
                <input type="hidden" id="id" name="id">
                <input type="hidden" id="action" name="action" value="create">
                <label for="week_id">Week ID:</label>
                <input type="number" id="week_id" name="week_id" required>
                <label for="assign_date">Assign Date:</label>
                <input type="date" id="assign_date" name="assign_date" required>
                <label for="conduct_date">Conduct Date:</label>
                <input type="date" id="conduct_date" name="conduct_date" required>
                <label for="start_time">Start Time:</label>
                <input type="time" id="start_time" name="start_time" required>
                <label for="lesson_time">Lesson Time:</label>
                <input type="time" id="lesson_time" name="lesson_time" required>
                <label for="mastery">Mastery:</label>
                <input type="text" id="mastery" name="mastery" required>
                <label for="section_number">Section Number:</label>
                <input type="number" id="section_number" name="section_number" required>
                <label for="course_content">Course Content:</label>
                <div id="course-content-editor" class="quill-editor"></div>
                <input type="hidden" id="course_content" name="course_content">
                <label for="teaching_date">Teaching Date:</label>
                <input type="date" id="teaching_date" name="teaching_date" required>
                <label for="note">Note:</label>
                <textarea id="note" name="note"></textarea>
                <button type="submit">Save</button>
                <button type="button" class="cancel" onclick="resetForm()">Cancel</button>
            </form>
        </div>
    </div>
</body>

</html>
