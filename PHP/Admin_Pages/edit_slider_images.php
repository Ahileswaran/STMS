<?php
// Start the session and connect to the database
session_start();
$username = "root";
$password = "";
$server = "localhost";
$database = "stms_database";

$connection = new mysqli($server, $username, $password, $database);
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}
/*
// Function to add image to the database
function addImage($path, $caption) {
    global $connection;
    $stmt = $connection->prepare("INSERT INTO carousel_images (image_path, caption) VALUES (?, ?)");
    $stmt->bind_param("ss", $path, $caption);
    $stmt->execute();
    return $stmt->affected_rows > 0;
}

// Function to remove image from the database
function removeImage($id) {
    global $connection;
    $stmt = $connection->prepare("DELETE FROM carousel_images WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->affected_rows > 0;
}

// Handle post requests for adding/removing images
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        addImage($_POST['image_path'], $_POST['caption']);
    } elseif (isset($_POST['remove'])) {
        removeImage($_POST['image_id']);
    }
}

// Fetch all images to list
$result = $connection->query("SELECT * FROM carousel_images");
$images = [];
while ($row = $result->fetch_assoc()) {
    $images[] = $row;
}
*/

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Carousel Images</title>
    <style>
        .container {
            padding: 250px;
            font-family: Arial, sans-serif;
            /* Consistent font */
        }

        .add-image-form {
            background-color: #f9f9f9;
            /* Light grey background */
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* Subtle shadow */
            display: flex;
            flex-direction: column;
            gap: 10px;
            /* Spacing between form elements */
            margin-bottom: 20px;
        }

        .add-image-form input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            /* Include padding in width calculation */
        }

        .add-image-form input[type="text"]:focus {
            border-color: #4CAF50;
            /* Highlight focus with theme color */
            outline: none;
            /* Remove default focus outline */
        }

        .add-image-form button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            /* Smooth transition for hover effect */
        }

        .add-image-form button:hover {
            background-color: #45a049;
        }

        /* Optional: Responsive adjustments */
        @media (max-width: 600px) {
            .add-image-form {
                width: calc(100% - 40px);
                /* Full-width on smaller screens */
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Manage Carousel Images</h2>
        <form method="post" class="add-image-form">
            <input type="text" name="image_path" placeholder="Enter image path" required>
            <input type="text" name="caption" placeholder="Enter caption">
            <button type="submit" name="add">Add Image</button>
        </form>


        <h3>Current Images</h3>
        <?php foreach ($images as $image) : ?>
            <div>
                <img src="<?= htmlspecialchars($image['image_path']) ?>" alt="Carousel Image" style="width: 100px;">
                <p><?= htmlspecialchars($image['caption']) ?></p>
                <form method="post">
                    <input type="hidden" name="image_id" value="<?= $image['id'] ?>">
                    <button type="submit" name="remove">Remove Image</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>

</html>