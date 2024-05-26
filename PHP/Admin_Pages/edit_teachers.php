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


// Define CRUD operations
function createTeacher($connection, $first_name, $last_name, $user_address, $age, $sex, $marital_status, $registration_id, $subject_name, $username, $email, $user_password) {
    $stmt = $connection->prepare("INSERT INTO teacher (first_name, last_name, user_address, age, sex, marital_status, registration_id, subject_name, username, email, user_password) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisssssss", $first_name, $last_name, $user_address, $age, $sex, $marital_status, $registration_id, $subject_name, $username, $email, $user_password);
    return $stmt->execute();
}

function readTeachers($connection) {
    $result = $connection->query("SELECT * FROM teacher");
    return $result->fetch_all(MYSQLI_ASSOC);
}

function updateTeacher($connection, $registration_id, $first_name, $last_name, $user_address, $age, $sex, $marital_status, $subject_name, $username, $email, $user_password) {
    $stmt = $connection->prepare("UPDATE teacher SET first_name = ?, last_name = ?, user_address = ?, age = ?, sex = ?, marital_status = ?, subject_name = ?, username = ?, email = ?, user_password = ? WHERE registration_id = ?");
    $stmt->bind_param("sssisssssss", $first_name, $last_name, $user_address, $age, $sex, $marital_status, $subject_name, $username, $email, $user_password, $registration_id);
    return $stmt->execute();
}

function deleteTeacher($connection, $registration_id) {
    $stmt = $connection->prepare("DELETE FROM teacher WHERE registration_id = ?");
    $stmt->bind_param("s", $registration_id);
    return $stmt->execute();
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['registration_id']) && !empty($_POST['registration_id'])) {
        // Update teacher
        $registration_id = $_POST['registration_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $user_address = $_POST['user_address'];
        $age = $_POST['age'];
        $sex = $_POST['sex'];
        $marital_status = $_POST['marital_status'];
        $subject_name = $_POST['subject_name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $user_password = $_POST['user_password'];
        updateTeacher($connection, $registration_id, $first_name, $last_name, $user_address, $age, $sex, $marital_status, $subject_name, $username, $email, $user_password);
    } elseif (isset($_POST['first_name']) && isset($_POST['last_name'])) {
        // Create new teacher
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $user_address = $_POST['user_address'];
        $age = $_POST['age'];
        $sex = $_POST['sex'];
        $marital_status = $_POST['marital_status'];
        $registration_id = $_POST['registration_id'];
        $subject_name = $_POST['subject_name'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $user_password = $_POST['user_password'];
        createTeacher($connection, $first_name, $last_name, $user_address, $age, $sex, $marital_status, $registration_id, $subject_name, $username, $email, $user_password);
    }

    if (isset($_POST['delete_id'])) {
        // Delete teacher
        $registration_id = $_POST['delete_id'];
        deleteTeacher($connection, $registration_id);
    }

    // Refresh the page to show the updated data
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
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
                        <button onclick="editTeacher('<?php echo $teacher['registration_id']; ?>')">Edit</button>
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
</script>
