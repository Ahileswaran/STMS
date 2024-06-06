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
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Assessment planner table
$teacher_username = $_SESSION['username'];
$assessment_table_name = "teacher_assessment_table_" . $teacher_username;

// Create assessment table if not exists
$create_assessment_table_query = "CREATE TABLE IF NOT EXISTS $assessment_table_name (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id VARCHAR(255) NOT NULL,
    mastery TEXT NOT NULL,
    mastery_level TEXT NOT NULL,
    assessment_type VARCHAR(255) NOT NULL,
    intended_date DATE NOT NULL,
    completion_date DATE NOT NULL
)";
$connection->query($create_assessment_table_query);

// Handle form submission for creating and updating assessment entries
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $registration_id = $_SESSION['registration_id'];
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $mastery = strip_tags($_POST['mastery']);
        $mastery_level = strip_tags($_POST['mastery_level']);
        $assessment_type = $_POST['assessment_type'];
        $intended_date = $_POST['intended_date'];
        $completion_date = $_POST['completion_date'];
        $id = $_POST['id'];

        if ($action == 'create_assessment') {
            $create_query = "INSERT INTO $assessment_table_name (registration_id, mastery, mastery_level, assessment_type, intended_date, completion_date)
                             VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($create_query);
            $stmt->bind_param("ssssss", $registration_id, $mastery, $mastery_level, $assessment_type, $intended_date, $completion_date);
            $stmt->execute();
        } elseif ($action == 'update_assessment') {
            $update_query = "UPDATE $assessment_table_name SET mastery = ?, mastery_level = ?, assessment_type = ?, intended_date = ?, completion_date = ?
                             WHERE id = ? AND registration_id = ?";
            $stmt = $connection->prepare($update_query);
            $stmt->bind_param("sssssis", $mastery, $mastery_level, $assessment_type, $intended_date, $completion_date, $id, $registration_id);
            $stmt->execute();
        } elseif ($action == 'delete_assessment' && $is_admin) {
            $delete_query = "DELETE FROM $assessment_table_name WHERE id = ? AND registration_id = ?";
            $stmt = $connection->prepare($delete_query);
            $stmt->bind_param("is", $id, $registration_id);
            $stmt->execute();
        }
    }
}

try {
    $assessment_query = "SELECT * FROM $assessment_table_name WHERE registration_id = ?";
    $assessment_stmt = $connection->prepare($assessment_query);
    $assessment_stmt->bind_param("s", $_SESSION['registration_id']);
    $assessment_stmt->execute();
    $assessment_result = $assessment_stmt->get_result();
} catch (mysqli_sql_exception $e) {
    // Handle SQL exception
    $assessment_result = null;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Planner</title>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var quillMastery = new Quill('#mastery-editor', {
                theme: 'snow'
            });
            var quillMasteryLevel = new Quill('#mastery-level-editor', {
                theme: 'snow'
            });

            $("#intended_date").datepicker({
                dateFormat: "yy-mm-dd"
            });
            $("#completion_date").datepicker({
                dateFormat: "yy-mm-dd"
            });

            document.getElementById('assessment-form').onsubmit = function() {
                var masteryContent = document.querySelector('#mastery-editor .ql-editor').innerHTML;
                document.getElementById('mastery').value = masteryContent.replace(/<\/?p[^>]*>/g, '');
                var masteryLevelContent = document.querySelector('#mastery-level-editor .ql-editor').innerHTML;
                document.getElementById('mastery_level').value = masteryLevelContent.replace(/<\/?p[^>]*>/g, '');
            };
        });

        function fillAssessmentForm(data) {
            document.getElementById('assessment_id').value = data.id;
            document.getElementById('mastery').value = data.mastery;
            document.querySelector('#mastery-editor .ql-editor').innerHTML = data.mastery;
            document.getElementById('mastery_level').value = data.mastery_level;
            document.querySelector('#mastery-level-editor .ql-editor').innerHTML = data.mastery_level;
            document.getElementById('assessment_type').value = data.assessment_type;
            document.getElementById('intended_date').value = data.intended_date;
            document.getElementById('completion_date').value = data.completion_date;
            document.getElementById('assessment_action').value = 'update_assessment';
        }

        function resetAssessmentForm() {
            document.getElementById('assessment-form').reset();
            document.querySelector('#mastery-editor .ql-editor').innerHTML = '';
            document.querySelector('#mastery-level-editor .ql-editor').innerHTML = '';
            document.getElementById('assessment_action').value = 'create_assessment';
        }
    </script>
</head>

<body>
    <div class="container">
        <div class="glass-container">
            <h3>Assessment Planner</h3>
            <table border="1">
                <tr>
                    <th>Mastery</th>
                    <th>Mastery Level</th>
                    <th>Assessment Type</th>
                    <th>Intended Date</th>
                    <th>Completion Date</th>
                    <th>Actions</th>
                </tr>
                <?php
                if ($assessment_result) {
                    if ($assessment_result->num_rows > 0) {
                        while ($row = $assessment_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['mastery']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['mastery_level']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['assessment_type']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['intended_date']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['completion_date']) . "</td>";
                            echo "<td>
                                    <button onclick='fillAssessmentForm(" . json_encode($row) . ")'>Edit</button>";
                            if ($is_admin) {
                                echo "<form method='post' action='' style='display:inline;'>
                                        <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                        <input type='hidden' name='action' value='delete_assessment'>
                                        <button type='submit'>Delete</button>
                                      </form>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='error'>No assessment details available for this user.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='error'>Assessment planner not available.</td></tr>";
                }
                ?>
            </table>
        </div>

        <div class="glass-container form-container">
            <h3>Add/Edit Assessment Entry</h3>
            <form id="assessment-form" method="post" action="">
                <input type="hidden" id="assessment_id" name="id">
                <input type="hidden" id="assessment_action" name="action" value="create_assessment">
                <label for="mastery">Mastery</label>
                <div id="mastery-editor" class="quill-editor"></div>
                <input type="hidden" id="mastery" name="mastery">
                <label for="mastery_level">Mastery Level</label>
                <div id="mastery-level-editor" class="quill-editor"></div>
                <input type="hidden" id="mastery_level" name="mastery_level">
                <label for="assessment_type">Assessment Type</label>
                <input type="text" id="assessment_type" name="assessment_type" required>
                <label for="intended_date">Intended Date</label>
                <input type="text" id="intended_date" name="intended_date">
                <label for="completion_date">Completion Date</label>
                <input type="text" id="completion_date" name="completion_date">
                <button type="submit">Save</button>
                <button type="button" class="cancel" onclick="resetAssessmentForm()">Cancel</button>
            </form>
        </div>
    </div>
</body>

</html>
