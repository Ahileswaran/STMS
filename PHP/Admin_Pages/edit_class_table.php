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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['delete_class_id'])) {
        // Delete operation
        $delete_class_id = $_POST['delete_class_id'];
        $delete_start_time = $_POST['delete_start_time'];
        $sql = "DELETE FROM class_time_table WHERE class_id=? AND start_time=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("ss", $delete_class_id, $delete_start_time);
        $stmt->execute();
    } else {
        // Create or Update operation
        $class_id = $_POST['class_id'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $monday = $_POST['monday'];
        $tuesday = $_POST['tuesday'];
        $wednesday = $_POST['wednesday'];
        $thursday = $_POST['thursday'];
        $friday = $_POST['friday'];
        $subject_id = $_POST['subject_id'];
        $category_id = $_POST['category_id'];

        if (isset($_POST['update_class_id'])) {
            // Update existing record
            $update_class_id = $_POST['update_class_id'];
            $update_start_time = $_POST['update_start_time'];
            $sql = "UPDATE class_time_table SET class_id=?, start_time=?, end_time=?, monday=?, tuesday=?, wednesday=?, thursday=?, friday=?, subject_id=?, category_id=? WHERE class_id=? AND start_time=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("ssssssssisss", $class_id, $start_time, $end_time, $monday, $tuesday, $wednesday, $thursday, $friday, $subject_id, $category_id, $update_class_id, $update_start_time);
        } else {
            // Insert new record
            $sql = "INSERT INTO class_time_table (class_id, start_time, end_time, monday, tuesday, wednesday, thursday, friday, subject_id, category_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("ssssssssii", $class_id, $start_time, $end_time, $monday, $tuesday, $wednesday, $thursday, $friday, $subject_id, $category_id);
        }
        $stmt->execute();
    }
}

$sql = "SELECT * FROM class_time_table";
$result = $connection->query($sql);

$class_time_table = [];
if ($result) {
    $class_time_table = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../../styles.css">
    <style>
        .container {
            display: flex;
            flex-direction: row;
            padding: 20px;
        }

        .scrollable {
            overflow-y: scroll;
            height: 1000px;
            flex: 1;
            max-width: 1174px;
            margin-left: 215px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
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

        .form-container input[type="text"],
        .form-container input[type="number"],
        .form-container select {
            width: calc(100% - 22px);
            padding: 10px;
            margin-bottom: 10px;
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
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown_details:hover .dropdown-content {
            display: block;
        }

        .dropdown-content p, .dropdown-content a {
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

        .main-content {
            width: 100%;
            height: 1000px; /* Adjust as needed */
            border: none;
        }
    </style>
</head>
<div class="container">
    <div class="scrollable">
        <h2>Edit Class Time Table</h2>
        <table>
            <thead>
                <tr>
                    <th>Class ID</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Monday</th>
                    <th>Tuesday</th>
                    <th>Wednesday</th>
                    <th>Thursday</th>
                    <th>Friday</th>
                    <th>Subject ID</th>
                    <th>Category ID</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($class_time_table as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['class_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['monday']); ?></td>
                    <td><?php echo htmlspecialchars($row['tuesday']); ?></td>
                    <td><?php echo htmlspecialchars($row['wednesday']); ?></td>
                    <td><?php echo htmlspecialchars($row['thursday']); ?></td>
                    <td><?php echo htmlspecialchars($row['friday']); ?></td>
                    <td><?php echo htmlspecialchars($row['subject_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['category_id']); ?></td>
                    <td>
                        <button class="edit-button" data-class-id="<?php echo $row['class_id']; ?>" data-start-time="<?php echo $row['start_time']; ?>">Edit</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_class_id" value="<?php echo $row['class_id']; ?>">
                            <input type="hidden" name="delete_start_time" value="<?php echo $row['start_time']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($class_time_table)): ?>
            <p>No records found.</p>
        <?php endif; ?>
    </div>

    <div class="form-container">
        <h3>Add/Edit Class Time Table</h3>
        <form id="class-time-table-form" method="POST">
            <input type="hidden" name="update_class_id" id="update_class_id">
            <input type="hidden" name="update_start_time" id="update_start_time">
            <label for="class_id">Class ID:</label>
            <input type="text" name="class_id" id="class_id" required>
            <label for="start_time">Start Time:</label>
            <input type="time" name="start_time" id="start_time" required>
            <label for="end_time">End Time:</label>
            <input type="time" name="end_time" id="end_time" required>
            <label for="monday">Monday:</label>
            <input type="text" name="monday" id="monday" required>
            <label for="tuesday">Tuesday:</label>
            <input type="text" name="tuesday" id="tuesday" required>
            <label for="wednesday">Wednesday:</label>
            <input type="text" name="wednesday" id="wednesday" required>
            <label for="thursday">Thursday:</label>
            <input type="text" name="thursday" id="thursday" required>
            <label for="friday">Friday:</label>
            <input type="text" name="friday" id="friday" required>
            <label for="subject_id">Subject ID:</label>
            <input type="number" name="subject_id" id="subject_id">
            <label for="category_id">Category ID:</label>
            <input type="number" name="category_id" id="category_id">
            <button type="submit">Save</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function bindEditFunction() {
            document.querySelectorAll('.edit-button').forEach(button => {
                button.addEventListener('click', function() {
                    const class_id = this.dataset.classId;
                    const start_time = this.dataset.startTime;
                    editRecord(class_id, start_time);
                });
            });
        }

        function editRecord(class_id, start_time) {
            const records = <?php echo json_encode($class_time_table); ?>;
            const record = records.find(r => r.class_id === class_id && r.start_time === start_time);
            if (record) {
                document.getElementById('update_class_id').value = record.class_id;
                document.getElementById('update_start_time').value = record.start_time;
                document.getElementById('class_id').value = record.class_id;
                document.getElementById('start_time').value = record.start_time;
                document.getElementById('end_time').value = record.end_time;
                document.getElementById('monday').value = record.monday;
                document.getElementById('tuesday').value = record.tuesday;
                document.getElementById('wednesday').value = record.wednesday;
                document.getElementById('thursday').value = record.thursday;
                document.getElementById('friday').value = record.friday;
                document.getElementById('subject_id').value = record.subject_id;
                document.getElementById('category_id').value = record.category_id;
            }
        }

        bindEditFunction();
    });
</script>
