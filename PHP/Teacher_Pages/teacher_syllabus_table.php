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

// Syllabus table
$teacher_username = $_SESSION['username'];
$syllabus_table_name = "teacher_syllabus_table_" . $teacher_username;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $registration_id = $_SESSION['registration_id'];
        $term_id = isset($_POST['term_id']) ? $_POST['term_id'] : null;
        $class_id = isset($_POST['class_id']) ? $_POST['class_id'] : null;
        $subject_id = isset($_POST['subject_id']) ? $_POST['subject_id'] : null;
        $assign_date = isset($_POST['assign_date']) ? $_POST['assign_date'] : null;
        $week_id = isset($_POST['week_id']) ? $_POST['week_id'] : null;
        $conduct_date = isset($_POST['conduct_date']) ? $_POST['conduct_date'] : null;
        $start_time = isset($_POST['start_time']) ? $_POST['start_time'] : null;
        $lesson_time = isset($_POST['lesson_time']) ? $_POST['lesson_time'] : null;
        $mastery = isset($_POST['mastery']) ? $_POST['mastery'] : null;
        $section_number = isset($_POST['section_number']) ? $_POST['section_number'] : null;
        $course_content = isset($_POST['course_content']) ? $_POST['course_content'] : null;
        $teaching_date = isset($_POST['teaching_date']) ? $_POST['teaching_date'] : null;
        $note = isset($_POST['note']) ? $_POST['note'] : null;

        if ($action == 'create') {
            $create_query = "INSERT INTO $syllabus_table_name (registration_id, term_id, class_id, subject_id, assign_date, week_id, conduct_date, start_time, lesson_time, mastery, section_number, course_content, teaching_date, note)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($create_query);
            $stmt->bind_param("ssssssssssssss", $registration_id, $term_id, $class_id, $subject_id, $assign_date, $week_id, $conduct_date, $start_time, $lesson_time, $mastery, $section_number, $course_content, $teaching_date, $note);
            $stmt->execute();
        } elseif ($action == 'update') {
            $update_query = "UPDATE $syllabus_table_name SET term_id = ?, class_id = ?, subject_id = ?, assign_date = ?, week_id = ?, conduct_date = ?, start_time = ?, lesson_time = ?, mastery = ?, section_number = ?, course_content = ?, teaching_date = ?, note = ?
                             WHERE registration_id = ? AND term_id = ? AND class_id = ? AND subject_id = ?";
            $stmt = $connection->prepare($update_query);
            $stmt->bind_param("ssssssssssssssss", $term_id, $class_id, $subject_id, $assign_date, $week_id, $conduct_date, $start_time, $lesson_time, $mastery, $section_number, $course_content, $teaching_date, $note, $registration_id, $term_id, $class_id, $subject_id);
            $stmt->execute();
        } elseif ($action == 'delete') {
            $delete_query = "DELETE FROM $syllabus_table_name WHERE registration_id = ? AND term_id = ? AND class_id = ? AND subject_id = ?";
            $stmt = $connection->prepare($delete_query);
            $stmt->bind_param("ssss", $registration_id, $term_id, $class_id, $subject_id);
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
        registration_id VARCHAR(255) NOT NULL,
        term_id VARCHAR(255),
        class_id VARCHAR(255) NOT NULL,
        subject_id VARCHAR(255) NOT NULL,
        assign_date DATE NOT NULL,
        week_id VARCHAR(255) NOT NULL,
        conduct_date DATE NOT NULL,
        start_time TIME NOT NULL,
        lesson_time VARCHAR(255) NOT NULL,
        mastery VARCHAR(255) NOT NULL,
        section_number INT NOT NULL,
        course_content TEXT NOT NULL,
        teaching_date DATE NOT NULL,
        note TEXT,
        PRIMARY KEY (registration_id, term_id, class_id, subject_id)
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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
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

        .scrollable-table-container {
            max-height: 400px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .error {
            color: red;
        }

        .form-container input,
        .form-container select,
        .form-container textarea {
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
</head>

<body>
    <div class="container">
        <div class="glass-container">
            <h3>Syllabus Table</h3>
            <div class="scrollable-table-container">
                <table border="1">
                    <tr>
                        <th>Registration ID</th>
                        <th>Term ID</th>
                        <th>Class ID</th>
                        <th>Subject ID</th>
                        <th>Assign Date</th>
                        <th>Week ID</th>
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
                                echo "<td>" . htmlspecialchars($row['registration_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['term_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['class_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['subject_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['assign_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['week_id']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['conduct_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['lesson_time']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['mastery']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['section_number']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['course_content']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['teaching_date']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['note']) . "</td>";
                                echo "<td>
                                        <button onclick='fillForm(" . json_encode($row) . ")'>Edit</button>
                                        <form method='post' action='' style='display:inline;'>
                                            <input type='hidden' name='registration_id' value='" . htmlspecialchars($row['registration_id']) . "'>
                                            <input type='hidden' name='term_id' value='" . htmlspecialchars($row['term_id']) . "'>
                                            <input type='hidden' name='class_id' value='" . htmlspecialchars($row['class_id']) . "'>
                                            <input type='hidden' name='subject_id' value='" . htmlspecialchars($row['subject_id']) . "'>
                                            <input type='hidden' name='action' value='delete'>
                                            <button type='submit'>Delete</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='15' class='error'>No syllabus details available for this user.</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='15' class='error'>Syllabus table not available.</td></tr>";
                    }
                    ?>
                </table>
            </div>
        </div>

        <div class="teacher-main-content">
            <div class="glass-container form-container">
                <h3>Add/Edit Syllabus Entry</h3>
                <form id="syllabus-form" method="post" action="">
                    <input type="hidden" id="action" name="action" value="create">
                    <label for="term_id">Term ID:</label>
                    <input type="text" id="term_id" name="term_id" required>
                    <label for="class_id">Class ID:</label>
                    <input type="text" id="class_id" name="class_id" required>
                    <label for="subject_id">Subject ID:</label>
                    <input type="text" id="subject_id" name="subject_id" required>
                    <label for="assign_date">Assign Date:</label>
                    <input type="date" id="assign_date" name="assign_date" required>
                    <label for="week_id">Week ID:</label>
                    <input type="text" id="week_id" name="week_id" required>
                    <label for="conduct_date">Conduct Date:</label>
                    <input type="date" id="conduct_date" name="conduct_date" required>
                    <label for="start_time">Start Time:</label>
                    <input type="time" id="start_time" name="start_time" required>
                    <label for="lesson_time">Lesson Time:</label>
                    <input type="text" id="lesson_time" name="lesson_time" required>
                    <label for="mastery">Mastery:</label>
                    <input type="text" id="mastery" name="mastery" required>
                    <label for="section_number">Section Number:</label>
                    <input type="number" id="section_number" name="section_number" required>
                    <label for="course_content">Course Content:</label>
                    <textarea id="course_content" name="course_content" rows="4" required></textarea>
                    <label for="teaching_date">Teaching Date:</label>
                    <input type="date" id="teaching_date" name="teaching_date" required>
                    <label for="note">Note:</label>
                    <textarea id="note" name="note" rows="4"></textarea>
                    <button type="submit">Save</button>
                    <button type="button" class="cancel" onclick="resetForm()">Cancel</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function fillForm(data) {
            document.getElementById('term_id').value = data.term_id;
            document.getElementById('class_id').value = data.class_id;
            document.getElementById('subject_id').value = data.subject_id;
            document.getElementById('assign_date').value = data.assign_date;
            document.getElementById('week_id').value = data.week_id;
            document.getElementById('conduct_date').value = data.conduct_date;
            document.getElementById('start_time').value = data.start_time;
            document.getElementById('lesson_time').value = data.lesson_time;
            document.getElementById('mastery').value = data.mastery;
            document.getElementById('section_number').value = data.section_number;
            document.getElementById('course_content').value = data.course_content.replace(/<\/?p>/g, ''); // Remove <p> tags
            document.getElementById('teaching_date').value = data.teaching_date;
            document.getElementById('note').value = data.note;
            document.getElementById('action').value = 'update';
        }

        function resetForm() {
            document.getElementById('syllabus-form').reset();
            document.getElementById('action').value = 'create';
        }
    </script>
</body>

</html>
