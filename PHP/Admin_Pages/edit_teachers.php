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

// Fetch all teachers
function readTeachers($connection) {
    $result = $connection->query("SELECT * FROM teacher");
    return $result->fetch_all(MYSQLI_ASSOC);
}

$teachers = readTeachers($connection);
?>

<div class="container">
    <div class="scrollable">
        <h2>Edit Teachers</h2>
        <table>
            <thead>
                <tr>
                    <th>Registration ID</th>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Subject</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td><?php echo htmlspecialchars($teacher['registration_id']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['subject_name']); ?></td>
                    <td>
                        <button class="edit-button" data-id="<?php echo $teacher['registration_id']; ?>">Edit</button>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_id" value="<?php echo $teacher['registration_id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="form-container">
        <h3>Add/Edit Teacher</h3>
        <form id="teacher-form" method="POST">
            <input type="hidden" name="registration_id" id="registration_id">
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" id="first_name" required>
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" id="last_name" required>
            <label for="user_address">Address:</label>
            <input type="text" name="user_address" id="user_address" required>
            <label for="age">Age:</label>
            <input type="number" name="age" id="age" required>
            <label for="sex">Sex:</label>
            <input type="text" name="sex" id="sex" required>
            <label for="marital_status">Marital Status:</label>
            <input type="text" name="marital_status" id="marital_status" required>
            <label for="subject_name">Subject:</label>
            <input type="text" name="subject_name" id="subject_name" required>
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
            <label for="email">Email:</label>
            <input type="text" name="email" id="email" required>
            <label for="user_password">Password:</label>
            <input type="text" name="user_password" id="user_password" required>
            <button type="submit">Save</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function bindEditTeacherFunction() {
            document.querySelectorAll('.edit-button').forEach(button => {
                button.addEventListener('click', function() {
                    const registration_id = this.dataset.id;
                    editTeacher(registration_id);
                });
            });
        }

        function editTeacher(registration_id) {
            // Fetch teacher data and fill the form for editing
            const teachers = <?php echo json_encode($teachers); ?>;
            const teacher = teachers.find(t => t.registration_id === registration_id);
            document.getElementById('registration_id').value = teacher.registration_id;
            document.getElementById('first_name').value = teacher.first_name;
            document.getElementById('last_name').value = teacher.last_name;
            document.getElementById('user_address').value = teacher.user_address;
            document.getElementById('age').value = teacher.age;
            document.getElementById('sex').value = teacher.sex;
            document.getElementById('marital_status').value = teacher.marital_status;
            document.getElementById('subject_name').value = teacher.subject_name;
            document.getElementById('username').value = teacher.username;
            document.getElementById('email').value = teacher.email;
            document.getElementById('user_password').value = teacher.user_password;
        }

        bindEditTeacherFunction();
    });
</script>
