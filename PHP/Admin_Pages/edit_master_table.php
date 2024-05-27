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
    if (isset($_POST['delete_id'])) {
        // Delete operation
        $delete_id = $_POST['delete_id'];
        $sql = "DELETE FROM master_time_table WHERE id=?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
    } else {
        // Create or Update operation
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $username = $_POST['username'];
        $registration_id = $_POST['registration_id'];
        $class_id = $_POST['class_id'];
        $subject_id = $_POST['subject_id'];
        $class_day = $_POST['class_day'];
        $start_time = $_POST['start_time'];
        $end_time = $_POST['end_time'];
        $period = $_POST['period'];

        if (empty($id)) {
            // Insert new record
            $sql = "INSERT INTO master_time_table (username, registration_id, class_id, subject_id, class_day, start_time, end_time, period) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sssssiii", $username, $registration_id, $class_id, $subject_id, $class_day, $start_time, $end_time, $period);
        } else {
            // Update existing record
            $sql = "UPDATE master_time_table SET username=?, registration_id=?, class_id=?, subject_id=?, class_day=?, start_time=?, end_time=?, period=? WHERE id=?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sssssiiii", $username, $registration_id, $class_id, $subject_id, $class_day, $start_time, $end_time, $period, $id);
        }
        $stmt->execute();
    }
}

$sql = "SELECT * FROM master_time_table";
$result = $connection->query($sql);

$master_time_table = [];
if ($result) {
    $master_time_table = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="container">
    <div class="scrollable">
        <h2>Edit Master Time Table</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Registration ID</th>
                    <th>Class ID</th>
                    <th>Subject ID</th>
                    <th>Class Day</th>
                    <th>Start Time</th>
                    <th>End Time</th>
                    <th>Period</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($master_time_table as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['registration_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['class_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['subject_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['class_day']); ?></td>
                    <td><?php echo htmlspecialchars($row['start_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['end_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['period']); ?></td>
                    <td>
                        <button class="edit-button" data-id="<?php echo $row['id']; ?>">Edit</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($master_time_table)): ?>
            <p>No records found.</p>
        <?php endif; ?>
    </div>

    <div class="form-container">
        <h3>Add/Edit Master Time Table</h3>
        <form id="master-time-table-form" method="POST">
            <input type="hidden" name="id" id="id">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
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
            <label for="period">Period:</label>
            <input type="number" name="period" id="period" required>
            <button type="submit">Save</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function bindEditFunction() {
            document.querySelectorAll('.edit-button').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    editRecord(id);
                });
            });
        }

        function editRecord(id) {
            const records = <?php echo json_encode($master_time_table); ?>;
            const record = records.find(r => r.id == id);
            if (record) {
                document.getElementById('id').value = record.id;
                document.getElementById('username').value = record.username;
                document.getElementById('registration_id').value = record.registration_id;
                document.getElementById('class_id').value = record.class_id;
                document.getElementById('subject_id').value = record.subject_id;
                document.getElementById('class_day').value = record.class_day;
                document.getElementById('start_time').value = record.start_time;
                document.getElementById('end_time').value = record.end_time;
                document.getElementById('period').value = record.period;
            }
        }

        bindEditFunction();
    });
</script>
