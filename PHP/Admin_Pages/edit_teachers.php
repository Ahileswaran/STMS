<?php
// Sample data for demonstration purposes
$teachers = [
    ['id' => 1, 'name' => 'John Doe', 'subject' => 'Math'],
    ['id' => 2, 'name' => 'Jane Smith', 'subject' => 'Science'],
    ['id' => 3, 'name' => 'Emily Johnson', 'subject' => 'English'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Teachers</title>
    <style>
        .container {
            padding: 20px;
            max-width: 800px; /* Set maximum width for the container */
            margin: 0 auto; /* Center the container */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .form-container {
            margin-top: 20px;
            max-width: 400px; /* Set maximum width for the form container */
            margin-left: auto; /* Align form to the right */
            margin-right: auto;
        }

        .form-container input[type="text"],
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
            width: 100%; /* Make the button full width */
        }

        .form-container button:hover {
            background-color: #45a049;
        }

        /* Responsive Design */
        @media only screen and (max-width: 768px) {
            .form-container {
                max-width: 100%; /* Set maximum width for smaller screens */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Teachers</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Subject</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td><?php echo htmlspecialchars($teacher['id']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['name']); ?></td>
                    <td><?php echo htmlspecialchars($teacher['subject']); ?></td>
                    <td>
                        <button onclick="editTeacher(<?php echo $teacher['id']; ?>)">Edit</button>
                        <button onclick="deleteTeacher(<?php echo $teacher['id']; ?>)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="form-container">
            <h3>Add/Edit Teacher</h3>
            <form id="teacher-form">
                <input type="hidden" id="teacher-id">
                <label for="teacher-name">Name:</label>
                <input type="text" id="teacher-name" required>
                <label for="teacher-subject">Subject:</label>
                <input type="text" id="teacher-subject" required>
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <script>
        function editTeacher(id) {
            // Fetch teacher data and fill the form for editing
            const teachers = <?php echo json_encode($teachers); ?>;
            const teacher = teachers.find(t => t.id === id);
            document.getElementById('teacher-id').value = teacher.id;
            document.getElementById('teacher-name').value = teacher.name;
            document.getElementById('teacher-subject').value = teacher.subject;
        }

        function deleteTeacher(id) {
            // Handle teacher deletion
            alert('Delete teacher with ID: ' + id);
        }

        document.getElementById('teacher-form').addEventListener('submit', function(event) {
            event.preventDefault();
            // Handle form submission for adding/editing teacher
            const id = document.getElementById('teacher-id').value;
            const name = document.getElementById('teacher-name').value;
            const subject = document.getElementById('teacher-subject').value;
            alert('Save teacher: ' + (id ? 'Edit' : 'Add') + ' - ' + name + ' (' + subject + ')');
        });
    </script>
</body>
</html>
