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

// Fetch profile picture from database
$session_username = $_SESSION['username'];
$sql = "SELECT profile_pic FROM profile_picture WHERE username = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $session_username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Profile picture found, display it
    $stmt->bind_result($profile_pic_data);
    $stmt->fetch();
    $profile_pic = base64_encode($profile_pic_data);
    $profile_pic_src = 'data:image/jpeg;base64,' . $profile_pic;
} else {
    // Profile picture not found, use a default image
    $profile_pic_src = 'path_to_default_image.jpg'; // Replace with the path to your default image
}

// Handle form submission for creating and updating letters
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save'])) {
        $content = $_POST['longText'];
        $letter_id = isset($_POST['letter_id']) ? $_POST['letter_id'] : null;

        if ($letter_id) {
            // Update existing letter
            $sql = "UPDATE leave_letter SET content = ? WHERE letter_id = ? AND username = ?";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("sis", $content, $letter_id, $session_username);
        } else {
            // Insert new letter
            $sql = "INSERT INTO leave_letter (username, content) VALUES (?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("ss", $session_username, $content);
        }

        if ($stmt->execute()) {
            echo "Leave letter saved successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } elseif (isset($_POST['delete'])) {
        // Delete letter
        $letter_id = $_POST['letter_id'];
        $sql = "DELETE FROM leave_letter WHERE letter_id = ? AND username = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param("is", $letter_id, $session_username);

        if ($stmt->execute()) {
            echo "Leave letter deleted successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
}

// Fetch all letters for the logged-in user
$sql = "SELECT letter_id, date, time, content FROM leave_letter WHERE username = ? ORDER BY date DESC";
$stmt = $connection->prepare($sql);
$stmt->bind_param("s", $session_username);
$stmt->execute();
$result = $stmt->get_result();
$letters = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <title>School Teacher Management System</title>
    <link rel="stylesheet" href="../../CSS/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f2f5;
        }

        .content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .leave-letter-page {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .leave-letter-page h1 {
            text-align: center;
            color: #333;
            font-size: 2.5em;
            margin-bottom: 20px;
        }

        .Write-letter {
            margin: 20px 0;
        }

        .Write-letter h3 {
            color: #333;
            font-size: 1.5em;
            margin-bottom: 10px;
        }

        .quill-editor {
            height: 400px;
        }

        .Write-letter button {
            background-color: #4CAF50;
            color: white;
            padding: 15px 20px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1em;
        }

        .Write-letter button:hover {
            background-color: #45a049;
        }

        .buttons-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
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

        .letters-container {
            max-height: 400px;
            overflow-y: auto;
            margin-top: 20px;
            display: none;
        }

        .letter-item {
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            position: relative;
        }

        .letter-item h4 {
            margin: 0;
            font-size: 1.2em;
            color: #333;
        }

        .letter-item p {
            margin: 5px 0;
            font-size: 1em;
            color: #555;
        }

        .letter-item button {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 8px;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .quill-editor {
                height: 300px;
            }

            .leave-letter-page h1 {
                font-size: 2em;
            }

            .Write-letter h3 {
                font-size: 1.2em;
            }
        }
    </style>
    <script>
        function toggleLetters(containerClass) {
            var container = document.querySelector(containerClass);
            if (container.style.display === "none" || container.style.display === "") {
                container.style.display = "block";
            } else {
                container.style.display = "none";
            }
        }

        function printLetter() {
            var printContents = document.querySelector('.ql-editor').innerHTML;
            var originalContents = document.body.innerHTML;

            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            window.location.reload();
        }

        document.addEventListener("DOMContentLoaded", function() {
            var quill = new Quill('#editor-container', {
                theme: 'snow'
            });
            window.quillEditor = quill;

            var form = document.querySelector('form');
            form.onsubmit = function() {
                var quillContent = document.querySelector('.ql-editor').innerHTML;
                document.getElementById('longText').value = quillContent;
            };
        });
    </script>
</head>

<body>
    <div class="content">
        <!-- main content goes here -->
        <div class="leave-letter-page">
            <div class="Write-letter">
                <h3><b>Write the Leave Letter Here</b></h3>
                <form method="post" action="">
                    <div id="editor-container" class="quill-editor"></div>
                    <input type="hidden" id="letter_id" name="letter_id">
                    <textarea id="longText" name="longText" style="display:none;"></textarea>
                    <button type="submit" name="save" value="txt">Submit</button>
                    <button type="button" onclick="printLetter()">Print</button>
                </form>
            </div>
            <div class="buttons-container">
                <button onclick="toggleLetters('.all-letters-container')">View All Letters</button>
                <button onclick="toggleLetters('.previous-letters-container')">View Previous Letters</button>
            </div>
            <div class="letters-container all-letters-container">
                <h3>All Letters</h3>
                <?php foreach ($letters as $letter): ?>
                    <div class="letter-item">
                        <h4>Letter ID: <?php echo $letter['letter_id']; ?></h4>
                        <p>Date: <?php echo $letter['date']; ?></p>
                        <p>Time: <?php echo $letter['time']; ?></p>
                        <p><?php echo nl2br(htmlspecialchars($letter['content'], ENT_QUOTES, 'UTF-8')); ?></p>
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="letter_id" value="<?php echo $letter['letter_id']; ?>">
                            <button type="submit" name="delete">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="letters-container previous-letters-container">
                <h3>Previous Letters</h3>
                <?php foreach (array_slice($letters, 0, 5) as $letter): ?>
                    <div class="letter-item">
                        <h4>Letter ID: <?php echo $letter['letter_id']; ?></h4>
                        <p>Date: <?php echo $letter['date']; ?></p>
                        <p>Time: <?php echo $letter['time']; ?></p>
                        <p><?php echo nl2br(htmlspecialchars($letter['content'], ENT_QUOTES, 'UTF-8')); ?></p>
                        <form method="post" action="" style="display:inline;">
                            <input type="hidden" name="letter_id" value="<?php echo $letter['letter_id']; ?>">
                            <button type="submit" name="delete">Delete</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>
