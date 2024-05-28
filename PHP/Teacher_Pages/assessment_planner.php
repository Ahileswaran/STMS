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

// Assessment planner table
$teacher_username = $_SESSION['username'];
$assessment_table_name = "teacher_assessment_table_" . $teacher_username;

// Create assessment table if not exists
$create_assessment_table_query = "CREATE TABLE IF NOT EXISTS $assessment_table_name (
    id INT AUTO_INCREMENT PRIMARY KEY,
    registration_id VARCHAR(255) NOT NULL,
    area VARCHAR(255) NOT NULL,
    criteria TEXT NOT NULL,
    rating INT NOT NULL,
    improvement TEXT
)";
$connection->query($create_assessment_table_query);

// Handle form submission for creating and updating assessment entries
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $registration_id = $_SESSION['registration_id'];
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $area = $_POST['area'];
        $criteria = $_POST['criteria'];
        $rating = $_POST['rating'];
        $improvement = $_POST['improvement'];
        $id = $_POST['id'];

        if ($action == 'create_assessment') {
            $create_query = "INSERT INTO $assessment_table_name (registration_id, area, criteria, rating, improvement)
                             VALUES (?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($create_query);
            $stmt->bind_param("sssds", $registration_id, $area, $criteria, $rating, $improvement);
            $stmt->execute();
        } elseif ($action == 'update_assessment') {
            $update_query = "UPDATE $assessment_table_name SET area = ?, criteria = ?, rating = ?, improvement = ?
                             WHERE id = ? AND registration_id = ?";
            $stmt = $connection->prepare($update_query);
            $stmt->bind_param("ssdsis", $area, $criteria, $rating, $improvement, $id, $registration_id);
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
            var quillCriteria = new Quill('#criteria-editor', {
                theme: 'snow'
            });

            document.getElementById('assessment-form').onsubmit = function() {
                var criteriaContent = document.querySelector('#criteria-editor .ql-editor').innerHTML;
                document.getElementById('criteria').value = criteriaContent;
            };
        });

        function fillAssessmentForm(data) {
            document.getElementById('assessment_id').value = data.id;
            document.getElementById('area').value = data.area;
            document.getElementById('criteria').value = data.criteria;
            document.querySelector('#criteria-editor .ql-editor').innerHTML = data.criteria;
            document.getElementById('rating').value = data.rating;
            document.getElementById('improvement').value = data.improvement;
            document.getElementById('assessment_action').value = 'update_assessment';
        }

        function resetAssessmentForm() {
            document.getElementById('assessment-form').reset();
            document.querySelector('#criteria-editor .ql-editor').innerHTML = '';
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
                    <th>Area</th>
                    <th>Criteria</th>
                    <th>Rating</th>
                    <th>Improvement</th>
                    <th>Actions</th>
                </tr>
                <?php
                if ($assessment_result) {
                    if ($assessment_result->num_rows > 0) {
                        while ($row = $assessment_result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['area']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['criteria']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['rating']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['improvement']) . "</td>";
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
                        echo "<tr><td colspan='5' class='error'>No assessment details available for this user.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='error'>Assessment planner not available.</td></tr>";
                }
                ?>
            </table>
        </div>

        <div class="glass-container form-container">
            <h3>Add/Edit Assessment Entry</h3>
            <form id="assessment-form" method="post" action="">
                <input type="hidden" id="assessment_id" name="id">
                <input type="hidden" id="assessment_action" name="action" value="create_assessment">
                <label for="area">Area:</label>
                <input type="text" id="area" name="area" required>
                <label for="criteria">Criteria:</label>
                <div id="criteria-editor" class="quill-editor"></div>
                <input type="hidden" id="criteria" name="criteria">
                <label for="rating">Rating:</label>
                <input type="number" id="rating" name="rating" min="1" max="5" required>
                <label for="improvement">Improvement:</label>
                <textarea id="improvement" name="improvement"></textarea>
                <button type="submit">Save</button>
                <button type="button" class="cancel" onclick="resetAssessmentForm()">Cancel</button>
            </form>
        </div>
    </div>
</body>

</html>
